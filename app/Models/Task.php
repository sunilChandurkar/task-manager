<?php

namespace App\Models;


use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'is_done', 'creator_id', 'project_id'];

    protected $casts = [
        'is_done' => 'boolean'
    ];

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected static function booted(): void 
    {
        static::addGlobalScope('member', function (Builder $builder) {
            $builder->where('creator_id', Auth::user()->id)
            ->orWhere('project_id', Auth::user()->memberships->pluck('id'));
        });
    }

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class);
    }
}
