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
	public $username; /* @var stores name of user allowed to access protected directory */
	public $pwd; 	  /* @var stores password for later encryption */
	public $authtype; /* @var stores Authentication Type */
	public $authname; /* @var stores the name of the folder */

	
	/** 
     * Group Variable declarations 
     * 
	 * All vars are similar to the ones above in function
	 * but hold arrays for group permissions 
	 * TODO: remove dependency on these and revert to prev vars
 	 * $groupusers, $grouppwds, $locations are group versions 
	 * of previous vars.
	 *
	 */  	
	public $groupusers; 
	public $grouppwds; 
	
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
     * Set given $variable as array 
	 *
	 * If variable given isn't already array it will seperate
	 * white-spaced values into array
	 *
	 * @param string $var required to be whitespaced between values
	 * @return array $var exploded into array
	 */	 
	public function setasarray($var) {
		if(!is_array($var)) {
			$var = explode(" ",$var);
		}
		return $var;
	}
	
	/** 
     * Counts array items 
	 *
	 * @param array $var
	 * @return int $var counts total number of array (non-recursive only)
	 */	 
	public function setarrayandcount($var) {
		if(!is_array($var)) {
			$this->setasarray($var);
		}
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
		
		$textclean = array($edit,$replace,$filepath);
		
		foreach($textclean as $key=>$cleaned) {
			$textclean[$key] = trim($cleaned);
		}

		$filestring = implode("",file($textclean[2]));		
		$filename = basename($textclean[2]);
	
		$filestring = str_replace($textclean[0], $textclean[1], $filestring);
		$this->create_file($textclean[2],$filestring);
	}

	/** 
     * Creates a file and gives it content
	 *
	 * @param string $file <File is created in path given>
	 * @param string $content <Content of file put here>
	 */	 
	private function create_file($file,$content) {
			
			//If exists file will be deleted
			$this->deletefile($file);
			
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
			if(file_exists($file)) {
				unlink($file);
			}
			else {
				return 'File doesnt exist';
			}
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
	
		$status = '';
		
		$filename = $this->setasarray($filename);
	
		if(is_array($filename)) {	
			foreach($filename as $paths) {
				$paths = $this->delete($paths);
			}
			
		}
		else {
			$filename = $this->delete($filename);
			$status .= basedir($filename);
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
		
		$this->location = $given_location;	
		
		//If Directory isn't created then create it
		if(!is_dir($given_location)) {
			mkdir($given_location,0777,true);
		}
	}
	
	/** 
     * Add Users to Apache config 
	 *
	 * Each user is counted and the appropiate method (.htgroup or 
	 * valid-user) chosen dependent on amount.
	 *
	 * @param string array $given_names
	 */	 	
	public function add_user($given_names) {

		$given_names = $this->setasarray($given_names);
		$count_entries = $this->setarrayandcount($given_names);
		
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
	
	/** 
     * Add User Passwords to Apache config 
	 *
	 * Each password is counted and the appropiate method (.htgroup or 
	 * valid-user) chosen dependent on amount.
	 * 
	 * (Must match up with the add_user() method) 
	 *
	 * @param string array $given_pwds
	 */	 	
	public function add_pwd($given_pwds) {
		
		$given_pwds = $this->setasarray($given_pwds);
		$count_entries = $this->setarrayandcount($given_pwds);

		if($count_entries == 1) {
			$this->pwd = $given_pwds[0];
		}
		elseif($count_entries > 1) {
			$this->grouppwds = $given_pwds;
		}
		else {
			//Error code here
		}
	}
	
	/** 
     * Add Directory Name and Authentication Method to Apache config 
	 *
	 * @param string $given_type, string $given_name
	 */	 	
	public function add_auths($given_type, $given_name) {
		$this->authtype = $given_type;
		$this->authname = $given_name;
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
		
		$htpass_location = $this->location."/.htpasswd";
		$htacc_location = $this->location."/.htaccess";
		
		//Creates .htaccess line-by-line
		$access =  "Options +Indexes \r\n";
		$access .= "AuthUserFile ".$htpass_location." \r\n";    
		
		if(count($this->grouppwds) > 1 && count($this->groupusers) > 1) {
			
			$ht_location = $this->location."/.htgroup";
			
			$groupname = 'users';
			$access .= "AuthGroupFile ".$ht_location." \r\n";    		
			$usertype = 'group '.$groupname;
			
			//Encode each password for htgroup file
			foreach($this->grouppwds as $key=>$grouppass) {
				$this->grouppwds[$key] = crypt($grouppass);
			}
			
			//Both user array and pwd array are combined into one
			$htgroupdetails = array_combine($this->groupusers,$this->grouppwds);			
			
			//add group users & create .htgroup
			$htgroup = $groupname.': '.implode(" ",$this->groupusers);				
			$this->create_file($ht_location, $htgroup);
			
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
		
			
		$access .= "AuthName \"". $this->authname."\" \r\n";
		$access .= "AuthType ".$this->authtype." \r\n";
		$access .= "<Limit GET POST PUT> \r\n";
		$access .= "require ".$usertype." \r\n";
		$access .= "</Limit> \r\n";

		$this->create_file($htacc_location,$access);		
		$this->create_file($htpass_location, $htpasswd);
						
			//Post-Request-Get Pattern (PRG)
			header('Location: '. $_SERVER['REQUEST_URI'] , true, 303);
			exit;

	}
}
?>