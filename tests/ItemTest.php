<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 3/18/2015
 * Time: 6:17 PM
 */

use Bnet\Cart\Cart;
use Mockery as m;

require_once __DIR__ . '/helpers/SessionMock.php';

class ItemTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Bnet\Cart\Cart
	 */
	protected $cart;

	public function setUp() {
		$events = m::mock('Illuminate\Contracts\Events\Dispatcher');
		$events->shouldReceive('fire');

		$this->cart = new Cart(
			new SessionMock(),
			$events,
			'shopping',
			'SAMPLESESSIONKEY'
		);
	}

	public function tearDown() {
		m::close();
	}

	public function test_item_get_sum_price_using_property() {
		$this->cart->add(455, 'Sample Item', 10099, 2, array());

		$item = $this->cart->get(455);

		$this->assertEquals(455, $item->id, 'Item summed price should be 20198');
		$this->assertEquals(20198, $item->priceSum(), 'Item summed price should be 20198');
	}

	public function test_item_get_sum_price_using_array_style() {
		$this->cart->add(455, 'Sample Item', 10099, 2, array());

		$item = $this->cart->get(455);

		$this->assertEquals(20198, $item->priceSum(), 'Item summed price should be 20198');
	}
}