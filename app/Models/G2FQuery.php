<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class G2FQuery extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'gene2func';
    // Custom primary key
    protected $primaryKey = 'jobID';

    public $timestamps = false;
}
