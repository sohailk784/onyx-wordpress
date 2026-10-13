<?php
/**
 * Creates the Fluent Forms used by the site (idempotent by title).
 * Run: wp eval-file scripts/lib/create-forms.php
 * Prints a JSON map of form key => form ID and stores it in the `onyx_form_ids` option.
 */

global $wpdb;
$k = 0;
$req = fn( $msg = 'This field is required' ) => array( 'required' => array( 'value' => true, 'message' => $msg ) );
$text = function ( $name, $label, $ph, $required = true, $type = 'text' ) use ( &$k, $req ) {
	$rules = $required ? $req() : array( 'required' => array( 'value' => false, 'message' => '' ) );
	if ( 'email' === $type ) {
		$rules['email'] = array( 'value' => true, 'message' => 'Enter a valid email address.' );
	}
	return array(
		'index'          => $k,
		'element'        => 'email' === $type ? 'input_email' : 'input_text',
		'attributes'     => array( 'type' => $type, 'name' => $name, 'value' => '', 'class' => '', 'placeholder' => $ph ),
		'settings'       => array( 'container_class' => '', 'label' => $label, 'label_placement' => '', 'admin_field_label' => '', 'help_message' => '', 'validation_rules' => $rules, 'conditional_logics' => array() ),
		'editor_options' => array( 'title' => 'Text', 'icon_class' => 'ff-edit-text', 'template' => 'inputText' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$area = function ( $name, $label, $ph, $required = false ) use ( &$k, $req ) {
	return array(
		'index'          => $k,
		'element'        => 'textarea',
		'attributes'     => array( 'name' => $name, 'value' => '', 'id' => '', 'class' => '', 'placeholder' => $ph, 'rows' => 4, 'cols' => 2 ),
		'settings'       => array( 'container_class' => '', 'label' => $label, 'label_placement' => '', 'help_message' => '', 'validation_rules' => $required ? $req() : array( 'required' => array( 'value' => false, 'message' => '' ) ), 'conditional_logics' => array() ),
		'editor_options' => array( 'title' => 'Text Area', 'icon_class' => 'ff-edit-textarea', 'template' => 'inputTextarea' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$select = function ( $name, $label, $placeholder, $opts ) use ( &$k, $req ) {
	return array(
		'index'          => $k,
		'element'        => 'select',
		'attributes'     => array( 'name' => $name, 'value' => '', 'id' => '', 'class' => '' ),
		'settings'       => array( 'label' => $label, 'help_message' => '', 'container_class' => '', 'label_placement' => '', 'placeholder' => $placeholder, 'advanced_options' => array_map( fn( $o ) => array( 'label' => $o, 'value' => $o, 'calc_value' => '' ), $opts ), 'enable_select_2' => 'no', 'validation_rules' => $req(), 'conditional_logics' => array() ),
		'editor_options' => array( 'title' => 'Dropdown', 'icon_class' => 'ff-edit-dropdown', 'element' => 'select', 'template' => 'select' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$country = function ( $label ) use ( &$k, $req ) {
	return array(
		'index'          => $k,
		'element'        => 'select_country',
		'attributes'     => array( 'name' => 'country', 'value' => '', 'id' => '', 'class' => '', 'placeholder' => 'Select country' ),
		'settings'       => array( 'container_class' => '', 'label' => $label, 'label_placement' => '', 'admin_field_label' => '', 'help_message' => '', 'enable_select_2' => 'no', 'country_list' => array( 'active_list' => 'all', 'visible_list' => array(), 'hidden_list' => array() ), 'validation_rules' => $req(), 'conditional_logics' => array() ),
		'options'        => array(),
		'editor_options' => array( 'title' => 'Country List', 'element' => 'country-list', 'icon_class' => 'ff-edit-country', 'template' => 'selectCountry' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$terms = function ( $html ) use ( &$k, $req ) {
	return array(
		'index'          => $k,
		'element'        => 'terms_and_condition',
		'attributes'     => array( 'type' => 'checkbox', 'name' => 'terms-n-condition', 'value' => false, 'class' => '' ),
		'settings'       => array( 'tnc_html' => $html, 'has_checkbox' => true, 'admin_field_label' => 'Terms', 'container_class' => '', 'validation_rules' => $req( 'Please confirm to continue.' ), 'conditional_logics' => array() ),
		'editor_options' => array( 'title' => 'Terms & Conditions', 'icon_class' => 'ff-edit-terms-condition', 'template' => 'termsCheckbox' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$cols = function ( ...$columns ) use ( &$k ) {
	return array(
		'index'          => $k,
		'element'        => 'container',
		'attributes'     => array(),
		'settings'       => array( 'container_class' => '', 'conditional_logics' => array() ),
		'columns'        => array_map( fn( $f ) => array( 'fields' => $f ), $columns ),
		'editor_options' => array( 'title' => 'Two Column Container', 'icon_class' => 'ff-edit-column-2' ),
		'uniqElKey'      => 'el_' . ( ++$k ),
	);
};
$submit = fn( $label ) => array(
	'uniqElKey'      => 'el_submit',
	'element'        => 'button',
	'attributes'     => array( 'type' => 'submit', 'class' => '' ),
	'settings'       => array( 'align' => 'left', 'button_style' => 'default', 'container_class' => '', 'help_message' => '', 'background_color' => '#2885ef', 'button_size' => 'md', 'color' => '#ffffff', 'button_ui' => array( 'type' => 'default', 'text' => $label, 'img_url' => '' ) ),
	'editor_options' => array( 'title' => 'Submit Button' ),
);

$forms = array(
	'contact'      => array(
		'title'  => 'Contact',
		'fields' => array(
			$cols( array( $text( 'first_name', 'First name', 'First name' ) ), array( $text( 'last_name', 'Last name', 'Last name' ) ) ),
			$cols( array( $text( 'email', 'Email', 'you@example.com', true, 'email' ) ), array( $text( 'phone', 'Phone', '7911 123456', true, 'tel' ) ) ),
			$country( 'Country' ),
			$area( 'message', 'Message', 'How can we help?' ),
		),
		'submit' => 'Send message',
		'thanks' => 'Thank you – your message has been sent. Our team will reply shortly.',
	),
	'wholesale'    => array(
		'title'  => 'Wholesale Application',
		'fields' => array(
			$cols( array( $text( 'full_name', 'Full name', 'Your full name' ) ), array( $text( 'business', 'Business / institution', 'Registered or trading name' ) ) ),
			$cols( array( $text( 'email', 'Business email', 'name@company.com', true, 'email' ) ), array( $text( 'phone', 'Phone / WhatsApp', 'Phone number', false, 'tel' ) ) ),
			$cols( array( $country( 'Country of registration' ) ), array( $select( 'business_type', 'Business type', 'Select business type', array( 'Distributor / wholesaler', 'Clinic / wellness centre', 'Pharmacy', 'Laboratory / research organisation', 'Online retailer / eCommerce', 'Brand / private label business', 'Other' ) ) ) ),
			$cols( array( $select( 'supply_format', 'Supply format required', 'Select supply format', array( 'ONYX branded', 'White label', 'Cartridge-only' ) ) ), array( $select( 'monthly_volume', 'Estimated monthly purchasing', 'Select expected volume', array( 'Under £2,500 / month', '£2,500–£5,000 / month', '£5,000–£10,000 / month', '£10,000–£25,000 / month', '£25,000+ / month' ) ) ) ),
			$select( 'products_interest', 'Products of interest', 'Select products of interest', array( 'Peptide pens', 'Cartridges', 'Vials', 'Multiple product formats', 'Not sure yet' ) ),
			$area( 'message', 'Additional requirements', 'Preferred products, strengths, packaging, launch date or other requirements.' ),
			$terms( 'I confirm this enquiry is made for a legitimate business or professional organisation and that the information provided is accurate.' ),
		),
		'submit' => 'Submit application',
		'thanks' => 'Thank you – your wholesale application has been received. Our team will review it and contact you.',
	),
	'consultation' => array(
		'title'  => 'Doctor Consultation Request',
		'fields' => array(
			$cols( array( $text( 'full_name', 'Full name', 'Your full name' ) ), array( $text( 'email', 'Email', 'you@example.com', true, 'email' ) ) ),
			$cols( array( $text( 'phone', 'Phone / WhatsApp', 'Phone number', false, 'tel' ) ), array( $country( 'Country' ) ) ),
			$area( 'message', 'What would you like to discuss?', 'Tell us briefly what you would like to discuss with an independent doctor.' ),
		),
		'submit' => 'Request consultation',
		'thanks' => 'Thank you – your request has been received. We will be in touch to arrange your consultation.',
	),
	'newsletter'   => array(
		'title'  => 'Newsletter',
		'fields' => array( $text( 'email', '', 'Your email', true, 'email' ) ),
		'submit' => 'Subscribe',
		'thanks' => 'Thanks for subscribing!',
	),
);

$ids = get_option( 'onyx_form_ids', array() );
foreach ( $forms as $key => $f ) {
	$fields = wp_json_encode( array( 'fields' => $f['fields'], 'submitButton' => $submit( $f['submit'] ) ) );
	$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}fluentform_forms WHERE title = %s", $f['title'] ) );
	$row = array( 'title' => $f['title'], 'status' => 'published', 'form_fields' => $fields, 'type' => 'form', 'has_payment' => 0, 'created_by' => 1, 'updated_at' => current_time( 'mysql' ) );
	if ( $existing ) {
		$wpdb->update( "{$wpdb->prefix}fluentform_forms", $row, array( 'id' => $existing ) );
		$id = (int) $existing;
	} else {
		$row['created_at'] = current_time( 'mysql' );
		$wpdb->insert( "{$wpdb->prefix}fluentform_forms", $row );
		$id = (int) $wpdb->insert_id;
	}
	$settings = array(
		'confirmation' => array( 'redirectTo' => 'samePage', 'messageToShow' => $f['thanks'], 'customPage' => null, 'samePageFormBehavior' => 'hide_form', 'customUrl' => null ),
		'restrictions' => array(),
		'layout'       => array( 'labelPlacement' => 'top', 'helpMessagePlacement' => 'with_label', 'errorMessagePlacement' => 'inline', 'asteriskPlacement' => 'asterisk-right' ),
	);
	$notification = array( 'name' => 'Admin notification', 'sendTo' => array( 'type' => 'email', 'email' => '{wp.admin_email}', 'field' => '', 'routing' => array() ), 'fromName' => '', 'fromEmail' => '', 'replyTo' => '', 'bcc' => '', 'subject' => 'New ' . $f['title'] . ' submission', 'message' => '<p>{all_data}</p>', 'conditionals' => array( 'conditions' => array(), 'status' => false, 'type' => 'all' ), 'enabled' => true, 'email_template' => '' );
	foreach ( array( 'formSettings' => $settings, 'notifications' => $notification ) as $mk => $mv ) {
		$wpdb->delete( "{$wpdb->prefix}fluentform_form_meta", array( 'form_id' => $id, 'meta_key' => $mk ) );
		$wpdb->insert( "{$wpdb->prefix}fluentform_form_meta", array( 'form_id' => $id, 'meta_key' => $mk, 'value' => wp_json_encode( $mv ) ) );
	}
	$ids[ $key ] = $id;
}
update_option( 'onyx_form_ids', $ids );
echo wp_json_encode( $ids ) . "\n";
