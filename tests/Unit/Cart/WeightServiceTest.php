<?php
namespace Tests\Unit\Services\User;

use Tests\TestCase;
use App\Lib\Cart\WeightService;

class WeightServiceTest extends TestCase
{

    /**
     * 
     * @var WeightService
     */
    private $target;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->target = new WeightService();
    }

    /**
     *
     * @param int $companyId
     * @param int $normalWeight
     * @param int $foamWeight
     * @param int $expcted
     *
     * @dataProvider getGrossWeightProvider
     */
    public function testGetGrossWeight($companyId, $normalWeight, $foamWeight, $expected)
    {
        $actual = $this->target->getGrossWeight($companyId, $normalWeight, $foamWeight);
        $this->assertEquals($expected, $actual);
    }

    public function getGrossWeightProvider()
    {
        return [
            // 经销商id，非泡货重，泡货重，毛重
            [1, 100, 200, 500],
            [999, 100, 300, 400], // 不存在，直接是 非泡货+泡货
        ];
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
