<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 *
 * @author      jason
 * @copyright   (c) dms_api , Inc
 * @project     dms_api
 * @since       2021/4/15 11:54 AM
 * @version     1.0.0
 *
 */
class MemberTest extends TestCase
{

    protected $uri = "http://www.local.dms.com/";

    protected $tokenStr = "FkUw1pOFkUw1pOBHh7xSI8jWf0X6JuryjWHjhuMapX69FKZSVgBHh7xSI8jWf0X6JuryjWHjhuMapX69FKZSVg";

    public function testSomethingIsTrue()
    {
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testMemberDetail()
    {
        $this->post($this->uri ."member/detail", ['memberId'=> '399'])
            ->seeJson([
                "code" => 10074
            ]);
    }
}