<?php 
/**
 *  File Definition
 *  Example Server
 *
 *  Help:
 *  http://www.reazulk.wordpress.com
 *  
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  
 */

	// Include Webservice library
	// I would suggest to include it based on your development environmen may be using Auto Load
	require_once 'webservice.php';
	
	//Include you class files
	require_once 'test.php';
    require_once("cryptography.php");

	// Create Server Instance
	$server = new Webservice();

	// Set Service Path And URL
	// Chnage it according to your environmet
	$server->setServiceUrl("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["PHP_SELF"])
		   ->setWsdlCachePath("wsdl/");

	if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST') 
    {
    	$server->setClassName("Test")
		  	   ->loadServer();
    }
    else
    { 							  
		if(isset($_GET['classname']))
		{
			$server->setClassName("Test")
			   	   ->setServiceName("app")
			   	   ->showWSDL();
		}
		else
		{
			$server->showUses();
		}
    }