<?php

namespace SIMRS\Karyawan;

use Illuminate\Database\Eloquent\Model;

class Karyawanjabatan extends Model
{
  	protected $table 		= 'tkaryjabatan';
  	protected $primaryKey 	= 'TKaryawan_Nomor';
    public $timestamps	 	= false;

}