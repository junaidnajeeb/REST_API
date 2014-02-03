<?php

require_once(dirname(__FILE__) . '/../library/BaseObject.php');

class User extends BaseObject {

	public $user_id;
	public $user_country;
	public $user_locale;
	public $user_age;
	public $created_on;

	/**
	 * 
	 * @param int $id
	 * @return boolean
	 * @throws Exception
	 */
	public function populateFromDB($id) {

		$query = "SELECT * FROM user WHERE user_id = $id";
		if (!$this->getDB()->query($query)) {
			$this->setError("unable to get data  ('{$this->getDB()->error()}')");
			throw new Exception($this->getErrorAsString());
		}

		$user = $this->getDB()->fetch_all();

		if (!empty($user)) {
			$user = $user[0];
			$this->populateProperties($user);
		} else {
			$this->setError("Unable to fetch data with id $id");
			throw new Exception($this->getErrorAsString());
		}

		return true;
	}

	/**
	 * How many total players are there
	 * @param string $error
	 * @return boolean
	 */
	public static function totalPlayers(&$error) {

		$db = self::getDB();

		$query = "SELECT count(*) AS total_players FROM user ";
		if (!$db->query($query)) {
			$error = 'failed to get user count';
			return false;
		}

		
		$users = $db->fetch_all();
		if (!empty($users)) {
			return $users[0]['total_players'];
		} else {
			$error = 'no users found';
			return false;
		}

		return true;
	}

	/**
	 * How many people played the game today?
	 * @param string $error
	 * @return boolean
	 */
	public static function totalPlayersPlayedToday(&$error) {

		$db = self::getDB();

		$query = "
				SELECT count(*) AS total_players_played_today FROM app_user au 
				JOIN user u ON u.user_id = au.user_id 
				WHERE au.created_on >= DATE_SUB(NOW(), INTERVAL 1 DAY)
				";
		if (!$db->query($query)) {
			$error = 'failed to get user count';
			return false;
		}

		$users = $db->fetch_all();

		if (!empty($users)) {
			return $users[0]['total_players_played_today'];
		} else {
			$error = 'no users found';
			return false;
		}

		return true;
	}

	/**
	 * List the top 10 players by score
	 * @param string $error
	 * @return boolean
	 */
	public static function top10Player(&$error) {

		$db = self::getDB();

		$query = "
				SELECT u.user_id, SUM(au.user_score) FROM app_user au 
				JOIN user u ON u.user_id = au.user_id 
				GROUP BY u.user_id
				ORDER BY au.user_score DESC
				LIMIT 10
				";
		if (!$db->query($query)) {
			$error = 'failed to get user';
			return false;
		}

		$users = $db->fetch_all();

		if (!empty($users)) {
			return $users;
		} else {
			$error = 'no users found';
			return false;
		}

		return true;
	}
	/**
	 * List the top 10 players who improved their score over the course of the week
	 * @param string $error
	 * @return boolean
	 */
	public static function top10ImprovedPlayer(&$error) {

		$db = self::getDB();

		$query = "
			SELECT 
				this_week_user.user_id AS user_id,
				this_week_user.user_score AS this_week_score,
				last_week_user.user_score AS last_week_score
			 FROM (
				SELECT u.user_id AS user_id, SUM(au.user_score) AS user_score
				FROM app_user au
				JOIN user u ON u.user_id = au.user_id 
				WHERE WEEKOFYEAR(au.created_on) = WEEKOFYEAR(NOW())
				GROUP BY u.user_id
				ORDER BY au.user_score DESC
				) AS this_week_user
			JOIN 
				(
				SELECT u.user_id AS user_id, au.user_score AS user_score
				FROM app_user au
				JOIN user u ON u.user_id = au.user_id 
				WHERE WEEKOFYEAR(au.created_on) = WEEKOFYEAR(NOW() + INTERVAL -7 DAY)
				GROUP BY u.user_id
				ORDER BY au.user_score DESC
				) AS last_week_user
			WHERE this_week_user.user_id = last_week_user.user_id
			AND this_week_user.user_score > last_week_user.user_score
			LIMIT 10
			";
		if (!$db->query($query)) {
			$error = 'failed to get user';
			return false;
		}

		$users = $db->fetch_all();

		if (!empty($users)) {
			return $users;
		} else {
			$error = 'no users found';
			return false;
		}

		return true;
	}

	public function save() {
		// not implemented
		return false;
	}

}
