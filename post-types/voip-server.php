<?php

// Create the VoIP Server post type.
function voipServerManager_registerVoipServerPostType() {
    $args = array(
        'public' => true,
	'menu_position' => 20,
	'supports' => array(
	     'title',
	),
        'labels' => array(
            'name'          => 'VoIP Servers',
            'name_singular' => 'VoIP Server',
        ),
        'register_meta_box_cb' => 'voipServerManager_addMetaBoxes'
    );
    register_post_type( 'voip-server', $args );
}
add_action( 'init', 'voipServerManager_registerVoipServerPostType' );

// Add meta box for VoIP Server Details.
function voipServerManager_addMetaBoxes() {
    add_meta_box ( 'voip-server-details-meta', 'Server Details', 'voipServerManager_ServerDetailsMeta', 'voip-server', 'normal', 'high' );
    add_meta_box ( 'voip-widget-preview-meta', 'Widget Preview', 'voipServerManager_widgetPreviewMeta', 'voip-server', 'normal', 'low' );
}
add_action( 'add_meta_boxes', 'voipServerManager_addMetaBoxes' );

// Server Details Meta Box.
function voipServerManager_ServerDetailsMeta() {

    // Server Type.
    echo "<p><label>Server Type:</label> <select><option>TeamSpeak 3</option></select></p>";

    // Server Hostname - Default: Localhost
    echo "<p><label>Host:</label> <input type='text' /></p>";

    // Server Password
    echo "<p><label>Password:</label> <input type='password' /></p>";
}

// Widget Preview Meta Box
function voipServerManager_widgetPreviewMeta() {

    echo '<p>This is an example of how this server would appear in the voip widget</p>';
}
