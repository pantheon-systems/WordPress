<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstStatement.php';

/**
 * Represents a text
 */
class XmlImportAstText extends XmlImportAstStatement
{
  /**
   * Value of a text node
   *
   * @var string
   */
  private $value;

  /**
   * Creates new instance
   *
   * @param string $value
   */
  public function __construct($value)
  {
    $this->value = $value;
  }

  /**
   * Gets value of a text node
   *
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * String representation of the node
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    $array = explode("\n", trim($this->getValue()));
    for ($i = 0; $i < count($array); $i++)
    {
      $array[$i] = '  ' . $array[$i];
    }
    $result .= implode("\n", $array) . "\n";
    $result .= "--> end " . get_class($this);

    return $result;
  }
}
