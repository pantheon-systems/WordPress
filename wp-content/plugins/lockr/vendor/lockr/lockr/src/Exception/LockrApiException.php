<?php
namespace Lockr\Exception;

use Exception;

class LockrApiException extends Exception
{
    /** @var array $errors */
    private $errors;

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
        $msg = $this->buildMessage();
        $code = $this->divineCode();
        parent::__construct($msg, $code);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function buildMessage()
    {
        if (!$this->errors) {
            return '';
        }
        $err = $this->errors[0];
        $msg = $err['message'];
        if (count($this->errors) > 1) {
            $extra = count($this->errors) - 1;
            $msg .= " (and {$extra} more)";
        }
        return $msg;
    }

    public function divineCode()
    {
        foreach ($this->errors as $err) {
            if (!empty($err['extensions']['status_code'])) {
                return (int) $err['extensions']['status_code'];
            }
        }
        return 0;
    }
}

// ex: ts=4 sts=4 sw=4 et:
