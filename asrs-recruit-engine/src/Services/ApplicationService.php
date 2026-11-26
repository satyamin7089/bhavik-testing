<?php
/**
 * Application Submission Orchestrator: Validate -> Save Resume -> Backup DB -> Sync Zoho Recruit.
 *
 * @package ASRS\RecruitEngine\Services
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Services;

use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use ASRS\RecruitEngine\Security\ResumeUploader;
use ASRS\RecruitEngine\Security\SecurityManager;
use ASRS\RecruitEngine\Zoho\RecruitService;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApplicationService {

	private SecurityManager $security;
	private ResumeUploader $uploader;
	private ApplicationRepository $applications;
	private RecruitService $recruit;
	private NotificationService $notifications;

	public function __construct(
		SecurityManager $security,
		ResumeUploader $uploader,
		ApplicationRepository $applications,
		RecruitService $recruit,
		NotificationService $notifications
	) {
		$this->security      = $security;
		$this->uploader      = $uploader;
		$this->applications  = $applications;
		$this->recruit       = $recruit;
		$this->notifications = $notifications;
	}

	/**
	 * Process a frontend job application submission.
	 *
	 * @param array<string, mixed> $post
	 * @param array<string, mixed> $files
	 * @return array<string, mixed>|WP_Error
	 */
	public function process( array $post, array $files ) {
		$sec = $this->security->verify_application_request( $post );
		if ( is_wp_error( $sec ) ) {
			return $sec;
		}

		$job_id    = sanitize_text_field( (string) ( $post['job_id'] ?? '' ) );
		$job_title = sanitize_text_field( (string) ( $post['job_title'] ?? '' ) );
		if ( '' !== $job_id ) {
			$cached_job = $this->recruit->find_cached_job( $job_id );
			if ( $cached_job ) {
				$job_title = (string) $cached_job['title'];
			}
		}

		$first_name = sanitize_text_field( (string) ( $post['first_name'] ?? '' ) );
		$last_name  = sanitize_text_field( (string) ( $post['last_name'] ?? '' ) );
		$email      = sanitize_email( (string) ( $post['email'] ?? '' ) );
		$phone      = sanitize_text_field( (string) ( $post['phone'] ?? '' ) );
		$city       = sanitize_text_field( (string) ( $post['city'] ?? '' ) );
		$exp_raw    = str_replace( ',', '.', trim( sanitize_text_field( (string) ( $post['experience_years'] ?? '' ) ) ) );
		$exp_years  = '' !== $exp_raw && is_numeric( $exp_raw ) ? (string) max( 0, min( 60, (float) $exp_raw ) ) : $exp_raw;
		$linkedin   = esc_url_raw( (string) ( $post['linkedin_url'] ?? '' ) );
		$cover_note = sanitize_textarea_field( (string) ( $post['cover_note'] ?? '' ) );

		$errors = array();
		if ( '' === $first_name ) {
			$errors['first_name'] = __( 'First name is required.', 'asrs-recruit-engine' );
		}
		if ( '' === $last_name ) {
			$errors['last_name'] = __( 'Last name is required.', 'asrs-recruit-engine' );
		}
		if ( ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter a valid email address.', 'asrs-recruit-engine' );
		}
		if ( '' === $phone ) {
			$errors['phone'] = __( 'Phone number is required.', 'asrs-recruit-engine' );
		}
		if ( ! empty( $errors ) ) {
			return new WP_Error( 'validation_failed', __( 'Please complete all required fields.', 'asrs-recruit-engine' ), array( 'fields' => $errors ) );
		}

		$resume = $this->uploader->handle_upload( is_array( $files['resume'] ?? null ) ? $files['resume'] : array() );
		if ( is_wp_error( $resume ) ) {
			return new WP_Error(
				'validation_failed',
				$resume->get_error_message(),
				array( 'fields' => array( 'resume' => $resume->get_error_message() ) )
			);
		}

		$app_id = $this->applications->create(
			array(
				'job_id'           => $job_id,
				'job_title'        => $job_title ?: __( 'General Application', 'asrs-recruit-engine' ),
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'email'            => $email,
				'phone'            => $phone,
				'city'             => $city,
				'experience_years' => $exp_years,
				'linkedin_url'     => $linkedin,
				'cover_note'       => $cover_note,
				'resume_path'      => $resume['path'],
				'resume_name'      => $resume['name'],
				'resume_size'      => $resume['size'],
				'ip_address'       => $this->security->get_client_ip(),
			)
		);

		if ( $app_id <= 0 ) {
			return new WP_Error( 'db_insert_failed', __( 'Could not save your application. Please try again.', 'asrs-recruit-engine' ) );
		}

		$sync_res = $this->recruit->sync_application( $app_id );
		if ( is_wp_error( $sync_res ) ) {
			$app = $this->applications->find( $app_id );
			if ( $app ) {
				$this->notifications->notify_sync_failure( $app, $sync_res->get_error_message() );
			}
		}

		return array(
			'application_id' => $app_id,
			'job_title'      => $job_title,
			'message'        => __( 'Thank you! Your application has been received. Our recruitment team will review your profile and reach out shortly.', 'asrs-recruit-engine' ),
		);
	}
}
