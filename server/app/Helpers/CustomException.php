<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;

class CustomException extends ValidationException
{

  /**
   * Create a new exception instance.
   *
   * @param  \Illuminate\Contracts\Validation\Validator  $validator
   * @param  \Symfony\Component\HttpFoundation\Response|null  $response
   * @param  string  $errorBag
   * @return void
   */
  public function __construct($validator, $response = null, $errorBag = 'default', $statusCode = 422)
  {
      parent::__construct('The given data was invalid.');

      $this->response = $response;
      $this->errorBag = $errorBag;
      $this->validator = $validator;
      $this->status_code = $statusCode;
  }

  /**
   * Create a new validation exception from a plain string of message.
   *
   * @param  string  $field
   * @param  string  $message
   * @return static
   */
  public static function withMessage(string $fieldName = null, string $message, $statusCode = 422)
  {
      return new static(tap(ValidatorFacade::make([], []), function ($validator) use ($fieldName, $message) {
        $validator->errors()->add($fieldName, $message);
      }), null, 'default', $statusCode);
  }

  /**
   * Get first message from the validation error messages.
   *
   * @return string
   */
  public function first_error()
  {
      return Arr::first(Arr::first($this->errors()));
  }

  /**
   * Get status code.
   *
   * @return string
   */
  public function status_code()
  {
      return $this->status_code;
  }
}
