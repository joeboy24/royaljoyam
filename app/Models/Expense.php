<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'companybranch_id',
        'title',
        'desc',
        'expense_cost',
        'del',
    ];

    public function companybranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class, 'companybranch_id');
    }
}
