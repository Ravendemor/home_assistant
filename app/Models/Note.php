<?php

namespace App\Models;

use App\StateMachines\NoteStatus;
use App\Traits\IsAdvancedModel;
use Asantibanez\LaravelEloquentStateMachines\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Note
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string $title
 * @property string|null $link
 * @property string|null $comment
 * @property string $status
 * @method static Builder|Note newModelQuery()
 * @method static Builder|Note newQuery()
 * @method static Builder|Note query()
 * @method static Builder|Note whereComment($value)
 * @method static Builder|Note whereCreatedAt($value)
 * @method static Builder|Note whereId($value)
 * @method static Builder|Note whereLink($value)
 * @method static Builder|Note whereStatus($value)
 * @method static Builder|Note whereTitle($value)
 * @method static Builder|Note whereUpdatedAt($value)
 * @method static Builder|Note whereUserId($value)
 * @mixin \Eloquent
 */
class Note extends Model
{
    use HasFactory, HasStateMachines, IsAdvancedModel;

    public $fillable = [
        'title',
        'link',
        'comment',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'user_id'
    ];

    public $stateMachines = [
        'status' => NoteStatus::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
