<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponse;

class Service
{
    protected function makeServiceResponse(array|string $data = [], bool $success = true): ServiceResponse
    {
        if(is_string($data)) {
            $data = [
                'response' => $data
            ];
        }

        return new ServiceResponse($data, $success);
    }

    protected function makeErrorResponse(array|string $errors = []): ServiceResponse
    {
        return $this->makeServiceResponse($errors, false);
    }
}
