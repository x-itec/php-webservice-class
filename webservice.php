<?php
/**
 *  File Definition
 *  Web service Module
 *  Version: 0.1.0
 *  
 *  Library
 *  - PHP Web service Class
 *  
 *  Requirements
 *  - PHP Version 5 or More
 *  - PHP DOM XML 
 *  
 *  Further Development
 *  - This class cannot handle complex/Composite data type definition
 *  - So Far this class handle all common data type like "string","Int","Float","Double"
 *  - Any Unknown data type considers as string
 *  
 *  Tips
 *  - You might you JSON to Transfer/Receive complex Data Type like Array
 *  
 *  Help:
 *  http://www.reazulk.wordpress.com
 *  
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
 *  
 */

/**
 * PHP Web service Class
 * 
 * @author rubel
 */
class Webservice extends Objects
{
	/**
	 * WSDL Cache file Extension
	 */
	public $ext = ".xml";

	/**
	 * __construct()
	 *
	 * @param string $class_name
	 * @param string $service_name
	 **/
	public function __construct(){}
	
	/**
	 * Retrun Service Url
	 * 
	 * @return string
	 */
	public function serviceUrl()
	{
			return $this->getserviceUrl() . "?classname={$this->getClassName()}&wsdl";
	}
	
	/**
	 * Create WSDL Cache Path
	 */
	private function wsdlCachePath()
	{
		return $this->getWsdlCachePath() . $this->getClassName() . $this->ext;
	}
	
	/**
	 * createWSDL Definition
	 *
	 * @return string
	 **/
	public function createWSDL() 
    {
		// Raise Exception is no service available
        if (!$this->getServiceName()) 
        {
			throw new Exception('No service name.');
		}

        $dom = new DOMDocument ( '1.0' );
        
        // Create WSDL Header
        $definition = $dom->createElement ( 'definitions' );
        $definition = $dom->appendChild ( $definition );
        $definition->setAttribute ( 'name', "{$this->getServiceName()}_{$this->getClassName()}");
        $definition->setAttribute ( 'targetNamespace', "urn:{$this->getServiceName()}_{$this->getClassName()}");
        $definition->setAttribute ( 'xmlns:wsdl', "http://schemas.xmlsoap.org/wsdl/");
        $definition->setAttribute ( 'xmlns:soap', "http://schemas.xmlsoap.org/wsdl/soap/");
        $definition->setAttribute ( 'xmlns:tns', "urn:" . $this->getServiceName());
        $definition->setAttribute ( 'xmlns:xsd', "http://www.w3.org/2001/XMLSchema");
        $definition->setAttribute ( 'xmlns:SOAP-ENC', "http://schemas.xmlsoap.org/soap/encoding/");
        $definition->setAttribute ( 'xmlns', "http://schemas.xmlsoap.org/wsdl/");    
            
        $type = $dom->createElement ( 'types' );
        $type = $definition->appendChild ( $type );
        $type->setAttribute ( 'xmlns', "http://schemas.xmlsoap.org/wsdl/");     

		// Validated Class Name
        if (!$this->getClassName())
        {
			throw new Exception('No class name.');
		}

        // Get Class Definition from Reflection API
		$class = new ReflectionClass($this->getClassName());

        // Check if the class Instantiable
		if (!$class->isInstantiable()) 
        {
			throw new Exception('Class is not instantiable.');
		}

		$methods = $class->getMethods();
	
		// Create Port
		$PortType = $dom->createElement ( 'portType' );
        $PortType->setAttribute ( 'name', "{$this->getServiceName()}_{$this->getClassName()}Port");        
		
		// Binding
		$Binding = $dom->createElement ( 'binding' );
        $Binding->setAttribute ( 'name', "{$this->getServiceName()}_{$this->getClassName()}Binding");
        $Binding->setAttribute ( 'type', "tns:{$this->getServiceName()}_{$this->getClassName()}Port");
        
        $soap = $dom->createElement ( 'soap:binding' );
        $soap = $Binding->appendChild ( $soap );
        $soap->setAttribute ( 'style', "rpc");
        $soap->setAttribute ( 'transport', "http://schemas.xmlsoap.org/soap/http");

		// Service
		$Service = $dom->createElement ( 'service' );
        $Service->setAttribute ( 'name', "{$this->getServiceName()}_{$this->getClassName()}");        

		$documentation = $dom->createElement ( 'documentation' );
     	$documentation = $Service->appendChild ( $documentation );
        
        $port = $dom->createElement ( 'port' );
        $port = $Service->appendChild ( $port );
        $port->setAttribute ( 'name', "{$this->getServiceName()}_{$this->getClassName()}Port");        
        $port->setAttribute ( 'binding', "tns:{$this->getServiceName()}_{$this->getClassName()}Binding");
		
        $soap = $dom->createElement ( 'soap:address' );
        $soap = $port->appendChild ( $soap );
        $soap->setAttribute ( 'location', $this->serviceUrl(true));        
	
		foreach ($methods as $method) 
        {
			if ($method->isPublic() && !$method->isConstructor()) 
            {
				$operation= $dom->createElement ( 'operation' );
				$operation = $PortType->appendChild ( $operation );
				$operation->setAttribute ( 'name', $method->getName());
				
				$input= $dom->createElement ( 'input' );
				$input = $operation->appendChild ( $input );
				$input->setAttribute ( 'message', "tns:" . $method->getName() . "Request");
				
				$output= $dom->createElement ( 'output' );
				$output = $operation->appendChild ( $output );
				$output->setAttribute ( 'message', "tns:". $method->getName() . "Response");				
				
            	// Binding
            	$operation= $dom->createElement ( 'operation' );
				$operation = $Binding->appendChild ( $operation );
				$operation->setAttribute ( 'name', $method->getName());
				
				$soap= $dom->createElement ( 'soap:operation' );
				$soap = $operation->appendChild ( $soap );
				$soap->setAttribute ( 'soapAction', "urn:{$this->getServiceName()}_{$this->getClassName()}".'#'.$this->getClassName().'#'.$method->getName());
				
				$input= $dom->createElement ( 'input' );
				$input = $operation->appendChild ( $input );
				
				$soap= $dom->createElement ( 'soap:body' );
				$soap = $input->appendChild ( $soap );
				$soap->setAttribute ( 'use', "encoded");
				$soap->setAttribute ( 'namespace', "urn:{$this->getServiceName()}_{$this->getClassName()}");
				$soap->setAttribute ( 'encodingStyle', "http://schemas.xmlsoap.org/soap/encoding/");

				$output= $dom->createElement ( 'output' );
				$output = $operation->appendChild ( $output );
				
				$soap= $dom->createElement ( 'soap:body' );
				$soap = $output->appendChild ( $soap );
				$soap->setAttribute ( 'use', "encoded");
				$soap->setAttribute ( 'namespace', "urn:{$this->getServiceName()}_{$this->getClassName()}");
				$soap->setAttribute ( 'encodingStyle', "http://schemas.xmlsoap.org/soap/encoding/");	
				
			    
				$Message= $dom->createElement ( 'message' );
        		$Message->setAttribute ( 'name', $method->getName()."Request");
        
				$parameters = $method->getParameters();

				foreach ($parameters as $parameter) 
                {
					if ($method->getDocComment()) 
                    {
						$pattern = '/@param\s+(string|boolean|int|integer|float|double)/i';
						preg_match($pattern, $method->getDocComment(), $matches);
						$type = $matches[1];
					}
					else 
                    {
						$type = 'string';
					}
					
					$part= $dom->createElement ( 'part' );
					$part = $Message->appendChild ( $part );
					$part->setAttribute ( 'name', $parameter->getName());
					$part->setAttribute ( 'type', "xsd:{$type}");
				}
				
				$definition->appendChild ( $Message );
				
				if ($method->getDocComment()) 
                {
					$pattern = '/@return\s+(string|boolean|int|integer|float|double)/i';
					preg_match($pattern, $method->getDocComment(), $matches);
					$return = $matches[1];
				}
				else 
                {
					$return = 'string';
				}

				$Message= $dom->createElement ( 'message' );
        		$Message->setAttribute ( 'name', $method->getName()."Response");
        		
        		$part= $dom->createElement ( 'part' );
				$part = $Message->appendChild ( $part );
				$part->setAttribute ( 'name', $method->getName());
				$part->setAttribute ( 'type', "xsd:{$return}");
				$definition->appendChild ( $Message );
			}
		}
		
		$definition->appendChild ( $PortType );
		$definition->appendChild ( $Binding );
		$definition->appendChild ( $Service );
		
		$xml = $dom->save ($this->wsdlCachePath());
	}
	
	/**
	 * Display WSDL Defination
	 * 
	 */
	public function showWSDL()
	{
		if(!file_exists($this->wsdlCachePath()))
		{
		 	$this->createWSDL();
		}
		
		header('Content-type: text/xml');
		readfile($this->wsdlCachePath());
		exit;
	}

	/**
	 * Load Soap Server
	 * 
	 * @return null;
	 */
	public function loadServer()
	{
		$Server = new SoapServer($this->serviceUrl());
		$Server->setClass($this->getClassName());
		$Server->handle();
	}
	
	/**
	 * Show WSDL URI
	 *
	 * @return string
	 */
	public function showUses() 
    {
        $dom = new DOMDocument ( '1.0' );
        $ref = $dom->createElement ( 'app:Uri' );
        $ref = $dom->appendChild ( $ref );
        $ref->setAttribute ( 'xmlns:app', "http://schemas.xmlsoap.org/app/");
        $ref->setAttribute ( 'xmlns:scl', "http://schemas.xmlsoap.org/app/scl/");

        $scl = $dom->createElement ( 'scl:Example' );
        $scl = $ref->appendChild ( $scl );
        $scl->setAttribute ( 'ref', "{$this->serviceUrl()}?classname=Test&wsdl");
		
        $message = $dom->createElement ( 'scl:Message' );
        $message = $ref->appendChild ( $message );
        $message->setAttribute ( 'ref', "No Class specified!");
        
        header('Content-type:text/xml');
        echo $dom->saveXML();
        exit;
	}
}


/**
 * An External Class to handle Magic functions
 * 
 * @author rubel
 */
class Objects
{
    protected $__data = array();

    /**
     *
     *  @param $method string
     *  @param $params array
     *  @return Objects
     */	
    public function __call($method, $params) 
    {
        $this->fetchtype = isset($this->fetchtype) ? $this->fetchtype : null;

        if( substr($method, 0, 3) == 'get')
        {
            $key = $this->method2_var_name(substr($method,3));
            return isset($this->__data[$key]) ? $this->__data[$key] : null;
        }
        else if ( substr($method, 0, 3) == 'set')
        {
            $key = $this->method2_var_name(substr($method,3));
            $this->__data[$key] = isset($params[0]) ? $params[0] : null;

            return $this;
        }
    }  

    private function method2_var_name( $method = "")
    {
        return strtolower($method);
    }
}
