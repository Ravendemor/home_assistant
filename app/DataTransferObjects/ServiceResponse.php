<?php

namespace App\DataTransferObjects;

class ServiceResponse
{
    public function __construct(public array $data = [], public bool $success = true)
    {
    }
}
