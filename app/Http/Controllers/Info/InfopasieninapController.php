<?php

namespace SIMRS\Http\Controllers\Info;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Info\Info;
use Input;
use View;
use Auth;
use DateTime;

use DB;

class InfopasieninapController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:14,002');
    }

   public function index()
    {
        return view::make('Info.Infopasieninap.home');
    }

}
