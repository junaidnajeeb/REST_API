<?php

require_once(dirname(__FILE__) . '/MySQLConnection.php');

abstract class BaseObject {

	private static $db;
	public $type;
	public $error;

	public function __construct($data = array()) {
		
		self::getDB();
		
		if (!empty($data)) {
			if (is_int($data) === true || is_string($data) === true) {
				$result = $this->populateFromDB($data);
				if ($result === false) {
					throw new Exception('Client ' . implode("; ", $this->getError()));
				}
			} else if (is_array($data) === true) {
				$this->populateProperties($data);
			}
		}
		
		$this->type = get_class($this);
	}

	public static function getDB() {
		return self::$db = self::$db !== NULL ? self::$db : new MySQLConnection(':/Applications/MAMP/tmp/mysql/mysql.sock', 'jtest', 'root', 'root');
	}

	public function setError($error) {
		if (is_array($error) === true) {
			foreach ($error as $item) {
				$this->error[] = $item;
			}
		} else {
			$this->error[] = $error;
		}
	}

	public function getError() {
		if (is_array($this->error)) {
			return $this->error;
		}
		return array();
	}

	public function getErrorAsString() {
		$error = '';
		if (is_string($this->error)) {
			$error = $this->error;
		} else if (is_array($this->error)) {
			$error = implode("\n", array_reverse($this->error));
		}
		return $error;
	}

	abstract public function populateFromDB($id);
	abstract public function save();

	protected function populateProperties($data) {

		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$set_function = "set_" . $key;
				if (method_exists($this, $set_function)) {
					$result = $this->$set_function($value);
					if ($result === false) {
						throw new Exception('Server' . "Unable to build object '" . get_class($this) . "'. Please verify input data");
					}
				} else {
					$this->$key = $value;
				}
			}
		}
	}

}
