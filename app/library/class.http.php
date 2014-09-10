<?php
namespace App\Library;

use \HTTP_Request2;

class Http {	
	public $creds;
	public $url;
	public $endpoint;
	public $params = array();
	public $use_auth = false;
	
	public function __construct() {}

	
	public function setParams( $param ) 
	{
		$this->params = array_merge( $this->params, $param );
	}

	/**
	 * Sanitize and setup the base url
	**/
	public function setUrl( $url , $override = false )
	{
		if( $override ) {
			$this->url = $url;
		} else {
			$this->url = sprintf( "https://%s:%s@%s.%s", $this->creds['key'], $this->creds['password'], $this->creds['user'] , $url );
		}
	}

	// Would probably handle this in a loader class given more time
	public function loadCreds( $service )
	{
		$creds = parse_ini_file( CONFIG_FILE, true );
		if( empty( $creds ) ) {
			throw new \Exception("Could not load creds file");
		}
		$this->creds = $creds[$service];
	}
	
	/**
	 * Set the endpoint based on pre-determined options
	 */
	public function setEndpoint( $endpoint , $vars = array() )
	{
			
		if ( !array_key_exists( $endpoint, $this->endpoints ) ) {
			throw new \Exception("Invalid endpoint specified for api ".get_class($this) );
		}

		$this->endpoint = $this->endpoints[$endpoint];

		// if we've passed vars do a simple string replace
		if( !empty( $vars) ) {
			foreach( $vars as $k => $v ) {
				$this->endpoint = preg_replace("/(\{$k\})/","$v", $this->endpoint);
			}
		}
		return $this->endpoint;
	}

	public function getUrl()
	{
		return $this->url.$this->endpoint;
	}

	/**
	 * Send the request w/ HTTP_Request2 (pear)
	 *  @params $method string (required) GET|PUT|POST|DELETE
	 *
	 */
	public function request( $method="GET" ) {
		require_once "HTTP/Request2.php";

		$http = new HTTP_Request2( $this->getUrl(), constant("HTTP_REQUEST2::METHOD_$method") );
		if( $method === 'GET' ) {
			$query = http_build_query( $this->params );
		}
	
		if ( $this->use_auth ) {
			$http->setAuth( $this->creds['user'], $this->creds['pass'] );
		}
	
		// for dev purposes
		$http->setConfig(array('ssl_verify_peer'=>false,'follow_redirects'=>true) );
		$http->setHeader("Content-Type: application/json");
		$http->setHeader("Accept: application/json");	

		if ( $method === "PUT" ) {
			$http->setBody( json_encode( $this->params ) );
			$http->setHeader('Content-type: application/json');
		}
		$resp = $http->send();
		return json_decode( $resp->getBody(), 1 );
	}
}
