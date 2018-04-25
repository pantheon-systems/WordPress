<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

/**
 * Represents ELSEIF branch of IF clause
 */
class XmlImportAstElseif
{
  /**
   * ELSEIF branch condition
   *
   * @var XmlImportAstExpression
   */
  private $condition;

  /**
   * ELSEIF branch body
   *
   * @var XmlImportAstSequence
   */
  private $body;

  /**
   * Creates new instance
   *
   * @param XmlImportAstExpression $condition
   * @param XmlImportAstSequence $body
   */
  public function __construct(XmlImportAstExpression $condition, XmlImportAstSequence $body)
  {
    $this->condition = $condition;
    $this->body = $body;
  }

  /**
   * Gets ELSEIF branch condition
   *
   * @return XmlImportAstExpression
   */
  public function getCondition()
  {
    return $this->condition;
  }

  /**
   * Gets body
   *
   * @return XmlImportAstSequence
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * String representation of an object
   *
   * @return string 
   */
  public function __toString()
  {
    $result = "  Elseif:\n";
    $result .= '  Condition: ' . $this->condition . "\n";
    $result .= "  Body:\n";
    $array = explode("\n", $this->body);
    for ($i = 0; $i < count($array); $i++)
    {
      $array[$i] = '    ' . $array[$i];
    }
    $result .= implode("\n", $array) . "\n";
    return $result;
  }
}
