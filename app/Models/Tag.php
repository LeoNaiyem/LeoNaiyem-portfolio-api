<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'sort_order'])]
class Tag extends Model
{
    use HasFactory, HasSortOrder, RevalidatesFrontend;

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function revalidationTags(): array
    {
        return ['tags', 'projects'];
    }
}
