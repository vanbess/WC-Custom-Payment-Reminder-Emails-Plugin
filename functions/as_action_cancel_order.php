<?php

/**
 * Action Scheduler action to cancel orders older than 91+ days
 */

defined('ABSPATH') ?: exit();

// Get the current time
$current_time = time();

// Determine the next midnight timestamp
$next_midnight = ($current_time >= strtotime('midnight')) ? strtotime('tomorrow midnight') : strtotime('midnight');

// Schedule the recurring action to run at midnight every day
if (false === as_next_scheduled_action('pmt_reminder_cancel_old_orders') && get_option('pmt-emails-enable-disable') === 'yes') {
    as_schedule_recurring_action($next_midnight, DAY_IN_SECONDS, 'pmt_reminder_cancel_old_orders', [], 'custom_pmt_reminders');
}

/**
 * Function to run when action hook is triggered
 */
add_action('pmt_reminder_cancel_old_orders', function () {

    error_log('================ ORDER 90 DAY+ CANCELLATION CHECK START ================');

    error_log('Setting up order query and executing');

    // get the date 91 days ago
    $date_91_days_ago = date('Y-m-d', strtotime('-91 days'));

    // fetch all relevant orders
    $orders = new WP_Query([
        'post_type'      => 'shop_order',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => ['wc-pending', 'wc-part-payment'],
        'date_query' => [
            [
                'column' => 'post_date',
                'before' => $date_91_days_ago
            ]
        ]
    ]);

    error_log('Checking for order IDs...');

    // retrieve order ids
    $order_ids = $orders->posts;

    // if order ids not empty
    if (!empty($order_ids)) :

        error_log('Order IDs found: ' . print_r($order_ids, true));

        error_log('Starting order ID loop');

        // loop through $order_ids and cancel any relevant orders 
        foreach ($order_ids as $order_id) :

            // update order as needed
            $updated = wp_update_post([
                'ID'          => $order_id,
                'post_status' => 'wc-cancelled'
            ]);

            // check for error/success and log as needed
            if (is_wp_error($updated)) :
                error_log('Order ID ' . $order_id . ' could not be cancelled. WP Error returned: ' . $updated->get_error_message());
            else :
                error_log('Order ID ' . $order_id . ' successfully cancelled.');
            endif;

            error_log('Moving on to next order...');

        endforeach;

        error_log('Order ID loop complete, ending execution');

    // if $order_ids empty
    else :
        error_log('No order IDs returned, bailing');
    endif;

    error_log('================ ORDER 90 DAY+ CANCELLATION CHECK END ================');
});
