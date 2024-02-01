<?php

namespace App\DataTransferObjects;

class RepositoryResponse
{
    public array $data = [];
    public bool $success;
    public function __construct(array|string $data = [], bool $success = true)
    {
        $this->data = is_array($data) ? $data : ['error' => $data];
        $this->success = $success;
    }
}
