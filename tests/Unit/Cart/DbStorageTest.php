<?php
namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\Cart\Storage\DbStorage;
use Illuminate\Support\Facades\Auth;
use App\Models\ManagerModel;
use App\Models\CartModel;
use Illuminate\Support\Collection;
use App\Lib\Cart\CartItem;

class DbStorageTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUnauthorizedGetException()
    {
        $this->expectException(\Exception::class);
        $target = new DbStorage();
        $target->get();
    }

    public function testGetForNonExistedCart()
    {
        Auth::setUser(ManagerModel::find(1));
        CartModel::where('manager_id', 1)->delete();

        $target = new DbStorage();
        $actual = $target->get();;
        $this->assertEquals([], $actual);
    }


    public function testSave()
    {
        Auth::setUser(ManagerModel::find(1));
        CartModel::where('manager_id', 1)->delete();

        $target = new DbStorage();
        $item1 = [
            'product_id' => 1,
            'quantity' => 1,
            'checked' => true,
            'type' => 'normal',
            'updated_at' => '2021-03-12 12:23:34',
        ];
        $target->save(new Collection([
            new CartItem('test_cart_item_identifier', $item1),
        ]));
        $savedModel = CartModel::where('manager_id', 1)->first();
        $savedCartItems = $savedModel->cart_items;
        $this->assertEquals($savedCartItems, $savedCartItems);
    }

    public function testGetForExistedCart()
    {
        Auth::setUser(ManagerModel::find(1));
        CartModel::where('manager_id', 1)->delete();
        $item1 = [
            'product_id' => 1,
            'quantity' => 1,
            'checked' => true,
            'type' => 'normal',
            'updated_at' => '2021-03-12 12:23:34',
        ];
        CartModel::create([
            'manager_id' => 1,
            'cart_items' => [
                $item1,
            ],
        ]);

        $target = new DbStorage();
        $actual = $target->get();
        $this->assertEquals([$item1], $actual);
    }
}
