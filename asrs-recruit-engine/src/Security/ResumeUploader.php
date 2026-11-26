<?php
/**
 * Secure Resume Uploader (PDF, DOC, DOCX) stored in a protected upload directory.
 *
 * @package ASRS\RecruitEngine\Security
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Security;

use ASRS\RecruitEngine\Core\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ResumeUploader {

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Validate and store an uploaded resume file.
	 *
	 * @param array<string, mixed> $file Entry from $_FILES['resume'].
	 * @return array{path: string, name: string, size: int}|WP_Error
	 */
	public function handle_upload( array $file ) {
		$err_code = (int) ( $file['error'] ?? UPLOAD_ERR_NO_FILE );
		if ( UPLOAD_ERR_NO_FILE === $err_code ) {
			return new WP_Error( 'resume_required', __( 'Please upload your resume (PDF, DOC, or DOCX).', 'asrs-recruit-engine' ) );
		}
		if ( UPLOAD_ERR_OK !== $err_code ) {
			return new WP_Error( 'resume_upload_error', __( 'Resume upload failed. Please try again with a smaller file.', 'asrs-recruit-engine' ) );
		}

		$tmp_name = (string) ( $file['tmp_name'] ?? '' );
		$orig_name = sanitize_file_name( (string) ( $file['name'] ?? 'resume.pdf' ) );
		$size      = (int) ( $file['size'] ?? 0 );

		$max_mb    = max( 1, min( 20, (int) $this->settings->get( 'max_resume_mb', 5 ) ) );
		$max_bytes = $max_mb * 1024 * 1024;
		if ( $size <= 0 || $size > $max_bytes ) {
			return new WP_Error(
				'resume_size_invalid',
				sprintf( __( 'Resume must be between 1 KB and %d MB.', 'asrs-recruit-engine' ), $max_mb )
			);
		}

		$allowed_mimes = array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		);

		$checked = wp_check_filetype_and_ext( $tmp_name, $orig_name, $allowed_mimes );
		$ext     = strtolower( (string) ( $checked['ext'] ?: pathinfo( $orig_name, PATHINFO_EXTENSION ) ) );

		if ( ! array_key_exists( $ext, $allowed_mimes ) ) {
			return new WP_Error( 'resume_type_invalid', __( 'Only PDF, DOC, and DOCX files are accepted.', 'asrs-recruit-engine' ) );
		}

		if ( '' === $tmp_name || ! is_uploaded_file( $tmp_name ) ) {
			return new WP_Error( 'resume_invalid_temp', __( 'Invalid uploaded file.', 'asrs-recruit-engine' ) );
		}

		$dir = $this->ensure_protected_dir();
		if ( is_wp_error( $dir ) ) {
			return $dir;
		}

		$safe_base = wp_generate_password( 12, false ) . '-' . $orig_name;
		$dest_path = trailingslashit( $dir ) . $safe_base;

		if ( ! @move_uploaded_file( $tmp_name, $dest_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return new WP_Error( 'resume_move_failed', __( 'Could not save uploaded resume on server.', 'asrs-recruit-engine' ) );
		}

		return array(
			'path' => $dest_path,
			'name' => $orig_name,
			'size' => $size,
		);
	}

	/**
	 * @return string|WP_Error
	 */
	private function ensure_protected_dir() {
		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			return new WP_Error( 'upload_dir_error', (string) $uploads['error'] );
		}

		$dir = trailingslashit( (string) $uploads['basedir'] ) . 'asrs-recruit-resumes';
		if ( ! file_exists( $dir ) && ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'upload_dir_create_failed', __( 'Could not create resume storage folder.', 'asrs-recruit-engine' ) );
		}

		$htaccess = trailingslashit( $dir ) . '.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			@file_put_contents( $htaccess, "Order Allow,Deny\nDeny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		$index = trailingslashit( $dir ) . 'index.php';
		if ( ! file_exists( $index ) ) {
			@file_put_contents( $index, "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		return $dir;
	}
}
