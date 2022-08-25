<?php

namespace App\Supports\Shared;

use App\Models\User;

trait HasUserRelation
{
    /**
     * @return mixed
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * @return mixed
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
