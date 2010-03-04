<?php 
/**
 *  File Definition
 *  Lib Class to manage JSON data
 *  
 *  Help:
 *  http://www.reazulk.wordpress.com
 *  
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  
 */

class Cryptography
{

    public function jsonEncode($a = NULL)
    {
	    if (is_null($a)) return 'null';
	    if ($a === false) return 'false';
	    if ($a === true) return 'true';
	    if (is_scalar($a))
	    {
	      if (is_float($a))
	      {
	        // Always use "." for floats.
	        return floatval(str_replace(",", ".", strval($a)));
	      }
	
	      if (is_string($a))
	      {
	        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
	        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
	      }
	      else
	        return $a;
	    }
	    $isList = true;
	    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	    {
	      if (key($a) !== $i)
	      {
	        $isList = false;
	        break;
	      }
	    }
	    $result = array();
	    if ($isList)
	    {
	      foreach ($a as $v) $result[] = $this->jsonEncode($v);
	      return '[' . join(',', $result) . ']';
	    }
	    else
	    {
	      foreach ($a as $k => $v) $result[] = $this->jsonEncode($k).':'.$this->jsonEncode($v);
	      return '{' . join(',', $result) . '}';
	    }
    }
    
    public function jsonDecode($json)
	{ 
	    // Author: walidator.info 2009
	    $comment = false;
	    $out = '$x=';
	   
	    for ($i=0; $i<strlen($json); $i++)
	    {
	        if (!$comment)
	        {
	            if ($json[$i] == '{')        $out .= ' array(';
	            else if ($json[$i] == '}')    $out .= ')';
	            else if ($json[$i] == ':')    $out .= '=>';
	            else                         $out .= $json[$i];           
	        }
	        else $out .= $json[$i];
	        if ($json[$i] == '"')    $comment = !$comment;
	    }
	    eval($out . ';');
	    return $x;
	}  
}