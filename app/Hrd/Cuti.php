<?php

namespace SIMRS\Hrd;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table 	= 'tcuti';
    protected $primaryKey 	= 'TCuti_KaryNomor';
    public $timestamps 	= false;
}
