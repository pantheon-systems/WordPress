<?php

class PMXI_ArrayToXML
{
    /**
    * The main function for converting to an XML document.
    * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
    *
    * @param array $data
    * @param string $rootNodeName - what you want the root node to be - defaultsto data.
    * @param SimpleXMLElement $xml - should only be used recursively
    * @return string XML
    */
    public static function toXml($data, $rootNodeName = 'data', $xml=null, $lvl = 0)
    {                

    	$data = apply_filters('wp_all_import_json_to_xml', $data);
     
      if ($xml == null)
      {
          $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><'.$rootNodeName .'/>');
      }

     	if ( !empty($data)){
	        // loop through the data passed in.
	        foreach($data as $key => $value)
	        {
	            // no numeric keys in our xml please!
	            if (!$key or is_numeric($key))
	            {
	                // make string key...
	                $key = "item_" . $lvl;

	            }
	            
	            // replace anything not alpha numeric
	            // preg_replace('/^[0-9]+/i', '', preg_replace('/[^a-z0-9_]/i', '', $key))
	            $key = preg_replace('/[^a-z0-9_]/i', '', $key);

                if ($key && is_numeric($key[0])) $key = 'v' . $key;

	            // if there is another array found recrusively call this function
	            if (is_array($value) or is_object($value))
	            {
	                $node = $xml->addChild($key);
	                // recrusive call.
	                PMXI_ArrayToXML::toXml($value, $rootNodeName, $node, $lvl + 1);
	            }
	            else
	            {                
	                // add single node.
	                $value =  htmlspecialchars(preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $value));
	                $xml->addChild($key, $value);

	            }
	            
	        }
	    }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }


}