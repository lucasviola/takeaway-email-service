<?php


namespace App\Http;


use Illuminate\Support\Facades\Validator;

class MessageRequestValidator
{
    private array $errors;

    public function hasErrors(array $requestBody): bool
    {
        $validator = Validator::make(['request' => $requestBody],
            [
                'request.from' => 'required',
                'request.from.name' => 'required',
                'request.from.email' => 'required',
                'request.to' => 'required',
                'request.to.name' => 'required',
                'request.to.email' => 'required',
                'request.subject' => 'required',
                'request.message' => 'required',
            ],
        );

        $this->errors = $validator->getMessageBag()->toArray();
        return $validator->fails();
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
