<?php

require_once(dirname(__FILE__) . '/../library/ObjectBase.php');

class App extends ObjectBase {

	public $app_id;
	public $app_secret;

	public function populateFromDB($id) {
		
	}

	public function save() {

		//TODO:: implement save
		return true;
	}

}
