<?php

namespace fuma;

use Illuminate\Database\Eloquent\Model;

class JobMonitor extends Model
{
    // Custom table name
    protected $table = 'JobMonitor';
    // Custom primary key
    protected $primaryKey = 'jobID';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
