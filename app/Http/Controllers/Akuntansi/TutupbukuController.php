<?php

namespace SIMRS\Http\Controllers\Akuntansi;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;
use SIMRS\Http\Controllers\Controller;

use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\formattanggal;
use SIMRS\Helpers\Terbilang;

use SIMRS\Logbook;
use SIMRS\Unit;
use SIMRS\Perkiraan;
use SIMRS\Akuntansi\Saldo;
use SIMRS\Akuntansi\Jurnal;
use SIMRS\Akuntansi\Posting;

use DB;
use View;
use Auth;
use DateTime;

class TutupbukuController extends Controller
{
    public function __construct()
    {
        $this->middleware('MenuLevelCheck:13,210');
    }

    public function index()
    {   
        $bulan = array (1 =>   'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober', 'November','Desember');

        return view::make('Akuntansi.Tutupbuku.create', compact('bulan'));
    }


    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   
        date_default_timezone_set("Asia/Bangkok");
        
        $bulan  = (strlen($request->bulan) < 2 ? '0'.$request->bulan : $request->bulan);
        $bulandepan  = (strlen($request->bulan) < 2 ? '0'.$request->bulan+1 : $request->bulan+1);

        $tahun  = $request->tahun;

        $bulanposting = $tahun.$bulan;
        $dt           = strtotime($bulandepan.'/01/'.$tahun);
        $tglsaldo     = date('Y-m-d'.' 00:00:00', $dt);

        \DB::beginTransaction();

        switch($request->submit) {
            case 'save': 
                
                //==== cek saldo, jika ada pada bulan berjalan, hapus =======
                $jumlahsaldo    = Saldo::where('TSaldo_Tgl','=', $tglsaldo)->count();

                if ($jumlahsaldo > 0) {
                    \DB::table('tsaldo')->where('TSaldo_Tgl', '=',$tglsaldo)->delete();
                }

                //========== insert saldo baru dari hasil jurnal=============

                $datasaldo  = DB::select("SELECT   PerkKode, UnitKode, '"  .$tglsaldo . "' AS Tanggal, ABS(Jumlah) as Jumlah,
                            ( CASE WHEN Jumlah >= 0 THEN 'D' ELSE 'K' END ) As Ket
                            FROM
                            (   SELECT PerkKode, UnitKode, SUM(JrnSaldo) as Jumlah
                                FROM
                                (   SELECT  \"TPerkiraan_Kode\" as PerkKode, \"TUnit_Kode\" as UnitKode, SUM(\"TJurnal_Debet\"-\"TJurnal_Kredit\") AS JrnSaldo, '0' as Jenis
                                    FROM        TJurnal
                                    WHERE   to_char(\"TJurnal_Tanggal\",'MM')='".$bulan."' AND to_char(\"TJurnal_Tanggal\",'YYYY')='".$tahun."'
                                    GROUP BY \"TPerkiraan_Kode\", \"TUnit_Kode\"

                                    UNION

                                    SELECT  \"TPerkiraan_Kode\" as PerkKode, \"TUnit_Kode\" as UnitKode, SUM((CASE WHEN \"TSaldo_Debet\"='D' THEN 1 ELSE -1 END) * \"TSaldo_Jumlah\") as JrnSaldo, '1' as Jenis
                                    FROM        TSaldo
                                    WHERE   to_char(\"TSaldo_Tgl\",'MM')='".$bulan."' AND to_char(\"TSaldo_Tgl\",'YYYY')='".$tahun."'
                                    GROUP BY \"TPerkiraan_Kode\", \"TUnit_Kode\" ) Saldo
                            GROUP BY PerkKode, UnitKode ) Saldo");

                foreach($datasaldo as $data){

                    $saldobaru  = new Saldo;
                    
                    $saldobaru->TPerkiraan_Kode     =$data->perkkode;
                    $saldobaru->TUnit_Kode          =$data->unitkode;
                    $saldobaru->TSaldo_Tgl          =$data->tanggal;
                    $saldobaru->TSaldo_Jumlah       =$data->jumlah;
                    $saldobaru->TSaldo_Debet        =$data->ket;
                    $saldobaru->IDRS                ='1';

                    $saldobaru->save();

                }

                //==== cek posting, jika ada pada bulan berjalan, hapus =======
                $posting    = Posting::where('TPosting_Bulan','=', $bulanposting)->count();

                if ($posting > 0) {
                    \DB::table('tposting')->where('TPosting_Bulan', '=',$bulanposting)->delete();
                }

                //========== insert posting baru dari hasil saldo =============
                $postingbaru   = new Posting;

                $postingbaru->TPosting_Bulan        = $bulanposting;
                $postingbaru->TPosting_Status       = 1;
                $postingbaru->TUsers_id             = (int)Auth::User()->id;
                $postingbaru->TPosting_UserDate     = date('Y-m-d H:i:s');
                $postingbaru->IDRS                  = '1';
                
                $postingbaru->save();


                //==== insert logbook===========================================
                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id                = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress     = $ip;
                $logbook->TLogBook_LogDate          = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo        = '13210';
                $logbook->TLogBook_LogMenuNama      = url()->current();
                $logbook->TLogBook_LogJenis         = 'C';
                $logbook->TLogBook_LogNoBukti       = 0;
                $logbook->TLogBook_LogKeterangan    = 'Tutup Buku: '.$bulanposting;
                $logbook->TLogBook_LogJumlah        = 0;
 
                $logbook->save();

                \DB::commit();
                // session()->flash('message', 'Tutup Buku Berhasil di Simpan');
                // return redirect('tutupbuku');
                return 1;
                
                break;

            case 'delete': 
              
                \DB::table('tposting')->where('TPosting_Bulan', '=',$bulanposting)->delete();

                \DB::table('tsaldo')->where('TSaldo_Tgl', '=',$tglsaldo)->delete();

                $logbook    = new Logbook;
                $ip         = $_SERVER['REMOTE_ADDR'];

                $logbook->TUsers_id            = (int)Auth::User()->id;
                $logbook->TLogBook_LogIPAddress = $ip;
                $logbook->TLogBook_LogDate      = date('Y-m-d H:i:s');
                $logbook->TLogBook_LogMenuNo    = '13210';
                $logbook->TLogBook_LogMenuNama  = url()->current();
                $logbook->TLogBook_LogJenis     = 'D';
                $logbook->TLogBook_LogNoBukti   = 0;
                $logbook->TLogBook_LogKeterangan = 'Hapus Tutup Buku: '.$bulanposting;
                $logbook->TLogBook_LogJumlah    = 0;

                $logbook->save();

                \DB::commit();
                // session()->flash('message', 'Hapus Buku Berhasil');
                // return redirect('tutupbuku');
                return 1;
                break;
        }       
        
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

}
