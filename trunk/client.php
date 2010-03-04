<?php 
/**
 *  File Definition
 *  Example Client Request 
 *  
 *  Help:
 *  http://www.reazulk.wordpress.com
 *  
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  
 */

include("cryptography.php");

// Service URL 
$url = "http://" . $_SERVER['SERVER_NAME'] .dirname($_SERVER["PHP_SELF"]) . "/server.php?classname=Test&wsdl";

// Create Client
$client = new SoapClient($url);

// Display Function List
$fxs = $client->__getFunctions();
pre($fxs);

// Test one 
$test_response = $client->myTestMethod("Test Param");
pre($test_response);

// Test Time
$test_response = $client->getMyTime();
pre($test_response);

$crypt = new Cryptography();
$test_response = $client->getInfo(1);
$data = $crypt->jsonDecode($test_response);
pre($data);


$arr = Array('language' =>'PHP','Type'=>'Webservice','method'=>'Soap');
$test_response = $client->postInfo($crypt->jsonEncode($arr));
pre($test_response);




// Simple Debug
function pre( $v )
{
    echo "<pre>";
    print_r($v);
    echo "</pre>";
}