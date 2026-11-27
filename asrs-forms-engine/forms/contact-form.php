<?php
/**
 * Form Definition: General Contact Form.
 *
 * Pre-mapped to Zoho CRM Leads fields with 2-column responsive layout.
 * Shortcodes: [asrs_form id="contact-form"] or [asrs_form_contact_form]
 *
 * @package ASRS\FormsEngine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'id'          => 'contact-form',
	'title'       => 'Contact Form',
	'description' => 'General B2B contact form pre-mapped to Zoho CRM Leads.',
	'settings'    => array(
		'show_title'       => false,
		'submit_label'     => 'Send Message',
		'submitting_label' => 'Sending...',
		'success_message'  => 'Thank you for contacting us. Our team will get back to you shortly.',
	),
	'zoho'        => array(
		'enabled'          => true,
		'module'           => 'Leads',
		'trigger_workflow' => true,
		'field_map'        => array(
			'full_name'      => 'Last_Name',
			'business_email' => 'Email',
			'phone'          => 'Phone',
			'company_name'   => 'Company',
			'job_title'      => 'Designation',
			'website'        => 'Website',
				'city'           => 'City',
				'state'          => 'State',
				'country'        => 'Country',
			'message'        => 'Description',
		),
		'static_values'    => array(
			'Lead_Source' => 'Contact Form',
			'Lead_Status' => 'Not Contacted',
		),
	),
	'fields'      => array(
		'full_name'      => array(
			'label'       => 'Full Name',
			'type'        => 'text',
			'width'       => 'half',
			'required'    => true,
			'placeholder' => 'Your full name',
			'rules'       => array(
				'min_length' => 2,
				'max_length' => 150,
			),
		),
		'business_email' => array(
			'label'       => 'Email Address',
			'type'        => 'email',
			'width'       => 'half',
			'required'    => true,
			'placeholder' => 'you@company.com',
			'rules'       => array(
				'max_length' => 190,
			),
		),
		'phone'          => array(
			'label'       => 'Phone Number',
			'type'        => 'tel',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => '+91 XXXXX XXXXX',
			'rules'       => array(
				'max_length' => 30,
			),
		),
		'company_name'   => array(
			'label'       => 'Company Name',
			'type'        => 'text',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'Your company or organization',
			'rules'       => array(
				'max_length' => 200,
			),
		),
		'job_title'      => array(
			'label'       => 'Job Title',
			'type'        => 'text',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'e.g. Procurement Manager',
			'rules'       => array(
				'max_length' => 120,
			),
		),
		'website'        => array(
			'label'       => 'Company Website',
			'type'        => 'url',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'https://example.com',
			'rules'       => array(
				'max_length' => 200,
			),
		),
		'country'        => array(
			'label'       => 'Country',
			'type'        => 'location_cascade',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'Select Country',
			'attributes'  => array(
				'location_level' => 'country',
				'location_group' => 'primary',
			),
		),
		'state'          => array(
			'label'       => 'State / Province',
			'type'        => 'location_cascade',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'Select State',
			'attributes'  => array(
				'location_level' => 'state',
				'location_group' => 'primary',
			),
		),
		'city'           => array(
			'label'       => 'City',
			'type'        => 'location_cascade',
			'width'       => 'half',
			'required'    => false,
			'placeholder' => 'Select City',
			'attributes'  => array(
				'location_level' => 'city',
				'location_group' => 'primary',
			),
		),
		'city_search'    => array(
			'label'       => 'Quick city search',
			'type'        => 'location_cascade',
			'width'       => 'full',
			'required'    => false,
			'placeholder' => 'Or type a city (e.g. Kanpur)',
			'attributes'  => array(
				'location_level' => 'search',
				'location_group' => 'primary',
			),
		),
		'message'        => array(
			'label'       => 'How Can We Help',
			'type'        => 'textarea',
			'width'       => 'full',
			'required'    => true,
			'placeholder' => 'Write your message here...',
			'attributes'  => array(
				'rows' => 5,
			),
			'rules'       => array(
				'min_length' => 5,
				'max_length' => 5000,
			),
		),
	),
);
