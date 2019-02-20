<?php

namespace SIMRS\Http\Controllers\Kamaroperasi;


use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Helpers\autoNumberTrans;

use Illuminate\Support\Facades\Input;
use SIMRS\Perusahaan;
use SIMRS\Ibs\Bedahjadwal;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Pasien;
use SIMRS\Icopim;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;

class InputjadwalopController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:09,004');
    }

    public function index()
    {   
        $prsh       = Perusahaan::all();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where(DB::raw('substr("TPelaku_Kode", 1, 1)'), '=' , 'D')
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')->get();
         
        $operasi    = Icopim::
                            orderBy('TICOPIM_Nama', 'ASC')
                            ->limit(100)
                            ->get();

        $tgl        = date('y').date('m');
        $autoNumber = autoNumberTrans::autoNumber('JOP-'.$tgl.'-','4', false);
        return view::make('Kamaroperasi.Inputjadwaloperasi.create',compact('autoNumber','prsh','pelakus','operasi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();


       $jadwalbedah = new BedahJadwal;
       $tgl        = date('y').date('m');

       $autoNumber = autoNumberTrans::autoNumber('JOP-'.$tgl.'-','4', false);

        $jadwalbedah->TBedahJadwal_Nomor          = $autoNumber;  
        $jadwalbedah->TBedahJadwal_Tgl            = date_format(new DateTime($request->tgljanji), 'Y-m-d');
        $jadwalbedah->TICOPIM_Kode                = $request->Kdoperasi;
        $jadwalbedah->TPasien_NomorRM             = $request->pasiennorm;
        $jadwalbedah->TBedahJadwal_Umur           = $request->pasienumurthn;
        $jadwalbedah->TPelaku_Kode                = $request->dokter;
        $jadwalbedah->TBedahJadwal_Keterangan     = $request->keterangan;
        $jadwalbedah->TUsers_id                    = (int)Auth::User()->id;
        $jadwalbedah->TBedahJadwal_UserDate       = date('Y-m-d H:i:s');
        $jadwalbedah->IDRS                        = 1;
        $jadwalbedah->TBedahJadwal_Jam            = $request->jamjanji;
    
        if($jadwalbedah->save()){
         $autoNumber = autoNumberTrans::autoNumber('JOP-'.$tgl.'-','4', true);

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber; 
                $logbook->TLogBook_LogKeterangan = 'Create Jadwal Operasi dengan Kode '.$autoNumber;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Jadwal Operasi Baru Berhasil Ditambahkan');
                }
            // ===========================================================================
        }

        return redirect('inputjadwalop');
    
    }

    public function show($id)
    {
         return view::make('Kamaroperasi.Inputjadwaloperasi.home');
    }

    public function edit($id)
    {   
        $prsh       = Perusahaan::all();
        $pelakus    = Pelaku::
                            where('TPelaku_Status', '=', '1')
                            ->whereIn('TSpesialis_Kode', array('DUM'))
                            ->orderBy('TPelaku_NamaLengkap', 'ASC')
                            ->get();

        $operasi    = Icopim::
                            orderBy('TICOPIM_Nama', 'ASC')
                            ->limit(100)
                            ->get();

        $jadwalbedah    = BedahJadwal::
                                    leftJoin('tpasien AS P', 'tbedahjadwal.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                                    ->leftJoin('tpelaku AS Pel', 'tbedahjadwal.TPelaku_Kode', '=', 'Pel.TPelaku_Kode')
                                    ->leftJoin('ticopim AS I', 'tbedahjadwal.TICOPIM_Kode', '=', 'I.TICOPIM_Kode')
                                    ->select('tbedahjadwal.*','P.TPasien_Nama', 'P.TPasien_Telp', 'P.TPasien_Alamat', 'P.TAdmVar_Gender', 'P.TPasien_TglLahir', 'Pel.TPelaku_Nama','tbedahjadwal.TICOPIM_Kode','I.TICOPIM_Nama')
                                    ->where('tbedahjadwal.id', '=', $id)
                                    ->first();
         
         return view::make('Kamaroperasi.Inputjadwaloperasi.edit', compact('jadwalbedah','operasi','pelakus'));
      
    }

    public function update(Request $request, $id)
    {
        Date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $jadwalbedah =  BedahJadwal::find($id); 
        $tgl        = date('y').date('m');

        $autoNumber = autoNumberTrans::autoNumber('JOP-'.$tgl.'-','4', false);

        $jadwalbedah->TBedahJadwal_Nomor          = $request->nomorkwi;  
        $jadwalbedah->TBedahJadwal_Tgl            = date_format(new DateTime($request->tgljanji), 'Y-m-d');
        $jadwalbedah->TICOPIM_Kode                = $request->Kdoperasi;
        $jadwalbedah->TPasien_NomorRM             = $request->pasiennorm;
        $jadwalbedah->TBedahJadwal_Umur           = $request->pasienumurthn;
        $jadwalbedah->TPelaku_Kode                = $request->dokter;
        $jadwalbedah->TBedahJadwal_Keterangan     = $request->keterangan;
        $jadwalbedah->TUsers_id                    = (int)Auth::User()->id;
        $jadwalbedah->TBedahJadwal_UserDate       = date('Y-m-d H:i:s');
        $jadwalbedah->IDRS                        = 1;
        $jadwalbedah->TBedahJadwal_Jam            = $request->jamjanji;
    
      $jadwalbedah->save();
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber; 
                $logbook->TLogBook_LogKeterangan = 'Update Transaksi IKB nomor : '.$request->nomorkwi;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Jadwal Operasi Berhasil Disimpan');
                }
            // ===========================================================================
        

        return redirect('inputjadwalop');
    
    }

    public function destroy($id)
    {
        //
    }

}
