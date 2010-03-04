<?php 
/**
 *  File Definition
 *  Test Class File
 *  
 *  Help:
 *  http://www.reazulk.wordpress.com
 *  
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  
 */

/**
 * Test Class File
 * 
 * @author rubel
 */
class Test
{
	/**
	 * Diclare Constractor
	 */
	public function __construct(){}
	
	/**
	 * A Method Return String Type data
	 * 
	 * @param string $arg1
	 * @return string;
	 */
	public function myTestMethod($arg1=NULL)
	{
		return isset($arg1) ? "Result:{$arg1}" : "Result: No param received";
	}

    /**
	 * Test Method return time
	 * 
	 * @return int;
	 */
	public function getMyTime()
	{
		return time();
	}

    /**
     * Process Array in JSON format
     *
	 * @param string $id
	 * @return string;
     */
    public function getInfo($id)
    {
        $arr = Array('language' =>'PHP','Type'=>'Webservice','method'=>'Soap');

        $crypt = new Cryptography();

        return  $crypt->jsonEncode($arr);
    }

    /**
     * Process Array in JSON format
     *
	 * @param string $data
	 * @return boolean;
     */
    public function postInfo($info)
    {
        $crypt = new Cryptography();
        $data = $crypt->jsonDecode($info);

        return  is_array($data);
    }
}