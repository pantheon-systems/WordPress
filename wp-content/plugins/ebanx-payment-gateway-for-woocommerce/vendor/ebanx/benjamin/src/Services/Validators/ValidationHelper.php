<?php
namespace Ebanx\Benjamin\Services\Validators;

class ValidationHelper
{
    /**
     * @var Callable[]
     */
    private $appliedRules = [];

    public function min($minValue)
    {
        $this->appliedRules[] = function ($subjectName, $subjectValue) use ($minValue) {
            if ($subjectValue >= $minValue) {
                return null;
            }

            return sprintf(
                '%s is below minimum of %d',
                $subjectName,
                $minValue
            );
        };

        return $this;
    }

    public function max($maxValue)
    {
        $this->appliedRules[] = function ($subjectName, $subjectValue) use ($maxValue) {
            if ($subjectValue <= $maxValue) {
                return null;
            }

            return sprintf(
                '%s is above maximum of %d',
                $subjectName,
                $maxValue
            );
        };

        return $this;
    }

    public function test($subjectName, $subjectValue)
    {
        $errors = [];

        while ($rule = array_shift($this->appliedRules)) {
            $errors[] = call_user_func_array($rule, [$subjectName, $subjectValue]);
        }

        return array_values(array_filter($errors));
    }
}
