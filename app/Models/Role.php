<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('roles')]
class Role extends Model
{
    protected $fillable = [
        'id',
        'role_name'
    ];

    public $timestamps = false;
}
