<?php

namespace App\Traits;

use App\DataTransferObjects\RepositoryQueryParams;
use App\DataTransferObjects\RepositoryResponse;
use App\Entities\ApiResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

trait IsRepository
{
    protected LengthAwarePaginator $paginator;

    protected function getModelClass(): string
    {
        return static::MODEL_CLASS;
    }

    /**
     * Возвращает название класса модели, с которой связан контроллер
     * @return class-string<Model>
     */
    protected function getControllerModelClass(): string
    {
        if (!method_exists(static::class, 'getModelClass') || !is_subclass_of(static::MODEL_CLASS, Model::class)) {
            throw new \BadMethodCallException('Не обнаружена модель');
        }
        return $this->getModelClass();
    }

    /**
     * Возвращает объект билдера запроса для привязанной модели
     * @return Builder
     */
    protected function getQuery(): Builder
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = $this->getControllerModelClass();
        return $modelClass::query();
    }

    /**
     * Извлекает из запроса и добавляет в билдер запроса фильтрацию, сортировку и пагинацию
     *
     * @param Request $request
     * @param Builder $query
     * @return void
     */
    protected function setQueryParams(RepositoryQueryParams $params, Builder $query): void
    {
        $this->setFilter($query, $params->filter);
        $this->setSort($query, $params->sort);
        $this->setPagination($query, $params->pagination);
    }

    /**
     * Устанавливает фильтр запроса
     *
     * @param Builder $query
     * @param array $filter
     * @return void
     */
    protected function setFilter(Builder $query, array $filter): void
    {
        foreach ($filter as $field => $value) {
            if (!is_array($value)) {
                $query->where($field, $value);
            } else {
                $query->whereIn($field, $value);
            }
        }
    }

    /**
     * Устанавливает сортировку запроса
     *
     * @param Builder $query
     * @param array $sort
     * @return void
     */
    protected function setSort(Builder $query, array $sort): void
    {
        if (array_key_exists('field', $sort) && array_key_exists('order', $sort)) {
            $query->orderBy($sort['field'], $sort['order']);
        }
    }

    /**
     * Устанавливает пагинацию запроса
     *
     * @param Builder $query
     * @param array $pagination
     * @return void
     */
    protected function setPagination(Builder $query, array $pagination): void
    {
        if (array_key_exists('limit', $pagination) && array_key_exists('page', $pagination)) {
            $this->paginator = $query->paginate($pagination['limit'], ['*'], 'page', $pagination['page']);
        }
    }

    /**
     * !MODERATED! Создаёт сущность согласно запросу и возвращает её
     *
     * @param Request $request
     * @param callable|null $callback
     * @return ApiResult
     */
    protected function create(Request $request, callable $callback = null): ApiResult
    {
        try {
            /** @var class-string<Model> $modelClass */
            $modelClass = $this->getControllerModelClass();
            $params = $request->toArray();

            /** @var Model $element */
            $element = new $modelClass();
            foreach ($params as $field => $value) {
                if (in_array($field, $element->getFillable())) {
                    $element->{$field} = $value;
                }
            }
            $element->save();
            if($callback) {
                $callback($element, $request);
            }
        } catch (\Throwable $e) {
            return new ApiResult(errors: ['Не удалось создать элемент']);
        }

        return new ApiResult();
    }

    /**
     * !MODERATED! Получает сущности по массиву id в запросе и возвращает их
     *
     * @param Request $request
     * @param callable|null $callback
     * @return ApiResult
     */
    protected function readByIds(Request $request, callable $callback = null): ApiResult
    {
        try {
            $query = $this->getQuery();
            $params = $request->toArray();
            $this->setFilter($query, ['id' => $params['id']]);

            $list = $query->get();

            if ($list->count() == 0) {
                return new ApiResult(errors: ['Результатов по запросу не найдено']);
            }

            if ($callback) {
                $result['items'] = $callback($list);
            } else {
                $result['items'] = $list->toArray();
            }
        } catch (\Throwable $e) {
            return new ApiResult(errors: ['Не удалось обработать результаты']);
        }

        return new ApiResult(data: $result);
    }

    /**
     * !MODERATED! Находит сущность, изменяет её согласно запросу и возвращает
     *
     * @param Request $request
     * @return ApiResult
     */
    protected function update(Request $request): ApiResult
    {
        try {
            $query = $this->getQuery();
            $params = $request->toArray();

            $element = $query->where('id', $params['id'])->firstOrFail();

            foreach ($params as $field => $value) {
                if (in_array($field, $element->getFillable())) {
                    $element->{$field} = $value;
                }
            }
            $element->save();
        } catch (ModelNotFoundException $e) {
            return new ApiResult(errors: ['Элемент не найден']);
        } catch (\Throwable $e) {
            return new ApiResult(errors: ['Не удалось обновить элемент']);
        }

        return new ApiResult(data: $element->toArray());
    }

    /**
     * !MODERATED! Находит сущности согласно запросу и удаляет их. Возвращает сообщение об успехе
     *
     * @param Request $request
     * @return ApiResult
     */
    protected function delete(Request $request): ApiResult
    {
        try {
            $query = $this->getQuery();
            $params = $request->toArray();

            $this->setFilter($query, ['id' => $params['id']]);

            $list = $query->get();

            foreach ($list as $element) {
                $element->delete();
            }
        } catch (\Throwable $e) {
            return new ApiResult(errors: ['Не удалось удалить']);
        }

        return new ApiResult();
    }
}
