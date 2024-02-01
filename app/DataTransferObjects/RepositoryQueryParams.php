<?php

namespace App\DataTransferObjects;

class RepositoryQueryParams
{
    public array $filter = [];
    public array $sort = [];
    public array $pagination = [];
    public function __construct(array $params)
    {
        if(isset($params['filter']) && is_array($params['filter'])) {
            $this->filter = $params['filter'];
        }
        if(isset($params['sort']) && is_array($params['sort'])) {
            $this->sort = $params['sort'];
        }
        if(isset($params['pagination']) && is_array($params['pagination'])) {
            $this->pagination = $params['pagination'];
        }
    }
}
