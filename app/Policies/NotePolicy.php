<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function read(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }
}
