<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:09
 */

namespace matrix42\slim_api;

use Slim\Slim;

class Matrix42_API_v1
{
    function __construct()
    {
        add_action('get_products', array($this, 'get_products'));
    }

    function get_products($slim)
    {
        echo "TTT";
        $context = $this;
        $slim->get('/slim/api/v1/products', function () use ($context) {
            echo "TETETET";
        });
    }
}