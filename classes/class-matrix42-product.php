<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:06
 */

namespace matrix42\slim_api;


use WC_Subscription_Downloads;

class Matrix42_Product
{
    public $id;
    public $title;
    public $description;
    public $created_at;
    public $updated_at;
    public $type;
    public $status;
    public $permalink;
    public $signup_fee;
    public $sku;
    public $img_featured;
    public $img_screenshots = array();
    public $subscription_ids = array();
    public $categories = array();
    public $downloads = array();

    static function get_products($untyped_array_of_products)
    {
        $typed_array_of_products = array();
        foreach ($untyped_array_of_products as $untyped_product) {
            $wc_product = wc_get_product($untyped_product->ID);
            $post = get_post($untyped_product->ID);

            if (!($wc_product->is_type('subscription'))) {
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

                array_push($typed_array_of_products, $typed_product);
            }

        }
        return $typed_array_of_products;
    }
}