<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'city',
        'state',
        'country',
        'team_id',
    ];

    protected $casts = [
        'team_id' => 'integer',
    ];

    /** @return BelongsTo<Team, Member> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsToMany<Project> */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_member', 'member_id', 'project_id')
            ->withTimestamps();
    }
}
