<?php
/**
 * High-level Zoho Recruit API v2 service: connection check, Job Openings sync, Candidate creation, Resume upload, and Job association.
 *
 * @package ASRS\RecruitEngine\Zoho
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Zoho;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecruitService {

	private const OPT_JOBS_CACHE = 'asrs_recruit_jobs_cache';
	private const OPT_ORG_ID     = 'asrs_recruit_org_id';

	private RecruitClient $client;
	private RecruitAuthService $auth;
	private Settings $settings;
	private ApplicationRepository $applications;

	public function __construct(
		RecruitClient $client,
		RecruitAuthService $auth,
		Settings $settings,
		ApplicationRepository $applications
	) {
		$this->client       = $client;
		$this->auth         = $auth;
		$this->settings     = $settings;
		$this->applications = $applications;
	}

	/**
	 * Test Zoho Recruit connection using GET /recruit/v2/settings/modules.
	 *
	 * @return array<string, mixed>|WP_Error
	 */
	public function test_connection() {
		$token = $this->auth->get_access_token( true );
		if ( is_wp_error( $token ) ) {
			$this->settings->update_zoho_health( 'failed', $token->get_error_message() );
			return $token;
		}

		$result = $this->client->request( 'GET', '/settings/modules' );
		if ( is_wp_error( $result ) ) {
			$this->settings->update_zoho_health( 'failed', $result->get_error_message() );
			return $result;
		}

		$modules = $result['body']['modules'] ?? array();
		$this->settings->update_zoho_health( 'connected', '' );
		$this->get_org_id( true );

		return array(
			'modules_count' => is_array( $modules ) ? count( $modules ) : 0,
			'tested_at'     => current_time( 'mysql', true ),
		);
	}

	/**
	 * Fetch and cache Zoho Recruit Organization ID (zgid) for clean deep links.
	 */
	public function get_org_id( bool $refresh = false ): string {
		if ( ! $refresh ) {
			$cached = get_option( self::OPT_ORG_ID, '' );
			if ( is_string( $cached ) && '' !== $cached ) {
				return $cached;
			}
		}

		$res = $this->client->request( 'GET', '/org' );
		if ( is_wp_error( $res ) ) {
			return '';
		}

		$org = $res['body']['org'][0] ?? null;
		if ( is_array( $org ) ) {
			$org_id = (string) ( $org['zgid'] ?? $org['id'] ?? '' );
			if ( '' !== $org_id ) {
				update_option( self::OPT_ORG_ID, $org_id, false );
				return $org_id;
			}
		}

		return '';
	}

	/**
	 * Fetch Job Openings from Zoho Recruit and cache locally for fast zero-latency Careers Page rendering.
	 *
	 * @return array<int, array<string, mixed>>|WP_Error
	 */
	public function sync_job_openings() {
		$result = $this->client->request( 'GET', '/JobOpenings?per_page=200' );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$raw_jobs = ( 204 === $result['code'] ) ? array() : ( $result['body']['data'] ?? array() );
		$jobs     = array();

		$only_published = (bool) $this->settings->get( 'only_published_jobs', false );
		$allowed_raw    = (string) $this->settings->get( 'allowed_job_statuses', '' );
		$allowed_list   = array_filter( array_map( static fn( string $s ): string => mb_strtolower( trim( $s ) ), explode( ',', $allowed_raw ) ) );

		if ( is_array( $raw_jobs ) ) {
			foreach ( $raw_jobs as $job ) {
				if ( ! is_array( $job ) || empty( $job['id'] ) ) {
					continue;
				}

				$status = (string) ( $job['Job_Opening_Status'] ?? '' );
				if ( ! empty( $allowed_list ) && '' !== $status && ! in_array( mb_strtolower( trim( $status ) ), $allowed_list, true ) ) {
					continue;
				}

				if ( $only_published && empty( $job['Publish'] ) ) {
					continue;
				}

				// The list endpoint can flatten rich text. Fetch each included job by ID
				// to retain the original headings, paragraphs and lists.
				$detail = $this->client->request( 'GET', '/Job_Openings/' . rawurlencode( (string) $job['id'] ) );
				if ( is_wp_error( $detail ) ) {
					return new WP_Error(
						'asrs_recruit_job_detail_error',
						sprintf(
							/* translators: 1: Zoho job ID, 2: API error message. */
							__( 'Could not fetch formatted details for job %1$s: %2$s. The previous job cache was kept.', 'asrs-recruit-engine' ),
							(string) $job['id'],
							$detail->get_error_message()
						)
					);
				}

				$full_job = $detail['body']['data'][0] ?? null;
				if ( ! is_array( $full_job ) || (string) ( $full_job['id'] ?? '' ) !== (string) $job['id'] ) {
					return new WP_Error(
						'asrs_recruit_invalid_job_detail',
						sprintf(
							/* translators: %s: Zoho job ID. */
							__( 'Zoho returned invalid details for job %s. The previous job cache was kept.', 'asrs-recruit-engine' ),
							(string) $job['id']
						)
					);
				}

				foreach ( array( 'Job_Description', 'Requirements', 'Benefits' ) as $field ) {
					if ( array_key_exists( $field, $full_job ) ) {
						$job[ $field ] = $full_job[ $field ];
					} elseif ( ! empty( $job[ $field ] ) ) {
						return new WP_Error(
							'asrs_recruit_missing_job_detail_field',
							sprintf(
								/* translators: 1: Zoho job ID, 2: field API name. */
								__( 'Zoho omitted %2$s from job %1$s details. The previous job cache was kept.', 'asrs-recruit-engine' ),
								(string) $job['id'],
								$field
							)
						);
					}
				}

				$dept = '';
				if ( isset( $job['Department_Name'] ) && is_array( $job['Department_Name'] ) ) {
					$dept = (string) ( $job['Department_Name']['name'] ?? '' );
				} elseif ( ! empty( $job['Department_Name'] ) ) {
					$dept = (string) $job['Department_Name'];
				}

				$jobs[] = array(
					'id'               => (string) $job['id'],
					'title'            => (string) ( $job['Posting_Title'] ?? $job['Job_Opening_Name'] ?? __( 'Open Position', 'asrs-recruit-engine' ) ),
					'department'       => $dept,
					'city'             => (string) ( $job['City'] ?? '' ),
					'state'            => (string) ( $job['State'] ?? '' ),
					'country'          => (string) ( $job['Country'] ?? '' ),
					'remote'           => ! empty( $job['Remote_Job'] ),
					'job_type'         => (string) ( $job['Job_Type'] ?? '' ),
					'experience'       => (string) ( $job['Work_Experience'] ?? '' ),
					'salary'           => (string) ( $job['Salary'] ?? '' ),
					'positions'        => (string) ( $job['Number_of_Positions'] ?? '1' ),
					'status'           => $status,
					'description'      => (string) ( $job['Job_Description'] ?? '' ),
					'requirements'     => (string) ( $job['Requirements'] ?? '' ),
					'benefits'         => (string) ( $job['Benefits'] ?? '' ),
					'target_date'      => (string) ( $job['Target_Date'] ?? '' ),
					'created_time'     => (string) ( $job['Created_Time'] ?? '' ),
				);
			}
		}

		update_option(
			self::OPT_JOBS_CACHE,
			array(
				'synced_at' => current_time( 'mysql', true ),
				'jobs'      => $jobs,
			),
			false
		);

		return $jobs;
	}

	/**
	 * Return cached Job Openings.
	 *
	 * @return array{synced_at: string, jobs: array<int, array<string, mixed>>}
	 */
	public function get_cached_jobs(): array {
		$cached = get_option( self::OPT_JOBS_CACHE, array() );
		if ( ! is_array( $cached ) || ! isset( $cached['jobs'] ) ) {
			return array( 'synced_at' => '', 'jobs' => array() );
		}
		return array(
			'synced_at' => (string) ( $cached['synced_at'] ?? '' ),
			'jobs'      => is_array( $cached['jobs'] ) ? $cached['jobs'] : array(),
		);
	}

	/**
	 * Find a single cached Job Opening by ID.
	 *
	 * @return array<string, mixed>|null
	 */
	public function find_cached_job( string $job_id ): ?array {
		$cached = $this->get_cached_jobs();
		foreach ( $cached['jobs'] as $job ) {
			if ( (string) ( $job['id'] ?? '' ) === $job_id ) {
				return $job;
			}
		}
		return null;
	}

	/**
	 * Idempotent 3-step sync for a local application -> Zoho Recruit Candidate + Resume + Job Association.
	 *
	 * @return array<string, mixed>|WP_Error
	 */
	public function sync_application( int $app_id ) {
		$app = $this->applications->find( $app_id );
		if ( ! $app ) {
			return new WP_Error( 'recruit_app_not_found', __( 'Application not found.', 'asrs-recruit-engine' ) );
		}

		$zoho_cfg = $this->settings->get_zoho_settings();
		if ( empty( $zoho_cfg['enabled'] ) ) {
			return array(
				'skipped' => true,
				'message' => __( 'Zoho Recruit integration is currently disabled.', 'asrs-recruit-engine' ),
			);
		}

		$attempts     = (int) $app['sync_attempts'] + 1;
		$candidate_id = (string) ( $app['zoho_candidate_id'] ?? '' );
		$candidate_url = (string) ( $app['zoho_candidate_url'] ?? '' );

		// Step 1: Create Candidate if not yet created.
		if ( '' === $candidate_id ) {
			$candidate_data = array(
				'First_Name' => (string) $app['first_name'],
				'Last_Name'  => (string) $app['last_name'],
				'Email'      => (string) $app['email'],
				'Mobile'     => (string) $app['phone'],
				'Phone'      => (string) $app['phone'],
				'Source'     => (string) ( $zoho_cfg['candidate_source'] ?: 'Website Careers' ),
			);

			if ( '' !== (string) $app['city'] ) {
				$candidate_data['City'] = (string) $app['city'];
			}
			if ( '' !== (string) $app['experience_years'] && is_numeric( $app['experience_years'] ) ) {
				$candidate_data['Experience_in_Years'] = (float) $app['experience_years'];
			}
			if ( '' !== (string) $app['linkedin_url'] ) {
				$candidate_data['Website'] = (string) $app['linkedin_url'];
			}
			if ( '' !== (string) $app['cover_note'] ) {
				$candidate_data['Additional_Info'] = (string) $app['cover_note'];
			}

			$payload = array( 'data' => array( $candidate_data ) );
			if ( ! empty( $zoho_cfg['trigger_workflow'] ) ) {
				$payload['trigger'] = array( 'workflow' );
			}

			$res = $this->client->request( 'POST', '/Candidates', $payload );
			if ( is_wp_error( $res ) ) {
				return $this->mark_failed( $app_id, $attempts, $res->get_error_message() );
			}

			$item = $res['body']['data'][0] ?? null;
			if ( ! is_array( $item ) || 'SUCCESS' !== strtoupper( (string) ( $item['code'] ?? '' ) ) ) {
				$msg = is_array( $item ) ? (string) ( $item['message'] ?? 'Candidate creation failed.' ) : 'Invalid Zoho Recruit response.';
				return $this->mark_failed( $app_id, $attempts, $msg );
			}

			$candidate_id  = (string) ( $item['details']['id'] ?? '' );
			$candidate_url = '' !== $candidate_id
				? RecruitDataCenter::build_candidate_url( (string) $zoho_cfg['data_center'], $candidate_id, $this->get_org_id() )
				: '';

			$this->applications->update_sync_progress(
				$app_id,
				array(
					'zoho_candidate_id'  => $candidate_id,
					'zoho_candidate_url' => $candidate_url,
				)
			);
		}

		// Step 2: Upload Resume Attachment if not yet uploaded.
		if ( empty( $app['resume_uploaded'] ) && ! empty( $app['resume_path'] ) ) {
			$upload_res = $this->client->upload_file(
				'/Candidates/' . rawurlencode( $candidate_id ) . '/Attachments?attachments_category=Resume',
				(string) $app['resume_path'],
				(string) ( $app['resume_name'] ?: basename( (string) $app['resume_path'] ) )
			);

			if ( is_wp_error( $upload_res ) ) {
				return $this->mark_failed( $app_id, $attempts, 'Candidate created, resume upload failed: ' . $upload_res->get_error_message() );
			}

			$this->applications->update_sync_progress( $app_id, array( 'resume_uploaded' => 1 ) );
		}

		// Step 3: Associate Candidate with the Job Opening if not yet associated.
		if ( empty( $app['job_associated'] ) && ! empty( $app['job_id'] ) ) {
			$assoc_res = $this->client->request(
				'PUT',
				'/Candidates/actions/associate',
				array(
					'data' => array(
						array(
							'ids'    => array( $candidate_id ),
							'jobids' => array( (string) $app['job_id'] ),
						),
					),
				)
			);

			if ( is_wp_error( $assoc_res ) ) {
				return $this->mark_failed( $app_id, $attempts, 'Candidate created, job association failed: ' . $assoc_res->get_error_message() );
			}

			$assoc_item = $assoc_res['body']['data'][0] ?? null;
			if ( is_array( $assoc_item ) && isset( $assoc_item['code'] ) && ! in_array( strtoupper( (string) $assoc_item['code'] ), array( 'SUCCESS', 'ALREADY_ASSOCIATED' ), true ) ) {
				return $this->mark_failed( $app_id, $attempts, 'Job association error: ' . (string) ( $assoc_item['message'] ?? $assoc_item['code'] ) );
			}

			$this->applications->update_sync_progress( $app_id, array( 'job_associated' => 1 ) );
		}

		$this->applications->update_sync_progress(
			$app_id,
			array(
				'sync_status'     => ApplicationRepository::STATUS_SYNCED,
				'sync_attempts'   => $attempts,
				'last_sync_error' => null,
				'last_sync_at'    => current_time( 'mysql', true ),
				'next_retry_at'   => null,
			)
		);

		return array(
			'candidate_id'  => $candidate_id,
			'candidate_url' => $candidate_url,
		);
	}

	private function mark_failed( int $app_id, int $attempts, string $error ): WP_Error {
		$max_attempts = (int) $this->settings->get( 'max_retry_attempts', 5 );
		$delay_min    = min( 720, (int) ( 5 * ( 2 ** max( 0, $attempts - 1 ) ) ) );
		$next_retry   = $attempts < $max_attempts ? gmdate( 'Y-m-d H:i:s', time() + ( $delay_min * MINUTE_IN_SECONDS ) ) : null;

		$this->applications->update_sync_progress(
			$app_id,
			array(
				'sync_status'     => ApplicationRepository::STATUS_FAILED,
				'sync_attempts'   => $attempts,
				'last_sync_error' => sanitize_text_field( $error ),
				'last_sync_at'    => current_time( 'mysql', true ),
				'next_retry_at'   => $next_retry,
			)
		);

		return new WP_Error( 'recruit_sync_failed', $error );
	}
}
