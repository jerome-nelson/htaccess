<?php
/** 
 * HTAccessible: An Apache Folder Protect Directive 
 * 
 * This class is designed to easily assemble and create protected 
 * directories on Apache Servers via .htaccess, .htpasswd and .htgroup
 * directives. 
 * 
 * The comment syntax is taken from the PHP DocBlock commenting
 * system. Google it! 
 *
 * Note: I've chosen to manually comment instead of using PHPDocumentor 
 *  \(-_-)/ Google is your friend!
 *
 * @author Jerome Nelson <j.nelson@hotmail.de> 
 * @license GNU GENERAL PUBLIC LICENSE (Version 2) 
 */  
 
class htaccessible {

	/** 
     * Single Variable declarations 
	 */  
	public $location; /* @var stores location of .htaccess, .htpasswd, .htgroup */
	protected $username = array(); /* @var stores name of user(s) allowed to access protected directory */
	protected $pwd = array(); 	  /* @var stores password for later encryption */
	public $authtype; /* @var stores Authentication Type */
	public $authname; /* @var stores the name of the folder */
	public $hashtype; /* @var stores the password encryption type */	
	protected $cryptshow = array(); /* @var shows encrypted passwords */
	public $test = array();
	protected $errors; /* @var used to hold SPL Exceptions */
	
	/** 
     * String Declaration Magic Operator 
     * 
	 * This function will run whenever class Object is treated 
	 * as a string by mistake.
	 *
	 * @return string variable explaining invalid choice
	 */
	public function __toString() {  
        return '<p>'.__CLASS__.' shouldn\'t be echoed. Instead please refer to the variables above.</p>';  
    }  
	
	/** 
     * __constructor function 
     * 
 	 * Currently sets up hashtype , sets localhost and test environments 
	 *
	 * @param $hashtype <string> - md5, sha1, digest-md5
	 * @param $options <array> - testmode (bool), localhost (bool)
	 */  	
	public function __construct($hashtype = 'md5',$options = array()) {
		
		//Setup password hashtype
		$this->hashtype = $hashtype;
		
		//Set mode variables
		$this->test['localmode'] = (isset($options[0]) && $options[0] == 'local-on' ? TRUE : FALSE);
		$this->test['testmode'] = (isset($options[1]) && $options[1] == 'test-on' ? TRUE : FALSE);
		
		(is_bool($this->test['testmode']) ? self::testmode() : "");
		
		//Init Exceptions SPL
		//$this->errors = new Exception();
	}
	
	/** 
     * Setup testmode variables 
     * 
 	 * If testmode is enabled then a few variables are set 
	 * 
	 * @param $hashtype <string> - md5, sha1, digest-md5
	 * @param $options <array> - testmode (bool), localhost (bool)
	 */  	
	protected function testmode() {
		
		//Setup session var - Put all data in here
		session_start();
		$_SESSION['testmode'] = array();
		$_SESSION['testmode']['is_on'] = $this->test['testmode'];
		
	}
	
	/** 
     * Password Hashing function 
     * 
	 * This function should be run whenever a password is requested 
	 * depending on void $hashtype (`md5`,`sha1` or `digest-md5`)
	 * a different algorithm is used.
	 *
	 * @param string $pass 
	 * @param string $hashtype
	 * @return array $pass encrypted (plus insert of all passwords in void $this->cryptshow)
	 */

	public function gethash($pass,$hashtype) {
		
		global $tmp;
		
		//MD5 implementation
		if($hashtype == 'md5') {
			$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
			$len = strlen($pass);
			$text = $pass.'$apr1$'.$salt;
			$bin = pack("H32", md5($pass.$salt.$pass));
			
			for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
			for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $pass{0}; }
			
			$bin = pack("H32", md5($text));

			for($i = 0; $i < 1000; $i++) {
				$new = ($i & 1) ? $pass : $bin;
				
				if ($i % 3) $new .= $salt;
				if ($i % 7) $new .= $pass;
			
				$new .= ($i & 1) ? $bin : $pass;
				$bin = pack("H32", md5($new));
			}
			
			for ($i = 0; $i < 5; $i++) {
				$k = $i + 6;
				$j = $i + 12;
				
				if ($j == 16) $j = 5;
					$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
				}
			
				$tmp = chr(0).chr(0).$bin[11].$tmp;
				$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/","./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
			
				$pass = "$"."apr1"."$".$salt."$".$tmp;
		}
		
		//SHA1 Implementation
		else if($hashtype == 'sha1') {		
			$pass = "{SHA}".base64_encode(sha1($pass, TRUE));
		}
		
		else if($hashtype == 'digest-md5') {
			$pass = md5($pass);
		}
		
		$this->cryptshow[] = $pass;	
		return $pass;	
	}

	
	 /** 
     * Set given $variable as array 
	 *
	 * If variable given isn't already array it will seperate
	 * white-spaced values into array
	 *
	 * @param string $var required to be whitespaced between values
	 * @return array $var exploded into array
	 */	 
	public function setasarray($var, $exploder = " ") {
	
		//If $var isn't variable then change into
		//TO DO: Add Exception to show if array isn't.
		$var = (!is_array($var) ? explode($exploder,$var): $var);
		return $var;
	}
	
	/** 
     * Counts array items 
	 *
	 * @param array $var
	 * @return int $var counts total number of array (non-recursive only)
	 */	 
	public function setarrayandcount($var) {
		
		//TO DO: Create Exception to show if not array
		(!is_array($var) ? self::setasarray($var) : "");
		$count_entries = count($var);
		return $count_entries;
	}

	/** 
     * Gets and edits a file 
	 *
	 * @param string $filepath <path to file>
	 * @param string $edit <the string you want to replace>
	 * @param string $replace <the string you want to use>
	 */	 
	public function edit($filepath,$edit,$replace) {
		
		//Put all $vars into array which is cleaned
		//TO DO: Expand on cleaning (only if necessary)
		$textclean = array($edit,$replace,$filepath);		
		foreach($textclean as $key=>$cleaned) {
			$textclean[$key] = trim($cleaned);
		}

		//Get file contents and put into string 
		//TO DO: If file doesn't exist EXCEPTIOOONNNN!!!
		$filestring = implode("",file($textclean[2]));		

		//Edit (search and replace) strings then create file
		//TO DO: Detect if searched string doesn't exist (give Exception - don't run create_file) 
		$filestring = str_replace($textclean[0], $textclean[1], $filestring);
		self::create_file($textclean[2],$filestring);
	}

	/** 
     * Creates a file and gives it content
	 *
	 * @param string $file <File is created in path given>
	 * @param string $content <Content of file put here>
	 */	 
	private function create_file($file,$content) {
			
			//If exists file will be deleted
			//TO DO: If not exists throw Exception (from deletefile())
			self::deletefile($file);
			
			//TO DO: If not exists do not do
			//Add content and create file
			$handle_group = fopen($file,"a");
			fputs($handle_group,$content);
			fclose($handle_group);
	}
	
	/** 
     * Delete a file 
	 *
	 * Based on a filepath, this method deletes the $var given if
	 * it exists (should not be used, use deletefile() instead.
	 *
	 * @param string $file
	 * @return nothing unless error
	 */	 
	private function delete($file) { 		
		//TO DO: Better if not exists handler (Exception)
		(file_exists($file) ? unlink($file) : '');
	}
	
	/** 
     * Deletes files 
	 *
	 * Based on filepath, this method will delete either a single 
	 * string or an array of filepaths (if given, non-recursive only).  
	 *
	 * @param string array $filename
	 * @return int $var counts total number of array (non-recursive only)
	 */	 

	public function deletefile($filename) {
	
		//Set object as array if not set
		//Set way to check if $arguement is empty
		$filename = self::setasarray($filename);			
		foreach($filename as $paths) {
			//Setup Exception or other appropiate handler to catch any errors
			$paths = self::delete($paths);
		}
	}
	
	/** 
     * Sets the location of the files 
	 *
	 * Based on filepaths this method takes the filepath given and 
	 * creates it 
	 *
	 * @param string $given_location
	 */	 
	public function filelocation($given_location) {
		
		//TODO: If filename not set then use defaults (in ht_create())
		$this->location = $given_location;	
		
		//If Directory isn't created then create it
		(!is_dir($given_location) ? mkdir($given_location,0777,true) : '');
		//TODO: Check possible invalid arguements
	}
	
	/** 
     * Add Users to Apache config 
	 *
	 * Each user entered is added to array
	 * This will later be counted and added to
	 * appropiate files.
	 *
	 * @param string array $given_names
	 */	 	
	public function add_user($given_names) {
		//Set array and count items
		$this->username = self::setasarray($given_names);
		//TODO: Merge with add_pwd() method
	}
	
	/** 
     * Add User Passwords to Apache config 
	 *
	 * Each password entered is added to array
	 * This will later be counted and added to
	 * appropiate files.
	 * 
	 * (Must match up with the add_user() method) 
	 *
	 * @param string array $given_pwds
	 */	 	
	public function add_pwd($given_pwds) {
		$this->pwd = self::setasarray($given_pwds);
		//TODO: Merge with add_user() method
	}
	
	/** 
     * Add Directory Name and Authentication Method to Apache config 
	 *
	 * @param string $given_type, string $given_name
	 */	 	
	public function add_auths($given_type, $given_name) {
	
		//NOTE: Digest Authentication doesn't allow whitespaces for some reason so the string will be cleared of whitespace
		$this->authname = ($given_type == 'digest' ? str_replace(" ","-",$given_name) : $given_name);		
		$this->authtype = $given_type;
	}
	
	/** 
     * Creates Apache Config
     * 
	 * This method is only called once all other essential methods have 
	 * been set otherwise it will fail. The creation and setup of 
	 * .htaccess, .htpasswd and .htgroup files is done here.
	 * 
	 */	 	
	public function htcreate() {
		
		//TODO: Allow full filename (if not set then use default)
		$htpass_location = $this->location."/.htpasswd";
		$htacc_location = $this->location."/.htaccess";
		
		//TODO: Check if this best way to create files
		//Creates .htaccess line-by-line
		$access =  "Options +Indexes \r\n";
		$access .= "AuthUserFile ".$htpass_location." \r\n";    
		
		//If Authentication Type is digest then need these lines
		if($this->authtype == 'digest') {
			//TODO: Expand these lines into extra methods
			//More research needed on both
			$access .= "AuthDigestDomain / \r\n";
			$access .= "AuthDigestProvider file \r\n";
		}
		
		//If entries in both password are greater then 1 use .htgroup
		//TODO: Compare both and make sure the same length (need else in place)
		if(self::setarrayandcount($this->pwd) > 1 && self::setarrayandcount($this->username) > 1) {
			
			$ht_location = $this->location."/.htgroup";
			$groupname = 'users';
			$access .= "AuthGroupFile ".$ht_location." \r\n";    		
			$usertype = 'group '.$groupname;
			
			if($this->authtype == 'digest') {
		
				//Encode each password for htgroup file
				foreach($this->pwd as $key=>$grouppass) {
					$this->pwd[$key] = $this->username[$key].':'.$this->authname.':' .self::gethash($this->username[$key].':'.$this->authname.':' .$grouppass,'digest-md5'); 
				}
				
				//Rename array
//				$htgroupdetails = $this->pwd;			
				
				//Create htpasswd strings
				$int = 0;
				foreach($this->pwd as $key => $htpass) {
					if($int == 0) {
						$htpasswd = $htpass."\r\n";
					}
					else {
						$htpasswd .= $htpass."\r\n";
					}
					$int++;
				}
			}

			else {
		
				//Encode each password for htgroup file
				foreach($this->pwd as $key=>$grouppass) {
					$this->pwd[$key] = self::gethash($grouppass, $this->hashtype);				
				}
			
				//Both user array and pwd array are combined into one
				$htgroupdetails = array_combine($this->username,$this->pwd);

				//Create htpasswd strings
				$int = 0;
				foreach($htgroupdetails as $key => $htpass) {
					
					//If $int is set at zero then set $htpasswd else add to it
					($int == 0 ? $htpasswd = $key.":".$htpass."\r\n" : $htpasswd .= $key.":".$htpass."\r\n");					
					$int++;
				}				
			}				
			
			//add group users & create .htgroup
			$htgroup = $groupname.':'.implode(" ",$this->username);				
			self::create_file($ht_location, $htgroup);				
		}
		
		//Else if only 1 user name and 1 pwd detected
		else {
			$usertype = 'valid-user';
			if($this->authtype == 'digest') {
				$htpasswd = $this->username[0].":".$this->authname.":".self::gethash($this->username[0].":".$this->authname.":".$this->pwd[0],'digest-md5')."\n\r";
			}
			else {
				$htpasswd = $this->username[0].":".self::gethash($this->pwd[0],$this->hashtype);
			}			
		}
					
		$access .= "AuthName \"". $this->authname."\" \r\n";
		$access .= "AuthType ".$this->authtype." \r\n";
		$access .= "<Limit GET POST PUT> \r\n";
		$access .= "require ".$usertype." \r\n";
		$access .= "</Limit> \r\n";

		self::create_file($htacc_location,$access);		
		self::create_file($htpass_location, $htpasswd);
						
		//Post-Request-Get Pattern (PRG)
//		header('Location: '. $_SERVER['REQUEST_URI'] , true, 303);
//		exit;

	}
}

?>