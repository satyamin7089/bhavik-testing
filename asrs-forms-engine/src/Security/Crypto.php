<?php
/**
 * Symmetric encryption and credential masking helper.
 *
 * @package ASRS\FormsEngine\Security
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Crypto {

	private const PREFIX = 'enc:v1:';
	private const CIPHER = 'aes-256-cbc';

	/**
	 * Encrypt a plaintext secret.
	 *
	 * @param string $plaintext Raw secret.
	 */
	public static function encrypt( string $plaintext ): string {
		if ( '' === $plaintext || str_starts_with( $plaintext, self::PREFIX ) ) {
			return $plaintext;
		}

		if ( ! function_exists( 'openssl_encrypt' ) ) {
			return $plaintext;
		}

		$iv_length = openssl_cipher_iv_length( self::CIPHER );
		if ( false === $iv_length || $iv_length <= 0 ) {
			return $plaintext;
		}

		try {
			$iv = random_bytes( $iv_length );
		} catch ( \Throwable $e ) {
			return $plaintext;
		}

		$encrypted = openssl_encrypt( $plaintext, self::CIPHER, self::derive_key(), OPENSSL_RAW_DATA, $iv );
		if ( false === $encrypted ) {
			return $plaintext;
		}

		return self::PREFIX . base64_encode( $iv . $encrypted );
	}

	/**
	 * Decrypt an encrypted secret.
	 *
	 * @param string $ciphertext Encrypted string.
	 */
	public static function decrypt( string $ciphertext ): string {
		if ( '' === $ciphertext || ! str_starts_with( $ciphertext, self::PREFIX ) ) {
			return $ciphertext;
		}

		if ( ! function_exists( 'openssl_decrypt' ) ) {
			return '';
		}

		$raw = base64_decode( substr( $ciphertext, strlen( self::PREFIX ) ), true );
		if ( false === $raw ) {
			return '';
		}

		$iv_length = openssl_cipher_iv_length( self::CIPHER );
		if ( false === $iv_length || strlen( $raw ) <= $iv_length ) {
			return '';
		}

		$iv        = substr( $raw, 0, $iv_length );
		$encrypted = substr( $raw, $iv_length );
		$decrypted = openssl_decrypt( $encrypted, self::CIPHER, self::derive_key(), OPENSSL_RAW_DATA, $iv );

		return false === $decrypted ? '' : $decrypted;
	}

	/**
	 * Mask a sensitive value for safe UI display.
	 *
	 * @param string $secret Secret string.
	 */
	public static function mask( string $secret ): string {
		if ( '' === $secret ) {
			return '';
		}

		$len = strlen( $secret );
		if ( $len <= 6 ) {
			return '••••••••';
		}

		return '••••••••' . substr( $secret, -4 );
	}

	/**
	 * Derive a 256-bit key from WordPress authentication salts.
	 */
	private static function derive_key(): string {
		$material = ( defined( 'AUTH_KEY' ) ? AUTH_KEY : 'asrs-auth' )
			. '|'
			. ( defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : 'asrs-secure-auth' );

		return hash( 'sha256', $material, true );
	}
}
