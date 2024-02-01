<?php

namespace App\Repositories;

use App\DataTransferObjects\RepositoryQueryParams;
use App\DataTransferObjects\RepositoryResponse;
use App\Traits\IsRepository;
use Illuminate\Database\QueryException;

class Repository
{
    use IsRepository;

    protected const MODEL_CLASS = null;

    /**
     * Получает сущности согласно переданным параметрам и возвращает их
     *
     * @param RepositoryQueryParams $params
     * @param callable|null $callback
     * @return RepositoryResponse
     */
    public function read(RepositoryQueryParams $params, bool $forUser = false, callable $callback = null): RepositoryResponse
    {
        try {
            $query = $this->getQuery();
            if($forUser && $user = app('request')->user()) {
                $params->filter['user_id'] = $user->id;
            }
            $this->setQueryParams($params, $query);

            $list = $query->get();

            if (!$list->count()) {
                return new RepositoryResponse('Результатов  не найдено', false);
            }
            if ($callback) {
                $result['items'] = $callback($list);
            } else {
                $result['items'] = $list->toArray();
            }
            if ($params->pagination) {
                $result['pagination'] = [
                    'page' => $this->paginator->currentPage(),
                    'pages' => ceil($this->paginator->total() / $this->paginator->perPage()),
                    'total' => $this->paginator->total()
                ];
            }
        } catch (QueryException) {
            return new RepositoryResponse('Некорректные параметры запроса', false);
        } catch (\Throwable) {
            return new RepositoryResponse('Не удалось обработать результаты', false);
        }

        return new RepositoryResponse($result);
    }
}
