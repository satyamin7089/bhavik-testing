<?php
/**
 * Zoho Recruit Multi-Data-Center endpoint registry.
 *
 * @see https://www.zoho.com/recruit/developer-guide/apiv2/whats-new.html
 * @package ASRS\RecruitEngine\Zoho
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Zoho;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecruitDataCenter {

	/**
	 * @return array<string, array{label: string, accounts: string, recruit_api: string, recruit_ui: string}>
	 */
	public static function all(): array {
		return array(
			'in'     => array(
				'label'       => 'India (IN - zoho.in)',
				'accounts'    => 'https://accounts.zoho.in',
				'recruit_api' => 'https://recruit.zoho.in',
				'recruit_ui'  => 'https://recruit.zoho.in',
			),
			'com'    => array(
				'label'       => 'United States (US - zoho.com)',
				'accounts'    => 'https://accounts.zoho.com',
				'recruit_api' => 'https://recruit.zoho.com',
				'recruit_ui'  => 'https://recruit.zoho.com',
			),
			'eu'     => array(
				'label'       => 'Europe (EU - zoho.eu)',
				'accounts'    => 'https://accounts.zoho.eu',
				'recruit_api' => 'https://recruit.zoho.eu',
				'recruit_ui'  => 'https://recruit.zoho.eu',
			),
			'com.au' => array(
				'label'       => 'Australia (AU - zoho.com.au)',
				'accounts'    => 'https://accounts.zoho.com.au',
				'recruit_api' => 'https://recruit.zoho.com.au',
				'recruit_ui'  => 'https://recruit.zoho.com.au',
			),
			'jp'     => array(
				'label'       => 'Japan (JP - zoho.jp)',
				'accounts'    => 'https://accounts.zoho.jp',
				'recruit_api' => 'https://recruit.zoho.jp',
				'recruit_ui'  => 'https://recruit.zoho.jp',
			),
			'com.cn' => array(
				'label'       => 'China (CN - zoho.com.cn)',
				'accounts'    => 'https://accounts.zoho.com.cn',
				'recruit_api' => 'https://recruit.zoho.com.cn',
				'recruit_ui'  => 'https://recruit.zoho.com.cn',
			),
		);
	}

	/**
	 * @return array{label: string, accounts: string, recruit_api: string, recruit_ui: string}
	 */
	public static function get( string $dc ): array {
		$all = self::all();
		return $all[ $dc ] ?? $all['in'];
	}

	public static function build_candidate_url( string $dc, string $candidate_id, string $org_id = '' ): string {
		$urls = self::get( $dc );
		if ( '' !== $org_id ) {
			return sprintf(
				'%s/recruit/org%s/tab/Candidates/%s',
				untrailingslashit( $urls['recruit_ui'] ),
				rawurlencode( $org_id ),
				rawurlencode( $candidate_id )
			);
		}
		return sprintf(
			'%s/recruit/tab/Candidates/%s',
			untrailingslashit( $urls['recruit_ui'] ),
			rawurlencode( $candidate_id )
		);
	}
}
