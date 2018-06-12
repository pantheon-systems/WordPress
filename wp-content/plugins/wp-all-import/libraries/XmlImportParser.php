<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 * @package General
 */

require_once dirname(__FILE__) . '/XmlImportTemplate.php';

/**
 * Is used to parse XML using specified template and root node
 */
class XmlImportParser {
	/**
	 * SimpleXmlElement instance for the xml file
	 *
	 * @var SimpleXMLElement
	 */
	protected $xml;

	/**
	 * XPath expression for selecting the root node of the record
	 *
	 * @var string
	 */
	protected $rootNodeXPath;

	/**
	 * Path to cached template
	 *
	 * @var string
	 */
	protected $cachedTemplate;

	/**
	 * Creates new parser instance
	 *
	 * @param string $xml
	 * @param string $rootNodeXPath XPath for the record root node
	 * @param string $cachedTemplate path to cached template
	 * @param bool $isURL whether xml is URL or path
	 */
	public function __construct($xml, $rootNodeXPath, $cachedTemplate, $isURL = false)
	{		
		if ($isURL) {
			$xml = file_get_contents($xml);
		}		
		
		// FIX: remove default namespace because libxml xpath implementation doesn't handle it properly
		$xml and $xml = preg_replace('%xmlns\s*=\s*([\'"]).*\1%sU', '', $xml);
	
		libxml_use_internal_errors(true);
		try{ 
			$this->xml = new SimpleXMLElement($xml);
		} catch (Exception $e){ 			
			try{ 
				$this->xml = new SimpleXMLElement(utf8_encode($xml));
			} catch (Exception $e){ 
				throw new XmlImportException($e->getMessage());
			}
		}			

		$this->rootNodeXPath = $rootNodeXPath;
		$this->cachedTemplate = $cachedTemplate;
	}

	/**
	 * Gets the parser results for all records or
	 * the part of them if $start and $count parameters are passed
	 *
	 * @param array[optional] $records Sequence numbers of records to import (first record corresponds to 1)
	 * @return array
	 */
	public function parse($records = array())
	{				
		empty($records) or is_array($records) or $records = array($records);
		
		$result = array();
		
		$rootNodes = $this->xml->xpath($this->rootNodeXPath);		
		if ($rootNodes === false)
		throw new XmlImportException('Invalid root node XPath \'' . $this->rootNodeXPath . '\' specified');
	    		
		for ($i = 0; $i < count($rootNodes); $i++) {
			if (empty($records) or in_array($i + 1, $records)) {                
				$rootNode = apply_filters('wpallimport_xml_row', $rootNodes[$i]);
				$template = new XmlImportTemplate($rootNode, $this->cachedTemplate);
				$result[] = $template->parse();
			}
		}				
		return $result;
	}

	/**
	 * Creates new parser instance for text template specified
	 *
	 * @param string $xml
	 * @param string $rootNodeXPath XPath for the record root node
	 * @param string $template template
	 * @param string &$file path to the cached template
	 * @return XmlImportParser
	 */
	public static function factory($xml, $rootNodeXPath, $template, &$file = NULL)
	{		
	
		$scanner = new XmlImportTemplateScanner();		
		$tokens = $scanner->scan(new XmlImportStringReader($template));				
		$t_parser = new XmlImportTemplateParser($tokens);
		$tree = $t_parser->parse();
		$codegenerator = new XmlImportTemplateCodeGenerator($tree);
		$file = $codegenerator->generate();
				
		return new self($xml, $rootNodeXPath, $file);
	}
}