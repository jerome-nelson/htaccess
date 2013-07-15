<?php
function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


function make_htaccess($location, $user, $authName, $passwdFile="")
{
	
	//If empty use default path (where .htaccess will go)
    if(empty($passwdFile)) {
		$passwdFile=dirname(__FILE__);
    }
	
	//Creates .htaccess line-by-line
    $access = "Options +Indexes \r\n";
	$access .=  'AuthUserFile ' . $passwdFile . '/.htpasswd' . "\r\n";    
    $access .= 'AuthName "' . $authName . '"' . "\r\n";
    $access .= "AuthType Basic"."\r\n";
	$access .= '<Limit GET POST PUT>' ."\r\n";
	$access .= 'require valid-user' ."\r\n";
	$access .= "</Limit>\r\n";
    
    $handle = fopen(".htaccess","w");
    fputs($handle,$access);
    fclose($handle);

}


function make_htpasswd($location, $user, $passwd, $passwdFile="")
{
    
    if(empty($passwdFile))
    
        $passwdFile=dirname(__FILE__);
     
    $passwd = crypt($passwd);
    $htpasswd = $user . ':' . $passwd . "\n\r"; 
/*	if(isset($_admin)){$htpasswd .= $_admin;} 
	$htpasswd .= ':';
	if(isset($_adminpass)){$htpasswd .= $_adminpass;}  
	$htpasswd .= "\n";*/
    
    $handle = fopen($passwdFile . '/.htpasswd',"a");
    fputs($handle,$htpasswd);
    fclose($handle);
    
}

// Der Text, der angezeigt wird wenn das Login-Fenster geöffnet wird.
$authName = 'htaccess';

//Den kompletten Pfad zu der Datei .htpasswd, ohne abschließenden Slash (/)
$passwdFile = '';

if ($_POST){
	
	if ((empty($_POST["passwd"])) or (empty($_POST["user"]))){
		echo "sry";
	}else{
	  make_htpasswd($_POST["user"], $_POST["passwd"], $passwdFile);
	  make_htaccess($_POST["user"], $authName, $passwdFile);
	}
	
}

class htacessible {
	
	var $location; //Location of .htaccess, .htpasswd
	var $username; //Name of user allowed to access
	var $pwd; //Uh duhhhh 
	var $authtype; //Htaccess Auth
	var $authname //Name of Protected Folder

	public function filelocation($given_location) {
		$this->location = $given_location;
		
		//If Directory isn't created then create it
		if(!is_dir($given_location)) {
			mkdir($given_location,0700);
		}
	}
		
	public function add_user($given_name) {
		$this->username = $given_name;
	}
	
	public function add_pwd($given_pwd) {
		$this->pwd = $given_pwd;
	}
	
	public function add_auths($given_type, $given_name) {
		$this->authtype = $given_type;
		$this->authname = $given_name;
	}
	
	public function htcreate() {
		
		//Creates .htaccess line-by-line
		$access =  "Options +Indexes \r\n";
		$access .= "AuthUserFile ".$this->location."/.htpasswd \r\n";    
		$access .= "AuthName ". $this->authname." \r\n";
		$access .= "AuthType ".$this->authtype." \r\n";
		$access .= "<Limit GET POST PUT> \r\n";
		$access .= "require valid-user \r\n";
		$access .= "</Limit> \r\n";
    
		$handle_hta = fopen($this->location.".htaccess","w");
		fputs($handle_hta,$access);
		fclose($handle_hta);
		
		
		$passwd = crypt($this->pwd);
		$htpasswd = $this->username. ':' . $passwd . "\n\r"; 
    
		$handle_pwd = fopen($passwdFile . '/.htpasswd',"a");
		fputs($handle_pwd,$htpasswd);
		fclose($handle_pwd);
	}
}
?>
<html> 
 <head> 
  <title>htpasswd</title> 
    <meta charset="utf-8">
    
    <style media="screen">
    *{margin: 0; padding: 0; line-height: 1em; font-family: Helvetica, Arial, sans-serif;}	
    body{margin: 50px; background-color: rgb(255,255,255); color: rgb(40,40,40);}
    h1{font-size: 24px; margin: 0 0 10px 0;}
    h2{font-size: 20px; margin: 0 0 10px 0;}
    p{font-size: 16px; line-height: 1.3em; margin: 0 0 10px 0;}
    form input{ margin-bottom:10px; min-width: 200px; padding: 4px; font-size: 14px; outline: none; }
    form input:focus {font-weight: bold; outline: none; }
    form label{display: block;}
    </style>
  
 </head> 
<body> 
 <form method=post action=<?= $_SERVER['PHP_SELF'];?>> 
  <label	for="user">User</label> <input type="text" name="user"><br> 
  <label	for="pass">Pass</label> <input type="text" name="passwd"><br> 
  <input	type="hidden"	name="huser"	value="weigl"	/>
  <input	type="hidden"	name="hpasswd"	value="start"	/>
  <input	type="submit"	name="senden"	value="create htaccess with passwd"	/>
  </form>
  </body> 
</html>