<?php

namespace RedirectionIO\Client\Wordpress;

/**
 * WordPress Core Functions Overrider
 * WordPress Version at the time of writing: 4.8.3.
 *
 * This class is used to clone and modify core wordpress functions to fit our needs
 * without changing initial ones for complete isolation
 */
class WPCoreFunctionsOverrider
{
    /**
     * See source function here: wp-admin/includes/templates.php | line 1308.
     *
     * @param mixed $page
     */
    public function doSettingsSections($page)
    {
        global $wp_settings_sections, $wp_settings_fields;

        if (!isset($wp_settings_sections[$page])) {
            return;
        }

        $sections = (array) $wp_settings_sections[$page];

        // remove projectKey, doNotRedirectAdmin sections from sections array to use it later
        $projectKeySection = $sections['redirectionio-section-project-key'];
        $doNotRedirectAdminSection = $sections['redirectionio-section-do-not-redirect-admin'];
        unset($sections['redirectionio-section-project-key'], $sections['redirectionio-section-do-not-redirect-admin']);

        $this->outputProjectKey($page, $projectKeySection);

        echo '<div id="rio_connections">';

        $nb = \count($sections);
        foreach ($sections as $section) {
            if ($section['title']) {
                if (1 === $nb) {
                    echo '<h2>' . $section['title'] . '</h2>';
                } else {
                    echo '<h2>' . $section['title'] . ' <span class="dashicons dashicons-trash rio_connections_remove" onclick="rioRemoveConnection(event)"></span></h2>';
                }
            }

            $connection = RedirectionIOSettingsPage::getConnectionFromSection($page, $section['id']);

            if (RedirectionIOSettingsPage::isWorkingConnection($connection)) {
                echo '
                    <div class="rio_connection_status rio_connection_working">
                        <span class="dashicons dashicons-yes"></span>' . __('working', 'redirectionio') .
                    '</div>
                ';
            } else {
                echo '
                    <div class="rio_connection_status rio_connection_not_working">
                    <span class="dashicons dashicons-no-alt"></span>' . __('not working', 'redirectionio') .
                    '</div>
                ';
            }

            if ($section['callback']) {
                \call_user_func($section['callback'], $section);
            }

            if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
                continue;
            }
            echo '<table class="form-table">';
            $this->doSettingsFields($page, $section['id']);
            echo '</table>';
        }

        echo '
            <button id="rio_connections_add" class="button" onclick="rioAddConnection(event)">' .
            __('Add') .
            '</button>
            </div>
        ';

        $this->outputDoNotRedirectAdminSection($page, $doNotRedirectAdminSection);
    }

    /**
     * See source function here: wp-admin/includes/templates.php | line 1343.
     *
     * @param mixed $page
     * @param mixed $section
     */
    private function doSettingsFields($page, $section)
    {
        global $wp_settings_fields;

        if (!isset($wp_settings_fields[$page][$section])) {
            return;
        }

        foreach ((array) $wp_settings_fields[$page][$section] as $field) {
            $class = '';

            if (!empty($field['args']['class'])) {
                $class = ' class="' . esc_attr($field['args']['class']) . '"';
            }

            echo "<tr{$class}>";

            if (!empty($field['args']['label_for'])) {
                echo '<th scope="row"><label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label></th>';
            } else {
                echo '<th scope="row">' . $field['title'] . '</th>';
            }

            echo '<td>';
            \call_user_func($field['callback'], $field['args']);
            echo '</td>';
            echo '</tr>';
        }
    }

    /**
     * @param mixed $page
     * @param mixed $section
     */
    private function outputProjectKey($page, $section)
    {
        if ($section['title']) {
            echo '<h2>' . $section['title'] . '</h2>';
        }

        echo '<p>' . __('Please fill here your redirection.io project key.', 'redirectionio') . '</p>';
        echo '<p>' . sprintf(__('
            You can find it in the instances section of your dashboard: %s.
        ', 'redirectionio'), '<a href="https://redirection.io/manager" target="_blank">https://redirection.io/manager</a>') . '</p>';
        echo '<table class="form-table">';
        $this->doSettingsFields($page, $section['id']);
        echo '</table>';
    }

    /**
     * @param mixed $page
     * @param mixed $section
     */
    private function outputDoNotRedirectAdminSection($page, $section)
    {
        if ($section['title']) {
            echo '<h2>' . $section['title'] . '</h2>';
        }

        echo '<p>' . __('This option let you ignore eventual redirection rules set on admin area pages.', 'redirectionio') . '</p>';
        echo '<p>' . sprintf(__("
            %sExample :%s if by mistake you add a redirection rule from %s/wp-login.php%s to %s/foo%s,
            you'll not be able to connect to your admin area anymore.
        ", 'redirectionio'), '<b>', '</b>', '<code>', '</code>', '<code>', '</code>') . '</p>';
        echo '<p>' . __('To prevent this, we recommend you to always leave this option enabled.', 'redirectionio') . '</p>';

        echo '<table class="form-table">';
        $this->doSettingsFields($page, $section['id']);
        echo '</table>';
    }
}
