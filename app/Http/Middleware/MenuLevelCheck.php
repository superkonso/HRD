<?php

namespace SIMRS\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use DB;

class MenuLevelCheck
{

    public function handle($request, Closure $next, $menukode, $menuitemkode)
    {
        if (is_null(Auth::user())) {
            return redirect()->guest('login');
        } else {
            $userAC         = Auth::user()->TAccess_Code;
            $accessItem     = array();

            $obj_accessItem  = DB::table('taccessitem')
                                ->select('TAccessItem_List')
                                ->where('TAccess_Code', '=', $userAC)
                                ->where('TAccessItem_Menu', '=', $menukode)
                                ->first();

            if(empty($obj_accessItem)){
                return redirect()->guest('login');
            }else{
                $accessItem = explode(';', $obj_accessItem->TAccessItem_List);
                                    
                $akses = in_array($menuitemkode, $accessItem);

                if($akses == 1 OR $akses == '1'){
                    return $next($request);
                }else{
                    return redirect()->guest('login');
                }
            }
        }      
    }
}