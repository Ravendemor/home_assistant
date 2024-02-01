<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\ServiceResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function makeResponse(array|string $data = [], bool $success = true): JsonResponse
    {
        $response['success'] = $success;
        if(!empty($data)) {
            $response['response'] = $data;
        }
        return new JsonResponse($response, $success ? 200 : 400);
    }

    protected function provideServiceResponse(ServiceResponse $response): JsonResponse
    {
        return $this->makeResponse($response->data, $response->success);
    }
}
