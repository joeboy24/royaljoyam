<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Closure extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'month', 'tot_qty', 'avl_qty', 'amt_sold', 'exp_amt', 'profits', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'status'];

    // public function student(){
    //     return $this->belongsTo('App\Models\Student');
    // }
}
