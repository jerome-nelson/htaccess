htaccessible
============

Based off aoshiftctrl `htaccess` ..this is a .htaccess/htpasswd combo creation PHP class (with example). The previous script wasn't working and so this script aims to correct and extend the features to allow for full creation of htaccess/htpasswd/htgroup files.

Example
-------

    include('htaccessible.class.php');
   
    $htaset = new htaccessible;
   
    $htaset->filelocation('Location where you want files to be put here');
   
    $htaset->add_user('username here');
   
    $htaset->add_pwd('password here');
    
    $htaset->add_auths('Auth_type and Directory Name here');
	 
    //This creates files
   
    $htaset->htcreate(); 


The Class File
--------------

The class name is _htaccessible_ and can be included in any PHP project as normal (via include or require)



Functions & Objects
-------------------

### Functions ###

	1. filelocation($given_location)
		1. This takes the _$given location_ and creates the directory given (with 0777 permissions) if it doesn't exist.
	2. add_user($given_name)
	3. add_pwd($given_pwd)	
	4. add_auths($given_type, $given_name)
	5. htcreate()
		1. Once all the other functions have been used then this one is run to create the necesscary files.
