<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( dirname(__FILE__) . '/API.php');
require_once(dirname(__FILE__) . '/App.php');

class AppAPI extends API {

	public function __construct($request) {
		parent::__construct($request);
	}

	public function insert_app() {

		if ($this->method == 'POST') {
			$app_id = (!empty($this->request['app_id'])) ? $this->request['app_id'] : '';
			$secret = (!empty($this->request['secret'])) ? $this->request['secret'] : '';

			if (!empty($app_id) && !empty($secret)) {
				$app_obj = new App(array(
					'app_id' => $app_id,
					'app_secret' => $secret,
				));

				try {
					$app_obj->save();
					return 'app created';
				} catch (Exception $ex) {
					return $ex->getMessage();
				}
			} else {
				return "Invalid input sent";
			}
		} else {
			return "Only accepts POST requests";
		}
	}
	
	public function update_app() {

		if ($this->method == 'PUT') {
			$app_id = (!empty($this->request['app_id'])) ? $this->request['app_id'] : '';
			$secret = (!empty($this->request['secret'])) ? $this->request['secret'] : '';

			if (!empty($app_id) && !empty($secret)) {
				$app_obj = new App(array(
					'app_id' => $app_id,
					'app_secret' => $secret,
				));

				try {
					$app_obj->save();
					return 'app updated';
				} catch (Exception $ex) {
					return $ex->getMessage();
				}
			} else {
				return "Invalid input sent";
			}
		} else {
			return "Only accepts PUT requests";
		}
	}
}
