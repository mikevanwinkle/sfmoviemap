<?php
namespace App\Control;

use \Exception;
use App\Models\Movies as MovieModel;

class Movies extends Control {
	public $locations_model = false;
	
	public function mainAction()
	{
		$this->view("map");
	}
}
