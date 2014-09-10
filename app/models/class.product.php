<?php
namespace App\Models;
use \PDO;

class Product extends Model {
	public $table = 'products';	

	public function __construct() 
	{
		parent::__construct();
	}

	/**
	 * @param $table string (required) table on which to operate
	 * @param $item array (required) should be an associative array of key=>value pairs 
	 * @param $ignore_duplicate bool (optional) if true use INSERT IGNORE DUPLICATE else use INSERT
	**/
	public function insert( $item, $ignore_duplicate = true ) 
	{
		$query = "INSERT ";
		if ( $ignore_duplicate ) {
			$query .= "IGNORE ";
		}
		$query .= "INTO %s ";
		$query = sprintf( $query, $this->table );
		$query .= "( `sku`, `name`, `quantity`, `price` ) VALUES ( ?, ?, ?, ? )";
		$db = $this->connect();
		$stm = $db->prepare( $query );
		$stm->bindParam(1, $item['sku'], PDO::PARAM_STR, 20);
		$stm->bindParam(2, $item['name'], PDO::PARAM_STR, 60);
		$stm->bindParam(3, $item['quantity'], PDO::PARAM_INT, 10 );
		$stm->bindParam(4, $item['price'], PDO::PARAM_STR, 8 );
		$stm->execute();
		return $stm->rowCount();		
	}
	
	/** 
	 * Update function
	 * @todo needs more validation/sanitization
	**/
	public function update( $id, $data ) 
	{
		$query = "UPDATE %s SET %s WHERE sku = '%s'";
		$sets = array();
		foreach( $data as $k => $v ) {
			$sets[] = sprintf("`%s` = '%s'", $k, $v);
		}
		$query = sprintf( $query, $this->table, join(", ", $sets), $id );
		$db = $this->connect();
		$result = $db->query($query);
		if( $result->rowCount() > 0 ) {
			return $this->get( $id );
		} else {
			throw new \Exception("Couldn't update product $id");
		}
	}

	/** 
	 * Get a single row, or all rows
	 * @todo need the ability to pass in params
	 */
	public function get( $sku = null, $limit = 0 ) 
	{
		$query = sprintf("SELECT * FROM %s where 1=1 ", $this->table);
		if( $sku ) {
			$query .= sprintf( "AND sku = '%s' ", $sku);
		}
		
		if ( $limit > 0 ) {
			$query .= sprintf( "LIMIT %d", $limit);
		}
		
		$result = $this->query( $query );
		return $result;
	}
}
