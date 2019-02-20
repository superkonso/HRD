<?php

namespace SIMRS\Http\Controllers\Ikb;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;

use Input;
use View;
use Auth;
use DateTime;

use DB;

class LaprekappasienbersalinController extends Controller
{
      public function __construct()
    {
        $this->middleware('MenuLevelCheck:10,102');
    }

    public function index()
    {
          return view::make('Ikb.Laporan.Laporanrekappasien.home');
    }


   public function laprekappasien(Request $request)
    {
         date_default_timezone_set("Asia/Bangkok");
        $key1       = $request->searchkey1;
        $key2       = $request->searchkey2;
         
        return view::make('Ikb.Laporan.Laporanrekappasien.Ctklaporanrekappasien', compact('key1', 'key2'));
    }

  }
