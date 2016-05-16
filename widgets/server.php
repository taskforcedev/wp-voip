<?php
/**
 * File: widgets/server.php
 *
 * Registers a WordPress widget for VoIP Servers.
 *
 * @author David Craig <david.craig10@nhs.net>
 */

/**
 * Class VoipWidget
 */
class VoipWidget extends WP_Widget
{
    /**
     * @var bool|\Voip\Models\Server|null|object Server post.
     */
    public $server;

    /**
     * Create a Server Widget.
     */
    public function __construct()
    {
        // Instantiate the parent object
        parent::__construct(false, 'VoIP Widget',
            ['description' => __('Customizable VoIP Server Widget', 'voip-server-manager')]);

        // Get the first server post type.

        $server = $this->getFirstServer();
        if ($server !== false) {
            $this->server = $server;
        }
    }

    /**
     * Gets the first server post type.
     * @return bool|\Voip\Models\Server|null|object
     */
    public function getFirstServer()
    {
        try {
            $server = (new \Voip\Models\Server())->getFirstOfType();
        } catch (Exception $e) {
            return null;
        }

        return $server;
    }

    /**
     * Display the server widget.
     * @param mixed $args     Widget arguments.
     * @param mixed $instance Widget instance
     */
    public function widget($args, $instance)
    {
        $fields = [ 'title', 'server_id' ];

        // Default Values
        $title              = 'false';

        foreach ($fields as $field) {
            if (array_key_exists($field, $instance)) {
                $$field = $instance[$field];
            }
        }

        if (isset($server_id)) {
            $this->server = \Voip\Models\Server::getById($server_id);
            if (is_null($this->server)) {
                return;
            }
        }

        if (is_null($this->server)) {
            $this->server = \Voip\Models\Server::getFirstOfType();
        }

        /* Start the output of the widget */

        // Prevent display of title if none is set.
        if ($title == 'false') { $title = ''; }

        if ($title !== '') {
            echo '<h2>' . $title . '</h2>';
        }

        // TODO: Output the rest of the widget.
    }

    /**
     * Update a widget with new instance values.
     *
     * @param $new_instance The new widget instance.
     * @param $old_instance The old widget instance.
     *
     * @return mixed
     */
    public function update($new_instance, $old_instance)
    {
        // Checkboxes
        $checkboxes = [];
        // Text Fields
        $textInputs = [ 'title' ];
        // Selects
        $selects = [ 'server_id' ];

        $instance = $old_instance;

        foreach ($checkboxes as $field) {
            if (array_key_exists($field, $new_instance)) {
                if ($new_instance[$field] == 'on' || $new_instance[$field] == 'true') {
                    $instance[$field] = 'true';
                }
            } else {
                $instance[$field] = 'false';
            }
        }

        foreach ($textInputs as $field) {
            if (array_key_exists($field, $new_instance)) {
                $instance[$field] = $new_instance[$field];
            }
        }

        foreach ($selects as $field) {
            if (array_key_exists($field, $new_instance)) {
                if ($new_instance[$field] !== '') {
                    $instance[$field] = $new_instance[$field];

                    if ($field == 'server_id') {
                        // Check if the ID is still valid
                        $server = \Voip\Models\Server::getById($instance[$field]);
                        if (is_null($server)) {
                            //  If invalid default to first of type.
                            $server = \Voip\Models\Server::getFirstOfType();
                            $instance[$field] = $server->postId;
                        }
                    }
                }
            }
        }

        return $instance;
    }

    /**
     * Display the widget edit form.
     * @param mixed $instance Widget instance.
     */
    public function form($instance)
    {
        $count = \Voip\Models\Server::getServerCount();

        if ($count == 1) {
            $s = \Voip\Models\Server::getFirstOfType();
            echo "<input type='hidden' id='{$this->get_field_id('server_id')}' name='{$this->get_field_name('server_id')}' value='{$s->postId}' />";
        }

        // Output admin widget options form
        $checkboxes = [
            //['name' => 'show_map', 'label' => 'Show Map'],
        ];

        $title = array_key_exists('title', $instance) ? $instance['title'] : '';
        $server_id = array_key_exists('server_id', $instance) ? $instance['server_id'] : '';

        echo "<p>Title</p>";
        echo "<p><input type='text' id='{$this->get_field_id('title')}' name='{$this->get_field_name('title')}' value='{$title}' /></p>";

        if ($count > 1) {
            echo "<p>Server</p>";
            $servers = \Voip\Models\Server::getServers();
            echo "<select id='{$this->get_field_id('server_id')}' name='{$this->get_field_name('server_id')}'>";

            // Output the servers as options
            foreach ($servers as $s) {
                if ($server_id == $s->postId) {
                    echo "<option value='{$s->postId}' selected='selected'>{$s->name}</option>";
                } else {
                    echo "<option value='{$s->postId}'>{$s->name}</option>";
                }
            }

            echo '</select>';
        }

        echo "<p>Options</p>";
        foreach ($checkboxes as $checkbox) {
            $name  = $checkbox['name'];
            $label = $checkbox['label'];
            // $show_map = $instance[ 'show_map' ] ? $instance[ 'show_map' ] : 'false';
            $$name = (array_key_exists($name, $instance) ? $instance[$name] : 'false');

            // Output the form
            echo "<p><input type=\"checkbox\"";

            checked($$name, 'true');

            $fieldId = $this->get_field_id($name);
            $fieldName = $this->get_field_name($name);

            echo "id=\"{$fieldId}\"
                name=\"{$fieldName}\"
            /> <label for=\"{$fieldId}\">{$label}</label></p>";
        }
    }
}

/**
 * Register the widget with WordPress.
 */
function voipServerManager_registerVoipWidget()
{
    register_widget('VoipWidget');
}
add_action('widgets_init', 'voipServerManager_registerVoipWidget');
