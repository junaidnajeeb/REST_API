<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__) . '/UserAPI.php');
require_once(dirname(__FILE__) . '/AppAPI.php');

abstract class APIFactory {

	private static $class_list = array('UserAPI', 'AppAPI');

	public static function createAPI($request) {

		$args = explode('/', rtrim($request, '/'));
		$function_name = array_shift($args);

		foreach (self::$class_list as $class) {
			$class_methods = get_class_methods($class);

			if (in_array($function_name, $class_methods)) {
				return new $class($request);
			}
		}
		throw new Exception('Invalid API call');
	}

}
