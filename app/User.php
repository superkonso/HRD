<?php

namespace SIMRS;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use SIMRS\CPanel;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password', 'TAccess_Code', 'TUnit_Kode', 'TPelaku_Kode', 'IDRS', 'created_at', 'foto', 'last_login', 'last_logout', 'updated_at', 
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getCPanelAttribute()
    {
    	$cpanel = CPanel::find(1);
    	
    	return $cpanel;
    }

}
