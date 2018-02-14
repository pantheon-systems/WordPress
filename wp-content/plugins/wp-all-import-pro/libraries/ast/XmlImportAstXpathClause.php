<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstStatement.php';
require_once dirname(__FILE__) . '/XmlImportAstXPath.php';
require_once dirname(__FILE__) . '/XmlImportAstSequence.php';

/**
 * Represents a clause that is based on XPath
 */
abstract class XmlImportAstXPathClause extends XmlImportAstStatement
{
  /**
   * XPath expression
   *
   * @var XmlImportAstXPath
   */
  private $xpath;

  /**
   * Clause body
   *
   * @var XmlImportAstSequence
   */
  private $body;

  /**
   * Creates new instance
   *
   * @param XmlImportAstXPath $xpath
   * @param XmlImportAstSequence $body
   */
  public function __construct(XmlImportAstXPath $xpath, XmlImportAstSequence $body)
  {
    $this->xpath = $xpath;
    $this->body = $body;
  }

  /**
   * Gets XPath
   *
   * @return XmlImportAstXPath
   */
  public function getXpath()
  {
    return $this->xpath;
  }

  /**
   * Gets clause body
   *
   * @return XmlImportAstSequence
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * String representation of a clause
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    $result .= '  XPath: ' . $this->xpath . "\n";
    $result .= "  Body:\n";

    $array = explode("\n", $this->body);
    for ($i = 0; $i < count($array); $i++)
    {
      $array[$i] = '    ' . $array[$i];
    }

    $result .= implode("\n", $array) . "\n";
    $result .= "--> end " . get_class($this) . "\n";
    return $result;
  }
}
