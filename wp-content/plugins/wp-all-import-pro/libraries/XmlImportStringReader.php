<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package General
 */

require_once dirname(__FILE__) . '/XmlImportReaderInterface.php';

/**
 * Allows to either peek or read a character from a string buffer
 */
class XmlImportStringReader implements XmlImportReaderInterface
{
  /**
   * String buffer
   *
   * @var string
   */
  private $buffer;

  /**
   * Current index
   *
   * @var int
   */
  private $index = -1;

  /**
   * Creates new instance
   *
   * @param string $input
   */
  public function __construct($input)
  {
    if (is_string($input))
      $this->buffer = $input;
    else
      throw new InvalidArgumentException("String expected as argument.");
  }

  /**
   * Returns the next symbol from the buffer without changes to current index
   * or false if buffer ends
   *
   * @return string
   */
  public function peek()
  {
    if ($this->index + 1 >= strlen($this->buffer))
      return false;
    else
      return $this->buffer[$this->index + 1];
  }

  /**
   * Returns the next symbol from the buffer or false if buffer is ended
   *
   * @return string
   */
  public function read()
  {
    $result = $this->peek();
    if ($this->index < strlen($this->buffer))
      $this->index++;
    return $result;
  }
}