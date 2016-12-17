<?php

namespace IPGAP;

use Illuminate\Database\Eloquent\Model;

class SubmitJob extends Model
{
    // Custom table name
    protected $table = 'SubmitJobs';
    // Custom primary key
    protected $primaryKey = 'jobID';
}
