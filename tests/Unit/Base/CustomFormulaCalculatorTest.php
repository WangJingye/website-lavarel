<?php
namespace Tests\Unit\Services\User;

use Tests\TestCase;
use App\Lib\Base\CustomFormulaCalculator;

class CustomFormulaCalculatorTest extends TestCase
{

    /**
     * 
     * @var CustomFormulaCalculator
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
        $this->target = new CustomFormulaCalculator();
    }

    /**
     *
     * @param string $formula
     * @param int $expcted
     *
     * @dataProvider getCeilProvider
     */
    public function testGetCeil($formula, $expected)
    {
        $actual = $this->target->getCeil($formula);
        $this->assertEquals($expected, $actual);
    }

    public function getCeilProvider()
    {
        return [
            ['',  0],
            ['0', 0],
            ['0.0000011', 1],
            ['0.000001', 0],
            ['0.0000009', 0],
            ['1.0000009', 1],
            ['1', 1],
            ['1.2', 2],
            ['7+2.2', 10],
        ];
    }

    /**
     *
     * @param string $formula
     * @param int $expcted
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($formula, $expected)
    {
        $actual = $this->target->getValue($formula);
        $this->assertEquals($expected, $actual);
    }

    public function getValueProvider()
    {
        return [
            ['', 0],
            ['23565', 1],
            ['0.00001', 1],
            ['0', 0.5],
            ['0.0000011', 1],
            ['0.000001', 0.5],
            ['-0.000001', 0.5],
            ['-0.0000011', 0],
            ['-2255', 0],
            ['-0.002', 0],
        ];
    }

    /**
     * 
     * @param string $formula
     * @param array  $replaceVariables
     * @param float  $expected
     * 
     * @dataProvider getFormulaProvider
     */
    public function testCalculate($formula, $replaceVariables = [], $expected)
    {
        $actual = $this->target->calculate($formula, $replaceVariables);
        $this->assertEquals($expected, $actual);
    }

    public function getFormulaProvider()
    {
        return [
            [
                '0',
                [],
                0,
            ],
            [
               '{{1000-p}-0.6}*80+{{p-1000}-0.1}*p*0.08',
               [
                   'p' => 800,
               ],
               80
            ],
            [
                '{{1000-p}-0.6}*80+{{p-1000}-0.1}*p*0.08',
                [
                    'p' => 2000,
                ],
                160
            ],
            [
                '{{200-p}-0.6}*(15+[(w-1000)/500]*5)',
                [
                    'p' => 200,
                    'w' => 1000,
                ],
                0,
            ],
            [
                '{{200-p}-0.6}*(15+[(w-1000)/500]*5)',
                [
                    'p' => 30,
                    'w' => 1000,
                ],
                15,
            ],
            [
                '{{200-p}-0.6}*(15+[(w-1000)/500]*5)',
                [
                    'p' => 30,
                    'w' => 1800,
                ],
                25,
            ],
            // 2公斤以下的包裹按照：首重500克 算10元，每续500克 按3元计算
            // 大于等于2公斤，小于5公斤的包裹按照：每1公斤6元；（如重5公斤＝6×5元＝￥30元）
            // 大于等于5公斤，小于10公斤的包裹按照：每1公斤5元计算；（如重10公斤＝5×10元＝50元）
            // 大于等于10公斤及以上的包裹按照：每1公斤4元计算；（如重20公斤＝4×20元＝80元）
            '2公斤以下的包裹按照：首重500克 算10元' => [
                '{{w}-0.1}*{{2000-w}-0.6}*(10+[(w-500)/500]*3)+{{w-2000}-0.1}*{{5000-w}-0.6}*[w/1000]*6+{{w-5000}-0.1}*{{10000-w}-0.6}*[w/1000]*5+{{w-10000}-0.1}*[w/1000]*4',
                [
                    'w' => 300,
                ],
                10,
            ],
            '2公斤以下的包裹按照：首重500克 算10元，每续500克 按3元计算' => [
                '{{w}-0.1}*{{2000-w}-0.6}*(10+[(w-500)/500]*3)+{{w-2000}-0.1}*{{5000-w}-0.6}*[w/1000]*6+{{w-5000}-0.1}*{{10000-w}-0.6}*[w/1000]*5+{{w-10000}-0.1}*[w/1000]*4',
                [
                    'w' => 1300,
                ],
                16,
            ],
            '大于等于2公斤，小于5公斤的包裹按照：每1公斤6元' => [
                '{{w}-0.1}*{{2000-w}-0.6}*(10+[(w-500)/500]*3)+{{w-2000}-0.1}*{{5000-w}-0.6}*[w/1000]*6+{{w-5000}-0.1}*{{10000-w}-0.6}*[w/1000]*5+{{w-10000}-0.1}*[w/1000]*4',
                [
                    'w' => 2300,
                ],
                18,
            ],
            '大于等于5公斤，小于10公斤的包裹按照：每1公斤5元计算' => [
                '{{w}-0.1}*{{2000-w}-0.6}*(10+[(w-500)/500]*3)+{{w-2000}-0.1}*{{5000-w}-0.6}*[w/1000]*6+{{w-5000}-0.1}*{{10000-w}-0.6}*[w/1000]*5+{{w-10000}-0.1}*[w/1000]*4',
                [
                    'w' => 6000,
                ],
                30,
            ],
            '10公斤以上直接按照每公斤4' => [
                '{{w}-0.1}*{{2000-w}-0.6}*(10+[(w-500)/500]*3)+{{w-2000}-0.1}*{{5000-w}-0.6}*[w/1000]*6+{{w-5000}-0.1}*{{10000-w}-0.6}*[w/1000]*5+{{w-10000}-0.1}*[w/1000]*4',
                [
                    'w' => 20000,
                ],
                80,
            ],
            '这是一个从线上抄来的公式' => [
                '[((8.3+0.83*([w/9.999-10]))+25.1)/h/0.98/0.97*d*10]/10*{1999.999-w}+{w-1999.999}*9999',
                [
                    'w' => 10,
                    'h' => 1,
                    'd' => 1,
                ],
                35.2,
            ],
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
