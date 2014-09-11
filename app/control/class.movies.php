<?php
namespace App\Control;

use \Exception;
use App\Models\Movies as MovieModel;
use App\Models\Locations as LocationsModel;

class Movies extends Control {
	public $locations_model = false;
	
	public function mainAction()
	{
		$this->view("map");
	}
	
	public function sync()
	{
		$this->view("sync");
	}
	
	public function sync_location()
	{
		
		if( $_SERVER['REQUEST_METHOD'] != "POST" ) {
			throw new Exception("Must use a POST request");
		}
	
		$this->locations_model = new LocationsModel();
		$this->locations_model->addIfNotExists();
	}
	
	public function get_from_title( $title )
	{
		$model = new MovieModel();
		$this->data = $model->getFromTitle(urldecode($title));
		$this->view('json');
	}

}
