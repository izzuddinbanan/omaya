<?php

namespace App\Supports\Shared;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;

trait SetTimeZone
{
    public $tz = 'UTC';
    
    public function getTz(){

        $this->tz = session('timezone');

    }

    public function getUpdatedAtAttribute($value){

        if(empty($value)) return "-";

        try {


            $this->getTz();

            return (new Carbon($value))->timezone($this->tz)->format(env("DATE_FORMAT", "d M Y h:ia"));


        } catch (\Exception $e) {

           return 'Invalid DateTime Exception: '.$e->getMessage();

        }        

    }

    public function getCreatedAtAttribute($value){

        if(empty($value)) return "-";
        
        try {


            $this->getTz();

            return (new Carbon($value))->timezone($this->tz)->format(env("DATE_FORMAT", "d M Y h:ia"));


        } catch (\Exception $e) {

           return 'Invalid DateTime Exception: '.$e->getMessage();

        }        
    }
}
