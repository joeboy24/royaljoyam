<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTransfer extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'from_branch',
        'to_branch',
        'qty',
        'notes',
        'del',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeReportSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($builder) use ($like) {
            $builder->where('notes', 'like', $like)
                ->orWhereHas('item', function ($itemQuery) use ($like) {
                    $itemQuery->where('name', 'like', $like)
                        ->orWhere('item_no', 'like', $like);
                })
                ->orWhereHas('user', function ($userQuery) use ($like) {
                    $userQuery->where('name', 'like', $like);
                });
        });
    }
}
