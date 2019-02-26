<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstStatement.php';
require_once dirname(__FILE__) . '/XmlImportAstElseif.php';

/**
 * Represents an IF clause
 */
class XmlImportAstIf extends XmlImportAstStatement
{
  /**
   * Condition
   *
   * @var XmlImportAstExpression
   */
  private $condition;

  /**
   * If body
   *
   * @var XmlImportAstSequence
   */
  private $ifBody;

  /**
   * List of elseif subclauses
   *
   * @var array
   */
  private $elseIfs = array();

  /**
   * Else body
   *
   * @var XmlImportAstSequence
   */
  private $elseBody;

  /**
   * Creates new instance
   *
   * @param XmlImportAstExpression $condition
   */
  public function __construct(XmlImportAstExpression $condition)
  {
    $this->condition = $condition;
  }

  /**
   * Gets condition
   *
   * @return XmlImportAstExpression
   */
  public function getCondition()
  {
    return $this->condition;
  }

  /**
   * Gets if body
   *
   * @return XmlImportAstSequence
   */
  public function getIfBody()
  {
    return $this->ifBody;
  }

  /**
   * Adds If body
   *
   * @param XmlImportAstSequence $body
   */
  public function addIfBody(XmlImportAstSequence $body)
  {
    $this->ifBody = $body;
  }

  /**
   * Gets list of elseif subcloses
   *
   * @return array
   */
  public function getElseIfs()
  {
    return $this->elseIfs;
  }

  /**
   * Gets else body
   *
   * @return XmlImportAstSequence
   */
  public function getElseBody()
  {
    return $this->elseBody;
  }

  /**
   * Adds else body
   *
   * @param XmlImportAstSequence $body
   */
  public function addElseBody(XmlImportAstSequence $body)
  {
    $this->elseBody = $body;
  }

  /**
   * Adds elseif subclause
   *
   * @param XmlImportAstElseif $elseif
   */
  public function addElseif(XmlImportAstElseif $elseif)
  {
    $this->elseIfs[] = $elseif;
  }

  /**
   * String represetation of an IF clause
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    $result .= '  Condition: ' . $this->condition . "\n";
    $result .= "  Body:\n";
    $array = explode("\n", $this->ifBody);
    for ($i = 0; $i < count($array); $i++)
    {
      $array[$i] = '    ' . $array[$i];
    }
    $result .= implode("\n", $array) . "\n";
    foreach($this->elseIfs as $elseIf)
    {
      $array = explode("\n", $elseIf);
      for ($i = 0; $i < count($array); $i++)
      {
        $array[$i] = '' . $array[$i];
      }
      $result .= implode("\n", $array) . "\n";
    }
    if (!is_null($this->elseBody))
    {
      $result .= "  Else:\n";
      $array = explode("\n", $this->elseBody);
      for ($i = 0; $i < count($array); $i++)
      {
        $array[$i] = '    ' . $array[$i];
      }
      $result .= implode("\n", $array) . "\n";
    }
    $result .= "--> end " . get_class($this) . "\n";
    return $result;
  }
}
