<?php

namespace RedirectionIO\Client\Wordpress;

class RedirectionIOSettingsPage
{
    private $overrider;

    public function __construct()
    {
        $this->overrider = new WPCoreFunctionsOverrider();

        add_action('init', [$this, 'setTranslations']);
        add_action('admin_menu', [$this, 'setUp']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'registerAssets']);
    }

    public function setUp()
    {
        add_options_page('redirection.io', 'redirection.io', 'manage_options', 'redirectionio', [$this, 'outputContent']);
    }

    public function setTranslations()
    {
        load_plugin_textdomain('redirectionio', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function outputContent()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $title = __('redirection.io settings', 'redirectionio');
        $intro = __('Proxy client for redirection.io | Put an end to 404 errors - Track HTTP
            errors and setup useful HTTP redirections. Please set here the connection
            options of your redirection.io agent [required].', 'redirectionio');
        $confirm = __('Are you sure ?', 'redirectionio');

        echo '
            <div class="wrap">
                <h1>' . $title . '</h1>
                <p>' . $intro . '</p>
                <form id="connections" method="post" action="options.php">
        ';

        settings_fields('redirectionio-group');
        $this->overrider->do_settings_sections('redirectionio');

        echo '<button id="connections_add" class="button" onclick="addConnection(event)">' . __('Add') . '</button>';

        submit_button();

        echo '
            </form></div>
            <script>
                var confirmStr = \'' . $confirm . '\';
            </script>
        ';
    }

    public function registerSettings()
    {
        register_setting(
            'redirectionio-group',
            'redirectionio',
            [$this, 'sanitizeInput']
        );

        $options = get_option('redirectionio');

        foreach ($options as $i => $option) {
            add_settings_section(
                'redirectionio-section-' . $i,
                sprintf(__('Connection #%s', 'redirectionio'), $i + 1),
                [$this, 'printSection'],
                'redirectionio'
            );

            foreach ($option as $key => $value) {
                switch ($key) {
                    case 'name':
                        $title = __('Name', 'redirectionio');
                        break;
                    case 'host':
                        $title = __('Host', 'redirectionio');
                        break;
                    case 'port':
                        $title = __('Port', 'redirectionio');
                        break;
                    default:
                        $title = 'unknown';
                }

                add_settings_field(
                    $id . '_' . $key,
                    $title,
                    [$this, 'printField'],
                    'redirectionio',
                    'redirectionio-section-' . $i,
                    [
                        'id' => $i,
                        'type' => $key,
                        'value' => $value,
                    ]
                );
            }
        }
    }

    public function sanitizeInput($input)
    {
        $newInput = [];

        foreach ($input as $i => $option) {
            foreach ($option as $key => $value) {
                $newInput[$i][$key] = sanitize_text_field($input[$i][$key]);
            }
        }

        return $newInput;
    }

    public function printSection()
    {
    }

    public function printField($args)
    {
        $id = array_key_exists('id', $args) ? $args['id'] : '';
        $type = array_key_exists('type', $args) ? $args['type'] : '';
        $value = array_key_exists('value', $args) ? $args['value'] : '';
        echo "<input id='redirectionio_{$id}_{$type}' name='redirectionio[$id][$type]' size='40' type='text' value='$value' />";
    }

    public function registerAssets()
    {
        wp_enqueue_style('redirectionio', plugins_url('assets/css/redirectionio.css', __FILE__));
        wp_enqueue_script('redirectionio', plugins_url('assets/js/redirectionio.js', __FILE__), [], false, true);
    }
}
