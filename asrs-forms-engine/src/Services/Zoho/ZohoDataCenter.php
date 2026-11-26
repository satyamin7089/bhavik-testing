<?php
/**
 * Zoho Multi-Data-Center endpoint registry.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/v7/multi-dc.html
 * @package ASRS\FormsEngine\Services\Zoho
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services\Zoho;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoDataCenter {

	/**
	 * Supported Zoho Data Centers and their official domains.
	 *
	 * @return array<string, array{label: string, accounts: string, api: string, crm_ui: string}>
	 */
	public static function all(): array {
		return array(
			'com'    => array(
				'label'    => 'United States (US - zoho.com)',
				'accounts' => 'https://accounts.zoho.com',
				'api'      => 'https://www.zohoapis.com',
				'crm_ui'   => 'https://crm.zoho.com',
			),
			'in'     => array(
				'label'    => 'India (IN - zoho.in)',
				'accounts' => 'https://accounts.zoho.in',
				'api'      => 'https://www.zohoapis.in',
				'crm_ui'   => 'https://crm.zoho.in',
			),
			'eu'     => array(
				'label'    => 'Europe (EU - zoho.eu)',
				'accounts' => 'https://accounts.zoho.eu',
				'api'      => 'https://www.zohoapis.eu',
				'crm_ui'   => 'https://crm.zoho.eu',
			),
			'com.au' => array(
				'label'    => 'Australia (AU - zoho.com.au)',
				'accounts' => 'https://accounts.zoho.com.au',
				'api'      => 'https://www.zohoapis.com.au',
				'crm_ui'   => 'https://crm.zoho.com.au',
			),
			'jp'     => array(
				'label'    => 'Japan (JP - zoho.jp)',
				'accounts' => 'https://accounts.zoho.jp',
				'api'      => 'https://www.zohoapis.jp',
				'crm_ui'   => 'https://crm.zoho.jp',
			),
			'ca'     => array(
				'label'    => 'Canada (CA - zohocloud.ca)',
				'accounts' => 'https://accounts.zohocloud.ca',
				'api'      => 'https://www.zohoapis.ca',
				'crm_ui'   => 'https://crm.zohocloud.ca',
			),
			'com.cn' => array(
				'label'    => 'China (CN - zoho.com.cn)',
				'accounts' => 'https://accounts.zoho.com.cn',
				'api'      => 'https://www.zohoapis.com.cn',
				'crm_ui'   => 'https://crm.zoho.com.cn',
			),
			'sa'     => array(
				'label'    => 'Saudi Arabia (SA - zoho.sa)',
				'accounts' => 'https://accounts.zoho.sa',
				'api'      => 'https://www.zohoapis.sa',
				'crm_ui'   => 'https://crm.zoho.sa',
			),
		);
	}

	/**
	 * Resolve domain URLs for a given DC key.
	 *
	 * @param string $dc Data center key.
	 * @return array{label: string, accounts: string, api: string, crm_ui: string}
	 */
	public static function get( string $dc ): array {
		$all = self::all();
		return $all[ $dc ] ?? $all['com'];
	}

	/**
	 * Build a Zoho CRM UI deep link to a record.
	 *
	 * @param string $dc        Data center key.
	 * @param string $module    Zoho module API name.
	 * @param string $record_id Record ID.
	 */
	public static function build_record_url( string $dc, string $module, string $record_id ): string {
		$urls = self::get( $dc );
		return sprintf(
			'%s/crm/tab/%s/%s',
			untrailingslashit( $urls['crm_ui'] ),
			rawurlencode( $module ),
			rawurlencode( $record_id )
		);
	}
}
