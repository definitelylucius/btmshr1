<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBankDetails extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'bank_name', 'account_number', 'account_holder'];
}
