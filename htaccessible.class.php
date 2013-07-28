<?php
class htaccessible {

	public $location; //Location of .htaccess, .htpasswd
	public $username; //Name of user allowed to access
	public $pwd; //Uh duhhhh 
	public $authtype; //Htaccess Auth
	public $authname; //Name of Protected Folder
	public $statusmsgs; //All message reports go here
	
	$statusmsgs = array(); //Used to contain all output msgs
	
	public function filelocation($given_location) {
		$this->location = $given_location;
		
		//If Directory isn't created then create it
		if(!is_dir($given_location)) {
			mkdir($given_location,0777,true);
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
	
	public function statusmsgs($wrappername,$children) {
			
			if(isset($wrapperelem)) {
				echo '<'.$wrapperelem.'>';				
			}
				if(isset($statusmsgs) && is_array($statusmsgs)) {
					foreach($statusmsgs as $msg) {
						if(isset($children)) {
							echo '<'.$children.'>';
						}
							echo $msg;
						if(isset($children)) {
							echo '</'.$children.'>';
						}
					}
				}
				else {
					echo 'Nothing to report';
				}
			if($wrapperelem) {
				echo '</'.$wrapperelem.'>';		
			}
	}
	
	public function htcreate() {
		
		//Creates .htaccess line-by-line
		$access =  "Options +Indexes \r\n";
		$access .= "AuthUserFile ".$this->location."/.htpasswd \r\n";    
		$access .= "AuthName \"". $this->authname."\" \r\n";
		$access .= "AuthType ".$this->authtype." \r\n";
		$access .= "<Limit GET POST PUT> \r\n";
		$access .= "require valid-user \r\n";
		$access .= "</Limit> \r\n";
    
		$handle_hta = fopen($this->location.".htaccess","w");
		fputs($handle_hta,$access);
		fclose($handle_hta);
		
		
		$passwd = crypt($this->pwd);
		$htpasswd = $this->username.":".$passwd."\n\r"; 
    
		$handle_pwd = fopen($this->location.".htpasswd","a");
		fputs($handle_pwd,$htpasswd);
		fclose($handle_pwd);
	}
}
?>