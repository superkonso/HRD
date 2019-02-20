<?php
use SIMRS\Helpers\saldoAkhirObat;

// ==== Mutasi obat 
	Route::get('/ajax-mutasiobatsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$mutasiobat = DB::table('tobatgdgmts as g')
						->leftJoin('tunit AS T', 'T.TUnit_Kode', '=', 'g.TUnit_Kode_Tujuan')
						->select('g.*', 'T.TUnit_Nama')
						->where(function ($query) use ($key3) {
								$query->where('TObatGdgMts_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('g.TUnit_Kode_Tujuan', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TUnit_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TObatGdgMts_Tanggal', array($tgl1, $tgl2));
								})
						->orderBy('TObatGdgMts_Nomor', 'ASC')
						->limit(100)->get();

		return Response::json($mutasiobat);
	});

//====== retur obat dari unit ke gudang
	Route::get('/ajax-returobatsearch', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');
		$key3 	= Request::get('key3');
		$key4	= Request::get('key4');
		$key5 	= Request::get('key5');

		$dt1 	= strtotime($key1);
		$tgl1 	= date('Y-m-d'.' 00:00:00', $dt1);

		$dt2 	= strtotime($key2);
		$tgl2 	= date('Y-m-d'.' 23:59:59', $dt2);

		$mutasiobat = DB::table('tobatgdgmts as g')
						->leftJoin('tunit AS T', 'T.TUnit_Kode', '=', 'g.TUnit_Kode_Tujuan')
						->select('g.*', 'T.TUnit_Nama')
						->where(function ($query) use ($key3) {
								$query->where('TObatGdgMts_Nomor', 'ILIKE', '%'.strtolower($key3).'%')
			  							->orWhere('T.TUnit_Nama', 'ILIKE', '%'.strtolower($key3).'%');
								})
						->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TObatGdgMts_Tanggal', array($tgl1, $tgl2));
								})
						->where('g.TUnit_Kode_Asal','=',$key4)
						->where('g.TUnit_Kode_Tujuan','=',$key5)
						->where('g.TObatGdgMts_Retur','=','1')
						->orderBy('TObatGdgMts_Nomor', 'ASC')
						->limit(100)->get();

		return Response::json($mutasiobat);
	});


	// ====List Mutasi obat 
	Route::get('/ajax-listmutasiobat', function(){
		$key1 	= Request::get('key1');
		$key2 	= Request::get('key2');

		$mutasiobat = DB::table('tobat as o')
						->rightjoin('tgrupmutasi as g','g.TObat_Kode','=','o.TObat_Kode')
						->select(DB::Raw('o.*, g."TUnit_Kode" ,0 as Stok, 0 as Jumlah'))		
						//->select('o.*','g.TUnit_Kode',DB::raw('0 Stok'),DB::raw('0 Jumlah'))
						->where(function ($query) use ($key2) {
								$query->where('o.TObat_Kode', 'ILIKE', '%'.strtolower($key2).'%')
			  							->orWhere('o.TObat_Nama', 'ILIKE', '%'.strtolower($key2).'%');
								})
						->where(function ($query) use ($key1) {
								$query->whereRaw('L.TTarifVar_Kode', '=', $tarifkel);
							})
						->orderBy('o.TObat_Kode', 'ASC')
						->limit(100)->get();
		
		$tgl        =  date('Y-m-d H:i:s');

		foreach($mutasiobat as $data){
			$CekStok = saldoAkhirObat::hitungSaldoAkhirObat( $tgl , $data->TUnit_Kode, $data->TObat_Kode, '' );	

			$stokQty = 0;
         	$stokJml = 0;
			foreach ($CekStok as $key) {
				$stokQty = $key->Saldo;
         		$stokJml = $key->JmlSaldo;
			}
    		$data->stok 	= $stokQty;
    		$data->jumlah 	= $stokJml;
		}

		return Response::json($mutasiobat);
	});