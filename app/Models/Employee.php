<?php

namespace App\Models;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','password','designation_id'];
    /**
      * Get the user that owns the Employee
      *
      * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
      */
     public function designation()
     {
         return $this->belongsTo(Designation::class, 'designation_id', 'id');
     }

}
