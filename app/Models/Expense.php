<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    public function companybranch(){
        return $this->belongsTo('App\Models\CompanyBranch');
    }
}
