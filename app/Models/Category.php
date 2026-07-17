<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function linkedItemCount(): int
    {
        return Item::where('cat', $this->name)->count();
    }

    public function isInUse(): bool
    {
        return $this->linkedItemCount() > 0;
    }
}
