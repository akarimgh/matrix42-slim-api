<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 02/06/15
 * Time: 09:01
 */

namespace matrix42\slim_api;


class Matrix42_Order
{
    public $id;
    public $created_at;
    public $updated_at;
    public $completed_at;
    public $status;
    public $customer;
    public $order_url;
    public $order_items;

    static function get_orders($untyped_array_of_orders)
    {
        $typed_array_of_products = array();
        foreach ($untyped_array_of_orders as $untyped_order) {
            $wc_order = wc_get_order($untyped_order->ID);

            $typed_order = new Matrix42_Order();
            $typed_order->id = $wc_order->id;
            $typed_order->created_at = $wc_order->order_date;
            $typed_order->updated_at = $wc_order->modified_date;
            $typed_order->completed_at = $wc_order->completed_date;
            $typed_order->status = $wc_order->get_status();

            $customer = get_user_by('id', $wc_order->customer_user);
            $typed_order->customer = array(
                'id' => $customer->ID,
                'email' => $customer->user_email,
                'first_name' => $customer->user_firstname,
                'last_name' => $customer->user_lastname,
                'login_name' => $customer->user_login
            );
            $typed_order->order_url = $wc_order->get_view_order_url();

            foreach ($wc_order->get_items() as $item_id => $item) {

                $product = $wc_order->get_product_from_item($item);

                $typed_order->order_items[] = array(
                    'id' => $item_id,
                    'price' => wc_format_decimal($wc_order->get_item_total($item), 2),
                    'quantity' => (int)$item['qty'],
                    'name' => $item['name'],
                    'product_id' => (isset($product->variation_id)) ? $product->variation_id : $product->id,
                    'sku' => is_object($product) ? $product->get_sku() : null,
                );
            }

            array_push($typed_array_of_products, $typed_order);
        }
        return $typed_array_of_products;
    }

    public static function get_order_downloads($order)
    {
        $downloads = array();

        $order_items = $order['order_items'];

        foreach($order_items as $order_item) {
            $product = wc_get_product($order_item->id);
            array_push($downloads, $product->get_files());
        }

        return $downloads;
    }
}