<?php

namespace RedirectionIO\Client\Wordpress;

use RedirectionIO\Client\Sdk\Client;
use RedirectionIO\Client\Sdk\Exception\AgentNotFoundException;
use RedirectionIO\Client\Sdk\HttpMessage\Request;

/**
 *  RedirectionIOSettingsPage class.
 *
 * This class is used to create a new page in the admin area
 * dedicated to redirection.io. It lets you configuring the plugin as you like.
 */
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
        load_plugin_textdomain('redirectionio', false, dirname(plugin_basename(__FILE__)) . '/../languages');
    }

    public function outputContent()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $title = 'redirection.io';
        $intro =
            '<p>' .
                __('redirection.io let you track HTTP errors and setup useful HTTP redirections.', 'redirectionio') .
            '</p><p>' .
               sprintf(__('This plugin works in combination with <a href="%s">redirection.io</a> 
                    and need an installed and configured agent on your server.
                    </br>
                    Before using it, please make sure that you have :', 'redirectionio'), esc_url('//redirection.io')) .
                '<ul>' .
                    '<li>' . sprintf(__('created a redirection.io account <a href="">here</a>', 'redirectionio'), esc_url('//redirection.io')) . '</li>' .
                    '<li>' . sprintf(__('followed the <a href="">installation guide</a> to setup a redirection.io agent on your server', 'redirectionio'), esc_url('//redirection.io')) . '</li>' .
                '</ul>' .
            '</p><p>' .
                __('Drop us an email to support@redirection.io if you need help or have any question.', 'redirectionio') .
            '</p><p>' .
                __('Note: in most cases, you only have one agent, so you only need to configure one connection.', 'redirectionio') .
            '</p>'
        ;
        $confirm = __('Are you sure ?', 'redirectionio');

        echo '
            <div class="wrap" id="redirectionio">
                <h1>' . $title . '</h1>
                <div>' . $intro . '</div>
                <form method="post" action="options.php">
        ';

        settings_fields('redirectionio-group');
        $this->overrider->doSettingsSections('redirectionio');

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

        foreach ($options['connections'] as $i => $option) {
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
                        $required = false;
                        $description = __('[Optional] If you have multiple connections, you may find useful to name them for better readibility.', 'redirectionio');
                        $placeholder = __('my-connection', 'redirectionio');
                        break;
                    case 'remote_socket':
                        $title = __('Agent address', 'redirectionio');
                        $required = true;
                        $description = __('[Required] Insert here your agent address. Internet Domain socket (AF_INET) and Unix Domain socket (AF_UNIX) are supported.<br/> Examples: 192.168.1.1:20301, agent.my-website.com:10301, /var/run/my-agent.sock', 'redirectionio');
                        $placeholder = '192.168.1.1:20301';
                        break;
                    default:
                        $title = 'unknown';
                        $required = false;
                        $description = '';
                        $placeholder = '';
                }

                add_settings_field(
                    $i . '_' . $key,
                    $title . ($required ? '*' : ''),
                    [$this, 'printField'],
                    'redirectionio',
                    'redirectionio-section-' . $i,
                    [
                        'id' => $i,
                        'type' => $key,
                        'value' => $value,
                        'placeholder' => $placeholder,
                        'description' => $description,
                    ]
                );
            }
        }

        // Add doNotRedirectAdmin option
        add_settings_section(
            'redirectionio-section-do-not-redirect-admin',
            __('Disable redirections for admin area', 'redirectionio'),
            [$this, 'printSection'],
            'redirectionio'
        );

        add_settings_field(
            'redirectionio-checkbox-do-not-redirect-admin',
            __("Yes, I wan't to disable it", 'redirectionio'),
            [$this, 'printCheckbox'],
            'redirectionio',
            'redirectionio-section-do-not-redirect-admin',
            $options['doNotRedirectAdmin']
        );
    }

    /**
     * @param array $input
     */
    public function sanitizeInput(array $input)
    {
        $newInput = [];

        foreach ($input['connections'] as $i => $option) {
            foreach ($option as $key => $value) {
                $newInput['connections'][$i][$key] = sanitize_text_field($input['connections'][$i][$key]);
            }
        }

        $newInput['doNotRedirectAdmin'] = $input['doNotRedirectAdmin'];

        return $newInput;
    }

    public function printSection()
    {
    }

    /**
     * @param array $args
     */
    public function printField(array $args)
    {
        $id = isset($args['id']) ? $args['id'] : '';
        $type = isset($args['type']) ? $args['type'] : '';
        $value = isset($args['value']) ? $args['value'] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $description = isset($args['description']) ? $args['description'] : '';

        echo "<input id='redirectionio_{$id}_{$type}' name='redirectionio[connections][$id][$type]' size='40' type='text' value='$value' placeholder='$placeholder' />";
        echo "<p class='description' id='redirectionio_{$id}_{$type}_description'>$description</p>";
    }

    /**
     * @param bool $checked
     */
    public function printCheckbox($checked)
    {
        echo '<input id="redirectionio_doNotRedirectAdmin" name="redirectionio[doNotRedirectAdmin]" type="checkbox" ' . ($checked ? 'checked' : '') . ' />';
    }

    public function registerAssets()
    {
        wp_enqueue_style('redirectionio', plugins_url('../assets/css/redirectionio.css', __FILE__));
        wp_enqueue_script('redirectionio', plugins_url('../assets/js/redirectionio.js', __FILE__), [], false, true);
    }

    /**
     * Return a connection (associative array with (`port` && `host`) || `remote_socket` keys).
     *
     * @param mixed $page
     * @param mixed $section
     */
    public static function getConnectionFromSection($page, $section)
    {
        global $wp_settings_fields;

        if (!isset($wp_settings_fields[$page][$section])) {
            return false;
        }

        foreach ((array) $wp_settings_fields[$page][$section] as $field) {
            if ($field['args']['type'] === 'remote_socket') {
                $remoteSocket = $field['args']['value'];
            }
        }

        return ['remote_socket' => $remoteSocket];
    }

    /**
     * Test if a connection is currently working.
     *
     * $connection param should be an associative array
     * with (`port` && `host`) || `remote_socket` keys
     *
     * @param mixed $connection array|bool(false)
     */
    public static function isWorkingConnection($connection)
    {
        if ($connection === false) {
            return false;
        }

        $client = new Client(
            ['checkStatus' => [
                'host' => $connection['host'],
                'port' => $connection['port'],
                'remote_socket' => $connection['remote_socket'],
            ]],
            10000,
            true
        );

        try {
            $request = new Request('', '', '');
            $response = $client->findRedirect($request);
        } catch (AgentNotFoundException $e) {
            return false;
        }

        return true;
    }
}
