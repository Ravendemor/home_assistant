<?php

namespace App\Traits;

trait IsAdvancedModel
{
    public function getFillableArrayValues(array $data): array
    {
        return $this->fillableFromArray($data);
    }
}
