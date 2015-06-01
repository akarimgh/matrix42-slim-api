<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 01/06/15
 * Time: 10:46
 */

namespace matrix42\slim_api;


class Matrix42_Subscription extends Matrix42_Product
{
    public $signup_fee;
    public $recurring_fee;
    public $recurring_interval;
    public $recurring_interval_type;

    static function get_subscriptions($untyped_array_of_products)
    {
        $typed_array_of_products = array();

        foreach ($untyped_array_of_products as $untyped_product) {
            $wc_product = wc_get_product($untyped_product->ID);

            if ($wc_product->is_type('subscription')) {
                $typed_product = new Matrix42_Product();

                $typed_product->id = $wc_product->id;
                $typed_product->title = $wc_product->get_title();
                $typed_product->description = $wc_product->post->post_content;
                $typed_product->created_at = $wc_product->post->post_date;
                $typed_product->updated_at = $wc_product->post->post_modified;
                $typed_product->type = $wc_product->product_type;
                $typed_product->status = $wc_product->post->post_status;
                $typed_product->permalink = $wc_product->get_permalink();
                $typed_product->sku = $wc_product->get_sku();
                $typed_product->signup_fee = $wc_product->get_price();
                $typed_product->img_featured = $untyped_product->id;
                $typed_product->img_screenshots = $untyped_product->id;
                $typed_product->related_ids = $untyped_product->id;
                $typed_product->categories = $untyped_product->id;
                $typed_product->downloads = $untyped_product->id;

                array_push($typed_array_of_products, $typed_product);
            }

        }
        return $typed_array_of_products;
    }
}