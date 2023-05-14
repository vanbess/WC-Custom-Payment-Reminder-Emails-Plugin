<?php

/**
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// get defined content overrides
$one_week_cont    = get_option('email-content-1-week');
$two_week_cont    = get_option('email-content-2-weeks');
$one_month_cont   = get_option('email-content-1-month');
$two_month_cont   = get_option('email-content-2-months');
$three_month_cont = get_option('email-content-3-months');

// get interval order meta to determine which email should be sent
$one_week_sent    = get_post_meta($order->get_id(), '_one_week_sent', true) ? 'yes' : 'no';
$two_week_sent    = get_post_meta($order->get_id(), '_two_week_sent', true) ? 'yes' : 'no';
$one_month_sent   = get_post_meta($order->get_id(), '_one_month_sent', true) ? 'yes' : 'no';
$two_month_sent   = get_post_meta($order->get_id(), '_two_month_sent', true) ? 'yes' : 'no';
$three_month_sent = get_post_meta($order->get_id(), '_three_month_sent', true) ? 'yes' : 'no';

// error_log('One week: ' . $one_week_sent);
// error_log('One month: ' . $one_month_sent);
// error_log('Two month: ' . $two_month_sent);
// error_log('Three month: ' . $three_month_sent);

// setup our placeholders
$placeholders = [
	'{billing_first_name}',
	'{billing_last_name}',
	'{order_number}',
	'{order_date}',
	'{payment_method}',
	'{shipping_method}',
	'{order_total}'
];

// setup our placeholder replacements
$replacements = [
	$order->get_billing_first_name(),
	$order->get_billing_last_name(),
	$order->get_order_number(),
	$order->get_date_created(),
	$order->get_payment_method(),
	$order->get_shipping_method(),
	$order->get_formatted_order_total()
];

/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);

// get order created date and current date
$order_created_date = $order->get_date_created();
$current_date       = new WC_DateTime();
$interval           = $order_created_date->diff($current_date);
$age_in_days        = intval($interval->days);

/************************************************************************
 * Setup custom headings as needed and if present if order needs payment
 ************************************************************************/
if ($order->has_status(['pending', 'on-hold', 'part-payment'])) :

	error_log('Order age in days: ' . $age_in_days);

	// ===========================
	// Order is older than a week 
	// ===========================
	// if ($age_in_days >= 7 && $one_week_cont && $one_week_sent === false) :
	if ($age_in_days >= 7 && $one_week_cont && $one_week_sent === 'no') :

		error_log('7 Day payment reminder sent for Order ID' . $order->get_id());

		// add order note
		$order->add_order_note('7 Day payment reminder send for order');

		// replace placeholders in content and set content
		$one_week_cont = str_replace($placeholders, $replacements, $one_week_cont);

		// echo content
		echo wp_kses_post(wpautop(wptexturize($one_week_cont)));

		// update appropriate meta key so that we don't send the email twice
		update_post_meta($order->get_id(), '_one_week_sent', 'yes');

	// ===========================
	// Order is older than 2 weeks
	// ===========================
	// if ($age_in_days >= 14 && $one_week_cont && $one_week_sent === false) :
	if ($age_in_days >= 14 && $two_week_cont && $two_week_sent === 'no') :

		error_log('14 Day payment reminder sent for Order ID' . $order->get_id());

		// add order note
		$order->add_order_note('14 Day payment reminder send for order');

		// replace placeholders in content and set content
		$two_week_cont = str_replace($placeholders, $replacements, $one_week_cont);

		// echo content
		echo wp_kses_post(wpautop(wptexturize($two_week_cont)));

		// update appropriate meta key so that we don't send the email twice
		update_post_meta($order->get_id(), '_two_week_sent', 'yes');

	// ============================
	// Order is older than a month
	// ============================
	elseif ($age_in_days >= 30 && $one_month_cont && $one_month_sent === 'no') :

		error_log('30 Day payment reminder sent for Order ID' . $order->get_id());

		// add order note
		$order->add_order_note('30 Day payment reminder sent for order');

		// replace placeholders in content and set content
		$one_month_cont = str_replace($placeholders, $replacements, $one_month_cont);

		// echo content
		echo wp_kses_post(wpautop(wptexturize($one_month_cont)));

		// update appropriate meta key so that we don't send the email twice
		update_post_meta($order->get_id(), '_one_month_sent', 'yes');

	// ===============================
	// Order is older than two months
	// ===============================
	elseif ($age_in_days >= 60 && $two_month_cont && $two_month_sent === 'no') :

		error_log('60 Day payment reminder sent for Order ID' . $order->get_id());

		// add order note
		$order->add_order_note('60 Day payment reminder sent for order');

		// replace placeholders in content and set content
		$two_month_cont = str_replace($placeholders, $replacements, $two_month_cont);

		// echo content
		echo wp_kses_post(wpautop(wptexturize($two_month_cont)));

		// update appropriate meta key so that we don't send the email twice
		update_post_meta($order->get_id(), '_two_month_sent', 'yes');

	// =================================
	// Order is older than three months
	// =================================
	elseif ($age_in_days >= 90 && $three_month_cont && $three_month_sent === 'no') :

		error_log('90 Day payment reminder sent for Order ID' . $order->get_id());

		// add order note
		$order->add_order_note('90 Day payment reminder sent for order');

		// replace placeholders in content and set content
		$three_month_cont = str_replace($placeholders, $replacements, $three_month_cont);

		// echo content
		echo wp_kses_post(wpautop(wptexturize($three_month_cont)));

		// update appropriate meta key so that we don't send the email twice
		update_post_meta($order->get_id(), '_three_month_sent', 'yes');

	endif;
endif;


/**
 * Hook for the woocommerce_email_order_details.
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Hook for the woocommerce_email_order_meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/**
 * Hook for woocommerce_email_customer_details.
 *
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
