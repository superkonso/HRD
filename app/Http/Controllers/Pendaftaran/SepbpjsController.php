<?php

namespace SIMRS\Http\Controllers\Pendaftaran;

use Illuminate\Http\Request;
use Illuminate\Html\HtmlServiceProvider;
use SIMRS\Http\Controllers\Controller;
use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;
use SIMRS\Helpers\bpjs;

use Input;
use View;
use Auth;
use DateTime;

use DB;

use SIMRS\Pendaftaran\Pasien;
use SIMRS\Rawatinap\Rawatinap;
use SIMRS\Admvar;
use SIMRS\Tarifvar;
use SIMRS\Unit;
use SIMRS\Logbook;
use SIMRS\Perusahaan;
use SIMRS\Kelas;
use SIMRS\Kamar;
use SIMRS\Ruang;
use SIMRS\Tmptidur;
use SIMRS\Wewenang\Pelaku;
use SIMRS\Pendaftaran\Poli;
use SIMRS\Pendaftaran\Wilayah2;

class SepbpjsController extends Controller{

    public function __construct() {
        $this->middleware('MenuLevelCheck:01,007');
    }

    public function index() {   
        date_default_timezone_set("Asia/Bangkok");

        $units      = Unit::all();
        $pelakus    = Pelaku::all();
        $admvars    = Admvar::all();
        $prsh       = Perusahaan::all();
        $kelas      = Kelas::all();
        $ruangs     = Ruang::all();
        $tarifvars  = Tarifvar::all();
        $tgl        = date('y').date('m').date('d');
        $provinsi   = Wilayah2::where('TWilayah2_Jenis', '=', '1')->orderBy('TWilayah2_Nama', 'ASC')->get();

        $autoNumber = autoNumberTrans::autoNumber('RI-'.$tgl.'-', '4', false);

        $data       = Admvar::where('TAdmVar_Seri','=','RSSEP')
                        ->where('TAdmVar_Kode','=','CID')
                        ->first();
        $secretKey  = Admvar::where('TAdmVar_Seri','=','RSSEP')
                        ->where('TAdmVar_Kode','=','SCK')
                        ->first();
        $refpoli    = DB::table('trefpoli')->get();

        return  view::make('Pendaftaran.Bpjs.create',compact('autoNumber','refpoli', 'units', 'pelakus', 'admvars', 'tarifvars', 'provinsi', 'prsh', 'kelas', 'ruangs'));
    }


    public function create() {
        //
    }

    public function store(Request $request) {   
        
        switch ($request->submit) {
            case 'save':
                # code...
                break;
            case 'edit':
                # code...
                break;
            case 'cetak':
                # code...
                break;
            default:
                # code...
                break;
        }
      return redirect('/sepbpjs');
    }

    public function show($id)    {
        //
    }

    public function edit($id)    {
        //
    }

    public function update(Request $request, $id)    {
        //
    }

    public function destroy($id)    {
        //
    }

    public function autocompletepesertabpjs(Request $request)   {
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

    public function autocompleteicd(Request $request)   {
        $term = $request->term;

        // $data = DB::table('ticd')->where('TICD_Kode', 'ilike', '%'.$term.'%')
        //         ->take(10)
        //         ->orderBy('TICD_Kode', 'ASC')
        //         ->get();
        // $result = array();
        // foreach ($data as $key => $v) {
        //     $result[] = ['id'=>$v->TICD_Kode, 'value'=>$v->TICD_Kode.' - '.$v->TICD_Nama];
        // }   
        // return response()->json($result);

        $uri  = DB::table('tadmvar')->select('TAdmVar_Nama')
            ->where('TAdmVar_Seri','=','WEBS')
            ->where('TAdmVar_Kode','=','RDIA')
            ->first();

        $url = DB::table('tadmvar')->select('TAdmVar_Nama')
            ->where('TAdmVar_Seri','=','RSSEP')
            ->where('TAdmVar_Kode','=','IPS')
            ->first(); 

        $data = bpjs::GetBpjsApi($url->TAdmVar_Nama.$uri->TAdmVar_Nama.$term); 

        $result = array();

        if ($data['metadata']['code']=='200') {
           $i=0;
            foreach ($data['response']['list'] as $k => $v) {
                $kode = $data['response']['list'][$i]['kodeDiagnosa'];
                $diag = $data['response']['list'][$i]['namaDiagnosa'];
                $result[] = ['id'=>$kode, 'value'=>$kode.'-'.$diag];
                $i++;
            }
        }      


        return response()->json($result);
    }

    public function insertsep(Request $request)
    {   
        $uri    = DB::table('tadmvar')->select('TAdmVar_Nama')
                ->where('TAdmVar_Seri','=','WEBS')
                ->where('TAdmVar_Kode','=','CSEP')
                ->first();

        $url    = DB::table('tadmvar')->select('TAdmVar_Nama')
                ->where('TAdmVar_Seri','=','RSSEP')
                ->where('TAdmVar_Kode','=','IPS')
                ->first(); 
        
        $url2   = $url->TAdmVar_Nama.$uri->TAdmVar_Nama;
        date_default_timezone_set("Asia/Bangkok");

        $tglSep = date_format(new DateTime($request->tglSep), 'Y-m-d').' '.date('H:i:s');
        $tglRujukan =  date_format(new DateTime($request->tglRujukan), 'Y-m-d').' '.date('H:i:s');
 
        // $scml = "<request>" ;
        // $scml .="<data>";
        // $scml .="<t_sep>" ;
        // $scml .="<noKartu>".$request->noKartu."</noKartu>"; 
        // $scml .="<tglSep>".$tglSep."</tglSep>";
        // $scml .="<tglRujukan>".$tglRujukan."</tglRujukan>"; 
        // $scml .="<noRujukan>".$request->noRujukan."</noRujukan>"; 
        // $scml .="<ppkRujukan>".$request->ppkRujukan."</ppkRujukan>"; 
        // $scml .="<ppkPelayanan>0173R037</ppkPelayanan>"; 
        // $scml .="<jnsPelayanan>".$request->jnsPelayanan."</jnsPelayanan>"; 
        // $scml .="<catatan>WS</catatan>"; 
        // $scml .="<diagAwal>".$request->diagAwal."</diagAwal>"; 
        // $scml .="<poliTujuan>".$request->poliTujuan."</poliTujuan> ";
        // $scml .="<klsRawat>".$request->klsRawat."</klsRawat>"; 
        // $scml .="<lakaLantas>2</lakaLantas>"; 
        // $scml .="<user>rsrs</user>"; 
        // $scml .="<noMr>".$request->noMR."</noMr>"; 
        // $scml .="</t_sep>"; 
        // $scml .="</data>"; 
        // $scml .="</request>";
        
        //post format untuk ke server dvlp bpjs - format baru    
        $scml  = "{";
        $scml .= "\"request\":";
        $scml .= "{";
        $scml .= "\"t_sep\":";
        $scml .= "{";
        $scml .= "\"noKartu\":\"$request->noKartu\",";
        $scml .= "\"tglSep\":\"$tglSep\",";
        $scml .= "\"tglRujukan\":\"$tglRujukan\",";
        $scml .= "\"noRujukan\":\"$request->noRujukan\",";
        $scml .= "\"ppkRujukan\":\"$request->ppkRujukan\",";
        $scml .= "\"ppkPelayanan\":\"0173R037\",";
        $scml .= "\"jnsPelayanan\":\"2\",";
        $scml .= "\"catatan\":\"Dari WS\",";
        $scml .= "\"diagAwal\":\"$request->diagAwal\",";
        $scml .= "\"poliTujuan\":\"UGD\",";
        $scml .= "\"klsRawat\":\"$request->klsRawat\",";
        $scml .= "\"lakaLantas\":\"2\",";
        $scml .= "\"lokasiLaka\":\"\",";
        $scml .= "\"user\":\"RS\",";
        $scml .= "\"noMr\":\"$request->noMR\"";
        $scml .= "}";
        $scml .= "}";
        $scml .= "}";


        $process = curl_init($url2); 
 
        curl_setopt($process, CURLOPT_URL, $url2);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLOPT_HTTPHEADER, bpjs::signaturepost());
        curl_setopt($process, CURLOPT_POST, true); 
        curl_setopt($process, CURLOPT_POSTFIELDS, $scml); 
        curl_setopt($process, CURLOPT_HTTPGET, 0);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);

        $return = curl_exec($process); 
        curl_close($process);
 
        $response = json_decode($return,true);
        
        return $response;
    }
}
