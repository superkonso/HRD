<?php

namespace SIMRS\Karyawan;

use Illuminate\Database\Eloquent\Model;

class Karyawanfoto extends Model
{
  	protected $table 		= 'tkaryfoto';
  	protected $primaryKey 	= 'TKaryFoto_Nomor';
    public $timestamps	 	= false;

}