<?php
namespace App\Validators;

use Illuminate\Support\MessageBag;
use Validator;

class BaseValidator
{
    protected $rules = [];
    protected $validator;

    public function setValidator($validator)
    {
        $this->validator = new $validator;
    }

    public function rulesForUpdate()
    {
        return [];
    }

    public function rulesForCreate()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }

    public function setValidationRules($type = 'create')
    {
        $this->rules = $this->{"rulesFor$type"};
        return $this;
    }

    public function validate($data)
    {
        $this->validator = Validator::make($data, $this->rules, $this->messages());
    }
}
