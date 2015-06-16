<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 01/06/15
 * Time: 10:46
 */

namespace matrix42\slim_api;

use WC_Subscriptions_Product;

class Matrix42_Subscription extends Matrix42_Product
{
    public $recurring_fee;
    public $recurring_interval;
    public $recurring_period;
    public $tags;

    static function get_subscriptions($untyped_array_of_products)
    {
        $typed_array_of_products = array();

        foreach ($untyped_array_of_products as $untyped_product) {
            $wc_product = wc_get_product($untyped_product->ID);

            if ($wc_product->is_type('subscription')) {
                $typed_product = new Matrix42_Subscription();

                $typed_product = Matrix42_Product::assign_product_details($wc_product, $typed_product);

                /*
                 * Subscription
                 */

                $typed_product->signup_fee = WC_Subscriptions_Product::get_sign_up_fee($wc_product->id);
                $typed_product->recurring_fee = WC_Subscriptions_Product::get_price($wc_product->id);
                $typed_product->recurring_interval = WC_Subscriptions_Product::get_interval($wc_product->id);
                $typed_product->recurring_period = WC_Subscriptions_Product::get_period($wc_product->id);

                array_push($typed_array_of_products, $typed_product);
            }

        }
        return $typed_array_of_products;
    }
}