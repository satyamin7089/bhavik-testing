<?php
/** Standalone job sync regression tests. Run: php asrs-recruit-engine/tests/job-sync-test.php */
declare(strict_types=1);

namespace {
	define( 'ABSPATH', __DIR__ );
	class WP_Error {
		public string $code;
		private string $message;
		public function __construct( string $code, string $message ) {
			$this->code = $code;
			$this->message = $message;
		}
		public function get_error_message(): string { return $this->message; }
	}
	function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
	function __( string $text, string $domain = '' ): string { return $text; }
	function current_time( string $type, bool $gmt = false ): string { return '2026-01-01 00:00:00'; }
	function get_option( string $key, $default = false ) { return $GLOBALS['options'][ $key ] ?? $default; }
	function update_option( string $key, $value, $autoload = null ): bool { $GLOBALS['options'][ $key ] = $value; return true; }
	function check( bool $condition, string $message ): void {
		if ( ! $condition ) { throw new \RuntimeException( $message ); }
	}
}

namespace ASRS\RecruitEngine\Core {
	class Settings {
		public array $values = array();
		public function get( string $key, $default = null ) { return $this->values[ $key ] ?? $default; }
	}
}
namespace ASRS\RecruitEngine\Repositories {
	class ApplicationRepository {}
}
namespace ASRS\RecruitEngine\Zoho {
	class RecruitAuthService {}
	class RecruitClient {
		public array $responses = array();
		public array $requests = array();
		public function request( string $method, string $endpoint ) {
			$this->requests[] = $endpoint;
			return $this->responses[ $endpoint ] ?? new \WP_Error( 'unexpected', 'Unexpected request: ' . $endpoint );
		}
	}
}
namespace {
	require __DIR__ . '/../src/Zoho/RecruitService.php';

	$client = new \ASRS\RecruitEngine\Zoho\RecruitClient();
	$settings = new \ASRS\RecruitEngine\Core\Settings();
	$service = new \ASRS\RecruitEngine\Zoho\RecruitService(
		$client,
		new \ASRS\RecruitEngine\Zoho\RecruitAuthService(),
		$settings,
		new \ASRS\RecruitEngine\Repositories\ApplicationRepository()
	);
	$record = array( 'id' => '123', 'Posting_Title' => 'Engineer', 'Job_Opening_Status' => 'Open', 'Job_Description' => 'Flattened heading and text', 'Requirements' => 'Flattened list' );
	$client->responses['/JobOpenings?per_page=200'] = array( 'code' => 200, 'body' => array( 'data' => array( $record ) ) );
	$rich = '<h2>Responsibilities</h2><ul><li>Build things</li></ul>';
	$client->responses['/Job_Openings/123'] = array( 'code' => 200, 'body' => array( 'data' => array( array( 'id' => '123', 'Job_Description' => $rich, 'Requirements' => '<p>Experience</p>', 'Benefits' => 'Plain text' ) ) ) );
	$jobs = $service->sync_job_openings();
	check( ! is_wp_error( $jobs ), 'Successful sync returned an error' );
	check( $jobs[0]['description'] === $rich, 'Rich HTML was not preserved' );
	check( $jobs[0]['requirements'] === '<p>Experience</p>', 'Requirements were not preserved' );
	check( $jobs[0]['benefits'] === 'Plain text', 'Plain text was not preserved' );
	check( $service->get_cached_jobs()['jobs'][0]['description'] === $rich, 'Cache did not store rich HTML' );

	$before = $service->get_cached_jobs();
	$client->responses['/Job_Openings/123'] = new \WP_Error( 'api_error', 'Temporary failure' );
	check( is_wp_error( $service->sync_job_openings() ), 'Detail error must be surfaced' );
	check( $service->get_cached_jobs() === $before, 'Failed sync must not overwrite cache' );

	$client->responses['/Job_Openings/123'] = array( 'code' => 200, 'body' => array( 'data' => array( array( 'id' => 'wrong' ) ) ) );
	check( is_wp_error( $service->sync_job_openings() ), 'Mismatched ID must be rejected' );
	check( $service->get_cached_jobs() === $before, 'Invalid detail must not overwrite cache' );

	$client->responses['/Job_Openings/123'] = array( 'code' => 200, 'body' => array( 'data' => array( array( 'id' => '123' ) ) ) );
	check( is_wp_error( $service->sync_job_openings() ), 'Missing rich field must not silently flatten description' );
	check( $service->get_cached_jobs() === $before, 'Missing field must not overwrite cache' );

	$client->responses['/Job_Openings/123'] = array( 'code' => 200, 'body' => array( 'data' => array( array( 'id' => '123', 'Job_Description' => null, 'Requirements' => null ) ) ) );
	$cleared = $service->sync_job_openings();
	check( ! is_wp_error( $cleared ) && $cleared[0]['description'] === '' && $cleared[0]['requirements'] === '', 'Cleared fields should remain empty' );

	$settings->values['allowed_job_statuses'] = 'Closed';
	$client->requests = array();
	$filtered = $service->sync_job_openings();
	check( $filtered === array(), 'Status filter did not exclude job' );
	check( $client->requests === array( '/JobOpenings?per_page=200' ), 'Excluded jobs must not trigger detail requests' );

	echo "Job sync tests passed.\n";
}
