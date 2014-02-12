<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( dirname(__FILE__) . '/API.php');
require_once(dirname(__FILE__) . '/User.php');

class UserAPI extends API {

	public function __construct($request) {
		parent::__construct($request);
	}

	public function total_players() {

		$error = '';
		if ($this->method == 'GET') {
			$users_count = User::totalPlayers($error);
			if ($users_count == false) {
				return $error;
			} else {
				return "Total users: " . $users_count;
			}
		} else {
			return "Only accepts GET requests";
		}
	}

	/**
	 * 
	 * @return string
	 */
	public function total_players_played_today() {

		$error = '';
		if ($this->method == 'GET') {
			$users = User::totalPlayersPlayedToday($error);
			if ($users === false) {
				return $error;
			} else {
				return "Total user played today are: " . $users;
			}
		} else {
			return "Only accepts GET requests";
		}
	}

	/**
	 * 
	 * @return string
	 */
	public function total_10_players() {

		$error = '';
		if ($this->method == 'GET') {
			$users = User::top10Player($error);
			if ($users === false) {
				return $error;
			} else {
				return $users;
			}
		} else {
			return "Only accepts GET requests";
		}
	}

	/**
	 * 
	 * @return string
	 */
	public function total_10_improved_players() {

		$error = '';
		if ($this->method == 'GET') {
			$users = User::top10ImprovedPlayer($error);
			if ($users === false) {
				return $error;
			} else {
				return $users;
			}
		} else {
			return "Only accepts GET requests";
		}
	}

}
