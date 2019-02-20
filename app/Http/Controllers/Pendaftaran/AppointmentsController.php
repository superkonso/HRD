<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

use Auth;
use DateTime;

use SIMRS\Pendaftaran\Appointment;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Pasien;

use Input;

use View;

class AppointmentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('MenuLevelCheck:01,002');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");
        
        $nowDate = date('Y-m-d');

        $appointments = Appointment::
                            leftJoin('tunit', 'tjanjijalan.TUnit_Kode', '=', 'tunit.TUnit_Kode')
                            ->leftJoin('tpelaku', 'tjanjijalan.TPelaku_Kode', '=', 'tpelaku.TPelaku_Kode')
                            ->select('tjanjijalan.*', 'tunit.TUnit_Nama', 'tpelaku.TPelaku_NamaLengkap AS TPelaku_Nama')
                            ->where('tjanjijalan.TJanjiJalan_TglJanji', '>=', $nowDate)
                            ->orderBy('tjanjijalan.id', 'asc')
                            ->get();


        return view::make('Pendaftaran.Appointment.home', compact('appointments'));
    }


    public function create()
    {
        $tgl        = date('y').date('m').date('d');

        $units      = Unit::all();
        $autoNumber = autoNumberTrans::autoNumber('AP-'.$tgl.'-', '4', false);

        return view::make('Pendaftaran.Appointment.create', compact('autoNumber', 'units'));
    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();

        $janji = new Appointment;

        $tgl    = date('y').date('m').date('d');
        $tgl2   = date_format(new DateTime($request->tgljanji), 'Ymd');

        $autoNumber     = autoNumberTrans::autoNumber('AP-'.$tgl.'-', '4', false);
        $autoNumber2    = autoNumber::autoNumber('JANJI-'.$tgl2.'-'.$request->unit, '3', false);

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('appointment');
        }

        // ================ End Check validate nomor RM ===================

        $this->validate($request, [
                'nama'  => 'required',
            ]);


        $janji->TJanjiJalan_NoJan           = $autoNumber; 
        $janji->TPasien_NomorRM             = $request->pasiennorm;
        $janji->TJanjiJalan_Nama            = $request->nama;
        $janji->TJanjiJalan_TglLahir        = date_format(new DateTime($request->tgllahir), 'Y-m-d');
        $janji->TJanjiJalan_Gender          = $request->jk;
        $janji->TJanjiJalan_Alamat          = $request->alamat;
        $janji->TJanjiJalan_PasienUmurThn   = $request->pasienumurthn;
        $janji->TJanjiJalan_PasienUmurBln   = $request->pasienumurbln;
        $janji->TJanjiJalan_PasienUmurHr    = $request->pasienumurhari;
        $janji->TJanjiJalan_PasienTelp      = $request->telepon;
        $janji->TPelaku_Kode                = $request->pelaku;
        $janji->TUnit_Kode                  = $request->unit;
        $janji->TJanjiJalan_JenisJanji      = '';
        $janji->TJanjiJalan_TglJanji        = date_format(new DateTime($request->tgljanji), 'Y-m-d');
        $janji->TJanjiJalan_JamJanji        = $request->jamjanji;
        //$janji->TJanjiJalan_TglBatal        = ''; // Default null karena tipe timestamp
        $janji->TJanjiJalan_Keterangan      = $request->keterangan;
        $janji->TJanjiJalan_Flag            = '0';
        $janji->TJanjiJalan_NoUrut          = $autoNumber2;
        $janji->TJanjiJalan_JalanNoReg      = '';
        $janji->Users_id                    = (int)Auth::User()->id;
        $janji->TJanjiJalan_update_id       = 0;
        $janji->TJanjiJalan_UserDate1       = date('Y-m-d H:i:s');
        //$janji->TJanjiJalan_update_date     = ''; // Default null karena tipe timestamp
        $janji->IDRS                        = 1;

        if($janji->save())
        {
            autoNumberTrans::autoNumber('AP-'.$tgl.'-', '4', true);
            autoNumber::autoNumber('JANJI-'.$tgl2.'-'.$request->unit, '3', true);

            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $request->nojan;
                $logbook->TLogBook_LogKeterangan = 'Create Appointment a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Pendaftaran Appointment Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('appointment');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $pasiennorm = '';

        $items      = Appointment::
                        leftJoin('tpasien', 'tjanjijalan.TPasien_NomorRM', '=', 'tpasien.TPasien_NomorRM')
                        ->select('tjanjijalan.*', 'tpasien.TPasien_NomorRM', 'tpasien.TPasien_TglLahir')
                        ->where('tjanjijalan.id', '=', $id)
                        ->first();

        $units      = Unit::all();

        $pelakus    = Pelaku::where('TPelaku_Status', '=', '1')
                        ->where("TUnit_Kode", '=', $items->TUnit_Kode)
                        ->orWhere("TUnit_Kode2", '=', $items->TUnit_Kode)
                        ->orWhere("TUnit_Kode3", '=', $items->TUnit_Kode)
                        ->whereNotIn("TSpesialis_Kode", array('PER', 'BDN'))
                        ->orderBy('TPelaku_NamaLengkap', 'ASC')
                        ->get();

        $pasiens    = Pasien::where('TPasien_NomorRM', '=', $items->TPasien_NomorRM)->first();

        if($pasiens) $pasiennorm = $pasiens->TPasien_NomorRM;

        $autoNumber = $items->TJanjiJalan_NoJan; 

        return view::make('Pendaftaran.Appointment.edit', compact('autoNumber', 'items', 'units', 'pelakus', 'pasiennorm'));

    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        \DB::beginTransaction();
        
        $janji = Appointment::find($id);

        // =================== Check validate nomor RM ====================
        $pasienCount = 0;

        $pasienCount = Pasien::where('TPasien_NomorRM', '=', $request->pasiennorm)->count();

        if((int)$pasienCount < 1){
            session()->flash('validate', 'Pasien tidak ditemukan, Nomor RM tidak sesuai');
            return redirect('appointment');
        }

        // ================ End Check validate nomor RM ===================

        $this->validate($request, [
                'nama'  => 'required',
            ]);

        $janji->TPasien_NomorRM             = $request->pasiennorm;
        $janji->TJanjiJalan_TglLahir        = date_format(new DateTime($request->tgllahir), 'Y-m-d');
        $janji->TJanjiJalan_Gender          = $request->jk;
        $janji->TJanjiJalan_Alamat          = $request->alamat;
        $janji->TJanjiJalan_PasienUmurThn   = $request->pasienumurthn;
        $janji->TJanjiJalan_PasienUmurBln   = $request->pasienumurbln;
        $janji->TJanjiJalan_PasienUmurHr    = $request->pasienumurhari;
        $janji->TJanjiJalan_Nama            = $request->nama;
        $janji->TJanjiJalan_PasienTelp      = $request->telepon;
        $janji->TPelaku_Kode                = $request->pelaku;
        $janji->TUnit_Kode                  = $request->unit;
        $janji->TJanjiJalan_JenisJanji      = '';
        $janji->TJanjiJalan_TglJanji        = date_format(new DateTime($request->tgljanji), 'Y-m-d');
        $janji->TJanjiJalan_JamJanji        = $request->jamjanji;
        $janji->TJanjiJalan_Keterangan      = $request->keterangan;
        $janji->TJanjiJalan_update_id       = (int)Auth::User()->id;
        $janji->TJanjiJalan_update_date     = date('Y-m-d H:i:s');

        if($janji->save()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id              = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'U';
                $logbook->TLogBook_LogNoBukti   = $request->nojan;
                $logbook->TLogBook_LogKeterangan = 'Update Appointment a/n '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Perubahan Pendaftaran Appointment Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('appointment');
    }

    public function destroy($id)
    {
        \DB::beginTransaction();

        $appointment = Appointment::find($id);
        
        if($appointment->delete()){
            // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'D';
                $logbook->TLogBook_LogNoBukti   = $appointment->TJanjiJalan_NoJan;
                $logbook->TLogBook_LogKeterangan = 'Delete Appointment no : '.$appointment->TJanjiJalan_NoJan;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Pendaftaran Appointment Berhasil Dihapus');
                }
            // ===========================================================================
        }

        return redirect('/appointment');

    }

    public function autocompletePasienByNoRM(Request $request)
    {
        $term = $request->term;

        $data = Pasien::where('TPasien_NomorRM', 'like', '%'.$term.'%')
                ->take(10)
                ->orderBy('TPasien_NomorRM', 'ASC')
                ->get();
        $result = array();

        foreach ($data as $key => $v) {
            $result[] = ['id'=>$v->id, 'value'=>$v->TPasien_NomorRM];
        }

        return response()->json($result);
    }

}
