<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstExpression.php';

/**
 * Represents a function
 */
class XmlImportAstMath extends XmlImportAstExpression
{
  /**
   * Function arguments
   *
   * @var array
   */
  private $arguments = array();

  /**
   * Creates new instance
   *
   * @param string $name
   */
  public function  __construct($name)
  {
    $this->name = $name;
  }

  /**
   * Adds argument to a function
   *
   * @param XmlImportAstExpression $argument
   */
  public function addArgument(XmlImportAstExpression $argument)
  {
    $this->arguments[] = $argument;
  }

  /**
   * Gets function arguments
   *
   * @return array
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * String representation of a function
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    foreach ($this->getArguments() as $argument)
    {
      $array = explode("\n", $argument);
      for ($i = 0; $i < count($array); $i++)
      {
        $array[$i] = '  ' . $array[$i];
      }
      $result .= implode("\n", $array) . "\n";
    }

    $result .= "--> end " . get_class($this);

    return $result;
  }
}