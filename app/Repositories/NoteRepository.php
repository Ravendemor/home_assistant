<?php

namespace App\Repositories;

use App\Models\Note;
use App\Repositories\Repository;

class NoteRepository extends Repository
{
    protected const MODEL_CLASS = Note::class;
}
