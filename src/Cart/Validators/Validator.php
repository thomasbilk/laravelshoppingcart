<?php namespace Bnet\Cart\Validators;

use Symfony\Component\Translation\Translator;
use Illuminate\Validation\Factory;

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 1/16/2015
 * Time: 10:59 AM
 */
abstract class Validator {

	protected static $factory;

	public static function instance() {
		if (!static::$factory) {
			$locale = class_exists('\Config') ? \Config::get('app.locale') : 'en';
			$translator = new Translator($locale);
			static::$factory = new Factory($translator);
		}

		return static::$factory;
	}

	public static function __callStatic($method, $args) {
		$instance = static::instance();

		switch (count($args)) {
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}
}