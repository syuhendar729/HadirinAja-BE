<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Table('attendances')]
#[Fillable(['user_id', 'status', 'location', 'notes', 'url_image'])]
class Attendance extends Model
{

    
}
