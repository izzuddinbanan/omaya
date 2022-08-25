<?php

namespace App\Supports\Shared;

use App\Models\Status;

trait HasStatusRelation
{
    /**
     * @return mixed
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }
}
