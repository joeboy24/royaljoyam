<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    //
    public function expense(){
        return $this->HasMany('App\Models\Expense');
    }
}
