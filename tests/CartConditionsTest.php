<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 1/12/2015
 * Time: 9:59 PM
 */

use Bnet\Cart\Cart;
use Bnet\Cart\Condition;
use Mockery as m;

require_once __DIR__ . '/helpers/SessionMock.php';

class CartConditionTest extends PHPUnit_Framework_TestCase {

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

	public function test_total_without_condition() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// no changes in subtotal as the condition's target added was for total
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be the same as subtotal
		$this->assertEquals(18749, $this->cart->total(), 'Cart should have a total of 18749');
	}

	protected function fillCart() {
		$items = array(
			array(
				'id' => 456,
				'name' => 'Sample Item 1',
				'price' => 6799,
				'quantity' => 1,
				'attributes' => array()
			),
			array(
				'id' => 568,
				'name' => 'Sample Item 2',
				'price' => 6925,
				'quantity' => 1,
				'attributes' => array()
			),
			array(
				'id' => 856,
				'name' => 'Sample Item 3',
				'price' => 5025,
				'quantity' => 1,
				'attributes' => array()
			),
		);

		$this->cart->add($items);
	}

	public function test_total_with_condition() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition = new Condition(array(
			'name' => 'VAT 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '12.5%',
		));

		$this->cart->condition($condition);

		// no changes in subtotal as the condition's target added was for total
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(21093, $this->cart->total(), 'Cart should have a total of 21093');
	}

	public function test_total_with_multiple_conditions_added_scenario_one() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition1 = new Condition(array(
			'name' => 'VAT 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '12.5%',
		));
		$condition2 = new Condition(array(
			'name' => 'Express Shipping $15',
			'type' => 'shipping',
			'target' => 'cart',
			'value' => '+1500',
		));

		$this->cart->condition($condition1);
		$this->cart->condition($condition2);

		// no changes in subtotal as the condition's target added was for subtotal
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(22593, $this->cart->total(), 'Cart should have a total of 22593');
	}

	public function test_total_with_multiple_conditions_added_scenario_two() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition1 = new Condition(array(
			'name' => 'VAT 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '12.5%',
		));
		$condition2 = new Condition(array(
			'name' => 'Express Shipping $15',
			'type' => 'shipping',
			'target' => 'cart',
			'value' => '-1500',
		));

		$this->cart->condition($condition1);
		$this->cart->condition($condition2);

		// no changes in subtotal as the condition's target added was for subtotal
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(19593, $this->cart->total(), 'Cart should have a total of 19593');
	}

	public function test_total_with_multiple_conditions_added_scenario_three() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition1 = new Condition(array(
			'name' => 'VAT 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '-12.5%',
		));
		$condition2 = new Condition(array(
			'name' => 'Express Shipping $15',
			'type' => 'shipping',
			'target' => 'cart',
			'value' => '-1500',
		));

		$this->cart->condition($condition1);
		$this->cart->condition($condition2);

		// no changes in subtotal as the condition's target added was for total
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(14905, $this->cart->total(), 'Cart should have a total of 14905');
	}

	public function test_cart_multiple_conditions_can_be_added_once_by_array() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition1 = new Condition(array(
			'name' => 'VAT 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '-12.5%',
		));
		$condition2 = new Condition(array(
			'name' => 'Express Shipping $15',
			'type' => 'shipping',
			'target' => 'cart',
			'value' => '-1500',
		));

		$this->cart->condition([$condition1, $condition2]);

		// no changes in subtotal as the condition's target added was for total
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(14905, $this->cart->total(), 'Cart should have a total of 14905');
	}

	public function test_total_with_multiple_conditions_added_scenario_four() {
		$this->fillCart();

		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// add condition
		$condition1 = new Condition(array(
			'name' => 'COUPON LESS 12.5%',
			'type' => 'tax',
			'target' => 'cart',
			'value' => '-12.5%',
		));
		$condition2 = new Condition(array(
			'name' => 'Express Shipping $15',
			'type' => 'shipping',
			'target' => 'cart',
			'value' => '+1500',
		));

		$this->cart->condition($condition1);
		$this->cart->condition($condition2);

		// no changes in subtotal as the condition's target added was for total
		$this->assertEquals(18749, $this->cart->subTotal(), 'Cart should have sub total of 18749');

		// total should be changed
		$this->assertEquals(17905, $this->cart->total(), 'Cart should have a total of 17905');
	}

	public function test_add_item_with_condition() {
		$condition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'tax',
			'value' => '-5%',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 100,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => $condition1
		);

		$this->cart->add($item);

		$this->assertEquals(95, $this->cart->get(456)->priceSumWithConditions());
		$this->assertEquals(95, $this->cart->subTotal());
	}

	public function test_add_item_with_multiple_item_conditions_in_multiple_condition_instance() {
		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'item',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		));
		$itemCondition3 = new Condition(array(
			'name' => 'MISC',
			'type' => 'misc',
			'target' => 'item',
			'value' => '+1000',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => [$itemCondition1, $itemCondition2, $itemCondition3]
		);

		$this->cart->add($item);

		$this->assertEquals(8000, $this->cart->get(456)->priceSumWithConditions(), 'Item subtotal with 1 item should be 80');
		$this->assertEquals(8000, $this->cart->subTotal(), 'Cart subtotal with 1 item should be 80');
	}

	public function test_add_item_with_multiple_item_conditions_with_one_condition_wrong_target() {
		// NOTE:
		// $condition1 and $condition4 should not be included in calculation
		// as the target is not for item, remember that when adding
		// conditions in per-item bases, the condition's target should
		// have a value of item

		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		)); // --> this should not be included in calculation
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		));
		$itemCondition3 = new Condition(array(
			'name' => 'MISC',
			'type' => 'misc',
			'target' => 'item',
			'value' => '+1000',
		));
		$itemCondition4 = new Condition(array(
			'name' => 'MISC 2',
			'type' => 'misc2',
			'target' => 'cart',
			'value' => '+10%',
		));// --> this should not be included in calculation

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => [$itemCondition1, $itemCondition2, $itemCondition3, $itemCondition4]
		);

		$this->cart->add($item);

		$this->assertEquals(8500, $this->cart->get(456)->priceSumWithConditions(), 'Cart subtotal with 1 item should be 85');
		$this->assertEquals(8500, $this->cart->subTotal(), 'Cart subtotal with 1 item should be 85');
	}

	public function test_add_item_condition() {
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		));
		$coupon101 = new Condition(array(
			'name' => 'COUPON 101',
			'type' => 'coupon',
			'target' => 'item',
			'value' => '-5%',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => [$itemCondition2]
		);

		$this->cart->add($item);

		// let's prove first we have 1 condition on this item
		$this->assertEquals(1, count($this->cart->get($item['id'])['conditions']), "Item should have 1 condition");

		// now let's insert a condition on an existing item on the cart
		$this->cart->addItemCondition($item['id'], $coupon101);

		$this->assertEquals(2, count($this->cart->get($item['id'])['conditions']), "Item should have 2 conditions");
	}

	public function test_add_item_condition_restrict_negative_price() {
		$condition = new Condition([
			'name' => 'Substract amount but prevent negative value',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		]);

		$item = [
			'id' => 789,
			'name' => 'Sample Item 1',
			'price' => 20,
			'quantity' => 1,
			'attributes' => [],
			'conditions' => [
				$condition,
			]
		];

		$this->cart->add($item);

		// Since the product price is 20 and the condition reduces it by 25,
		// check that the item's price has been prevented from dropping below zero.
		$this->assertEquals(0, $this->cart->get($item['id'])->priceSumWithConditions(), "The item's price should be prevented from going below zero.");
	}

	public function test_get_cart_condition_by_condition_name() {
		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-2500',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$itemCondition1, $itemCondition2]);

		// get a condition applied on cart by condition name
		$condition = $this->cart->getCondition($itemCondition1->getName());

		$this->assertEquals($condition->getName(), 'SALE 5%');
		$this->assertEquals($condition->getTarget(), 'cart');
		$this->assertEquals($condition->getType(), 'sale');
		$this->assertEquals($condition->getValue(), '-5%');
	}

	public function test_remove_cart_condition_by_condition_name() {
		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-2500',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$itemCondition1, $itemCondition2]);

		// let's prove first we have now two conditions in the cart
		$this->assertEquals(2, $this->cart->getConditions()->count(), 'Cart should have two conditions');

		// now let's remove a specific condition by condition name
		$this->cart->removeCartCondition('SALE 5%');

		// cart should have now only 1 condition
		$this->assertEquals(1, $this->cart->getConditions()->count(), 'Cart should have one condition');
		$this->assertEquals('Item Gift Pack 2500', $this->cart->getConditions()->first()->getName());
	}

	public function test_remove_item_condition_by_condition_name() {
		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'item',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => [$itemCondition1, $itemCondition2]
		);

		$this->cart->add($item);

		// let's very first the item has 2 conditions in it
		$this->assertEquals(2, count($this->cart->get(456)['conditions']), 'Item should have two conditions');

		// now let's remove a condition on that item using the condition name
		$this->cart->removeItemCondition(456, 'SALE 5%');

		// now we should have only 1 condition left on that item
		$this->assertEquals(1, count($this->cart->get(456)['conditions']), 'Item should have one condition left');
	}

	public function test_remove_item_condition_by_condition_name_scenario_two() {
		// NOTE: in this scenario, we will add the conditions not in array format

		$itemCondition = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'item',
			'value' => '-5%',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => $itemCondition // <--not in array format
		);

		$this->cart->add($item);

		// let's very first the item has 2 conditions in it
		$this->assertNotEmpty($this->cart->get(456)['conditions'], 'Item should have one condition in it.');

		// now let's remove a condition on that item using the condition name
		$this->cart->removeItemCondition(456, 'SALE 5%');

		// now we should have only 1 condition left on that item
		$this->assertEmpty($this->cart->get(456)->conditions, 'Item should have no condition now');
	}

	public function test_clear_item_conditions() {
		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'item',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'item',
			'value' => '-2500',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
			'conditions' => [$itemCondition1, $itemCondition2]
		);

		$this->cart->add($item);

		// let's very first the item has 2 conditions in it
		$this->assertEquals(2, count($this->cart->get(456)['conditions']), 'Item should have two conditions');

		// now let's remove all condition on that item
		$this->cart->clearItemConditions(456);

		// now we should have only 0 condition left on that item
		$this->assertEquals(0, count($this->cart->get(456)['conditions']), 'Item should have no conditions now');
	}

	public function test_clear_cart_conditions() {
		// NOTE:
		// This only clears all conditions that has been added in a cart bases
		// this does not remove conditions on per item bases

		$itemCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-2500',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$itemCondition1, $itemCondition2]);

		// let's prove first we have now two conditions in the cart
		$this->assertEquals(2, $this->cart->getConditions()->count(), 'Cart should have two conditions');

		// now let's clear cart conditions
		$this->cart->clearCartConditions();

		// cart should have now only 1 condition
		$this->assertEquals(0, $this->cart->getConditions()->count(), 'Cart should have no conditions now');
	}

	public function test_get_calculated_value_of_a_condition() {
		$cartCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$cartCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-25',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 100,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$cartCondition1, $cartCondition2]);

		$subTotal = $this->cart->subTotal();

		$this->assertEquals(100, $subTotal, 'Subtotal should be 100');

		// way 1
		// now we will get the calculated value of the condition 1
		$cond1 = $this->cart->getCondition('SALE 5%');
		$this->assertEquals(5, $cond1->getCalculatedValue($subTotal), 'The calculated value must be 5');

		// way 2
		// get all cart conditions and get their calculated values
		$conditions = $this->cart->getConditions();
		$this->assertEquals(5, $conditions['SALE 5%']->getCalculatedValue($subTotal), 'First condition calculated value must be 5');
		$this->assertEquals(25, $conditions['Item Gift Pack 2500']->getCalculatedValue($subTotal), 'First condition calculated value must be 5');
	}

	public function test_get_conditions_by_type() {
		$cartCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$cartCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 2500',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-2500',
		));
		$cartCondition3 = new Condition(array(
			'name' => 'Item Less 8%',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-8%',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$cartCondition1, $cartCondition2, $cartCondition3]);

		// now lets get all conditions added in the cart with the type "promo"
		$promoConditions = $this->cart->getConditionsByType('promo');

		$this->assertEquals(2, $promoConditions->count(), "We should have 2 items as promo condition type.");
	}

	public function test_remove_conditions_by_type() {
		// NOTE:
		// when add a new condition, the condition's name will be the key to be use
		// to access the condition. For some reasons, if the condition name contains
		// a "dot" on it ("."), for example adding a condition with name "SALE 3500"
		// this will cause issues when removing this condition by name, this will not be removed
		// so when adding a condition, the condition name should not contain any "period" (.)
		// to avoid any issues removing it using remove method: removeCartCondition($conditionName);

		$cartCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
		));
		$cartCondition2 = new Condition(array(
			'name' => 'Item Gift Pack 20',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-2500',
		));
		$cartCondition3 = new Condition(array(
			'name' => 'Item Less 8%',
			'type' => 'promo',
			'target' => 'cart',
			'value' => '-8%',
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$cartCondition1, $cartCondition2, $cartCondition3]);

		// now lets remove all conditions added in the cart with the type "promo"
		$this->cart->removeConditionsByType('promo');

		$this->assertEquals(1, $this->cart->getConditions()->count(), "We should have 1 condition remaining as promo conditions type has been removed.");
	}

	public function test_add_cart_condition_without_condition_attributes() {
		$cartCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%'
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$cartCondition1]);

		// prove first we have now the condition on the cart
		$contition = $this->cart->getCondition("SALE 5%");
		$this->assertEquals('SALE 5%', $contition->getName());

		// when get attribute is called and there is no attributes added,
		// it should return an empty array
		$conditionAttribute = $contition->getAttributes();
		$this->assertInternalType('array', $conditionAttribute);
	}

	public function test_add_cart_condition_with_condition_attributes() {
		$cartCondition1 = new Condition(array(
			'name' => 'SALE 5%',
			'type' => 'sale',
			'target' => 'cart',
			'value' => '-5%',
			'attributes' => array(
				'description' => 'october fest promo sale',
				'sale_start_date' => '2015-01-20',
				'sale_end_date' => '2015-01-30',
			)
		));

		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 1,
			'attributes' => array(),
		);

		$this->cart->add($item);

		$this->cart->condition([$cartCondition1]);

		// prove first we have now the condition on the cart
		$contition = $this->cart->getCondition("SALE 5%");
		$this->assertEquals('SALE 5%', $contition->getName());

		// when get attribute is called and there is no attributes added,
		// it should return an empty array
		$conditionAttributes = $contition->getAttributes();
		$this->assertInternalType('array', $conditionAttributes);
		$this->assertArrayHasKey('description', $conditionAttributes);
		$this->assertArrayHasKey('sale_start_date', $conditionAttributes);
		$this->assertArrayHasKey('sale_end_date', $conditionAttributes);
		$this->assertEquals('october fest promo sale', $conditionAttributes['description']);
		$this->assertEquals('2015-01-20', $conditionAttributes['sale_start_date']);
		$this->assertEquals('2015-01-30', $conditionAttributes['sale_end_date']);
	}


	public function test_condition_with_quantity_independend_amount() {
		$itemCondition = new Condition(array(
			'name' => 'Test Fix 500',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => true,
			'value' => '500'
		));
		$itemCondition1 = new Condition(array(
			'name' => 'Test 10%',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => true,
			'value' => '+10%'
		));
		$itemCondition2 = new Condition(array(
			'name' => 'Test 10% per Item',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => false,
			'value' => '+10%'
		));
		$itemCondition3 = new Condition(array(
			'name' => 'Test Fix 250',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => false,
			'value' => '250'
		));


		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 3,
			'conditions' => [
				$itemCondition,
				$itemCondition1,
				$itemCondition2,
				$itemCondition3
			],
		);

		$this->cart->add($item);
		# Fix quantity independent
		$this->assertEquals(500, $itemCondition->applyCondition(0), 'condition');
		$this->assertEquals(500, $itemCondition->applyConditionWithQuantity(0, 3), 'Quantitycondition');
		$this->assertEquals(500, $itemCondition->applyConditionWithQuantity(1000, 3), 'Quantitycondition +itemAmount');

		# % quantity independent
		$this->assertEquals(0, $itemCondition1->applyCondition(0), 'condition');
		$this->assertEquals(10, $itemCondition1->applyCondition(100), 'condition');
		$this->assertEquals(10, $itemCondition1->applyConditionWithQuantity(100, 3), 'Quantitycondition +itemAmount');

		# % with quantity
		$this->assertEquals(0, $itemCondition2->applyCondition(0), 'condition');
		$this->assertEquals(10, $itemCondition2->applyCondition(100), 'condition');
		$this->assertEquals(300, $itemCondition2->applyConditionWithQuantity(1000, 3), 'Quantitycondition +itemAmount');


		# Fixed with quantity
		$this->assertEquals(250, $itemCondition3->applyCondition(0), 'condition');
		$this->assertEquals(750, $itemCondition3->applyConditionWithQuantity(0, 3), 'Quantitycondition');
		$this->assertEquals(750, $itemCondition3->applyConditionWithQuantity(1000, 3), 'Quantitycondition +itemAmount');

		$this->assertEquals(35250, $this->cart->subTotal(), 'match subTotal');
		$this->assertEquals(35250, $this->cart->total(), 'match total');
	}


	public function test_two_conditions_with_same_name() {
		$itemCondition = new Condition(array(
			'name' => 'Test1',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => true,
			'value' => '500'
		));
		$itemCondition1 = new Condition(array(
			'name' => 'Test1',
			'type' => 'sale',
			'target' => 'item',
			'quantity_independent' => true,
			'value' => '500'
		));


		$item = array(
			'id' => 456,
			'name' => 'Sample Item 1',
			'price' => 10000,
			'quantity' => 3,
			'conditions' => [
				$itemCondition,
				$itemCondition1,
			],
		);

		$this->cart->add($item);

		$this->assertEquals(500, $itemCondition->applyCondition(0), 'condition');
		$this->assertEquals(500, $itemCondition->applyConditionWithQuantity(0, 3), 'Quantitycondition');
		$this->assertEquals(500, $itemCondition->applyConditionWithQuantity(1000, 3), 'Quantitycondition +itemAmount');

		$this->assertEquals(500, $itemCondition1->applyCondition(0), 'condition');
		$this->assertEquals(500, $itemCondition1->applyConditionWithQuantity(0, 3), 'Quantitycondition');
		$this->assertEquals(500, $itemCondition1->applyConditionWithQuantity(1000, 3), 'Quantitycondition +itemAmount');

		$this->assertEquals(31000, $this->cart->total(), 'match total');
	}
}
