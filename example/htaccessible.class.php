<?php
class htaccessible {

	public $location; //Location of .htaccess, .htpasswd
	public $username; //Name of users allowed to access
	public $pwd; //Uh duhhhh 
	public $authtype; //Htaccess Auth
	public $authname; //Name of Protected Folder
	public $groupname; //Name of Group Users
	public $groupusers;
	public $grouppwds;
	
	public function filelocation($given_location) {
		$this->location = $given_location;
		
		//If Directory isn't created then create it
		if(!is_dir($given_location)) {
			mkdir($given_location,0777,true);
		}
	}
		
	public function add_user($given_names) {

		$given_names = explode(" ",$given_names);

		$count_entries = count($given_names);
		
		if($count_entries == 1) {
			$this->username = $given_names[0];
		}
		elseif($count_entries > 1) {
			$this->groupusers = $given_names;
		}
		else {
			//Error code here
		}
		
	}
	
	public function add_pwd($given_pwds) {
		
		$given_pwds = explode(" ",$given_pwds);
			
		$count_entries = count($given_pwds);

		if($count_entries == 1) {
			$this->pwd = $given_pwds;
		}
		elseif($count_entries > 1) {
			$this->grouppwds = $given_pwds;
		}
		else {
			//Error code here
		}
	}
	
	public function add_auths($given_type, $given_name) {
		$this->authtype = $given_type;
		$this->authname = $given_name;
	}
	
	public function htcreate() {
		
		//Creates .htaccess line-by-line
		$access =  "Options +Indexes \r\n";
		$access .= "AuthUserFile ".$this->location."/.htpasswd \r\n";    
		
		if(count($this->grouppwds) > 1 && count($this->groupusers) > 1) {
			
			$groupname = 'users';
			$access .= "AuthGroupFile ".$this->location."/.htgroup \r\n";    		
			$usertype = 'group '.$groupname;
			
			//Encode each password for htgroup file
			foreach($this->grouppwds as $key=>$grouppass) {
				$this->grouppwds[$key] = crypt($grouppass);
			}
			
			//Both user array and pwd array are combined into one
			$htgroupdetails = array_combine($this->groupusers,$this->grouppwds);
			
			//add group users
			$htgroup = $groupname.': '.implode(" ",$this->groupusers);	
			$int = 0;
			foreach($htgroupdetails as $key => $htpass) {
				if($int == 0) {
					$htpasswd = $key.":".$htpass."\r\n";
				}
				else {
					$htpasswd .= $key.":".$htpass."\r\n";
				}
				$int++;
			}
						
		}
		else {
			if($this->pwd) {
				$passwd = crypt($this->pwd);
			}
			$usertype = 'valid-user';
			$htpasswd = $this->username.":".$passwd."\n\r";
		}
		
		var_dump($usertype);
		
		$access .= "AuthName \"". $this->authname."\" \r\n";
		$access .= "AuthType ".$this->authtype." \r\n";
		$access .= "<Limit GET POST PUT> \r\n";
		$access .= "require ".$usertype." \r\n";
		$access .= "</Limit> \r\n";
    
		$handle_hta = fopen($this->location.".htaccess","w");
		fputs($handle_hta,$access);
		fclose($handle_hta);

		$handle_group = fopen($this->location.".htgroup","a");
		fputs($handle_group,$htgroup);
		fclose($handle_group);
		
		$handle_pwd = fopen($this->location.".htpasswd","a");
		fputs($handle_pwd,$htpasswd);
		fclose($handle_pwd);	
	}
}
?>