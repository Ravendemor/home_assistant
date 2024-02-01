<?php

namespace App\StateMachines;

use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

class NoteStatus extends StateMachine
{
    public const STATUS_CREATED = 'created';
    public const STATUS_READ = 'read';
    public const STATUS_FINISHED = 'finished';

    public const STATES = [
        self::STATUS_CREATED,
        self::STATUS_READ,
        self::STATUS_FINISHED
    ];
    public function recordHistory(): bool
    {
        return true;
    }

    public function transitions(): array
    {
        return [
            static::STATUS_CREATED => [static::STATUS_READ, static::STATUS_FINISHED],
            static::STATUS_READ => [static::STATUS_CREATED, static::STATUS_FINISHED],
            static::STATUS_FINISHED => []
        ];
    }

    public function defaultState(): ?string
    {
        return static::STATUS_CREATED;
    }
}
