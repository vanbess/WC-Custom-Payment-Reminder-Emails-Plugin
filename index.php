<?php

/**
 * Plugin Name:       EG24 Custom Payment Reminders
 * Description:       Custom periodic payment reminder emails
 * Version:           1.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            WC Bessinger
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eg24-pmt-reminders
 */

defined('ABSPATH') || exit();

/**
 * Load functions et al after WC has loaded
 */
add_action('woocommerce_loaded', function () {

    // contants
    define('PMT_REM_PATH', plugin_dir_path(__FILE__));
    define('PMT_REM_URL', plugin_dir_url(__FILE__));

    // admin
    require_once PMT_REM_PATH . 'functions/admin.php';
});

/**
 * Hook our Action Scheduler actions to init
 */
add_action('init', function () {
    require_once PMT_REM_PATH . 'functions/as_action.php';
    require_once PMT_REM_PATH . 'functions/as_action_cancel_order.php';
});

/**
 * WooCommerce template path filter for overriding WC templates from plugin directory
 */
add_filter('woocommerce_locate_template', 'pmt_rem_woo_template_path', 1, 3);

function pmt_rem_woo_template_path($template, $template_name, $template_path)
{

    global $woocommerce;

    // set plugin path to path which contains WC overrides (same structure to follow as when overriding WC templates via theme)
    $plugin_path  = PMT_REM_PATH  . 'woocommerce/';

    // if file exists in plugin path, set our template to the template contained in our plugin
    if (file_exists($plugin_path . $template_name)) :
        return $plugin_path . $template_name;
    endif;

    // return template
    return $template;
}

// add_filter('woocommerce_email_classes', 'customize_customer_invoice_email_template', 10, 1);

// function customize_customer_invoice_email_template($email_classes) {

//     // Check if the customer invoice email is being sent
//     if (isset($email_classes['WC_Email_Customer_Invoice'])) {
//         // Set custom template path
//         $email_classes['WC_Email_Customer_Invoice']->template_html = 'path/to/custom-customer-invoice-template.php';
//     }

//     return $email_classes;
// }


/**
 * Filter invoice email subject text
 */
add_filter('woocommerce_email_subject_customer_invoice', 'my_custom_invoice_email_subject', 10, 2);

function my_custom_invoice_email_subject($subject, $order)
{

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

    // get order created date and current date
    $order_created_date = $order->get_date_created();
    $current_date       = new WC_DateTime();
    $interval           = $order_created_date->diff($current_date);
    $days_old           = intval($interval->days);

    // get custom subjects
    $one_week_subj    = get_option('email-subject-1-week');
    $two_weeks_subj   = get_option('email-subject-2-weeks');
    $one_month_subj   = get_option('email-subject-1-month');
    $two_month_subj   = get_option('email-subject-2-months');
    $three_month_subj = get_option('email-subject-3-months');

    // get interval order meta to determine which email should be sent
    $one_week_sent    = get_post_meta($order->get_id(), '_one_week_sent', true) ? 'yes' : 'no';
    $two_week_sent    = get_post_meta($order->get_id(), '_two_week_sent', true) ? 'yes' : 'no';
    $one_month_sent   = get_post_meta($order->get_id(), '_one_month_sent', true) ? 'yes' : 'no';
    $two_month_sent   = get_post_meta($order->get_id(), '_two_month_sent', true) ? 'yes' : 'no';
    $three_month_sent = get_post_meta($order->get_id(), '_three_month_sent', true) ? 'yes' : 'no';

    if ($order->has_status(['pending', 'on-hold', 'part-payment'])) :

        // order is >= 7 days old and < 14 days old
        if ($days_old >= 7 && $days_old < 14 && $one_week_sent === 'no') :
            return str_replace($placeholders, $replacements, $one_week_subj);
        endif;

        // order is >= 14 days old and < 30 days old
        if ($days_old >= 14 && $days_old <30 && $two_week_sent === 'no') :
            return str_replace($placeholders, $replacements, $one_week_subj);
        endif;

        // order is >= 30 days old and < 60 days old
        if ($days_old >= 30  && $days_old < 60 && $one_month_sent === 'no') :
            return str_replace($placeholders, $replacements, $one_month_subj);
        endif;

        // order is >= 60 days old and < 90 days old
        if ($days_old >= 60 && $days_old < 90 && $two_month_sent === 'no') :
            return str_replace($placeholders, $replacements, $two_month_subj);
        endif;

        // order is >= 90 days old
        if ($days_old >= 90 && $three_month_sent === 'no') :
            return str_replace($placeholders, $replacements, $three_month_subj);
        endif;

    endif;

    // default
    return $subject;
}
