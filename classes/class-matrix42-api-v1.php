<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:09
 */

namespace matrix42\slim_api;

use WP_Query;

class Matrix42_API_v1
{
    function __construct()
    {
        add_action('get_products', array($this, 'get_products'));
        add_action('get_product', array($this, 'get_product'));
        add_action('get_subscriptions', array($this, 'get_subscriptions'));
        add_action('get_subscription', array($this, 'get_subscription'));
        add_action('get_orders', array($this, 'get_orders'));
        add_action('get_order', array($this, 'get_order'));
        add_action('get_order_downloads', array($this, 'get_order_downloads'));
    }

    function get_products($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/products', function () use ($context) {

            /*
             * Get all posts of type product
             */
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1
            );
            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of products out of the query results
             */
            $results = Matrix42_Product::get_products($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_product($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/products/:product_id', function ($product_id) use ($context) {

            /*
             * Get one specific posts of type product
             */
            $args = array(
                'post_type' => 'product',
                'p' => $product_id
            );
            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of products out of the query results
             */
            $results = Matrix42_Product::get_products($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_subscriptions($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/subscriptions', function () use ($context) {

            /*
             * Get all posts of type subscription
             */
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1
            );
            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of subscription out of the query results
             */
            $results = Matrix42_Subscription::get_subscriptions($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_subscription($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/subscriptions/:subscription_id', function ($subscription_id) use ($context) {

            /*
             * Get one specific posts of type subscription
             */
            $args = array(
                'post_type' => 'product',
                'p' => $subscription_id
            );
            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of subscription out of the query results
             */
            $results = Matrix42_Subscription::get_subscriptions($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_orders($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/orders', function () use ($context) {
            /*
             * Get all posts of type order
             */
            $args = array(
                'post_type' => 'shop_order',
                'post_status' => array_keys(wc_get_order_statuses()),
                'posts_per_page' => -1
            );

            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of orders out of the query results
             */
            $results = Matrix42_Order::get_orders($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_order($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/orders/:order_id', function ($order_id) use ($context) {
            /*
             * Get a specific posts of type order
             */
            $args = array(
                'post_type' => 'shop_order',
                'post_status' => array_keys(wc_get_order_statuses()),
                'posts_per_page' => -1,
                'p' => $order_id
            );

            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            /*
             * create a new array of orders out of the query results
             */
            $results = Matrix42_Order::get_orders($query_posts);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function get_order_downloads($slim) {
        $context = $this;
        $slim->get('/slim/api/v1/orders/:order_id/downloads', function ($order_id) use ($context) {
            /*
             * Get a specific posts of type order
             */
            $args = array(
                'post_type' => 'shop_order',
                'post_status' => array_keys(wc_get_order_statuses()),
                'posts_per_page' => -1,
                'p' => $order_id
            );

            $query_results = new WP_Query($args);
            $query_posts = $query_results->get_posts();

            $order = Matrix42_Order::get_orders($query_posts);

            /*
             * create a new array of orders out of the query results
             */
            $results = Matrix42_Order::get_order_downloads($order);

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }
}


