<?php 

namespace RedirectionIO\Client\Wordpress;

/**
 * WordPress Core Functions Overrider
 * WordPress Version at the time of writing: 4.8.3
 *
 * This class is used to clone and modify core wordpress functions to fit our needs
 * without changing initial ones for complete isolation
 */
class WPCoreFunctionsOverrider
{
    /**
     * See source function here: wp-admin/includes/templates.php | line 1308
     */
    public function do_settings_sections($page)
    {
        global $wp_settings_sections, $wp_settings_fields;
    
        if (!isset($wp_settings_sections[$page])) {
            return;
        }
    
        $sections = (array) $wp_settings_sections[$page];
        $nb = count($sections);
    
        foreach ($sections as $section) {
            if ($section['title']) {
                if (1 === $nb) {
                    echo '<h2>' . $section['title'] . '</h2>';
                } else {
                    echo '<h2>' . $section['title'] . ' <span class="dashicons dashicons-trash connections_remove" onclick="removeConnection(event)"></span></h2>';
                }
            }
    
            if ($section['callback']) {
                call_user_func($section['callback'], $section);
            }
    
            if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
                continue;
            }
            echo '<table class="form-table">';
            $this->do_settings_fields($page, $section['id']);
            echo '</table>';
        }
    }
    
    /**
     * See source function here: wp-admin/includes/templates.php | line 1343
     */
    public function do_settings_fields($page, $section)
    {
        global $wp_settings_fields;
    
        if (!isset($wp_settings_fields[$page][$section])) {
            return;
        }
    
        echo "<tr{$class}>";

        foreach ((array) $wp_settings_fields[$page][$section] as $field) {
            $class = '';
    
            if (!empty($field['args']['class'])) {
                $class = ' class="' . esc_attr($field['args']['class']) . '"';
            }
    
            if (!empty($field['args']['label_for'])) {
                echo '<th scope="row"><label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label></th>';
            } else {
                echo '<th scope="row">' . $field['title'] . '</th>';
            }
    
            echo '<td>';
            call_user_func($field['callback'], $field['args']);
            echo '</td>';
        }

        echo '</tr>';
    }
}
