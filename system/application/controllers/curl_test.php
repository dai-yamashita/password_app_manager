<?php

class Curl_test extends Controller {

    function __construct() {
    	parent::Controller();
        $this->load->library('curl');
    }

    function getmysite() {
// Simple call to remote URL
$site = 'http://localhost/passwordmanager/admin/auth/login';
#echo $this->curl->simple_get($site);
        echo '<h1>Simple Get</h1>';
        echo '<p>--------------------------------------------------------------------------</p>';
        echo '<pre>';
        echo $this->curl->simple_post($site, array(
            'username'=>'admin', 'password' => 'admin', 'tmpid' => '1030831'
            ), array(CURLOPT_BUFFERSIZE => 10));

    }
    
    function simple_get()
    {
        $responce = $this->curl->simple_get('curl_test/get_message');
        
        echo '<h1>Simple Get</h1>';
        echo '<p>--------------------------------------------------------------------------</p>';
        
        if($responce) {
            
            echo $responce;
            
            
            echo '<br/><p>--------------------------------------------------------------------------</p>';
            echo '<h3>Debug</h3>';
            echo '<pre>';
            print_r($this->curl->info);
            echo '</pre>';
            
        } else {
            echo '<strong>cURL Error</strong>: '.$this->curl->error_string;
        }
    }
    
    function simple_post()
    {
        $responce = $this->curl->simple_post('curl_test/message', 
			array('message'=>'Sup buddy', 'submit' => 'go' )
			);
        
        echo '<h1>Simple Post</h1>';
        echo '<p>--------------------------------------------------------------------------</p>';
        
        if($responce) {
            
            echo $responce;
            
            
            echo '<br/><p>--------------------------------------------------------------------------</p>';
            echo '<h3>Debug</h3>';
            echo '<pre>';
            print_r($this->curl->info);
            echo '</pre>';
            
        } else {
            echo '<strong>cURL Error</strong>: '.$this->curl->error_string;
        }
    }
    
	function post2(){
	$site = "http://localhost/passwordmanager/admin/auth/login";
		
	// Start session (also wipes existing/previous sessions)
	$this->curl->create($site);

	// Option & Options
	$this->curl->option(CURLOPT_BUFFERSIZE, 10);
	$this->curl->options(array(CURLOPT_BUFFERSIZE => 10));

	// Login to HTTP user authentication
	//$this->curl->http_login('username', 'password');

	// Post - If you do not use post, it will just run a GET request
	$post = array('username'=>'admin', 'password' => 'admin', 'tmpid' => '103083' );
	$this->curl->post($post);

	// Cookies - If you do not use post, it will just run a GET request
	#$vars = array('foo'=>'bar');
	#$this->curl->set_cookies($vars);

	// Proxy - Request the page through a proxy server
	// Port is optional, defaults to 80
	// $this->curl->proxy('http://example.com', 1080);
	// $this->curl->proxy('http://example.com');

	// Proxy login
	//$this->curl->proxy_login('username', 'password');

	// Execute - returns responce
	echo $this->curl->execute();

	// Debug data ------------------------------------------------

	// Errors
	$this->curl->error_code; // int
	$this->curl->error_string;

	// Information
	echo '<pre>';
	print_r( $this->curl->info ); // array 	
	}
    function message() {
        echo "<h2>Posted Message</h2>";
        //echo $_POST['message'];
		echo "<pre>";
		print_r( $_POST );
    }
    
    function get_message() {
        echo "<h2>Get got!</h2>";
    }

    function advance()
    {
        $this->curl->create('curl_test/cookies')
                    ->set_cookies(array('message'=>'Im advanced :-p'));

        $responce = $this->curl->execute();
        
        echo '<h1>Advanced</h1>';
        echo '<p>--------------------------------------------------------------------------</p>';
        
        if($responce) {
            
            echo $responce;
            
            echo '<br/><p>--------------------------------------------------------------------------</p>';
            echo '<h3>Debug</h3>';
            echo '<pre>';
            print_r($this->curl->info);
            echo '</pre>';
            
        } else {
            echo '<strong>cURL Error</strong>: '.$this->curl->error_string;
        }
    }
    
    function cookies() {
        echo "<h2>Cookies</h2>";
        print_r($_COOKIE);
    }
}
 