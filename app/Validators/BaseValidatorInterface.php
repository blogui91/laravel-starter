<?php
namespace App\Validators;

/**
 * Interface BaseValidatorInterface
 * @package App\Validator
 */
interface BaseValidatorInterface
{
    public function setValidator($validator);

    public function rulesForUpdate();

    public function rulesForCreate();

    public function messages();

    public function setValidationRules($type = 'create');

    public function validate($data);
}
