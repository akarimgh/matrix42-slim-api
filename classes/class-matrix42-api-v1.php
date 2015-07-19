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

        add_action('post_product_downloads', array($this, 'post_product_downloads'));
        add_action('post_subscription_downloads', array($this, 'post_subscription_downloads'));

        add_action('get_attachments', array($this, 'get_attachments'));
        add_action('post_attachment', array($this, 'post_attachment'));         
    }

    /*
     * post_product_downloads
     * Update product downloads: update post meta '_downloadable_files_'
     */
    function post_product_downloads($slim)
    {
        $context = $this;
        $slim->post('/slim/api/v1/products/:product_id/downloads', function ($product_id) use ($context, $slim) {

            $allPostVars = $slim->request()->post();
            $allGetVars = $slim->request()->get();

            //$product_id = $allGetVars['product_id'];
            
            $datei = fopen(@"E:\MarketplaceDev\wc-logs\Log.txt","w");
            //$downloads = $allPostVars['data'];
            foreach ($allPostVars as $key => $value) {               
                $downloads = $allPostVars[$key];                             
                fwrite($datei, "$downloads" . "   prodid= " . "$product_id",100000); 
                fwrite($datei, PHP_EOL);        
            }

            $all_product_downloads = Matrix42_Product::post_downloads($product_id, $downloads);
            
            // Set status to success
            $slim->response->setStatus(200);

            fwrite($datei, var_dump("$all_product_downloads"));
            fclose($datei);

            echo json_encode($all_product_downloads);
        });
    }

    /*
     * post_subscription_downloads
     * Update subscription downloads: update post meta '_downloadable_files_'
     */
    function post_subscription_downloads($slim)
    {
        $context = $this;
        $slim->post('/slim/api/v1/subscriptions/:subscription_id/downloads', function ($subscription_id) use ($context, $slim) {

            $allPostVars = $slim->request()->post();
            $allGetVars = $slim->request()->get();

            //$product_id = $allGetVars['product_id'];
            
            $datei = fopen(@"E:\MarketplaceDev\wc-logs\Log.txt","w");
            //$downloads = $allPostVars['data'];
            foreach ($allPostVars as $key => $value) {               
                $downloads = $allPostVars[$key];                             
                fwrite($datei, "$downloads" . "   subscription_id= " . "$subscription_id",100000); 
                fwrite($datei, PHP_EOL);        
            }

            $all_subscription_downloads = Matrix42_Product::post_downloads($subscription_id, $downloads);
            
            // Set status to success
            $slim->response->setStatus(200);

            fwrite($datei, var_dump("$all_subscription_downloads"));
            fclose($datei);

            echo json_encode($all_subscription_downloads);
        });
    }

    function get_attachments($slim)
    {
         $context = $this;
        $slim->get('/slim/api/v1/attachments', function () use ($context) {

        $results = array('Name' => 'Abderrahim Karim', 'Function' => 'get_attachments');

            /*
             * return the result as json
             */
            echo json_encode($results);
        });
    }

    function post_attachment($slim)
    {
        $context = $this;

        $slim->post('/slim/api/v1/attachments', function () use ($context, $slim) 
        {
            //$slim = \Slim\Slim::getInstance();

            $allPostVars = $slim->request()->post();
            $allGetVars = $slim->request()->get();

            $product_id = $allGetVars['product_id'];
            
            $datei = fopen(@"E:\MarketplaceDev\wc-logs\Log.txt","w");
            //$download = $allPostVars['data'];
            foreach ($allPostVars as $key => $value) {               
                $download = $allPostVars[$key];                             
                fwrite($datei, "var_dump($download)",100000);           
            }

            fclose($datei); 
            //1085
            $attach_id = Matrix42_Attachment::post_attachment($download, $product_id);   

            // Set status to success
            $slim->response->setStatus(200); 
            //$slim->response()->setStatus(404); Error
            //$slim->response()->headers->set('Content-Type', 'application/json');

            //$download = json_decode($download);
            //echo var_dump($download->name);     
            /*
             * return the result as json
             */
            echo $attach_id;// json_encode($download);            
        });
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
            $result = Matrix42_Product::get_product($product_id);

            /*
             * return the result as json
             */
            echo json_encode($result);
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
            // $args = array(
            //     'post_type' => 'product',
            //     'p' => $subscription_id
            // );
            // $query_results = new WP_Query($args);
            // $query_posts = $query_results->get_posts();

            // /*
            //  * create a new array of subscription out of the query results
            //  */
            $results = Matrix42_Subscription::get_subscription($subscription_id);

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


