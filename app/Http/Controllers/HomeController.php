<?php

namespace SIMRS\Http\Controllers;

use Illuminate\Http\Request;

use SIMRS\Pendaftaran\Pasien;
use SIMRS\Pendaftaran\Appointment;
use SIMRS\Pendaftaran\Rawatugd;
use SIMRS\Pendaftaran\Poli;
use SIMRS\Karyawan\Karyawan;
use SIMRS\Menuitem;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jmlpasien      = Pasien::count();
        $jmlappointment = Appointment::count();
        $jmlpoli        = Poli::count();
        $jmlugd         = Rawatugd::count();

        $jmlkaryawan        = Karyawan::count();

        return view('home', compact('jmlpasien', 'jmlappointment', 'jmlpoli', 'jmlugd','jmlkaryawan'));
    }
}
