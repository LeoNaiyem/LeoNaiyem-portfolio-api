<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['year', 'role', 'company', 'description', 'sort_order'])]
class Experience extends Model
{
    use HasFactory, HasSortOrder, RevalidatesFrontend;
}
