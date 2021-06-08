<?php
namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ManagerModel;
use Illuminate\Support\Facades\Auth;
use App\Models\CartModel;
use App\Lib\Cart\CartService;

class CartServiceTest extends TestCase
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
    public function testConvert()
    {
        $managerId = 4;
        Auth::setUser(ManagerModel::find($managerId));
        CartModel::where('manager_id', $managerId)->delete();
        /**
         * @var CartService $target
         */
        $target = app(CartService::class);
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
        $contents = $target->contentsConvert(true);
        $this->assertArrayHasKey('products', $contents);
        $this->assertArrayHasKey('total', $contents);
    }

    /**
     *
     * @return void
     */
    public function testGetFormatedContents()
    {
        $managerId = 4;
        CartModel::where('manager_id', $managerId)->delete();
        CartModel::create([
            'manager_id' => $managerId,
            'cart_items' => [
                [
                    'product_id' => 1,
                    'quantity' => 5,
                    'checked' => true,
                    'type' => 'normal',
                    'updated_at' => '2021-03-12 12:23:34',
                ],
                [
                    'product_id' => 3,
                    'quantity' => 2,
                    'checked' => true,
                    'type' => 'normal',
                    'updated_at' => '2021-03-10 12:23:34',
                ],
                [
                    'product_id' => 4,
                    'quantity' => 4,
                    'checked' => true,
                    'type' => 'normal',
                    'updated_at' => '2021-03-11 12:23:34',
                ],
                [
                    'product_id' => 5,
                    'quantity' => 3,
                    'checked' => true,
                    'type' => 'true',
                    'updated_at' => '2021-03-17 12:23:34',
                ],
                [
                    'product_id' => 7,
                    'quantity' => 12,
                    'checked' => true,
                    'type' => 'normal',
                    'updated_at' => '2021-03-19 12:23:34',
                ],
                [
                    'product_id' => 8,
                    'quantity' => 10,
                    'checked' => false,
                    'type' => 'normal',
                    'updated_at' => '2021-03-16 12:23:34',
                ],
            ],
        ]);

        Auth::setUser(ManagerModel::find($managerId));
        /**
         * @var CartService $target
         */
        $target = app(CartService::class);
        $contents = $target->getFormatedContents();
//         dump($contents);
        $this->assertArrayHasKey('items', $contents);
        $this->assertArrayHasKey('total', $contents);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
