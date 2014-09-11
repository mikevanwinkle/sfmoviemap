<?php
namespace App\Models;
use \PDO;

class Movies extends Model {

	public function __construct() 
	{
		parent::__construct();
	}
	
	public function getFromTitle( $title )
	{
		$db = $this->connect();
		$stm = $db->prepare("SELECT * FROM films WHERE title = ?");
		$stm->bindParam(1, $title, PDO::PARAM_STR, 20);
		$stm->execute();
		if( $stm->rowCount() > 0 ) {
			return $stm->fetch();
		}
		return false;
	}

}
