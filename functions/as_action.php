<?php

/**
 * Action Scheduler action to rund sending of emails
 */

defined('ABSPATH') ?: exit();

// Get the current time
$current_time = time();

// Determine the next midnight timestamp
$next_midnight = ($current_time >= strtotime('midnight')) ? strtotime('tomorrow midnight') : strtotime('midnight');

// Schedule the recurring action to run at midnight every day
if (false === as_next_scheduled_action('send_custom_pmt_reminder_invoice_emails') && get_option('pmt-emails-enable-disable') === 'yes') {
    as_schedule_recurring_action($next_midnight, DAY_IN_SECONDS, 'send_custom_pmt_reminder_invoice_emails', [], 'custom_pmt_reminders');
}

/**
 * Function to run when action hook is triggered
 */
add_action('send_custom_pmt_reminder_invoice_emails', function () {

    error_log('================ CUSTOM PAYMENT REMINDER ACTION START ================');

    error_log('Setting up order query and executing');

    // fetch all orders
    $orders = new WP_Query([
        'post_type'      => 'shop_order',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => ['wc-pending', 'wc-part-payment', 'wc-on-hold']
    ]);

    error_log('Checking for order IDs...');

    // retrieve order ids
    $order_ids = $orders->posts;

    // if order ids not empty
    if (!empty($order_ids)) :

        error_log('Order IDs found: ' . print_r($order_ids, true));

        error_log('Starting order ID loop');

        // loop through $order_ids and trigger sending of invoice email
        // loop through $order_ids and trigger sending of invoice email
        foreach ($order_ids as $order_id) :

            // Get the order object
            $order = wc_get_order($order_id);

            // Calculate order age in days
            $order_created_date = $order->get_date_created();
            $current_date       = new WC_DateTime();
            $interval           = $order_created_date->diff($current_date);
            $age_in_days        = intval($interval->days);

            // Get the meta values for each interval
            $one_week_sent    = get_post_meta($order_id, '_one_week_sent', true) ? 'yes' : 'no';
            $two_week_sent    = get_post_meta($order_id, '_two_week_sent', true) ? 'yes' : 'no';
            $one_month_sent   = get_post_meta($order_id, '_one_month_sent', true) ? 'yes' : 'no';
            $two_month_sent   = get_post_meta($order_id, '_two_month_sent', true) ? 'yes' : 'no';
            $three_month_sent = get_post_meta($order_id, '_three_month_sent', true) ? 'yes' : 'no';

            // If the order is less than 7 days old, skip it
            if ($age_in_days < 7) {
                error_log('Order ID ' . $order_id . ' is less than 7 days old, skipping');
                continue;
            }
            
            // if order is between 7 and 14 days old and 7-day email has not been sent
            if ($age_in_days >= 7 && $age_in_days < 14 && $one_week_sent === 'no') {

                error_log('Triggering 7-day email for order ID ' . $order_id);
                WC()->mailer()->get_emails()['WC_Email_Customer_Invoice']->trigger($order_id);

            } 
            
            // if order is between 14 and 30 days old and 14-day email has not been sent
            if ($age_in_days >= 14 && $age_in_days < 30 && $two_week_sent === 'no') {

                error_log('Triggering 14-day email for order ID ' . $order_id);
                WC()->mailer()->get_emails()['WC_Email_Customer_Invoice']->trigger($order_id);

            } 
            
            // if order is between 30 and 60 days old and 30-day email has not been sent
            if ($age_in_days >= 30 && $age_in_days < 60 && $one_month_sent === 'no') {

                error_log('Triggering 30-day email for order ID ' . $order_id);
                WC()->mailer()->get_emails()['WC_Email_Customer_Invoice']->trigger($order_id);

            }
            
            // if order is between 60 and 90 days old and 60-day email has not been sent
            if ($age_in_days >= 60 && $age_in_days < 90 && $two_month_sent === 'no') {

                error_log('Triggering 60-day email for order ID ' . $order_id);
                WC()->mailer()->get_emails()['WC_Email_Customer_Invoice']->trigger($order_id);

            }
            
            // if order is 90 days old or older and 90-day email has not been sent
            if ($age_in_days >= 90 && $three_month_sent === 'no') {

                error_log('Triggering 90-day email for order ID ' . $order_id);
                WC()->mailer()->get_emails()['WC_Email_Customer_Invoice']->trigger($order_id);

            } 

            // if no conditions met, move on to next order
            error_log('Order ID '.$order_id.' does not meet the required conditions, or an email or emails have already been sent for it. Moving on to next order...');

        endforeach;

        error_log('Order ID loop complete, ending execution');

    // if $order_ids empty
    else :
        error_log('No order IDs returned, bailing');
    endif;

    error_log('================ CUSTOM PAYMENT REMINDER ACTION END ================');
});
