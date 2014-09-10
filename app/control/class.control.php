<?php
namespace App\Control;
use \Exception;

class Control {
	static $instance = NULL;	
	public $data = array();
	public $headers = array();
	public $request_method;

	public function __construct()
	{
		$this->request_method = $_SERVER['REQUEST_METHOD'];
		return $this;
	}

	public function view( $view ) {
	
		if ( "json" === $view ) {
			$data = json_encode( $this->data );
			header( "Content-type: application/json" );
			echo $data;
		} else {
			$this->viewExists( $view );
			extract( $this->data );

			include VIEWS_PATH."/$view.html.php";
		}
	}

	public function viewExists( $view ) {
		if ( !file_exists( VIEWS_PATH."/$view.html.php" ) ) {
			throw new Exception( "Couldn't find the view you requests!" );
		}
	}

}
