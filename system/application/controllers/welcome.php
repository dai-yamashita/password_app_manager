<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		#$this->load->view('welcome_message');
		#$this->template->render();
		echo "hello mark";
	}

	function humandate()
	{	
	$now = time();
	$human = unix_to_human($now);
	echo "human = $human <br>";
	$unix = human_to_unix($human);
	echo "unix = $unix <br>";	
	}	
	
        function checkoverdue() {
            echo '<pre>';
            $rs = $this->alerts->check_overdue_account();
            
            #print_r($rs);
        }

        function test(){
$stop_date = '2009-09-30 20:24:00';
$stop_date = date('Y-m-d H:i:s', time());
echo 'date before day adding: ' . $stop_date;

$stop_date = date('Y-m-d H:i:s', strtotime($stop_date . ' + 1 day'));
echo '<br/>date after adding 1 day: ' . $stop_date;

echo '<br>' . time();


        }
		
		function test2(){
		$this->load->helper( 'date' );
$now = time();
$t2 = unix_to_human($now);		
echo $t2;
echo human_to_unix($t2);

//$now = now();
$gmt = gmt_to_local(now(), 'UP8', FALSE );
echo "  <br/><br/>gmt=$gmt  ";
echo date('F d Y g:i:s a', $gmt);

$timezone  = +8; //(GMT -5:00) EST (U.S. & Canada)
$t2 = 3600*($timezone+date("I")) ;
echo "  <br/><br/>localtime=". gmdate("Y/m/j g:i:s a", time() + $t2 );


		}

	function test3()
	{		
		$ftp_server = "ftp.eyelog.net";
		// set up a connection or die
		$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 	 		   
		print_r( $conn_id  );
	}		
	       
	function testcpanel() {
		$this->load->library( 'cpanel' );
		$params = array('user' => 'eyelogn', 'pass' => 'Rl4ac83R4izK', 'host' => 'eyelog.net' );
        $this->cpanel->execute_page('index.html', $params );

	}
	
	function testweb()
	{	
		$params = array();
		$url = 'http://localhost/attorney/wp-login.php';
		$returl = 'http://localhost/attorney/wp-admin/';
		
		// $url = 'http://localhost/passwordmanager/admin/auth/login/';
		// $returl = 'http://localhost/passwordmanager/admin/user/browse';		
		
        $ch = curl_init();
		$agent	=	$_SERVER["HTTP_USER_AGENT"]; 
        curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        #curl_setopt($ch, CURLOPT_POSTFIELDS,'tmpid=103083&username=admin&password=admin' );
        curl_setopt($ch, CURLOPT_POSTFIELDS,'log=admin&pwd=admin&testcookie=1&wp-submit=Log%20In&redirect_to=http://localhost/attorney/wp-admin/' );
        curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); 		
        $result = curl_exec($ch);		
        curl_close($ch);
		echo "$result ";
		#redirect($returl);
		
	
	}	
	
				
	function testweb2() {
		$username="admin";
		$password="admin";
		$url="http://localhost/attorney/";
		$returl = 'http://localhost/attorney/wp-admin/';
		$cookie="cookie.txt";

		$postdata = "log=". $username ."&pwd=". $password ."&wp-submit=Log%20In&redirect_to=". $url ."wp-admin/&testcookie=1";
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url . "wp-login.php");

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);
		#curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, ''); 
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($ch, CURLOPT_POST, 1);
		$result = curl_exec ($ch);
		curl_close($ch);
		#header("location:$returl");
		redirect($returl, 'refresh' );
		die();
		#redirect($returl);
		#echo $result;
	}
	
	function testdrupal() {
		$url = "http://localhost/mydrupal/";
		$username="admin1";
		$password="admin1";
		$postdata = "name=". $username ."&pass=". $password ."&op=Log%20In";
		
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url . "/node?destination=node");
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);
		#curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, ''); 
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($ch, CURLOPT_POST, 1);
		$result = curl_exec ($ch);
		curl_close($ch);
		echo $result;		
		
		#header("location:$returl");
		#redirect($returl, 'refresh' );
		die();
		#redirect($returl);
		
	}
	
	function testweb3() {

		$username="admin";
		$password="admin";
		$url="http://localhost/attorney/";
		$returl = 'http://localhost/attorney/wp-admin/';
		$cookie="cookie.txt";
		
		$post_data['log'] = $username;
		$post_data['pwd'] = $password;
		$post_data['wp-submit'] = 'Log%20In';
		$post_data['redirect_to'] = $url ."wp-admin/&testcookie=1";
		
		#$postdata = "log=". $username ."&pwd=". $password .
		# "&wp-submit=Log%20In&redirect_to=". $url ."wp-admin/&testcookie=1";

		
		//traverse array and prepare data for posting (key1=value1)
		foreach ( $post_data as $key => $value) {
		$post_items[] = $key . '=' . $value;
		}

		//create the final string to be posted using implode()
		$post_string = implode ('&', $post_items);

		//we also need to add a question mark at the beginning of the string
		$post_string = '?' . $post_string;

		//we are going to need the length of the data string
		$data_length = strlen($post_string);

		//let's open the connection
		$connection = fsockopen($url, 80);

		//sending the data
		fputs($connection, "POST /wp-login.php HTTP/1.1 \n");
		fputs($connection, "Host: $url \n");
		fputs($connection, "Content-Type: application/x-www-form-urlencoded \n");
		fputs($connection, "Content-Length: $data_length \n");
		fputs($connection, "Connection: close \n");
		fputs($connection, $post_string);

		//closing the connection
		fclose($fp);	
	}
		
	function fsocket1() {
		$url="http://localhost/attorney/";	
		$fp = fsockopen($url, 80, $errno, $errstr, 30);
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
		} else {
		    $out = "GET / HTTP/1.1\r\n";
		    $out .= "Host: $url\r\n";
		    $out .= "Connection: Close\r\n\r\n";
		    fwrite($fp, $out);
		    while (!feof($fp)) {
		        echo fgets($fp, 128);
		    }
		    fclose($fp);
		}	
	}

	function autocomplete(){
		$sys_lib = BASEPATH . '/libraries';
		$app_lib = APPPATH . '/libraries';
		$app_model = APPPATH . '/models';

		echo "&lt;?php<br><br>";
		echo "/**<br/>";
		echo '* @property CI_DB_active_record $db<br/>';
		echo '* @property CI_DB_forge $dbforge<br/>';

		if ($handle = opendir($sys_lib)) {
			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				if($file[0] == '.') continue;
				$files = explode('.', $file);
				$file = $files[0];
				$file2 = $file;
				if($file == 'index') continue;
				if($file == 'Loader') $file2 = 'load';
				echo "* @property CI_" . $file . " $" . strtolower($file2) . "<br/>";

			}
			closedir($handle);
		}
		if ($handle = opendir($app_lib)) {
			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				if($file[0] == '.') continue;
				$files = explode('.', $file);
				$file = $files[0];
				$file_parts = explode('_', $file);
				$first_part = $file_parts[0];
				if($first_part == 'index' || $first_part == 'MY') continue;
				if(count($file_parts) > 1){
					$last_part = $file_parts[1];
					echo "* @property " . ucfirst($first_part) . "_" . ucfirst($last_part) . " $" . strtolower($first_part) . "_" . strtolower($last_part) ."<br/>";
				}
				else{
					echo "* @property " . ucfirst($first_part) . " $" . strtolower($first_part)."<br/>";
				}

			}
			closedir($handle);
		}
		if ($handle = opendir($app_model)) {
			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				if($file[0] == '.') continue;
				$files = explode('.', $file);
				$file = $files[0];
				if($file == 'index') continue;
				$file_parts = explode('_', $file);
				$first_part = $file_parts[0];
				$last_part = $file_parts[1];
				echo "* @property " . ucfirst($first_part) . "_" . ucfirst($last_part) . " $" . strtolower($first_part) . "_" . strtolower($last_part) ."<br/>";

			}
			closedir($handle);
		}
		echo "*/<br><br>";
		echo "class Controller {}<br><br>";
		echo "?>";
	}


}


/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */

