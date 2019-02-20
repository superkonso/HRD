<?php

namespace SIMRS\Http\Controllers\Gudangfarmasi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;

use SIMRS\Wewenang\Grup;
use SIMRS\Gudangfarmasi\Obat;
use SIMRS\Supplier;
use SIMRS\Pabrik;
use SIMRS\Logbook;
use SIMRS\Tarifvar;

use DB;
use View;
use Auth;


class MasterobatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:05,601');
    }

    public function index()
    {   

        $golobats       = Grup::where('TGrup_Jenis','=','GOLOBAT')->orderBy('TGrup_Nama','ASC')->get();
        $bentukobats    = Grup::where('TGrup_Jenis','=','OBAT')->orderBy('TGrup_Nama','ASC')->get();
        $PPN_obj        = Tarifvar::select('TTarifVar_Nilai')
                                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                                    ->where('TTarifVar_Kode', '=', 'PPN')
                                    ->first();

        $PPN            = $PPN_obj->TTarifVar_Nilai;

        return view::make('Gudangfarmasi.Obatmaster.home',compact('golobats','bentukobats', 'PPN'));
    }


    public function create()
    {
        $golobats       = Grup::where('TGrup_Jenis','=','GOLOBAT')->orderBy('TGrup_Kode','ASC')->get();
        $bentukobats    = Grup::where('TGrup_Jenis','=','OBAT')->orderBy('TGrup_Kode','ASC')->get();
        $satuanbeli     = Grup::where('TGrup_Jenis','=','OBATSAT1')->orderBy('TGrup_Kode','ASC')->get();
        $satuanjual     = Grup::where('TGrup_Jenis','=','OBATSAT2')->orderBy('TGrup_Kode','ASC')->get();        
        $suppliers      = Supplier::where('TSupplier_Status','=','A')->get();
        $pabriks        = Pabrik::where('TPabrik_Status','=','A')->get();
        $PPN_obj        = Tarifvar::select('TTarifVar_Nilai')
                                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                                    ->where('TTarifVar_Kode', '=', 'PPN')
                                    ->first();

        $PPN            = $PPN_obj->TTarifVar_Nilai;

        date_default_timezone_set("Asia/Bangkok");
           
        $kodetgl= 'AJ'.date('y').date('m');
        $autoNumber = autoNumberTrans::autoNumber($kodetgl, '4', false);

        return view::make('Gudangfarmasi.Obatmaster.create',compact('autoNumber','golobats','bentukobats','suppliers','pabriks','satuanjual','satuanbeli', 'PPN'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $Obatbaru = new Obat;
        
        \DB::beginTransaction();
        
        // ============ validation ================
           $this->validate($request, [
                'nama'  => 'required',
                'hargabeli'   => 'required',
                'hna'   => 'required',
            ]);

           $hargapokok  = 0;
           if ($request->ppn == '1') {
               $hargapokok  = floatval(str_replace(',', '', $request->hargabeli)) + (floatval(str_replace(',', '',$request->hargabeli)) *10/100);
           } else {
               $hargapokok  = floatval(str_replace(',', '', $request->hargabeli));
           }
           
            $kodetgl    = $request->jenis.date('y').date('m');

            $autoNumber = autoNumberTrans::autoNumber($kodetgl, '4', false);

        // ========================================

        $Obatbaru->TObat_Kode                   = $autoNumber;
        $Obatbaru->TGrup_Kode                   = $request->bentuk;
        $Obatbaru->TGrup_Kode_Gol               = $request->golongan;
        $Obatbaru->TObat_Nama                   = $request->nama;
        $Obatbaru->TObat_NamaGenerik            = empty($request->namagenerik)? '' : $request->namagenerik;
        $Obatbaru->TObat_Satuan                 = $request->satbeli;
        $Obatbaru->TObat_Satuan2                = $request->satjual;
        $Obatbaru->TObat_SatuanFaktor           = $request->faktor;
        $Obatbaru->TObat_HargaPokok             = $hargapokok;
        $Obatbaru->TObat_HargaAskes             = 0;
        $Obatbaru->TObat_HargaGakin             = 0;
        $Obatbaru->TObat_HNA                    = floatval(str_replace(',', '', $request->hargabeli));
        $Obatbaru->TObat_DiscBeli1              = $request->disc1;
        $Obatbaru->TObat_DiscBeli2              = $request->disc2;
        $Obatbaru->TObat_Askes                  = 0;
        $Obatbaru->TObat_Generik                = empty($request->generik)? '0': $request->generik;
        $Obatbaru->TObat_GenerikGakin           = '';
        $Obatbaru->TObat_Karyawan               = '0';
        $Obatbaru->TObat_Minimal                = empty($request->min)? 0: $request->min;
        $Obatbaru->TObat_Maksimal               = empty($request->maks)? 0 : $request->maks;
        $Obatbaru->TObat_Catatan                = empty($request->catatan)? '' : $request->catatan;
        $Obatbaru->TObat_Kemasan                = $request->isi;
        $Obatbaru->TObat_Status                 = $request->status;
        $Obatbaru->TObat_GdQty                  = 0;
        $Obatbaru->TObat_GdJml                  = 0;
        $Obatbaru->TObat_RpQty                  = 0;
        $Obatbaru->TObat_RpJml                  = 0;
        $Obatbaru->TObat_JualFaktor             = $request->faktor;
        $Obatbaru->TObat_HNA_PPN                = floatval(str_replace(',', '', $request->hna));
        $Obatbaru->TSupplier_Kode               = $request->supplier;
        $Obatbaru->TPabrik_Kode                 = $request->pabrik;
        $Obatbaru->TObat_PPn                    = empty($request->ppn)? 0 : $request->ppn;
        $Obatbaru->TObat_NonFormularium         = floatval(str_replace(',', '', $request->formula));
        $Obatbaru->TUsers_id                    = (int)Auth::User()->id;
        $Obatbaru->TObat_UserDate1              = date('Y-m-d H:i:s');
        $Obatbaru->IDRS                         = '1';

        if($Obatbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $autoNumber = autoNumberTrans::autoNumber($kodetgl, '4', true);

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05061';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'C';
                $logbook->TLogBook_LogNoBukti   = $autoNumber;
                $logbook->TLogBook_LogKeterangan = 'Obat Baru '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Data Obat Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('masterobat');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $golobats       = Grup::where('TGrup_Jenis','=','GOLOBAT')->orderBy('TGrup_Nama','ASC')->get();
        $bentukobats    = Grup::where('TGrup_Jenis','=','OBAT')->orderBy('TGrup_Nama','ASC')->get();
        $satuanbeli     = Grup::where('TGrup_Jenis','=','OBATSAT1')->orderBy('TGrup_Nama','ASC')->get();
        $satuanjual     = Grup::where('TGrup_Jenis','=','OBATSAT2')->orderBy('TGrup_Nama','ASC')->get();
        $suppliers      = Supplier::where('TSupplier_Status','=','A')->get();
        $pabriks        = Pabrik::where('TPabrik_Status','=','A')->get();
        $Obats          = Obat::where('tobat.id', '=', $id)
                        ->first();
        $PPN_obj        = Tarifvar::select('TTarifVar_Nilai')
                                    ->where('TTarifVar_Seri', '=', 'GENERAL')
                                    ->where('TTarifVar_Kode', '=', 'PPN')
                                    ->first();

        $PPN            = $PPN_obj->TTarifVar_Nilai;

         return view::make('Gudangfarmasi.Obatmaster.edit',compact('Obats','golobats','bentukobats','suppliers','pabriks','satuanjual','satuanbeli', 'PPN'));
    }


    public function update(Request $request, $id)
    {  
        date_default_timezone_set("Asia/Bangkok");

        $Obatbaru = Obat::find($id);
        
        \DB::beginTransaction();
        
        // ====================================== validation ==================
           $this->validate($request, [
                'nama'  => 'required',
                'hargabeli'   => 'required',
                'hna'   => 'required',
            ]);
        // ==================================================================

       $hargapokok  = 0;
       if ($request->ppn == '1') {
           $hargapokok  = floatval(str_replace(',', '', $request->hargabeli)) + (floatval(str_replace(',', '',$request->hargabeli)) *10/100);
       } else {
           $hargapokok  = floatval(str_replace(',', '', $request->hargabeli));
       }
       
        $kodetgl    = $request->jenis.date('y').date('m');

        $Obatbaru->TObat_Kode                   = $Obatbaru->TObat_Kode;
        $Obatbaru->TGrup_Kode                   = $request->bentuk;
        $Obatbaru->TGrup_Kode_Gol               = $request->golongan;
        $Obatbaru->TObat_Nama                   = $request->nama;
        $Obatbaru->TObat_NamaGenerik            = empty($request->namagenerik)? '' : $request->namagenerik;
        $Obatbaru->TObat_Satuan                 = $request->satbeli;
        $Obatbaru->TObat_Satuan2                = $request->satjual;
        $Obatbaru->TObat_SatuanFaktor           = $request->faktor;
        $Obatbaru->TObat_HargaPokok             = $hargapokok;
        $Obatbaru->TObat_HargaAskes             = 0;
        $Obatbaru->TObat_HargaGakin             = 0;
        $Obatbaru->TObat_HNA                    = floatval(str_replace(',', '', $request->hargabeli));
        $Obatbaru->TObat_DiscBeli1              = $request->disc1;
        $Obatbaru->TObat_DiscBeli2              = $request->disc2;
        $Obatbaru->TObat_Askes                  = 0;
        $Obatbaru->TObat_Generik                = empty($request->generik)? 0 : $request->generik;
        $Obatbaru->TObat_GenerikGakin           = '';
        $Obatbaru->TObat_Karyawan               = '0';
        $Obatbaru->TObat_Minimal                = empty($request->min)? 0: $request->min;
        $Obatbaru->TObat_Maksimal               = empty($request->maks)? 0 : $request->maks;
        $Obatbaru->TObat_Catatan                = empty($request->catatan)? '' : $request->catatan;
        $Obatbaru->TObat_Kemasan                = $request->isi;
        $Obatbaru->TObat_Status                 = $request->status;
        $Obatbaru->TObat_GdQty                  = 0;
        $Obatbaru->TObat_GdJml                  = 0;
        $Obatbaru->TObat_RpQty                  = 0;
        $Obatbaru->TObat_RpJml                  = 0;
        $Obatbaru->TObat_JualFaktor             = $request->faktor;
        $Obatbaru->TObat_HNA_PPN                = floatval(str_replace(',', '', $request->hna));
        $Obatbaru->TSupplier_Kode               = $request->supplier;
        $Obatbaru->TPabrik_Kode                 = $request->pabrik;
        $Obatbaru->TObat_PPn                    = empty($request->ppn)? 0 : $request->ppn; 
        $Obatbaru->TObat_NonFormularium         = floatval(str_replace(',', '', $request->formula));
        $Obatbaru->TUsers_id                    = (int)Auth::User()->id;
        $Obatbaru->TObat_UserDate1              = date('Y-m-d H:i:s');
        $Obatbaru->IDRS                         = '1';    

        if($Obatbaru->save())
        {
                // ========================= simpan ke tlogbook ==============================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id             = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '05601';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'E';
                $logbook->TLogBook_LogNoBukti   = $Obatbaru->TObat_Kode;
                $logbook->TLogBook_LogKeterangan = 'Edit Obat '.$request->nama;
                $logbook->TLogBook_LogJumlah    = '0';
                $logbook->IDRS                  = '1';

                if($logbook->save()){
                    \DB::commit();
                    session()->flash('message', 'Edit Data Obat Berhasil Disimpan');
                }
            // ===========================================================================
        }

        return redirect('masterobat');
    }

    public function destroy($id)
    {
        //
    }
}
