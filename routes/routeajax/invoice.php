<?php 
Route::get('/ajax-invView', function(){
    $tgl1  = Request::get('tgl1');
    $tgl2  = Request::get('tgl2');
    $tipe  = Request::get('tipe');
    $jenis = Request::get('jenis');
    $key   = Request::get('key');
    $agen  = Request::get('agen');

    $dt     = strtotime($tgl1);
    $tgl1   = date('Y-m-d'.' 00:00:00', $dt);
    $dt2    = strtotime($tgl2);
    $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);

    
    $hasil = DB::table('tptginv')
            ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('TPtgINV_Tanggal', array($tgl1, $tgl2));
                            })
            ->where(function ($query) use ($key) {
                            $query->where('TPtgINV_Nomor', 'like', '%'.strtolower($key).'%')
                                    ->orWhere('TPtgINV_Nama', 'like', '%'.strtolower($key).'%');
                             })
            ->get();

    return Response::json($hasil);
});

Route::get('/ajax-invsearch', function(){
    $tgl1 = Request::get('tgl1');
    $tgl2 = Request::get('tgl2');
    $tipe = Request::get('tipe');
    $key = Request::get('key');
    $penjamin = Request::get('penjamin');

    $dt     = strtotime($tgl1);
    $tgl1   = date('Y-m-d'.' 00:00:00', $dt);
    $dt2    = strtotime($tgl2);
    $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);

    $hasil = DB::table('vpiutangsaldoinap')
            ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('piutang_tanggal', array($tgl1, $tgl2));
                            })
            ->where(function ($query) use ($key) {
                            $query->where('pasien_nama', 'ilike', '%'.strtolower($key).'%')
                                    ->orWhere('no_reg', 'ilike', '%'.strtolower($key).'%');
                             })
            ->where('prsh_kode','=',$penjamin)
            ->whereNotIn('piutang_nomor', function($q){
                $q->select('TPtgINVDetil_Nomor')->from('tptginvdetil');
                })
            ->get();

    $hasil2 = DB::table('vpiutangsaldojalan as v')
            ->where(function ($query) use ($tgl1, $tgl2) {
                    $query->whereBetween('v.piutang_tanggal', array($tgl1, $tgl2));
                })
            ->where(function ($query) use ($key) {
                $query->where('v.pasien_nama', 'ilike', '%'.strtolower($key).'%')
                        ->orWhere('v.no_reg', 'ilike', '%'.strtolower($key).'%');
                })
            ->where('v.prsh_kode','=',$penjamin)
            ->whereNotIn('v.piutang_nomor', function($q){
                $q->select('TPtgINVDetil_Nomor')->from('tptginvdetil');
                })
            ->select('v.*')
            ->get();

    $data = $hasil->merge($hasil2);
    
    return Response::json($data);
});

Route::get('/ajax-invoicerekap', function(){
    $tgl1  = Request::get('tgl1');
    $tgl2  = Request::get('tgl2');
    $tipe  = Request::get('tipe');
    $key   = Request::get('key');
 
    $dt     = strtotime($tgl1);
    $tgl1   = date('Y-m-d'.' 00:00:00', $dt);
    $dt2    = strtotime($tgl2);
    $tgl2   = date('Y-m-d'.' 23:59:59', $dt2);   
    
    $hasil = DB::table('tptginv')
            ->where(function ($query) use ($tgl1, $tgl2) {
                                $query->whereBetween('TPtgINV_Tanggal', array($tgl1, $tgl2));
                            })
            ->where(function ($query) use ($tipe) {
                                $query->whereRaw('"TPtgINV_Status" =\''.$tipe.'\' OR \'A\'=\''.$tipe.'\'');
                            })  
            ->where(function ($query) use ($key) {
                            $query->where('TPtgINV_Nomor', 'ilike', '%'.strtolower($key).'%')
                                    ->orWhere('TPtgINV_Nama', 'ilike', '%'.strtolower($key).'%');
                            })
            ->get();

    return Response::json($hasil);
});