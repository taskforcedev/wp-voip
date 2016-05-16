<?php namespace Voip\Models;
/**
 * File: models/server.php
 *
 * Model for VoIP Server post type to provide centralised methods of retrieving and manipulating
 * post data.
 *
 * @author PrivateSniper <privatesniper@gmail.com>
 */

use \WP_Query;

/**
 * Class Server
 * @package Voip\Models
 */
class Server
{
    /**
     * @var string $name Server name.
     */
    public $name;
    /**
     * @var int $postId Post ID.
     */
    public $postId;
    /**
     * @var array $meta Server metadata.
     */
    public $meta;

    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set the servers name.
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the servers metadata.
     * @param $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    public static function getServerCount()
    {
        $args = [ 'post_type' => 'voip-server' ];

        $servers = new WP_Query($args);
        $count = 0;
        if ($servers->have_posts()) {
            while ($servers->have_posts()) {
                $servers->the_post();
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the first server from the database.
     * @return bool|Server|object
     */
    public static function getFirstOfType()
    {
        $args = [ 'post_type' => 'voip-server' ];

        $servers = new WP_Query($args);

        // The Loop
        if ($servers->have_posts()) {
            while ($servers->have_posts()) {
                $servers->the_post();
                $meta = get_post_meta(get_the_ID());

                $server = new Server();
                $server->postId = get_the_ID();
                $server->setName(get_the_title());
                $server->setMeta($meta);

                return $server;
            }
        }
        return false;
    }

    public static function getServers()
    {
        $servers = [];

        $args = [ 'post_type' => 'voip-server' ];

        $q = new WP_Query($args);
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $server = Server::getById(get_the_ID());
                $servers[] = $server;
            }
        }
        return $servers;
    }

    /**
     * Get the required fields from server metadata.
     * @param $fields
     *
     * @return array
     */
    private function getFieldsFromMeta($fields)
    {
        $meta = $this->meta;
        $data = [];

        foreach ($fields as $field => $name) {
            if (array_key_exists($field, $meta)) {
                $data[$name] = $meta[$field][0];
            } else {
                $data[$name] = '';
            }
        }

        return $data;
    }

    /**
     * Retrieve a server by it's post id.
     *
     * @param object|int $post Post id or post object.
     *
     * @return Server
     */
    public static function getById($post)
    {
        $post = get_post($post);
        if (is_null($post)) {
            return null;
        }

        $server = new Server();
        $server->postId = $post->ID;
        $server->setName($post->post_title);
        $server->setMeta(get_post_meta($post->ID));

        return $server;
    }
}
