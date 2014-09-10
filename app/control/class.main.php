<?php
namespace App;
use \Exception;
/** 
 * Front controller that handles requests / output
 *  iniates statically Wink\App::run();
**/
class Main {
	static $app;
	public $debug = false;
	public $data = array(); // data to be displayed in the view
	public $route = false;
	public $view = false;
	public $default_control = "movies";
	
	public function __construct() 
	{
		if( $this->debug ) {
			ini_set("display_errors",1);
			error_reporting(E_ALL);
		}
		// register the autoloader
		spl_autoload_register( array( $this, 'autoloader' ) );
		return $this;
	}
	
	/** 
	 * Singleton instance
	**/
	static function instance() 
	{
		if ( !isset( self::$app) AND !is_object( self::$app ) ) {
			self::$app = new self();
		}
		return self::$app;
	}

	/** 
	 * Run the app
	**/
	static function run()
	{
		$app = Main::instance();
		$app->route();
		$app->shutdown();
	}

	/**
	 * Does the actual work of finding a route and calling the method
	 * @todo needs unittesting to prevent breaks
	 **/
	public function route()
	{
		$this->parseRoute();
		
		$control = ucfirst($this->route[0]) ?: $this->default_control;
		$control = "App\\Control\\$control";
		
		if( class_exists( $control ) ) {
			array_shift($this->route);
			// work our way through the request to get the pieces
			if( !empty($this->route[0]) and method_exists( $control, $this->route[0] ) ) {
				$action = $this->route[0];
				array_shift($this->route);
			} else {
				$action = 'mainAction';
			}

			// load an instance and run it
			$control_obj = new $control();	
			call_user_func_array( array( $control_obj, $action ), $this->route );
		} else {
			throw new Exception( "Invalid control specified" );
		}
	}

	/**
	 * Simple method to parse a route and send it through controller
	 * @todo this type of function would need a ton of unit tests because it affects every other aspect of the app
	**/
	public function parseRoute()
	{
		$uri = parse_url($_SERVER['REQUEST_URI']);
		$path = trim($uri['path'], '/');
		$parts = explode( "/", $path );
		$this->route = $parts;
				
		// other ways to validate
	}

	/**
	 * Useful to have a shutdown function for adding benchmark data
	**/
	public function shutdown()
	{
		if ( $this->debug ) {
			// useful info
			echo PHP_EOL."<!-- ";	
			echo ( memory_get_peak_usage() / 1024 /1024 );
			echo "MB -->";
		}
	}

	/**
	 * Autoloader function
	 * @info decides how to find class based on namespace;
	**/
	public function autoloader( $class ) 
	{
		// this is a little hacky and I don't really like it yet
		if ( class_exists( $class ) ) return; 
		
		$class = explode( "\\", $class );

		// remove the namespace
		$namespace = array_shift( $class );

		// no need to go further if we're not in our namespace
		if ( "App" != $namespace ) return;
	
		$name = array_pop( $class );
		$filename = "class.$name.php";
		$class = APP_PATH."/".join("/",$class) . "/". $filename;
		$class = strtolower($class);
		if( file_exists( $class ) ) {
			include_once $class;
		}
	}	
}
