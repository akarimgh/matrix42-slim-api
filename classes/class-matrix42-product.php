<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:06
 */

namespace matrix42\slim_api;

use WC_Subscription_Downloads;
use WP_Query;

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
    public $platform = array();
    public $version = array();
    public $service_store_compatibility = array();
    public $empirum_compatibility;
    public $software_vendor;
    public $os;
    public $vendor;
    public $tags;

    static function get_product($product_id)
    {
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
        $results = self::get_products($query_posts);
        
        return count($results) > 0 ? $results[0] : $results;
    }

    static function get_products($untyped_array_of_products)
    {
        $typed_array_of_products = array();
        foreach ($untyped_array_of_products as $untyped_product) {
            $wc_product = wc_get_product($untyped_product->ID);

            if (!($wc_product->is_type('subscription'))) {
                $typed_product = new Matrix42_Product();

                $typed_product = self::assign_product_details($wc_product, $typed_product);

                array_push($typed_array_of_products, $typed_product);
            }

        }
        return $typed_array_of_products;
    }

    /**
     * @param $product_id
     * @param $downloads
     * @return $files (all product downloads)
     */
    static function post_downloads($product_id, $downloads)
    {
        // $filename should be the path to a file in the upload directory.
        // example $filename = 'woocommerce_uploads/2015/07/aka_Matrix42_Tool_CleanDatabase_7.2.1.20150625.aka.zip';        
        $downloads = json_decode($downloads);

        // Get all existing post downloads and add the new passed downloads
        $files = array();
        $result_all_files = array();
        $wc_product = wc_get_product($product_id);
        $untyped_array_of_downloads =  $wc_product->get_files();        
        foreach ($untyped_array_of_downloads as $download_key => $download_value) { 
            // Add existing download to the $files array
            $existing_download = (object)$download_value;

            $files[md5($existing_download->file)] = array( 
               'name' => $existing_download->name, 
               'file' => $existing_download->file
            );
        }

        foreach ($downloads as $key => $new_download) {
            // Add the new downloads to the existing post downloads 
            $files[md5($new_download->file)] = array( 
               'name' => $new_download->name, 
               'file' => $new_download->file 
            );
        }        

        // Update product post meta (_downloadable_files) 
        update_post_meta($product_id, '_downloadable_files', $files);

        // Collect downloads just for function result
        foreach ($files as $key => $download) {
            array_push($result_all_files, $download);
        }

        return $result_all_files;
    }    

    /**
     * @param $wc_product
     * @param $typed_product
     * @return Matrix42_Product $typed_product
     */
    public static function assign_product_details($wc_product, $typed_product)
    {
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
        foreach ($img_screenshots_ids as $img_screenshot_id) {
            array_push($typed_product->img_screenshots, wp_get_attachment_url($img_screenshot_id));
        }

        $typed_product->subscription_ids = WC_Subscription_Downloads::get_subscriptions($wc_product->id);

        $category_string = $wc_product->get_categories(',__,');
        $typed_product->categories = explode(',__,', $category_string);

        // Downloads
        // Use directly the download object without the object name with a Guid
        // Required for creation of the .net class from json
        $typed_array_of_downloads = array();
        $untyped_array_of_downloads =  $wc_product->get_files();
        foreach ($untyped_array_of_downloads as $download) {
            array_push($typed_array_of_downloads, $download);
        }
        $typed_product->downloads = $typed_array_of_downloads;
        
        $typed_product->platform = explode(', ', $wc_product->get_attribute('platform'));
        $typed_product->version = explode(', ', $wc_product->get_attribute('version'));
        $typed_product->service_store_compatibility = explode(', ', $wc_product->get_attribute('service-store-compatibility'));
        $typed_product->empirum_compatibility = explode(', ', $wc_product->get_attribute('empirum-compatibility'));
        $typed_product->software_vendor = explode(', ', $wc_product->get_attribute('software-vendor'));
        $typed_product->os = explode(', ', $wc_product->get_attribute('os'));

        $typed_product->vendor = get_product_vendors($wc_product->id);
        foreach($typed_product->vendor as $vendor) {
            unset($vendor->slug);
            unset($vendor->description);
            unset($vendor->paypal_email);
            unset($vendor->admin);
            unset($vendor->admins);
            unset($vendor->image_hash);
            unset($vendor->image);
            unset($vendor->phone);
            unset($vendor->fax);
        }
        $typed_product->tags = array_filter(explode(',__,', $wc_product->get_tags(',__,', '', ''  )));

        return $typed_product;
    }
}