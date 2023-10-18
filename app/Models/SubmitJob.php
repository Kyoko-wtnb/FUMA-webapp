<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SubmitJob::class, 'parent_id');
    }

    public function child(): HasOne
    {
        return $this->hasOne(SubmitJob::class, 'parent_id')->withDefault(['jobID' => null]);
    }

    public function childs(): HasMany
    {
        return $this->hasMany(SubmitJob::class, 'parent_id');
    }

    public function getJobList_snp2gene_and_geneMap_only($user_id): Collection
    {
        return $this->where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getScheduledJobs_snp2gene_and_geneMap_only($user_id): Collection
    {
        return $this->where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->wherein('status', ['QUEUED', 'RUNNING', 'NEW'])
            ->get();
    }

    public function getJob_ids_and_titles_snp2gene_and_geneMap_only($user_id): Collection
    {
        return $this->where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->get(['jobID', 'title']);
    }

    public function getOkJob_ids_and_titles_snp2gene_and_geneMap_only($user_id): Collection
    {
        return $this->where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->where('status', 'OK')
            ->get(['jobID', 'title']);
    }

    public function getNewJobs_snp2gene_and_geneMap_only($user_id): Collection
    {
        return $this->where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->where('status', 'NEW')
            ->get();
    }

    public function updateStatus($job_id, $status): void
    {
        $this->where('jobID', $job_id)
            ->update(['status' => $status]);
    }

    public function find_public_job_from_id($id): SubmitJob | NULL
    {
        $job = $this->where('old_id', $id)
            ->where('is_public', 1)
            ->first();

        if ($job != NULL) {
            return $job;
        } else {
            return $this->find($id);
        }
    }

    public function get_public_job_id_from_old_or_not_id($id): int
    {
        $job = $this->where('old_id', $id)
            ->where('is_public', 1)
            ->first();

        if ($job != NULL) {
            return $job->jobID;
        } else {
            return $this->find($id)->jobID;
        }
    }
}
