<?php
namespace Ebanx\Benjamin\Models\Responses;

use Ebanx\Benjamin\Models\BaseModel;

class ErrorResponse extends BaseModel
{
    /**
     * Error code
     *
     * @var string
     */
    protected $code;

    /**
     * Error message
     *
     * @var string
     */
    protected $message;

    /**
     * Returns error code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
