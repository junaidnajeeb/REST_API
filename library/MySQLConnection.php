<?php 

static $CONNECTIONS = array();


class MySQLConnection {
	
	/**
	 * Credentials.
	 * @var string
	 */
	private $host = null;
	private $db = null;
	private $user = null;
	private $pass = null;
	
	/**
	 * Current connection.
	 * @var resource
	 */
	protected $link = null;
	
	/**
	 * Result of last query. 
	 * @var mixed
	 */
	private $resultSet = null;
	
	/**
	 * Transaction counter.
	 * @var int
	 */
	private $transaction_counter = 0;

	/**
	 * Log a message.
	 * @param $msg
	 * @param $file
	 * @param $line
	 * @return void
	 */
	protected static function log($msg, $file, $line) {
		error_log(sprintf("%s:%s -> %s", $file, $line, $msg));
	}
	
	/**
	 * Construct.
	 * @param $host
	 * @param $db
	 * @param $user
	 * @param $pass
	 * @param $dbcheck
	 * @param $new_link
	 * @return void
	 */
	public function __construct($host, $db, $user, $pass) {
		
		$this->host = $host;
		$this->db = $db;
		$this->user = $user;
		$this->pass = $pass;
		
		$this->connect();
		
	}
	
	/**
	 * Acquire connection.
	 * @return boolean true on success, false on failure
	 */
	protected function connect() {
		
		if ( $this->link ) {
			mysql_close($this->link);
			$this->link = null;
		}

		$this->link = mysql_connect($this->host, $this->user, $this->pass, true);
		
		if ( !$this->link ) {
			self::log("Connection to db server could not be established.", __FILE__, __LINE__);
			return false;
		}
		
		$this->query('SET NAMES utf8');
		$this->select_db();
		
		return true;
		
	}

	public function close() {
		if ($this->link) {
			mysql_close($this->link);
		}
	}

	/**
	 * Sets the current active database.
	 * @return boolean
	 */
	public function select_db() {
		
		if (!$this->link) {
			return false;
		}

		return mysql_select_db($this->db, $this->link);
	}
	
	/**
	 * Ping a server connection or reconnect if there is no connection.
	 * @return boolean TRUE if the connection to the server MySQL server is working, otherwise FALSE.
	 */
	public function ping() {
		
		if (!$this->link) {
			return false;
		}

		return mysql_ping($this->link);
		
	}
	
	/**
	 * Execute query.
	 * @param string $query a query string
	 * @param int $retry number of retries, set to -1 for unlimited (query will not be retried if inside of transaction)
	 * @return boolean
	 */
	public function query($query, $retry = 1) {
		
		$this->resultSet = null;
		
		if (!$this->link) {
			return false;
		}

		// run query
		
		do {

			$this->resultSet = mysql_unbuffered_query($query, $this->link);
			
			if ($this->resultSet !== false) {
				return true;
			}

			// query failed, retry if necessary
			
			if (strpos($query, 'CHECK_TABLE_EXISTS') !== false) {
				return false;
			}

			$m_error = $this->error();
			
			if ( $this->transaction_counter ) {
				self::log("Error executing mysql query $m_error. Query:\n" . substr($query, 0, 1000) . "\n", __FILE__, __LINE__);
				return false;
			}
			
			if ( substr($m_error, 0, 5) == '#2006' && $retry ) {
				
				// retry in 2s if '#2006 Mysql has gone away' is received unless in the middle of a transaction
				
				self::log("Got error #2006, retrying in 2 seconds.", __FILE__, __LINE__);
				
				usleep(2000000);
				
				// reconnect
				
				$this->connect();
				
				if ( !$this->link ) {
					self::log("Connection failed on query retry.", __FILE__, __LINE__);
					return false;
				}
				
				continue;
				
			}

			self::log("Error executing mysql query $m_error. Query:\n" . substr($query, 0, 1000) . "\n", __FILE__, __LINE__);
			return false;
				
		} while( $this->resultSet === false && $retry-- );
		
		return true;
		
	}
	
	/**
	 * Start transaction.
	 * @return boolean
	 */
	public function transaction() {

		// already in transaction
		if ($this->transaction_counter) {
			return false;
		} 

		$query = "START TRANSACTION";

		if (!$this->query($query)) {
			return false;
		}

		$this->transaction_counter++;

		return $this->fetch_all();

	}
	
	/**
	 * Commit a transaction.
	 * @return mixed
	 */
	public function commit() {

		if ($this->transaction_counter != 1) {
			return false;
		} // not in transaction

		$query = "COMMIT";

		if (!$this->query($query)) {
			return false;
		}

		$this->transaction_counter = 0;

		return $this->fetch_all();

	}

	/**
	 * Rollback a transaction.
	 *
	 * @param string $project Project name
	 * @return mixed
	 */
	public function rollback() {
		
		if ($this->transaction_counter != 1) {
			return false;
		} // not in transaction

		$query = "ROLLBACK";

		if (!$this->query($query)) {
			return false;
		}

		$this->transaction_counter = 0;
		
		return $this->fetch_all();
		
	}
	
	/**
	 * Get last mysql error.
	 * @return string
	 */
	public function error() {
		
		if (!$this->link) {
			return "No connection to db server!";
		}

		$errno = mysql_errno($this->link);
		
		return $errno ? "#$errno: " . mysql_error($this->link) : '';
		
	}
	
	/**
	 * Get number of affected rows.
	 * @return mixed Returns the number of affected rows on success, and -1 if the last query failed, or false if invalid connection
	 */
	public function affected_rows() {
		
		if (!$this->link) {
			return false;
		}

		return mysql_affected_rows($this->link);
		
	}
	
	/**
	 * Get last insert id
	 * @return mixed The ID generated for an AUTO_INCREMENT column by the previous INSERT query on success, 0 if the previous query does not generate an AUTO_INCREMENT value, or FALSE if no MySQL connection was established.
	 */
	public function last_insert_id() {
		
		if (!$this->link) {
			return false;
		}

		return mysql_insert_id($this->link);
		
	}
	
	/**
	 * Smart quoting function to prevent SQL injection.
	 * @param string $value String value to be escaped
	 * @return string
	 */
	public function escape($value, $quotes = true) {
		if (!$this->link) {
			return false;
		}

		$result = mysql_real_escape_string($value, $this->link);
		
		if ($result === false) {
			return false;
		}

		return $quotes ? "'$result'" : $result;
		
	}
	
	/**
	 * Fetch entire query result.
	 * @return mixed, array or true on success, false on failure
	 */
	public function fetch_all() {
		
		return $this->fetch(0);
		
	}

	/**
	 * Fetch query result up to a specified rows.
	 * @param int $limit the number of rows to fetch. If 0, then it will fetch all.
	 * @return mixed, array or true on success, false on failure
	 */
	public function fetch($limit) {
		
		if ($this->resultSet === true) {
			return true;
		}

		if ( is_resource($this->resultSet) ) {
			
			$result = array();
				
			$count = 1;
			while ( $row = mysql_fetch_assoc($this->resultSet) ) {
				$result[] = $row;

				if (!empty($limit) && ($count >= $limit)) {
					break;
				}

				$count++;
			}
				
			return $result;
			
		}

		return false;
		
	}

	/**
	 * Return host.
	 * @return string
	 */
	public function get_host() {
		return $this->host;
	}
}

