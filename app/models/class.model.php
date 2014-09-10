<?php
namespace App\Models;
use PDO;

class Model {
	public $creds;
	public $connection;

	public function __construct()
	{
		$this->connect();
	}

	/** 
	 * Make the database connection based on creds in the config file
	**/
	public function connect()
	{
		if ( $this->connection instanceof PDO ) {
			return $this->connection;
		}
		
		$creds = parse_ini_file(CONFIG_FILE,TRUE);
		$this->creds = $creds['mysql'];
		$this->connection = new PDO( sprintf( "mysql:host=%s;dbname=%s;charset=utf8", $this->creds['host'], $this->creds['name'] ), $this->creds['user'], $this->creds['pass'] );
	}

	/**
	 * Simple method to run a query
	 * @todo add logging if in debug mode
	 * @todo method should also handle basic bindings
	**/
	public function query( $query ) 
	{
		$db = $this->connect();
		$result = $db->query($query);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	} 

}
