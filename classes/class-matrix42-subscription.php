<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 01/06/15
 * Time: 10:46
 */

namespace matrix42\slim_api;


use WC_Subscription_Downloads;
use WC_Subscriptions_Order;
use WC_Subscriptions_Product;

class Matrix42_Subscription extends Matrix42_Product
{
    public $recurring_fee;
    public $recurring_interval;
    public $recurring_period;

    static function get_subscriptions($untyped_array_of_products)
    {
        $typed_array_of_products = array();

        foreach ($untyped_array_of_products as $untyped_product) {
            $wc_product = wc_get_product($untyped_product->ID);

            if ($wc_product->is_type('subscription')) {
                $typed_product = new Matrix42_Subscription();

                /*
                 * Product
                 */

                $typed_product->id = $wc_product->id;
                $typed_product->title = $wc_product->get_title();
                $typed_product->description = $wc_product->post->post_content;
                $typed_product->created_at = $wc_product->post->post_date;
                $typed_product->updated_at = $wc_product->post->post_modified;
                $typed_product->type = $wc_product->product_type;
                $typed_product->status = $wc_product->post->post_status;
                $typed_product->permalink = $wc_product->get_permalink();
                $typed_product->sku = $wc_product->get_sku();
                $typed_product->img_featured = wp_get_attachment_url(get_post_thumbnail_id($wc_product->id));

                $typed_product->img_screenshots = array();
                $img_screenshots_ids = $wc_product->get_gallery_attachment_ids();
                foreach($img_screenshots_ids as $img_screenshot_id) {
                    array_push($typed_product->img_screenshots, wp_get_attachment_url($img_screenshot_id));
                }

                $typed_product->subscription_ids = WC_Subscription_Downloads::get_subscriptions($wc_product->id);
                $category_string = $wc_product->get_categories(',__,');
                $typed_product->categories = explode(',__,', $category_string);
                $typed_product->downloads = $wc_product->get_files();


                /*
                 * Subscription
                 */

                $typed_product->signup_fee =  WC_Subscriptions_Product::get_sign_up_fee($wc_product->id);
                $typed_product->recurring_fee = WC_Subscriptions_Product::get_price($wc_product->id);
                $typed_product->recurring_interval = WC_Subscriptions_Product::get_interval($wc_product->id);
                $typed_product->recurring_period = WC_Subscriptions_Product::get_period($wc_product->id);

                array_push($typed_array_of_products, $typed_product);
            }

        }
        return $typed_array_of_products;
    }
}