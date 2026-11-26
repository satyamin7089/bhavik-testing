<?php
/**
 * Form Definition: Contact / Product Enquiry Form.
 *
 * Designed with 2-column responsive layout matching B2B polymer enquiry UI.
 * Shortcodes: [asrs_form id="contact-product-enquiry"] or [asrs_form_contact_product_enquiry]
 *
 * @package ASRS\FormsEngine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'id'          => 'contact-product-enquiry',
	'title'       => 'Contact / Product Enquiry Form',
	'description' => 'Primary B2B polymer & material enquiry form synced to Zoho CRM Leads.',
	'settings'    => array(
		'show_title'       => false,
		'submit_label'     => 'Send Enquiry',
		'submitting_label' => 'Sending Enquiry...',
		'success_message'  => 'Thank you for your enquiry. Our team will get back to you shortly.',
	),
	'zoho'        => array(
		'enabled'          => true,
		'module'           => 'Leads',
		'trigger_workflow' => true,
		'field_map'        => array(
			'full_name'           => 'Last_Name',
			'phone_number'        => 'Phone',
			'email_address'       => 'Email',
			'product_required'    => 'Lead_Source',
			'requirement_details' => 'Description',
		),
		'static_values'    => array(
			'Lead_Status' => 'Not Contacted',
		),
	),
	'fields'      => array(
		'full_name'            => array(
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
		'phone_number'         => array(
			'label'       => 'Phone Number',
			'type'        => 'tel',
			'width'       => 'half',
			'required'    => true,
			'placeholder' => '+91 XXXXX XXXXX',
			'rules'       => array(
				'min_length' => 6,
				'max_length' => 30,
			),
		),
		'email_address'        => array(
			'label'       => 'Email Address',
			'type'        => 'email',
			'width'       => 'half',
			'required'    => true,
			'placeholder' => 'you@company.com',
			'rules'       => array(
				'max_length' => 190,
			),
		),
		'product_required'     => array(
			'label'       => 'Product Required',
			'type'        => 'select',
			'width'       => 'half',
			'required'    => true,
			'placeholder' => 'Select polymer / material',
			'options'     => array(
				'HDPE',
				'LDPE',
			),
		),
		'approximate_quantity' => array(
			'label'       => 'Approximate Quantity',
			'type'        => 'text',
			'width'       => 'full',
			'required'    => false,
			'placeholder' => 'e.g., 10 MT, 1 truckload, or monthly requirement',
			'rules'       => array(
				'max_length' => 150,
			),
		),
		'requirement_details'  => array(
			'label'       => 'Requirement Details',
			'type'        => 'textarea',
			'width'       => 'full',
			'required'    => true,
			'placeholder' => 'Share grade preference, processing method (film, pipe, blow or injection moulding), colour and delivery city.',
			'attributes'  => array(
				'rows' => 5,
			),
			'rules'       => array(
				'min_length' => 10,
				'max_length' => 5000,
			),
		),
	),
);
