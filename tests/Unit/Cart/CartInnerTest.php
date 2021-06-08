<?php
namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\Cart\StorageInterface;
use App\Lib\Cart\Storage\DbStorage;
use App\Lib\Cart\CartInner;
use App\Models\ManagerModel;
use Illuminate\Support\Facades\Auth;
use App\Models\CartModel;

class CartInnerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     *
     * @return void
     */
    public function testInsertIntoEmptyCart()
    {
        $managerId = 4;
        Auth::setUser(ManagerModel::find($managerId));
        CartModel::where('manager_id', $managerId)->delete();
        /**
         * @var CartInner $target
         */
        $target = app(CartInner::class);
        $target->insertMulti([
            [
                'product_id' => 1,
                'type' => 'normal',
                'quantity' => 10,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
            [
                'product_id' => 4,
                'type' => 'normal',
                'quantity' => 8,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
        ]);
        $contents = $target->contents(true);
        $this->assertEquals(count($contents), 2);
//         dump($contents);
    }

    public function testInsertIntoExistedCart()
    {
        // 测试合并、新增
        $managerId = 4;
        Auth::setUser(ManagerModel::find($managerId));
        CartModel::where('manager_id', $managerId)->delete();
        $existedItems = [
            [
                'product_id' => 1,
                'type' => 'normal',
                'quantity' => 10,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
            [
                'product_id' => 4,
                'type' => 'normal',
                'quantity' => 8,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
        ];
        CartModel::create([
            'manager_id' => $managerId,
            'cart_items' => $existedItems,
        ]);

        /**
         * @var CartInner $target
         */
        $target = app(CartInner::class);
        $target->insertMulti([
            [
                'product_id' => 1,
                'type' => 'normal',
                'quantity' => 10,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
            [
                'product_id' => 3,
                'type' => 'normal',
                'quantity' => 6,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
        ]);
        $target->item = $target->contents();
        $this->assertEquals(count($target->contents()), 3);
        $this->assertEquals($target->item('1_normal')->quantity, 20);
        $this->assertEquals($target->item('3_normal')->quantity, 6);
    }

    public function testRemove()
    {
        $managerId = 4;
        Auth::setUser(ManagerModel::find($managerId));
        CartModel::where('manager_id', $managerId)->delete();
        $existedItems = [
            [
                'product_id' => 1,
                'type' => 'normal',
                'quantity' => 10,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
            [
                'product_id' => 4,
                'type' => 'normal',
                'quantity' => 8,
                'checked' => true,
                'updated_at' => '2021-03-12 12:23:34',
            ],
        ];
        CartModel::create([
            'manager_id' => $managerId,
            'cart_items' => $existedItems,
        ]);

        /**
         * @var CartInner $target
         */
        $target = app(CartInner::class);
        $target->remove('1_normal');
        $target->item = $target->contents();
        $this->assertEquals(count($target->contents()), 1);
        $this->assertNull($target->item('1_normal'));
        $this->assertEquals($target->item('4_normal')->quantity, 8);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
