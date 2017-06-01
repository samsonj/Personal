<?php
namespace Aka\Twitter;

class Admin {
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('pre_update_option_mtctess_settings', array($this, 'preserve_password'), 10, 2);
    }

    public function admin_menu()
    {
        add_options_page('Twitter Configuration', 'Twitter', 'manage_options', 'aka_twitter', array($this, 'options_page'));
        add_settings_section(
            'akatwitter_api',
            'Twitter API Settings',
            function() { return "Enter API settings here"; },
            'aka_twitter'
        );
        add_settings_field(
            'akatwitter_consumer_key',
            'Consumer Key',
            $this->getFieldCallback('akatwitter_consumer_key', get_option('akatwitter_consumer_key')),
            'aka_twitter',
            'akatwitter_api',
            array('label_for' => 'akatwitter_consumer_key', 'class' => '')
        );
        add_settings_field(
            'akatwitter_consumer_secret',
            'Consumer Secret',
            $this->getFieldCallback('akatwitter_consumer_secret', get_option('akatwitter_consumer_secret')),
            'aka_twitter',
            'akatwitter_api',
            array('label_for' => 'akatwitter_consumer_secret', 'class' => '')
        );
        add_settings_field(
            'akatwitter_oauth_token',
            'Consumer OAuth Token',
            $this->getFieldCallback('akatwitter_oauth_token', get_option('akatwitter_oauth_token')),
            'aka_twitter',
            'akatwitter_api',
            array('label_for' => 'akatwitter_oauth_token', 'class' => '')
        );
        add_settings_field(
            'akatwitter_oauth_token_secret',
            'Consumer OAuth Token Secret',
            $this->getFieldCallback('akatwitter_oauth_token_secret', get_option('akatwitter_oauth_token_secret')),
            'aka_twitter',
            'akatwitter_api',
            array('label_for' => 'akatwitter_oauth_token_secret', 'class' => '')
        );
        // add_settings_section()
    }

    public function register_settings()
    {
        register_setting('aka_twitter', 'akatwitter_consumer_key');
        register_setting('aka_twitter', 'akatwitter_consumer_secret');
        register_setting('aka_twitter', 'akatwitter_oauth_token');
        register_setting('aka_twitter', 'akatwitter_oauth_token_secret');
    }

    public function options_page()
    {
        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }
        ?>
        <div class="wrap">

            <?php if (false && false !== $_REQUEST['settings-updated'] ) : ?>
            <div class="updated fade"><p><strong><?php _e( 'Tessitura settings saved!', 'mtc_wptess' ); ?></strong></p></div>
            <?php endif; ?>

            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <form action="options.php" method="post">
                <?php settings_fields('aka_twitter'); ?>
                <?php do_settings_sections('aka_twitter'); ?>
                <p class="submit">
                    <input type="submit" value="Save Changes" class="button button-primary">
                </p>
            </form>

        <?php
    }

    public function getFieldCallback($name, $value) {
        return function($args) use($name, $value) {
            ?>
            <input type="text" name="<?= esc_attr($name); ?>" value="<?= esc_attr($value); ?>">
            <?php
        };
    }

}
