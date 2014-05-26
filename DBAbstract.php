<?php
	abstract class DBAbstract
	{
		private static $host = "localhost";
		private static $user = "user";
		private static $pass = "+o}P^!3=U(of";
		protected $db = "base_db";
		private $sqlQuery;
		private $mysqli;
		public $numrows;
		
		public function __construct(){
			$this->open_connection();
		}

		abstract protected function get(); 
		abstract protected function set(); 
		abstract protected function edit(); 
		abstract protected function delete(); 

		private function open_connection(){
			$this->mysqli = new mysqli(self::$host, self::$user, self::$pass, $this->$db);
			if ($this->mysqli->connect_error) {
				die('Connect Error: ' . $this->mysqli->connect_error);
			}
		}
		
		protected function close()
		{
			$this->mysqli->close();
		}
		
		protected function query($sql)
		{
			$this->sqlQuery = $this->mysqli->query($sql);
		}
		
		protected function result()
		{
			$output = array();
			while($row = @$this->sqlQuery->fetch_assoc())
				$output[] = $row;

			return $output;
		}

		protected function free_result()
		{
			$this->mysqli->free_result();
		}

		protected function numrows()
		{
			return $this->sqlQuery->num_rows;
		}
		
		protected function affectedrows(){
			return $this->mysqli->affected_rows;
		}
		
		protected function escape_string($string){
			return $this->mysqli->real_escape_string($string);
		}
		
		protected function last_id(){
			return $this->mysqli->insert_id;
		}

	}

	
