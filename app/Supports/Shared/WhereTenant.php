<?php

namespace App\Supports\Shared;

use App\Scopes\TenantScope;


trait WhereTenant
{

	protected static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(new TenantScope);
    }

}
