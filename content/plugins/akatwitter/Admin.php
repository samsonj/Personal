<?php
namespace AKA\Twitter;

class Admin {
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        // add_filter('pre_update_option_mtctess_settings', array($this, 'preserve_password'), 10, 2);
    }

    public function admin_menu()
    {
        add_options_page('Twitter', 'Twitter feeds', 'manage_options', 'aka_twitter', array($this, 'options_page'));
    }

    public function register_settings()
    {
        register_setting('mtctess_settings', 'mtctess_settings');

    }

    public function options_page()
    {
        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }

        // Get existing settings
        $settings = get_option( 'mtctess_settings', array() );
        ?>

        <div class="wrap">

            <?php if (false && false !== $_REQUEST['settings-updated'] ) : ?>
                <div class="updated fade"><p><strong><?php _e( 'Tessitura settings saved!', 'mtc_wptess' ); ?></strong></p></div>
            <?php endif; ?>

            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

            <form method="post" action="options.php">
                <?php settings_fields( 'mtctess_settings' ); ?>

                <h3>Ticket exchanges</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Allow ticket exchanges', 'mtc_wptess'); ?></label></th>
                        <td>
                            <label><input name="mtctess_settings[allow_exchanges]" type="radio" value="1" <?php checked((int)$settings['allow_exchanges'], 1); ?>> Yes</label>
                            <label><input name="mtctess_settings[allow_exchanges]" type="radio" value="0" <?php checked((int)$settings['allow_exchanges'], 0); ?>> No</label>
                        </td>
                    </tr>
                </table>

                <h3>Email settings</h3>

                <table class="form-table">
                    <tr valign="top"><th scope="row"><label><?php _e( 'Email template directory', 'mtc_wptess' ); ?></label></th>
                        <td>
                            This setting may become available at a later date. As of now, templates should be located in the <code>tessitura/email_templates</code> directory of the currently active theme.
                        </td>
                    </tr>
                </table>

                <h3>Integration settings</h3>

                <table class="form-table">
                    <tr valign="top"><th scope="row"><label><?php _e( 'Tessitura working timezone', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <select name="mtctess_settings[timezone]">
                            <?php echo wp_timezone_choice($settings['timezone']); ?>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Account Root Page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][root]',
                                'selected' => $settings['pages']['root'],
                                'show_option_none' => 'NO PAGE SELECTED'
                            )); ?>

                        </td>
                    </tr>

                    <?php if ($settings['pages']['root']): ?>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Login page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][login]',
                                'selected' => $settings['pages']['login'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Account Home page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][account_home]',
                                'selected' => $settings['pages']['account_home'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Upcoming Events page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][upcoming_events]',
                                'selected' => $settings['pages']['upcoming_events'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Personal Details page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][personal_details]',
                                'selected' => $settings['pages']['personal_details'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Personal Details edit page', 'mtc_wptess' ); ?></label></th>

                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][edit_details]',
                                'selected' => $settings['pages']['edit_details'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Personal Details delete page', 'mtc_wptess' ); ?></label></th>

                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][delete_details]',
                                'selected' => $settings['pages']['delete_details'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Additional Tickets page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][additional_tickets]',
                                'selected' => $settings['pages']['additional_tickets'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Subscription Details page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][subscription_details]',
                                'selected' => $settings['pages']['subscription_details'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Exchange Tickets page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][exchange_tickets]',
                                'selected' => $settings['pages']['exchange_tickets'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Forgot Password page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][forgot_password]',
                                'selected' => $settings['pages']['forgot_password'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'FAQ page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][faq]',
                                'selected' => $settings['pages']['faq'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <tr valign="top"><th scope="row"><label><?php _e( 'Show Information Page', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name'=>'mtctess_settings[pages][show_info]',
                                'selected' => $settings['pages']['show_info'],
                                'show_option_none' => 'NO PAGE SELECTED',
                                // 'child_of' => $settings['pages']['root']
                            )); ?>

                        </td>
                    </tr>

                    <?php endif; ?>
                </table>

                <h3>Order Cart Defaults</h3>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><label for="source_additional_tix"><?php _e( 'Additional Tickets Source ID', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[sources][additional]" id="source_additional_tix" value="<?php echo esc_attr($settings['sources']['additional']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="source_exchange_tix"><?php _e( 'Exchange Tickets Source ID', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[sources][exchange]" id="source_exchange_tix" value="<?php echo esc_attr($settings['sources']['exchange']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                </table>

                <h3>Modes of Sale</h3>
                <p>Please specify these as numeric MOS IDs from Tessitura. Starting a new purchase path will regenerate the login session, abandon any current carts and switch to the relevant MOS.</p>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><label for="mos_default"><?php _e( 'Default', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[mos][default]" id="mos_default" value="<?php echo esc_attr($settings['mos']['default']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="mos_subscribers"><?php _e( 'Subscriptions', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[mos][subscribers]" id="mos_subscribers" value="<?php echo esc_attr($settings['mos']['subscribers']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="mos_additional_tix"><?php _e( 'Additional Tickets', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[mos][additional]" id="mos_additional_tix" value="<?php echo esc_attr($settings['mos']['additional']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="mos_exchange_tix"><?php _e( 'Exchange Tickets', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[mos][exchange]" id="mos_exchange_tix" value="<?php echo esc_attr($settings['mos']['exchange']); ?>" class="small-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                </table>

                <h3>SOAP API settings</h3>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><label for="soap_url_simple"><?php _e( 'Simple Types WSDL URL', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[soap_url_simple]" id="soap_url_simple" value="<?php echo esc_attr($settings['soap_url_simple']); ?>" class="regular-text"><br />
                            <!-- <label class="description" for="soap_url_simple"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="soap_url_full"><?php _e( 'Full WSDL URL', 'mtc_wptess' ); ?></label></th>
                        <td>
                        <input type="text" name="mtctess_settings[soap_url_full]" id="soap_url_full" value="<?php echo esc_attr($settings['soap_url_full']); ?>" class="regular-text"><br />
                            <!-- <label class="description" for="soap_url_full"><?php _e( 'SOAP endpoint for the Tessitura Web API', 'mtc_wptess' ); ?></label> -->
                        </td>
                    </tr>
                </table>

                <h3>REST API settings</h3>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><label for="rest_url"><?php _e( 'REST API base URL', 'mtc_wptess' ); ?></label></th>
                        <td>
                            <input type="text" name="mtctess_settings[rest_url]" id="rest_url" value="<?php echo esc_attr($settings['rest_url']); ?>" class="regular-text"><br />
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="rest_username"><?php _e( 'REST API username', 'mtc_wptess' ); ?></label></th>
                        <td>
                        <input type="text" name="mtctess_settings[rest_username]" id="rest_username" value="<?php echo esc_attr($settings['rest_username']); ?>" class="medium-text"><br />
                        </td>
                    </tr>
                    <tr valign="top"><th scope="row"><label for="rest_password"><?php _e( 'REST API password', 'mtc_wptess' ); ?></label></th>
                        <td>
                        <input type="password" name="mtctess_settings[rest_password]" id="rest_password" value="" class="medium-text"><br />
                        <label class="description"><?php _e( 'Leave blank to keep the existing password', 'mtc_wptess' ); ?></label>
                        </td>
                    </tr>
                </table>

                <input type="submit" value="Save">

            </form>
        </div>

        <?php
    }

}
