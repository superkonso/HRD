<?php

namespace SIMRS\Helpers;

use DB;
use Auth;

class accessList{

	public static function checkMenuAccess($menuKode, $userID)
    {
    	$userAC 		= '';
    	$accessItem 	= array();

    	$obj_userAC  = DB::table('users')
							->select('TAccess_Code')
		                    ->where('id', '=', $userID)
		                    ->first();

		$userAC = $obj_userAC->TAccess_Code;

    	$obj_accessItem  = DB::table('taccessitem')
							// ->select('TAccess_Code')
		                    ->where('TAccess_Code', '=', $userAC)
		                    ->where('TAccessItem_Menu', '=', $menuKode)
		                    ->count();

  		return $obj_accessItem;

    } // public static function checkMenuAccess($menuKode)

	public static function checkUserAccess($menuKode, $menuItemKode, $userID)
    {
    	$userAC 		= '';
    	$accessItem 	= array();

    	$obj_userAC  = DB::table('users')
							->select('TAccess_Code')
		                    ->where('id', '=', $userID)
		                    ->first();

		$userAC = $obj_userAC->TAccess_Code;

    	$obj_accessItem  = DB::table('taccessitem')
							->select('TAccessItem_List')
		                    ->where('TAccess_Code', '=', $userAC)
		                    ->where('TAccessItem_Menu', '=', $menuKode)
		                    ->first();

		$accessItem = explode(';', $obj_accessItem->TAccessItem_List);
		                    	
		$bolAccess = in_array($menuItemKode, $accessItem);

  		return $bolAccess;
  		//exit();

    } // public static function checkUserAccess($menuKode, $menuItemKode)

    public static function checkUserAccessMenu($menuKode, $menuItemKode, $level)
    {
    	$userAC 		= '';
    	$accessItem 	= array();

    	$obj_accessItem  = DB::table('taccessitem')
							->select('TAccessItem_List')
		                    ->where('TAccess_Code', '=', $level)
		                    ->where('TAccessItem_Menu', '=', $menuKode)
		                    ->first();

		if(is_null($obj_accessItem)){
			$bolAccess = false;
		}else{
			$accessItem = explode(';', $obj_accessItem->TAccessItem_List);                	
			$bolAccess 	= in_array($menuItemKode, $accessItem);
		}

  		return $bolAccess;

    } // public static function checkUserAccessMenu($menuKode, $menuItemKode)

    public static function checkAccessMenu($menuKode, $menuItemKode, $level)
    {
    	$userAC 		= '';
    	$accessItem 	= array();

    	$obj_accessItem  = DB::table('taccessitem')
							->select('TAccessItem_List')
		                    ->where('TAccess_Code', '=', $level)
		                    ->where('TAccessItem_Menu', '=', $menuKode)
		                    ->first();

		if(is_null($obj_accessItem)){
			$bolAccess = false;
		}else{   	
			$bolAccess 	= true;
		}

  		return $bolAccess;

    } // public static function checkAccessMenu($menuKode, $menuItemKode, $level)
}