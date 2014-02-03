<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__) . '/../library/ObjectBase.php');

class DummyDataClass extends ObjectBase {

	private static $reference_data = Array(
		'appId' => 126767144061773,
		'secret' => '21db65a65e204cca7b5afcbad91fea59',
		'signed_request' => 'cjv1NZlSRCthYq9rAyWEidD7QE98p0PKZvVwpQ7gPwg.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImV4cGlyZXMiOjEzMjI4NTYwMDAsImlzc3VlZF9hdCI6MTMyMjg1MDc1NCwib2F1dGhfdG9rZW4iOiJBQUFCelMwYVhTMDBCQUlob0I1bmhrYnZJU0xLSGpNb3ZIN2ZTTmMzWkFxbnVNT2NvYmpJUHoxNGFmWXV1dzBkbkZzeVpBV2JHU2MycXZBakdjRzZUQ1RWZzBLOUVGUWJ5WkJwNTU0ZXE5M2FTWkFXZXpVeEYiLCJ1c2VyIjp7ImNvdW50cnkiOiJ1cyIsImxvY2FsZSI6ImVuX1VTIiwiYWdlIjp7Im1pbiI6MjF9fSwidXNlcl9pZCI6IjEwMDAwMzI5MTY2MTkwOSJ9'
	);
	private static $decoded_signed_request = array(
		'algorithm' => 'HMAC-SHA256',
		'expires' => 1322856000,
		'issued_at' => 1322850754,
		'oauth_token' => 'AAABzS0aXS00BAIhoB5nhkbvISLKHjMovH7fSNc3ZAqnuMOcobjIPz14afYuuw0dnFsyZAWbGSc2qvAjGcG6TCTVg0K9EFQbyZBp554eq93aSZAWezUxF',
		'user' => array(
			'country' => 'us',
			'locale' => 'en_US',
			'age' => Array(
				'min' => 21
			)
		),
		'user_id' => 100003291661909
	);

	public function populateFromDB($id) {
		// not implemented
		return false;
	}

	public function save() {
		// not implemented
		return false;
	}

	public static function insertDummyData() {

		$db = self::getDB();

		//var_dump($db);
		$query = "INSERT IGNORE INTO app "
				. "(app_id, secret, created_on) "
				. "VALUES "
				. "(" . self::$reference_data['appId'] . ", " . $db->escape(self::$reference_data['secret']) . ", NOW())";

		echo "$query";
		if (!$db->query($query)) {
			echo "Error on inserting into app";
			return false;
		}

		for ($i = 0; $i < 1000000; $i++) {
			echo "$i \n";
			$query = "INSERT IGNORE INTO user "
					. "(user_id, user_country, user_locale, user_age, created_on) "
					. "VALUES "
					. "(" . (self::$decoded_signed_request['user_id'] + mt_rand(1, 10000)) . ", "
					. "" . $db->escape(self::$decoded_signed_request['user']['country']) . ", "
					. "" . $db->escape(self::$decoded_signed_request['user']['locale']) . ", "
					. "" . $db->escape(self::$decoded_signed_request['user']['age']['min']) . ","
					. "NOW())";

			if (!$db->query($query)) {
				echo "Error on inserting into user";
				return false;
			}

			$query = "INSERT IGNORE INTO app_user "
					. "(app_id, user_id, user_score,created_on) "
					. "VALUES "
					. "(" . (self::$reference_data['appId']) . ", "
					. "" . $db->escape((self::$decoded_signed_request['user_id'] + rand(1, 10000))) . ", "
					. "" . mt_rand(1, 5000) . ", "
					. " NOW())";


			if (!$db->query($query)) {
				echo "Error on inserting into app_user";
				return false;
			}
		}

		return true;
	}

}

$data = DummyDataClass::insertDummyData();
