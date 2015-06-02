<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 02/06/15
 * Time: 08:22
 */

namespace matrix42\slim_api\tests;


use PHPUnit_Framework_TestCase;
use Slim\Environment;
use Slim\Slim;

/**
 * @property \Slim\Http\Request request
 * @property \Slim\Http\Response response
 * @property Slim app
 */
class Route_Test extends PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $this->get('/');
        $this->assertEquals('200', $this->response->status());
    }

    public function get($path, $options = array())
    {
        $this->request('GET', $path, $options);
    }

    public function request($method, $path, $options = array())
    {
        // Capture STDOUT
        ob_start();

        // Prepare a mock environment
        Environment::mock(array_merge(array(
            'REQUEST_METHOD' => $method,
            'PATH_INFO' => $path,
            'SERVER_NAME' => 'mock.matrix42.com',
        ), $options));

        $app = new Slim();
        $this->app = $app;
        $this->request = $app->request();
        $this->response = $app->response();

        // Return STDOUT
        return ob_get_clean();
    }
}
