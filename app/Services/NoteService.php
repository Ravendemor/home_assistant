<?php

namespace App\Services;

use App\DataTransferObjects\RepositoryQueryParams;
use App\DataTransferObjects\ServiceResponse;
use App\Models\Note;
use App\Repositories\NoteRepository;
use App\StateMachines\NoteStatus;
use Asantibanez\LaravelEloquentStateMachines\Exceptions\TransitionNotAllowedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NoteService extends Service
{
    public function __construct(private readonly NoteRepository $noteRepository, private readonly UserService $userService)
    {
    }

    public function add(array $noteData): ServiceResponse
    {
        $note = new Note();
        try {
            Note::query()->create($note->getFillableArrayValues(array_merge($noteData, ['user_id' => $this->userService->getCurrentUser()->id])));
        } catch(\Throwable) {
            return $this->makeErrorResponse('Не удалось добавить запись');
        }

        return $this->makeServiceResponse();
    }

    public function update(array $noteData): ServiceResponse
    {
        if((!$note = Note::whereId($noteData['id'])->first()) ||
            !$this->userService->getCurrentUser()->can('update', [Note::class, $note])) {
            return $this->makeErrorResponse('Запись не найдена');
        }

        try {
            $note->update($note->getFillableArrayValues($noteData));
        } catch(\Throwable) {
            return $this->makeErrorResponse('Не удалось изменить запись');
        }

        return $this->makeServiceResponse();
    }

    public function get(array $params): ServiceResponse
    {
        if(isset($params['id']) && is_int($params['id'])) {
            return $this->getNote($params['id']);
        }

        return $this->getNotes($params);
    }

    public function getRandom(): ServiceResponse
    {
        try {
            $note = Note::whereUserId($this->userService->getCurrentUser()->id)
                ->whereStatus(NoteStatus::STATUS_CREATED)
                ->inRandomOrder()
                ->firstOrFail();
            return $this->makeServiceResponse($note->toArray());
        } catch(ModelNotFoundException) {
            return $this->makeErrorResponse('Непрочитанные записи не найдены');
        }
    }

    public function changeStatus(int $id, string $status): ServiceResponse
    {
        if(
            (!$note = Note::whereId($id)->first()) ||
            !$this->userService->getCurrentUser()->can('update', [Note::class, $note])
        ) {
            return $this->makeErrorResponse('Запись не найдена');
        }

        try {
            $note->status()->transitionTo($status);
        } catch(TransitionNotAllowedException) {
            return $this->makeErrorResponse('Изменение статуса недоступно');
        }

        return $this->makeServiceResponse();
    }

    private function getNote(int $id): ServiceResponse
    {
        try {
            return $this->makeServiceResponse(Note::whereId($id)->whereUserId($this->userService->getCurrentUser()->id)->firstOrFail()->toArray());
        } catch(ModelNotFoundException) {
            return $this->makeErrorResponse('Запись не найдена');
        }
    }

    private function getNotes(array $params): ServiceResponse
    {
        $repositoryParams = new RepositoryQueryParams($params);
        $repositoryResponse = $this->noteRepository->read($repositoryParams, true);

        return $this->makeServiceResponse($repositoryResponse->data, $repositoryResponse->success);
    }
}
