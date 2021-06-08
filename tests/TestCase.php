<?php
namespace Tests;

/**
 *
 * @author      jason
 * @copyright   (c) dms_api , Inc
 * @project     dms_api
 * @since       2021/4/15 12:02 PM
 * @version     1.0.0
 *
 */
class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
