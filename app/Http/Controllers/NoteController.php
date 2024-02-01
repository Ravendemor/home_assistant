<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\ChangeNoteStatusRequest;
use App\Http\Requests\Note\GetNoteRequest;
use App\Http\Requests\Note\ListRequest;
use App\Http\Requests\Note\NewNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Services\NoteService;
use App\StateMachines\NoteStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(private readonly NoteService $noteService)
    {
    }

    public function addNote(NewNoteRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->add($request->all()));
    }

    public function updateNote(UpdateNoteRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->update($request->all()));
    }

    public function getNote(GetNoteRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->get($request->all()));
    }

    public function getNotes(ListRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->get($request->all()));
    }

    public function getRandomNote(): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->getRandom());
    }

    public function readNote(ChangeNoteStatusRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->changeStatus($request->id, NoteStatus::STATUS_READ));
    }

    public function finishNote(ChangeNoteStatusRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->changeStatus($request->id, NoteStatus::STATUS_FINISHED));
    }

    public function returnNoteToNotRead(ChangeNoteStatusRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->noteService->changeStatus($request->id, NoteStatus::STATUS_CREATED));
    }
}
