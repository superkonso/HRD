<?php

namespace SIMRS\Http\Controllers\Rawatjalan;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;

class RekappiutangjalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

           public function ctkrekappiutangjalan(Request $request)
    {
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
        $key3       = $request->Jenis;
            
        return view::make('RawatJalan.Report.Lapobatpakaijalan.Ctklappakaiobatjalan', compact('key1', 'key2', 'key3'));
    }

}
