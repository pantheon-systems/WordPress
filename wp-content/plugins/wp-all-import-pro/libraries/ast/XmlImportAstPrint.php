<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstStatement.php';

/**
 * Represents a PRINT node
 */
class XmlImportAstPrint extends XmlImportAstStatement
{
  /**
   * Expression to print
   *
   * @var XmlImportAstExpression
   */
  private $value;

  /**
   * Creates new instance of a statement
   *
   * @param XmlImportAstExpression $value
   */
  public function __construct(XmlImportAstExpression $value)
  {
    $this->value = $value;
  }

  /**
   * Get value to be printed
   *
   * @return XmlImportAstExpression
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * String representation of a PRINT clause
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    $array = explode("\n", $this->value);
    for ($i = 0; $i < count($array); $i++)
    {
      $array[$i] = '  ' . $array[$i];
    }
    $result .= implode("\n", $array) . "\n";
    
    $result .= "--> end " . get_class($this);

    return $result;
  }
}