<?php

function voipServerManager_registerVoipServerPostType() {
    $args = array(
        'public' => true,
	'menu_position' => 20,
        'labels' => array(
            'name'          => 'VoIP Servers',
            'name_singular' => 'VoIP Servers'
      )
    );
    register_post_type( 'voip-server', $args );
}
add_action( 'init', 'voipServerManager_registerVoipServerPostType' );