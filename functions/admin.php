<?php

defined('ABSPATH') ?: exit();

/**
 * Register admin page
 */
add_action('admin_menu', function () {
    add_menu_page(
        'Custom Payment Reminder Emails',
        'Custom Payment Reminder Emails',
        'manage_options',
        'custom-payment-reminder-emails',
        'custom_payment_reminder_emails',
        'dashicons-email',
        20
    );
});


/**
 * Render admin page
 *
 * @return void
 */
function custom_payment_reminder_emails() {

    global $title;

?>

    <div id="custom_payment_reminder_emails">

        <h2><?php echo $title; ?></h2>

        <?php
        // save email content
        if (isset($_POST['submit'])) :

            // grab all subbed fields and save to db
            $one_week_subj    = isset($_POST['email-subject-1-week']) ? sanitize_text_field($_POST['email-subject-1-week']) : null;
            $two_week_subj    = isset($_POST['email-subject-2-weeks']) ? sanitize_text_field($_POST['email-subject-2-weeks']) : null;
            $one_month_subj   = isset($_POST['email-subject-1-month']) ? sanitize_text_field($_POST['email-subject-1-month']) : null;
            $two_month_subj   = isset($_POST['email-subject-2-months']) ? sanitize_text_field($_POST['email-subject-2-months']) : null;
            $three_month_subj = isset($_POST['email-subject-3-months']) ? sanitize_text_field($_POST['email-subject-3-months']) : null;
            $one_week_cont    = isset($_POST['email-content-1-week']) ? stripslashes(sanitize_textarea_field($_POST['email-content-1-week'])) : null;
            $two_week_cont    = isset($_POST['email-content-2-weeks']) ? stripslashes(sanitize_textarea_field($_POST['email-content-2-weeks'])) : null;
            $one_month_cont   = isset($_POST['email-content-1-month']) ? stripslashes(sanitize_textarea_field($_POST['email-content-1-month'])) : null;
            $two_month_cont   = isset($_POST['email-content-2-months']) ? stripslashes(sanitize_textarea_field($_POST['email-content-2-months'])) : null;
            $three_month_cont = isset($_POST['email-content-3-months']) ? stripslashes(sanitize_textarea_field($_POST['email-content-3-months'])) : null;

            // emails disabled/enabled
            update_option('pmt-emails-enable-disable', $_POST['emails-enable-disable']);

            // update
            update_option('email-subject-1-week', $one_week_subj);
            update_option('email-subject-2-weeks', $two_week_subj);
            update_option('email-subject-1-month', $one_month_subj);
            update_option('email-subject-2-months', $two_month_subj);
            update_option('email-subject-3-months', $three_month_subj);
            update_option('email-content-1-week', $one_week_cont);
            update_option('email-content-2-weeks', $two_week_cont);
            update_option('email-content-1-month', $one_month_cont);
            update_option('email-content-2-months', $two_month_cont);
            update_option('email-content-3-months', $three_month_cont); ?>

            <div class="notice notice-success is-dismissible" style="left: -15px;">
                <p><?php _e('Reminder email content updated.', 'woocommerce'); ?></p>
            </div>

        <?php endif; ?>

        <div class="notice notice-warning is-dismissible" style="left: -15px; margin-bottom: 3em;">

            <p><b><i><u>IMPORTANT NOTES:</u></i></b></p>

            <ul id="custom_payment_reminder_emails_instructions">

                <li>
                    Avoid using contractions such as don't, won't, can't etc in your text as these will not be rendered properly. Use do not, will not, cannot and so on instead.
                </li>

                <li>
                    Placeholders which can be used in your custom content or subject:
                    <ul id="custom_payment_reminder_emails_placeholders">
                        <li><b>{billing_first_name}:</b> This placeholder is replaced with the first name of the client.</li>
                        <li><b>{billing_last_name}:</b> This placeholder is replaced with the last name of the client.</li>
                        <li><b>{order_number}:</b> This placeholder is replaced with the order number of the invoice.</li>
                        <li><b>{order_date}:</b> This placeholder is replaced with the date the order was placed.</li>
                        <li><b>{payment_method}:</b> This placeholder is replaced with the payment method used for the order.</li>
                        <li><b>{shipping_method}:</b> This placeholder is replaced with the shipping method used for the order.</li>
                        <li><b>{order_total}</b>: This placeholder is replaced with the total amount of the order.</li>
                    </ul>
                </li>

                <li>
                    If email subject is not supplied for a particular email, said email will <b>not</b> be sent.
                </li>

                <li>
                    If email content is not supplied for a particular email, said email will <b>not</b> be sent.
                </li>

            </ul>

        </div>

        <div id="custom_payment_reminder_emails_form">

            <form method="post" action="">

                <!-- enable/disable -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="emails-enable-disable" class="block-label"><b><i>Enable auto payment reminder emails?</i></b></label>
                    </p>
                    <p>
                        <select name="emails-enable-disable" id="emails-enable-disable" class="regular-text">

                            <?php if (get_option('pmt-emails-enable-disable') && get_option('pmt-emails-enable-disable') === 'yes') : ?>
                                <option value="no">Disable</option>
                                <option value="yes" selected>Enable</option>
                            <?php elseif ((get_option('pmt-emails-enable-disable') && get_option('pmt-emails-enable-disable') === 'no') || !get_option('pmt-emails-enable-disable')) : ?>
                                <option value="no" selected>Disable</option>
                                <option value="yes">enabled</option>
                            <?php endif; ?>

                        </select>
                    </p>
                    <span class="pmt-input-help warning"><i><b>It is recommended that you leave this set to Disabled while you are editing/adding emails, or if you are experiencing any problems.</b></i></span>
                </div>

                <!-- 1 week -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="email-subject-1-week" class="block-label"><b><i>Email subject - 1 week</i></b></label>
                    </p>
                    <p>
                        <input type="text" id="email-subject-1-week" name="email-subject-1-week" value="<?php echo !is_null(get_option('email-subject-1-week')) ? get_option('email-subject-1-week') : ''; ?>" class="regular-text">
                    </p>
                    <p>
                        <label for="email-content-1-week" class="block-label"><b><i>Email content - 1 week</i></b></label>
                    </p>
                    <p>
                        <textarea oninput="autoResize(event)" id="email-content-1-week" name="email-content-1-week" class="regular-text auto-resize" rows="20"><?php echo !is_null(get_option('email-content-1-week')) ? get_option('email-content-1-week') : ''; ?></textarea>
                    </p>

                </div>

                <!--2 weeks -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="email-subject-2-weeks" class="block-label"><b><i>Email subject - 2 weeks</i></b></label>
                    </p>
                    <p>
                        <input type="text" id="email-subject-2-weeks" name="email-subject-2-weeks" value="<?php echo !is_null(get_option('email-subject-2-weeks')) ? get_option('email-subject-2-weeks') : ''; ?>" class="regular-text">
                    </p>
                    <p>
                        <label for="email-content-2-weeks" class="block-label"><b><i>Email content - 2 weeks</i></b></label>
                    </p>
                    <p>
                        <textarea oninput="autoResize(event)" id="email-content-2-weeks" name="email-content-2-weeks" class="regular-text auto-resize" rows="20"><?php echo !is_null(get_option('email-content-2-weeks')) ? get_option('email-content-2-weeks') : ''; ?></textarea>
                    </p>

                </div>

                <!-- 1 month -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="email-subject-1-month" class="block-label"><b><i>Email subject - 1 month</i></b></label>
                    </p>
                    <p>
                        <input type="text" id="email-subject-1-month" name="email-subject-1-month" value="<?php echo !is_null(get_option('email-subject-1-month')) ? get_option('email-subject-1-month') : ''; ?>" class="regular-text">
                    </p>
                    <p>
                        <label for="email-content-1-month" class="block-label"><b><i>Email content - 1 month</i></b></label>
                    </p>
                    <p>
                        <textarea oninput="autoResize(event)" id="email-content-1-month" name="email-content-1-month" class="regular-text auto-resize" rows="20"><?php echo !is_null(get_option('email-content-1-month')) ? get_option('email-content-1-month') : ''; ?></textarea>
                    </p>
                </div>


                <!-- 2 months -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="email-subject-2-months" class="block-label"><b><i>Email subject - 2 months</i></b></label>
                    </p>
                    <p>
                        <input type="text" id="email-subject-2-months" name="email-subject-2-months" value="<?php echo !is_null(get_option('email-subject-2-months')) ? get_option('email-subject-2-months') : ''; ?>" class="regular-text">
                    </p>
                    <p>
                        <label for="email-content-2-months" class="block-label"><b><i>Email content - 2 months</i></b></label>
                    </p>
                    <p>
                        <textarea oninput="autoResize(event)" id="email-content-2-months" name="email-content-2-months" class="regular-text auto-resize" rows="20"><?php echo !is_null(get_option('email-content-2-months')) ? get_option('email-content-2-months') : ''; ?></textarea>
                    </p>
                </div>

                <hr>

                <!-- 3 months -->
                <div class="custom_payment_reminder_emails_input_group">
                    <p>
                        <label for="email-subject-3-months" class="block-label"><b><i>Email subject - 3 months</i></b></label>
                    </p>
                    <p>
                        <input type="text" id="email-subject-3-months" name="email-subject-3-months" value="<?php echo !is_null(get_option('email-subject-3-months')) ? get_option('email-subject-3-months') : ''; ?>" class="regular-text">
                    </p>
                    <p>
                        <label for="email-content-3-months" class="block-label"><b><i>Email content - 3 months</i></b></label>
                    </p>
                    <p>
                        <textarea oninput="autoResize(event)" id="email-content-3-months" name="email-content-3-months" class="regular-text auto-resize" rows="20"><?php echo !is_null(get_option('email-content-3-months')) ? get_option('email-content-3-months') : ''; ?></textarea>
                    </p>
                </div>

                <!-- submit -->
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </form>

        </div>

    </div>

    <style>
        .custom_payment_reminder_emails_input_group {
            padding-bottom: 1px;
        }

        span.pmt-input-help.warning {
            margin-top: -7px;
            display: block;
            margin-left: 3px;
            color: red;
            font-size: 13px;
        }

        div#custom_payment_reminder_emails .block-label {
            display: block;
            margin-bottom: -8px;
            padding-left: 3px;
        }

        div#custom_payment_reminder_emails>h2 {
            background: white;
            padding: 1em 1.5em;
            margin-top: 0;
            margin-left: -19px;
            box-shadow: 0px 2px 4px lightgray;
        }

        div#custom_payment_reminder_emails input,
        div#custom_payment_reminder_emails textarea {
            box-shadow: 2px 2px 2px lightgray;
        }

        ul#custom_payment_reminder_emails_instructions {
            list-style: auto;
            padding-left: 1.5em;
            line-height: 2;
        }

        ul#custom_payment_reminder_emails_placeholders {
            list-style: circle;
            padding-left: 1.5em;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('#test-1-week').click(function(e) {
                e.preventDefault();

                data = {
                    '_ajax_nonce': '<?php echo wp_create_nonce('test pmt email') ?>',
                    'action': 'test_pmt_emails',
                    'subject': $('#email-subject-1-week').val(),
                    'content': $('#email-content-1-week').val()
                }

                $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                    console.log(response)
                })

            });
        });

        // Auto resize event for textare inputs
        function autoResize(event) {
            setTimeout(() => {
                let textarea = event.target;
                textarea.style.height = 'auto';
                textarea.style.height = parseInt(10) + textarea.scrollHeight + 'px';
            }, 2000);
        }

        const textareas = document.querySelectorAll('.auto-resize');
        textareas.forEach(function(textarea) {
            autoResize({
                target: textarea
            });
        });
    </script>

<?php }


?>