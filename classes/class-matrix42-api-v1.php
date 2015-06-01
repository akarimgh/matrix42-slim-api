<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:09
 */

namespace matrix42\slim_api;

use WC_Product;
use WP_Query;

require(ABSPATH . "/wp-config.php");

class Matrix42_API_v1
{
    function __construct()
    {
        add_action('get_all_products', array($this, 'get_all_products'));
    }

    function get_all_products($slim)
    {
        $context = $this;
        $slim->get('/slim/api/v1/products', function () use ($context) {

            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'product'
            );

            $query = new WP_Query($args);
            $result = $query->get_posts();
            $products = array();
            foreach ($result as $product) {
                $wcproduct = new WC_Product($product->ID);
                $mx_product = new Matrix42_Product();

                $mx_product->id = $wcproduct->id;
                $mx_product->title = $wcproduct->get_title();

                array_push($products, $mx_product);
            }
            echo json_encode($products);
        });
    }
}