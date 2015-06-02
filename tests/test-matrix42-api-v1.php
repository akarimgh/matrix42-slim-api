<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:10
 */

namespace matrix42\slim_api\tests;

use PHPUnit_Framework_TestCase;

class Matrix42_API_v1_Test extends PHPUnit_Framework_TestCase {
    public function test_get_products() {
        $api = new Route_Test();
        $result = $api->request('GET', '/products', '');
        $this->assertEquals(200, $result);
    }
}
