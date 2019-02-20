<?php

namespace SIMRS\Http\Controllers\Rawatinap;

use Illuminate\Http\Request;
use SIMRS\Http\Controllers\Controller;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Input;

use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\getTagihanInap;
use SIMRS\Helpers\pembulatan;

use PDF;
use DB;
use View;
use Auth;
use DateTime;

use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Ruang;
use SIMRS\Kelas;
use SIMRS\Perusahaan;
use SIMRS\Admvar;
use SIMRS\Tarifvar;

use SIMRS\Pendaftaran\Wilayah2;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Wewenang\Tarifinap;
use SIMRS\Rawatinap\Inaptrans;
use SIMRS\Rawatinap\Kasir;
use SIMRS\Rawatinap\Rawatinap;

class KasirangsuransController extends Controller
{
    private $uri            = '';
    private $jnstrans       = 'T';
    private $jnsbayar       = 'UM';
    private $titleheader    = '';
    private $nametitle      = '';

    public function __construct()
    {
        //$this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);   
        $this->uri = parse_url(url()->current(), PHP_URL_PATH); 

        if ($this->uri == '/kasiruangmuka') {
            $this->middleware('MenuLevelCheck:04,101');
            $this->jnstrans     = 'T';
            $this->jnsbayar     = 'UM';
            $this->titleheader  = 'Penerimaan Uang Muka';
            $this->nametitle    = 'Uang Muka';

        }elseif ($this->uri == '/kasirangsuran') {
            $this->middleware('MenuLevelCheck:04,102');
            $this->jnstrans     = 'A';
            $this->jnsbayar     = 'UM';
            $this->titleheader  = 'Penerimaan Angsuran Pembayaran';
            $this->nametitle    = 'Angsuran';

        }elseif ($this->uri == '/kembaliuangmuka') {
            $this->middleware('MenuLevelCheck:04,103');
            $this->jnstrans     = 'K';
            $this->jnsbayar     = 'BU';
            $this->titleheader  = 'Pengembalian Uang Muka';
            $this->nametitle    = 'Kembali';

        }elseif ($this->uri == '/kasirinapbayar') {
            $this->middleware('MenuLevelCheck:04,103');
            $this->jnstrans     = 'B';
            $this->jnsbayar     = 'KRI';
            $this->titleheader  = 'Pelunasan Tagihan Inap';
            $this->nametitle    = 'Pelunasan';

        }
    }

    public function index()
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate        = date('Y-m-d H:i:s');
        $tgl            = date('y').date('m');

        $jnstrans       = $this->jnstrans;
        $titleheader    = $this->titleheader;
        $nametitle      = $this->nametitle;

        $autonumber     = autoNumberTrans::autoNumber($this->jnsbayar.'-'.$tgl.'-', '6', false);
        $jnsbayar       = $this->jnsbayar;

        if($this->jnstrans == 'T'){
            return view::make('Rawatinap.Kasir.Uangmuka.create', compact('autonumber', 'jnsbayar', 'jnstrans'));     
        }elseif($this->jnstrans == 'A'){
            return view::make('Rawatinap.Kasir.Angsuran.create', compact('autonumber', 'jnsbayar', 'jnstrans'));     
        }elseif($this->jnstrans == 'K'){
            return view::make('Rawatinap.Kasir.Kembalian.create', compact('autonumber', 'jnsbayar', 'jnstrans'));
        }else{
            return view::make('Rawatinap.Kasir.Pelunasan.create', compact('autonumber', 'jnsbayar', 'jnstrans'));
        }

           
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m');

        \DB::beginTransaction();

        $autonumber = autoNumberTrans::autoNumber($request->jnsbayar.'-'.$tgl.'-', '6', false);

        $rawatinap  = Rawatinap::where('TRawatInap_NoAdmisi', '=', $request->noreg)->first();

        if($request->jnstrans == 'B' && $request->cetakulang == 'Y'){
            \DB::commit();
            return $this->ctkKasir($request->jnstrans, $rawatinap->TKasir_Nomor, 'Y');
        }else{

            // Masukkan ke TKasir =================================================
            $kasir = new Kasir;

            $kasir->TKasir_Nomor            = $autonumber;
            $kasir->TKasir_Tanggal          = $nowDate;
            $kasir->TKasir_Jenis            = 'I';
            $kasir->TRawatInap_NoAdmisi     = $request->noreg;
            $kasir->TPasien_NomorRM         = $request->pasiennorm;
            $kasir->TKasir_JenisBayar       = $request->jnstrans;
            $kasir->TKasir_Kuitansi         = $request->nonota;
            $kasir->TKasir_AtasNama         = $request->namakuitansi;
            $kasir->TKasir_BayarKet         = ($request->jnstrans == 'T' ? 'Pembayaran Uang Muka : '.$request->namakuitansi : ($request->jnstrans == 'A' ? 'Pembayaran Angsuran : '.$request->namakuitansi : ($request->jnstrans == 'K' ? 'Pengembalian Uang Muka : '.$request->namakuitansi : 'Pelunasan : '.$request->namakuitansi)));
            $kasir->TKasir_BayarJml         = floatval(str_replace(',', '', $request->bayar));
            $kasir->TKasir_TagJumlah        = floatval(str_replace(',', '', $request->biayainap));
            $kasir->TKasir_TagPotong        = floatval(str_replace(',', '', $request->potongan));
            $kasir->TKasir_TagBulat         = floatval(str_replace(',', '', pembulatan::getpembulatan(str_replace(',', '', $request->tunai))));
            $kasir->TKasir_TagBayar         = floatval(str_replace(',', '', $request->jmlbayar));
            $kasir->TKasir_TagAsuransi      = floatval(str_replace(',', '', $request->asuransi));
            $kasir->TKasir_TagPiutang       = floatval(str_replace(',', '', $request->sisatagihan));
            $kasir->TKasir_Status           = '0';
            $kasir->TKasir_KartuKode        = '';
            $kasir->TKasir_KartuAlamat      = '';
            $kasir->TKasir_KartuNama        = '';
            $kasir->TKasir_Kartu            = floatval(str_replace(',', '', $request->kartu));
            $kasir->TKasir_Tunai            = floatval(str_replace(',', '', $request->tunai));
            $kasir->TKasir_Pribadi          = floatval(str_replace(',', '', $request->tunai));
            $kasir->TKasir_UserID           = (int)Auth::User()->id;
            $kasir->TKasir_UserDate         = date('Y-m-d H:i:s');
            $kasir->TKasir_UserShift        = '1';
            $kasir->IDRS                    = 1;

            if($kasir->save()){

                // Masukkan ke TInapTrans ===========================================
                $inaptrans = new Inaptrans;

                $pengali = ($request->jnstrans == 'K' ? -1 : 1);

                $inaptrans->TInapTrans_Nomor     = $autonumber;
                $inaptrans->TransAutoNomor       = 0;
                $inaptrans->TarifKode            = '99999';
                $inaptrans->TRawatInap_NoAdmisi  = $request->noreg;
                $inaptrans->TTNomor              = $rawatinap->TTmpTidur_Kode;
                $inaptrans->KelasKode            = substr($rawatinap->TTmpTidur_Kode, 3, 2);
                $inaptrans->PelakuKode           = $rawatinap->TPelaku_Kode;
                $inaptrans->TransKelompok        = ($request->jnstrans == 'T' ? 'UMK' : 'BYR');
                $inaptrans->TarifJenis           = ($request->jnstrans == 'T' ? 'UMK' : 'BYR');
                $inaptrans->TransTanggal         = date('Y-m-d H:i:s');
                $inaptrans->TransKeterangan      = ($request->jnstrans == 'T' ? 'Pembayaran Uang Muka : '.$request->namakuitansi : ($request->jnstrans == 'A' ? 'Pembayaran Angsuran : '.$request->namakuitansi : ($request->jnstrans == 'K' ? 'Pengembalian Uang Muka : '.$request->namakuitansi : 'Pelunasan : '.$request->namakuitansi)));
                $inaptrans->TransDebet           = 'K';
                $inaptrans->TransBanyak          = 1;
                $inaptrans->TransTarif           = floatval(str_replace(',', '', $request->uangmuka)) * $pengali;
                $inaptrans->TransJumlah          = floatval(str_replace(',', '', $request->uangmuka)) * $pengali;
                $inaptrans->TransDiskonPrs       = 0;
                $inaptrans->TransDiskon          = 0;
                $inaptrans->TransAsuransi        = 0;
                $inaptrans->TransPribadi         = floatval(str_replace(',', '', $request->uangmuka));
                $inaptrans->TarifAskes           = '0';
                $inaptrans->TransDokter          = 0;
                $inaptrans->TransDiskonDokter    = 0;
                $inaptrans->TransRS              = 0;
                $inaptrans->TransDiskonRS        = 0;
                $inaptrans->TUsers_id            = (int)Auth::User()->id;
                $inaptrans->TInapTrans_UserDate  = date('Y-m-d H:i:s');
                $inaptrans->IDRS                 = 1;

                if($inaptrans->save()){
                    $hasil = getTagihanInap::getTagihanInap2($request->noreg, $request->jalan_nomor, $request->penjamin_kode);

                    if($hasil == '0'){ // jika status sudah dibayar maka return adalah '1'
                       
                    }else{
                        
                    }

                    // Update TRawatInap untuk Pelunasan Tagihan 
                    if($request->jnstrans == 'B'){
                        $rawatinap->TRawatInap_Status       = '1';
                        $rawatinap->TRawatInap_StatusBayar  = '1';
                        //$rawatinap->TRawatInap_BanyakCetak  = 1;
                        $rawatinap->TRawatInap_BayarTgl     = date('Y-m-d H:i:s');
                        $rawatinap->TKasir_Nomor            = $autonumber;
                    }

                    if($rawatinap->save()){
                        $autonumber = autoNumberTrans::autoNumber($request->jnsbayar.'-'.$tgl.'-', '6', true);
                        \DB::commit();
                        return $this->ctkKasir($request->jnstrans, $autonumber, 'N');
                    }

                }
            }

        } // ... if($request->jnstrans == 'B' && $request->cetakulang == 'Y'){

    }

    public function show($id)
    {
        return View::make('Rawatinap.Diagnostik.home');
    }

    public function edit($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $nowDate    = date('Y-m-d H:i:s');
        $tgl        = date('y').date('m').date('d');

        \DB::beginTransaction();

    }

    public function destroy($id)
    {
        //
    }

    private function ctkKasir($jenis, $nokasir, $cetakulang){

        $datakasir  = DB::table('tkasir AS K')
                        ->leftJoin('trawatinap AS RI', 'K.TRawatInap_NoAdmisi', '=', 'RI.TRawatInap_NoAdmisi')
                        ->leftJoin('tpasien AS P', 'RI.TPasien_NomorRM', '=', 'P.TPasien_NomorRM')
                        ->leftJoin('tpelaku AS D', 'RI.TPelaku_Kode', '=', 'D.TPelaku_Kode')
                        ->leftJoin('ttmptidur AS T', 'RI.TTmpTidur_Kode', '=', 'T.TTmpTidur_Nomor')
                        ->leftJoin('tperusahaan AS H', 'RI.TPerusahaan_Kode', '=', 'H.TPerusahaan_Kode')
                        ->select('K.TKasir_Nomor', 'K.TKasir_AtasNama', 'K.TKasir_TagAsuransi', 'K.TKasir_TagBayar', 'K.TKasir_BayarJml', 'K.TKasir_TagJumlah', 'K.TKasir_TagPiutang', 'K.TKasir_BayarKet', 'RI.TRawatInap_NoAdmisi', 'RI.TPasien_NomorRM', 'RI.TRawatInap_TglMasuk', 'RI.TRawatInap_UmurThn', 'RI.TRawatInap_UmurBln', 'RI.TRawatInap_UmurHr', 'P.TPasien_Nama', 'D.TPelaku_NamaLengkap', 'T.TTmpTidur_Nama', 'H.TPerusahaan_Nama')
                        ->where('K.TKasir_Nomor', '=', $nokasir)
                        ->first();

        $header     = ($jenis == 'T' ? 'PEMBAYARAN UANG MUKA RAWAT INAP' : ($jenis == 'A' ? 'PEMBAYARAN ANGSURAN RAWAT INAP' : ($jenis == 'K' ? 'PENGEMBALIAN UANG MUKA RAWAT INAP' : 'PELUNASAN TAGIHAN INAP')));

        if($cetakulang == 'Y') $header = 'CETAK ULANG '.$header;

        if($this->jnstrans == 'T'){
            session()->flash('message', 'Transaksi Pembayaran Uang Muka Rawat Inap Berhasil Disimpan !');
        }else if($this->jnstrans == 'A'){
            session()->flash('message', 'Transaksi Pembayaran Angsuran Rawat Inap Berhasil Disimpan !');
        }else if($this->jnstrans == 'K'){
            session()->flash('message', 'Transaksi Pengembalian Uang Muka Rawat Inap Berhasil Disimpan !');
        }else if($this->jnstrans == 'B'){
            session()->flash('message', 'Transaksi Pelunasan Tagihan Rawat Inap Berhasil Disimpan !');
        }else{
            session()->flash('message', 'Transaksi Pelunasan Tagihan Rawat Inap Berhasil Disimpan !');
        }

        return view::make('Rawatinap.Kasir.Cetak.ctkkasir', compact('datakasir', 'jenis', 'header'));
    }


}
