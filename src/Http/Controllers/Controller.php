<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Local\CMS\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Local\CMS\Traits\Helpers;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

    public function __construct()
    {
        app()->singleton(
            ExceptionHandler::class,
            Handler::class
        );
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $messages = $this->__getFormattedMessages($messages);
        $customAttributes = $this->__getFormattedAttributes($rules);

        return $this->getValidationFactory()->make(
            $request->all(),
            $rules,
            $messages,
            $customAttributes
        )->validate();
    }
}