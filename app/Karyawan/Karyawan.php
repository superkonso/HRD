<?php

namespace SIMRS\Karyawan;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
  	protected $table 	= 'tkaryawan';
  	protected $primaryKey 	= 'id';
    public $timestamps 	= false;

}