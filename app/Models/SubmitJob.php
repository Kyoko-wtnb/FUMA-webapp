<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitJob extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'SubmitJobs';
    // Custom primary key
    protected $primaryKey = 'jobID';

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
