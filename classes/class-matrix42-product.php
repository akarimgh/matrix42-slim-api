<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:06
 */

namespace matrix42\slim_api;


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
    public $sku;
    public $price;
    public $related_ids = array();
    public $categories = array();
    public $img_featured = array();
    public $img_screenshots = array();
    public $downloads = array();
}