<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaModule extends Model
{
    use HasFactory;

    public function scopeFilter($query, $filter){

        if($filter == ''){
            return;
        }

       return  $query->where('group','LIKE', '%'.$filter.'%')
        ->orWhere('name','LIKE', '%'.$filter.'%');
    }
}
