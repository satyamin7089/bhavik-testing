<?php
/**
 * AES-256-CBC symmetric encryption helper for Zoho OAuth credentials.
 *
 * @package ASRS\RecruitEngine\Security
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Crypto {

	private const CIPHER = 'aes-256-cbc';
	private const PREFIX = 'enc::';

	public static function encrypt( string $plaintext ): string {
		if ( '' === $plaintext ) {
			return '';
		}
		if ( str_starts_with( $plaintext, self::PREFIX ) ) {
			return $plaintext;
		}
		if ( ! function_exists( 'openssl_encrypt' ) ) {
			return base64_encode( $plaintext ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}

		$iv_len = openssl_cipher_iv_length( self::CIPHER );
		$iv     = random_bytes( $iv_len ?: 16 );
		$cipher = openssl_encrypt( $plaintext, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv );

		if ( false === $cipher ) {
			return '';
		}

		return self::PREFIX . base64_encode( $iv . $cipher ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	public static function decrypt( string $stored ): string {
		if ( '' === $stored ) {
			return '';
		}
		if ( ! str_starts_with( $stored, self::PREFIX ) ) {
			return $stored;
		}
		if ( ! function_exists( 'openssl_decrypt' ) ) {
			return '';
		}

		$raw    = base64_decode( substr( $stored, strlen( self::PREFIX ) ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$iv_len = openssl_cipher_iv_length( self::CIPHER ) ?: 16;

		if ( false === $raw || strlen( $raw ) <= $iv_len ) {
			return '';
		}

		$iv     = substr( $raw, 0, $iv_len );
		$cipher = substr( $raw, $iv_len );
		$plain  = openssl_decrypt( $cipher, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv );

		return false === $plain ? '' : $plain;
	}

	public static function mask( string $secret ): string {
		if ( '' === $secret ) {
			return '';
		}
		return '••••••••••••' . substr( $secret, -4 );
	}

	private static function key(): string {
		$material = ( defined( 'AUTH_KEY' ) ? AUTH_KEY : '' )
			. ( defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '' )
			. (string) wp_salt( 'auth' );

		return hash( 'sha256', $material, true );
	}
}
