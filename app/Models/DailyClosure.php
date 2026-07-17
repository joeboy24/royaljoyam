<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scope_key',
        'branch_label',
        'close_date',
        'cash',
        'cheque',
        'momo',
        'debt_sold',
        'collected_debt',
        'expenses',
        'gross_collected',
        'net_total',
        'counted_cash',
        'variance',
        'notes',
        'status',
        'del',
    ];
}
