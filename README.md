htaccessible
============

Based off aoshiftctrl `htaccess` ..this is a .htaccess/htpasswd combo creation PHP class (with example). The previous script wasn't working and so this script aims to correct and extend the features to allow for full creation of htaccess/htpasswd/htgroup files.


### How to Setup ###

1. Include htaccessible.class.php and create a new object.*

	#### Example ####
		include("htaccessible.class.php");
		$htaset = new htaccessible;

	<hr />
		
2. Once this is set you need to configure these htaccessible variables to get it to work (full list of functions below):

	#### Example ####
		$htaset->filelocation(<filepath for all files goes here>);
		$htaset->add_user(<string of username(s) go here (white-space seperated)>);
		$htaset->add_pwd(<string of password(s) go here (white-space seperated)>);
		$htaset->add_auths(<string type of authentication used>, <string name of directory>);`

	<hr />
		

3. After this initiate file creation *(only once the above variables are complete otherwise it will not work correctly)*

	#### Example ####
		$htaset->htcreate();


### Methods/Functions 		

1. `add_auths(<string $authentication_type>,<string $directory_name>)` *required function*
	
	Sets `$this->authtype` and `$this->authname` respectively. Both variables are required for the .htaccess file to render and work correctly.
	
	Please see these links for more info:
	1. http://httpd.apache.org/docs/2.2/mod/core.html#authtype (Apache Docs AuthType Directive)
	
	2. http://httpd.apache.org/docs/2.2/mod/core.html#authname (Apache Docs AuthName Directive)
		
	*(Digest authentication not yet added. Feature is planned - see Repo Milestones for est. time)*

	<hr />
	
2. `add_pwd(<string $password | string $passwords [white-space seperated]>)` *required function*
	
	Sets `$this->pwd` if string is single result else `$this->grouppwds` if this string has multiple seperated [white-spaced] entries. Multiple
	entries are split into an array for later use.
	
	*`add_pwd` and `add_users` string results must match up otherwise htcreate() will throw an error*

	<hr />

3. `add_users(<string $username | string $usernames [white-space seperated]>)` *required function*
	
	Sets `$this->username` if string is single result else `$this->groupusers` if this string has multiple seperated [white-spaced] entries. Multiple
	entries are split into an array for later use.

	*`add_pwd` and `add_users` string results must match up otherwise htcreate() will throw an error*

	<hr />	

4. `create_file(<string $file>,<string $content>)`

	Creates file as long as it is a valid path. 
	
	<hr />	
	
5. `delete(<string $filepath>)`

	Deletes file as long as it is a valid path. 
	*Use deletefiles() instead as delete() is a private function used in the deletefiles method*
	
	<hr />	
	
6. `deletefiles(<string $filepath | array $filepaths>)`
	
	Detects whether files exist and proceeds to delete them.

	<hr />	

7. `edit(<string $filepath | string $edit | string $replace>)`
	
	Will edit already existing files with new content
	
	*Have not implemented any timeout/size limit functions for this (planned feature - 04/08/2013)*
	<hr />	

	
8. `htcreate()` *required function*
	
	Creates the .htaccess, .htgroup and htpasswd config using variables set.
	
	*Variables must be set before calling otherwise error will be thrown*

	<hr />	
	
9. `setasarray(<string $variable [seperated-variables]>,<string $exploder [string delimiter]>)`

	Takes string with delimiter-spaced variables and creates array using explode. If $exploder isn't set then assumes delimiter is whitespace.

	<hr />	
		
10. `setarrayandcount(<string $variable [whitespaced-variables] || array $variables>)`
    
	Takes string or array and outputs number of array items as an integer. If string is given then it will be converted into an array then counted.