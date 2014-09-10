<?php
namespace App\Control;

use App\Models\Product as ProductModel;

class Products extends Control {

	public function __construct() 
	{
		parent::__construct();
	}

	public function mainAction( $id = false ) {
		if ( $id ) {
			switch($this->request_method) {
				case "PUT":
					parse_str( file_get_contents("php://input"), $input );
					$this->updateProduct( $id , $input );
					break;
				case "DELETE":
					echo "delete";
					break;
				default:
					$this->getOne($id);
				break;
			}
		} else {
			$this->getAll();
		}
	}

	public function getAll()
	{
		$productmodel = new ProductModel();
		$results = $productmodel->get();
		$this->data['success'] = 1;
		$this->data['status'] = 200;
		$this->data['products'] = $results;
		$this->view("json");
	}

	public function getOne( $id ) 
	{	
		$productmodel = new ProductModel();
		$results = $productmodel->get( $id );	
		$this->data['success'] = 1;
		$this->data['status'] = 200;
		$this->data['products'] = $results;
		$this->view("json");
	}

	public function updateProduct( $id , $input )
	{
		$product = new ProductModel();
		$results = $product->update( $id, $input );
		if( $results ) {
			$this->data['success'] = 1;
			$this->data['status'] = 200;
			$this->data['product'] = $results;
			$this->view("json");
		}
	}

}
