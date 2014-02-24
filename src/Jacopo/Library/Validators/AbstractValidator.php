<?php namespace Jacopo\Library\Validators;

use Validator as V;
use Event;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $errors;
    /**
     * Validation rules
     * @var array
     */
    protected static $rules;

    public function validate($input)
    {
        Event::fire('validating', [$input]);
        $validator = V::make($input, static::$rules);

        if($validator->fails())
        {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}