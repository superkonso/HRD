<?php

use SIMRS\Helpers\accessList;

// === Get Menu Item berdasarkan Menu Kode 
	Route::get('/ajax-getmenuitem', function(){
		$key = Request::get('key');

		$menuitem  = DB::table('tmenuitem AS M')->where('M.TMenu_Kode', '=', $key)->limit(100)
						->orderBy('M.TMenuItem_Item','ASC')
	                    ->get();

		return Response::json($menuitem);
	});

// === End Get Menu Item berdasarkan Menu Kode 

// === Get Akses Menu
	Route::get('/ajax-checkcountsubmenu', function(){

		$menukode 	= Request::get('menukode');
		$jenis 		= Request::get('jenis');

		$total 	= 0; 

		$total  = DB::table('tmenuitem')
		                    ->where('TMenu_Kode', '=', $menukode)
		                    ->where('TMenuItem_Jenis', '=', $jenis)
		                    ->count();

		return Response::json($total); 
	});
// === End Get Akses Menu

	// === Get Menu Item by Level User Permission ============================
	Route::get('/ajax-getmenuitembylevel', function(){

		$level 	= Request::get('level');
		$userID = Request::get('userID');

		$listMenu  = DB::select(
			DB::raw("
				SELECT 
					M.\"TMenu_Nama\", 0 AS Akses, 0 AS AksesMenu, 
					MI.\"TMenu_Kode\", MI.\"TMenuItem_Item\", MI.\"TMenuItem_Link\", 
					MI.\"TMenuItem_Nama\", MI.\"TMenuItem_Logo\", MI.\"TMenuItem_Jenis\"
				FROM tmenuitem MI  
				LEFT JOIN tmenu M ON MI.\"TMenu_Kode\" = M.\"TMenu_Kode\" 
				ORDER BY \"TMenu_Kode\" ASC, \"TMenuItem_Item\" ASC;
			")
		);

		foreach ($listMenu as $item) {

			$akses 		= accessList::checkUserAccessMenu($item->TMenu_Kode, $item->TMenuItem_Item, $level);
			$aksesMenu 	= accessList::checkAccessMenu($item->TMenu_Kode, $item->TMenuItem_Item, $level);

			if($akses){
				$item->Akses = 1;
			}else{
				$item->Akses = 0;
			}

			if($aksesMenu){
				$item->AksesMenu = 1;
			}else{
				$item->AksesMenu = 0;
			}

		} // ... foreach ($listMenu as $item) {

		return Response::json($listMenu); 
	});
// === End Get Menu Item by Level User Permission ======================