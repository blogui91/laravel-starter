<?php
namespace App\Validators\Traits;

trait ValidatorTrait
{
  protected $validator;

  public function getValidator()
  {
    return $this->validator;
  }

  public function setValidator(Validator $validator)
  {
    $this->validator = $validator;
    return $this;
  }
}
