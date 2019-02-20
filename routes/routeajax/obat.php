<?php
use SIMRS\Helpers\saldoAkhirObat;

// === Pencarian Obat 
	Route::get('/ajax-obatsearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdObat');

		$obats = DB::table('tobat')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TObat_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where('TObat_Status', '=', 'A')
					->orderBy('TObat_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($obats);
	});

// === Pencarian Obat untuk opname unit (dengan kode unit + sisa saldo qty - jml di unit tersebut)
	Route::get('/ajax-obatsearchmutasi', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdObat');
		$kdUnit		= Request::get('kdUnit');
		$tgl 		= Request::get('tgl');
		$notrans	= Request::get('notrans');
		$dt2 		= strtotime($tgl);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$obats = DB::table('tgrupmutasi as g')
					->leftjoin('tobat as o','g.TObat_Kode','=','o.TObat_Kode')
					->select(DB::raw('o.*', 'g.TGrupMutasi_ObatMax','"0" as Stok','"0" as Jumlah'))
					->where(function ($query) use ($kuncicari) {
							$query->where('o.TObat_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('o.TObat_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where('g.TUnit_Kode', '=', $kdUnit)
					->orderBy('o.TObat_Nama', 'ASC')
					->limit(100)->get();

		foreach($obats as $data){
			$CekStok = saldoAkhirObat::hitungSaldoAkhirObat( $tgl2 , $kdUnit, $data->TObat_Kode, $notrans );
			$stokQty = 0;
         	$stokJml = 0;	
			foreach ($CekStok as $key) {
				$stokQty = $key->Saldo;
         		$stokJml = $key->JmlSaldo;
			}
    		$data->Stok 	= $stokQty;
    		$data->Jumlah 	= $stokJml;
		}

		return Response::json($obats);
	});
            	
            	
// === Master Data Obat

	Route::get('/ajax-obatmaster', function(){
		$jenis 		= Request::get('key1');
		$bentuk 	= Request::get('key2');
		$kuncicari 	= Request::get('key3');

		$obats = DB::table('tobat')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TObat_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(function ($query) use ($bentuk) {
								$query->whereRaw('"TGrup_Kode"=\''.$bentuk.'\' OR \'ALL\'=\''.$bentuk.'\'');
							})
					->where(function ($query) use ($jenis) {
								$query->whereRaw('"TGrup_Kode_Gol"=\''.$jenis.'\' OR \'ALL\'=\''.$jenis.'\'');
							})
					->orderBy('TObat_Nama', 'ASC')
					->limit(50)->get();

		return Response::json($obats);
	});

// === Pencarian Obat 
	Route::get('/ajax-obatgrupmutasisearch', function(){
		$kuncicari 	= Request::get('kuncicari');
		$kdObat 	= Request::get('kdObat');
		$kdUnit 	= Request::get('kdUnit');

		$obats = DB::table('tobat AS O')
					->leftJoin('tgrupmutasi AS GM', 'O.TObat_Kode', '=', 'GM.TObat_Kode')
					->where(function ($query) use ($kuncicari) {
							$query->where('O.TObat_Nama', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('O.TObat_Kode', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where('O.TObat_Status', '=', 'A')
					->where('GM.TUnit_Kode', $kdUnit)
					->orderBy('O.TObat_Nama', 'ASC')
					->limit(100)->get();

		return Response::json($obats);
	});

// === Grup Obat 
	Route::get('/ajax-grupobatsearch', function(){
		$kel 	= Request::get('kel');
		$key 	= Request::get('key');
		
		if ($kel == 'ALL') {
			$grups = DB::table('tgrup')
                 ->whereIn('TGrup_Jenis',array('GOLOBAT','OBAT', 'OBATSAT1','OBATSAT2'))
                 ->where(function ($query) use ($key) {
							$query->where('TGrup_Nama', 'ILIKE', '%'.strtolower($key).'%')
		  							->orWhere('TGrup_Kode', 'ILIKE', '%'.strtolower($key).'%');
							})
                 ->orderBy('TGrup_Jenis','ASC')
                 ->orderBy('TGrup_Kode','ASC')
                 ->limit(30)->get();

		} else {
			$grups = DB::table('tgrup')
                 ->where('TGrup_Jenis','=', $kel)
                 ->where(function ($query) use ($key) {
							$query->where('TGrup_Nama', 'ILIKE', '%'.strtolower($key).'%')
		  							->orWhere('TGrup_Kode', 'ILIKE', '%'.strtolower($key).'%');
							})
                 ->orderBy('TGrup_Jenis','ASC')
                 ->orderBy('TGrup_Kode','ASC')
                 ->limit(30)->get();

		}
		
		
		return Response::json($grups);
	});
// === Terima obat bantuan / lain lan
	Route::get('/ajax-obatterimabantu', function(){
		$kuncicari 	= Request::get('key3');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$terimas = DB::table('tterimabnt')
					->where(function ($query) use ($kuncicari) {
							$query->where('TTerimaBnt_Nomor', 'ILIKE', '%'.strtolower($kuncicari).'%')
		  							->orWhere('TTerimaBnt_BantuanKet', 'ILIKE', '%'.strtolower($kuncicari).'%');
							})
					->where(function ($query) use ($tgl1, $tgl2) {
										$query->whereBetween('TTerimaBnt_Tgl', array($tgl1, $tgl2));
								})
					->orderBy('TTerimaBnt_Nomor', 'ASC')
					->limit(100)->get();

		return Response::json($terimas);
	});

// === Laporan Stok GUDANG
	Route::get('/ajax-obatkartustokgudang', function(){
		$kuncicari 	= Request::get('key3');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatgdgkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatGdgKartu_Saldo",0) AS "TObatGdgKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatGdgKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatGdgKartu_Tanggal','DESC')
						->orderBy('TObatGdgKartu_Nomor','DESC')
						->orderBy('TObatGdgKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatGdgKartu_Saldo)) ? 0 : $saldoQty->TObatGdgKartu_Saldo;

		$saldoJml = DB::table('tobatgdgkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatGdgKartu_JmlSaldo",\'0\') AS "TObatGdgKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatGdgKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatGdgKartu_Tanggal','DESC')
						->orderBy('TObatGdgKartu_Nomor','DESC')
						->orderBy('TObatGdgKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatGdgKartu_JmlSaldo)) ? 0 : $saldoJml->TObatGdgKartu_JmlSaldo;


		$first = DB::table('tobatgdgkartu as k')
					->select('TObat_Kode', 'TObatGdgKartu_Tanggal', 'TObatGdgKartu_Nomor', 'TObatGdgKartu_AutoNomor', 'TObatGdgKartu_Keterangan','TObatGdgKartu_Debet', 'TObatGdgKartu_Kredit', 'TObatGdgKartu_Saldo','TObatGdgKartu_JmlDebet', 'TObatGdgKartu_JmlKredit', 'TObatGdgKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatGdgKartu_Tanggal', array($tgl1, $tgl2));
								})
					->orderBy('TObatGdgKartu_Tanggal','ASC')
		                       ->orderBy('TObatGdgKartu_Nomor','ASC')
		                       ->orderBy('TObatGdgKartu_AutoNomor','ASC')
					->get();

		$second = DB::table('tobatgdgkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatGdgKartu_Tanggal\", '' as \"TObatGdgKartu_Nomor\", '0' AS \"TObatGdgKartu_AutoNomor\", 'Saldo Awal' AS \"TObatGdgKartu_Keterangan\",'0' as \"TObatGdgKartu_Debet\", '0' As \"TObatGdgKartu_Kredit\"," .$ObatQty. " AS \"TObatGdgKartu_Saldo\",'0' AS \"TObatGdgKartu_JmlDebet\", '0' AS \"TObatGdgKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatGdgKartu_JmlSaldo\""))
					->orderBy('TObatGdgKartu_Tanggal','ASC')
		                       ->orderBy('TObatGdgKartu_Nomor','ASC')
		                       ->orderBy('TObatGdgKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

	Route::get('/ajax-obatkartupersediaangudang', function(){
		$kuncicari 	= Request::get('key3');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatgdgkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatGdgKartu_Saldo",0) AS "TObatGdgKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatGdgKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatGdgKartu_Tanggal','DESC')
						->orderBy('TObatGdgKartu_Nomor','DESC')
						->orderBy('TObatGdgKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatGdgKartu_Saldo)) ? 0 : $saldoQty->TObatGdgKartu_Saldo;

		$saldoJml = DB::table('tobatgdgkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatGdgKartu_JmlSaldo",\'0\') AS "TObatGdgKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatGdgKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatGdgKartu_Tanggal','DESC')
						->orderBy('TObatGdgKartu_Nomor','DESC')
						->orderBy('TObatGdgKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatGdgKartu_JmlSaldo)) ? 0 : $saldoJml->TObatGdgKartu_JmlSaldo;


		$first = DB::table('tobatgdgkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->select('k.TObat_Kode', 'TObatGdgKartu_Tanggal', 'TObatGdgKartu_Nomor', 'TObatGdgKartu_AutoNomor', 'TObatGdgKartu_Keterangan','O.TObat_HargaPokok','TObatGdgKartu_Debet',DB::raw('"k"."TObatGdgKartu_Debet" * "O"."TObat_HargaPokok" AS "RupiahDebet"'), 'TObatGdgKartu_Kredit',DB::raw('"k"."TObatGdgKartu_Kredit" * "O"."TObat_HargaPokok" AS "RupiahKredit"'), 'TObatGdgKartu_Saldo',DB::raw('"k"."TObatGdgKartu_Saldo" * "O"."TObat_HargaPokok" AS "RupiahSaldo"'),'TObatGdgKartu_JmlDebet', 'TObatGdgKartu_JmlKredit', 'TObatGdgKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('k.TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatGdgKartu_Tanggal', array($tgl1, $tgl2));
								})
					->orderBy('TObatGdgKartu_Tanggal','ASC')
		                       ->orderBy('TObatGdgKartu_Nomor','ASC')
		                       ->orderBy('TObatGdgKartu_AutoNomor','ASC')
					->get();
		return Response::json($first);	

		$second = DB::table('tobatgdgkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatGdgKartu_Tanggal\", '' as \"TObatGdgKartu_Nomor\", '0' AS \"TObatGdgKartu_AutoNomor\", 'Saldo Awal' AS \"TObatGdgKartu_Keterangan\",'0' as \"TObat_HargaPokok\",'0' as \"TObatGdgKartu_Debet\",'0' as \"JumlahDebet\", '0' As \"TObatGdgKartu_Kredit\",'0' as \"JumlahKredit\"," .$ObatQty. " AS \"TObatGdgKartu_Saldo\",'0' as \"JumlahSaldo\",'0' AS \"TObatGdgKartu_JmlDebet\", '0' AS \"TObatGdgKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatGdgKartu_JmlSaldo\""))
					->orderBy('TObatGdgKartu_Tanggal','ASC')
		                       ->orderBy('TObatGdgKartu_Nomor','ASC')
		                       ->orderBy('TObatGdgKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

	// === Laporan Stok Farmasi
	Route::get('/ajax-obatkartustokfarmasi', function(){
		$kuncicari 	= Request::get('key3');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatkmrkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatKmrKartu_Saldo",0) AS "TObatKmrKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatKmrKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatKmrKartu_Tanggal','DESC')
						->orderBy('TObatKmrKartu_Nomor','DESC')
						->orderBy('TObatKmrKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatKmrKartu_Saldo)) ? 0 : $saldoQty->TObatKmrKartu_Saldo;

		$saldoJml = DB::table('tobatkmrkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatKmrKartu_JmlSaldo",\'0\') AS "TObatKmrKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatKmrKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatKmrKartu_Tanggal','DESC')
						->orderBy('TObatKmrKartu_Nomor','DESC')
						->orderBy('TObatKmrKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatKmrKartu_JmlSaldo)) ? 0 : $saldoJml->TObatKmrKartu_JmlSaldo;


		$first = DB::table('tobatkmrkartu as k')
					->select('TObat_Kode', 'TObatKmrKartu_Tanggal', 'TObatKmrKartu_Nomor', 'TObatKmrKartu_AutoNomor', 'TObatKmrKartu_Keterangan','TObatKmrKartu_Debet', 'TObatKmrKartu_Kredit', 'TObatKmrKartu_Saldo','TObatKmrKartu_JmlDebet', 'TObatKmrKartu_JmlKredit', 'TObatKmrKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatKmrKartu_Tanggal', array($tgl1, $tgl2));
								})
					->orderBy('TObatKmrKartu_Tanggal','ASC')
		                       ->orderBy('TObatKmrKartu_Nomor','ASC')
		                       ->orderBy('TObatKmrKartu_AutoNomor','ASC')
					->get();

		$second = DB::table('tobatkmrkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatKmrKartu_Tanggal\", '' as \"TObatKmrKartu_Nomor\", '0' AS \"TObatKmrKartu_AutoNomor\", 'Saldo Awal' AS \"TObatKmrKartu_Keterangan\",'0' as \"TObatKmrKartu_Debet\", '0' As \"TObatKmrKartu_Kredit\"," .$ObatQty. " AS \"TObatKmrKartu_Saldo\",'0' AS \"TObatKmrKartu_JmlDebet\", '0' AS \"TObatKmrKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatKmrKartu_JmlSaldo\""))
					->orderBy('TObatKmrKartu_Tanggal','ASC')
		                       ->orderBy('TObatKmrKartu_Nomor','ASC')
		                       ->orderBy('TObatKmrKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

	Route::get('/ajax-obatkartupersediaanfarmasi', function(){
		$kuncicari 	= Request::get('key3');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatkmrkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatKmrKartu_Saldo",0) AS "TObatKmrKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatKmrKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatKmrKartu_Tanggal','DESC')
						->orderBy('TObatKmrKartu_Nomor','DESC')
						->orderBy('TObatKmrKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatKmrKartu_Saldo)) ? 0 : $saldoQty->TObatKmrKartu_Saldo;

		$saldoJml = DB::table('tobatkmrkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatKmrKartu_JmlSaldo",\'0\') AS "TObatKmrKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatKmrKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->orderBy('TObatKmrKartu_Tanggal','DESC')
						->orderBy('TObatKmrKartu_Nomor','DESC')
						->orderBy('TObatKmrKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatKmrKartu_JmlSaldo)) ? 0 : $saldoJml->TObatKmrKartu_JmlSaldo;


		$first = DB::table('tobatkmrkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->select('k.TObat_Kode', 'TObatKmrKartu_Tanggal', 'TObatKmrKartu_Nomor', 'TObatKmrKartu_AutoNomor', 'TObatKmrKartu_Keterangan','O.TObat_HargaPokok','TObatKmrKartu_Debet',DB::raw('"k"."TObatKmrKartu_Debet" * "O"."TObat_HargaPokok" AS "RupiahDebet"'), 'TObatKmrKartu_Kredit',DB::raw('"k"."TObatKmrKartu_Kredit" * "O"."TObat_HargaPokok" AS "RupiahKredit"'), 'TObatKmrKartu_Saldo',DB::raw('"k"."TObatKmrKartu_Saldo" * "O"."TObat_HargaPokok" AS "RupiahSaldo"'),'TObatKmrKartu_JmlDebet', 'TObatKmrKartu_JmlKredit', 'TObatKmrKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('k.TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatKmrKartu_Tanggal', array($tgl1, $tgl2));
								})
					->orderBy('TObatKmrKartu_Tanggal','ASC')
		                       ->orderBy('TObatKmrKartu_Nomor','ASC')
		                       ->orderBy('TObatKmrKartu_AutoNomor','ASC')
					->get();
		return Response::json($first);	

		$second = DB::table('tobatkmrkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatKmrKartu_Tanggal\", '' as \"TObatKmrKartu_Nomor\", '0' AS \"TObatKmrKartu_AutoNomor\", 'Saldo Awal' AS \"TObatKmrKartu_Keterangan\",'0' as \"TObat_HargaPokok\",'0' as \"TObatKmrKartu_Debet\",'0' as \"JumlahDebet\", '0' As \"TObatKmrKartu_Kredit\",'0' as \"JumlahKredit\"," .$ObatQty. " AS \"TObatKmrKartu_Saldo\",'0' as \"JumlahSaldo\",'0' AS \"TObatKmrKartu_JmlDebet\", '0' AS \"TObatKmrKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatKmrKartu_JmlSaldo\""))
					->orderBy('TObatKmrKartu_Tanggal','ASC')
		                       ->orderBy('TObatKmrKartu_Nomor','ASC')
		                       ->orderBy('TObatKmrKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

	// === Laporan Stok UNIT
	Route::get('/ajax-obatkartustokunit', function(){
		$kuncicari 	= Request::get('key3');
		$unit = Request::get('key4');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatrngkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatRngKartu_Saldo",0) AS "TObatRngKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatRngKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
						->orderBy('TObatRngKartu_Tanggal','DESC')
						->orderBy('TObatRngKartu_Nomor','DESC')
						->orderBy('TObatRngKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatRngKartu_Saldo)) ? 0 : $saldoQty->TObatRngKartu_Saldo;

		$saldoJml = DB::table('tobatrngkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatRngKartu_JmlSaldo",\'0\') AS "TObatRngKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatRngKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
						->orderBy('TObatRngKartu_Tanggal','DESC')
						->orderBy('TObatRngKartu_Nomor','DESC')
						->orderBy('TObatRngKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatRngKartu_JmlSaldo)) ? 0 : $saldoJml->TObatRngKartu_JmlSaldo;


		$first = DB::table('tobatrngkartu as k')
					->select('TObat_Kode', 'TObatRngKartu_Tanggal', 'TObatRngKartu_Nomor', 'TObatRngKartu_AutoNomor', 'TObatRngKartu_Keterangan','TObatRngKartu_Debet', 'TObatRngKartu_Kredit', 'TObatRngKartu_Saldo','TObatRngKartu_JmlDebet', 'TObatRngKartu_JmlKredit', 'TObatRngKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatRngKartu_Tanggal', array($tgl1, $tgl2));
								})
					->orderBy('TObatRngKartu_Tanggal','ASC')
		                       ->orderBy('TObatRngKartu_Nomor','ASC')
		                       ->orderBy('TObatRngKartu_AutoNomor','ASC')
					->get();

		$second = DB::table('tobatrngkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatRngKartu_Tanggal\", '' as \"TObatRngKartu_Nomor\", '0' AS \"TObatRngKartu_AutoNomor\", 'Saldo Awal' AS \"TObatRngKartu_Keterangan\",'0' as \"TObatRngKartu_Debet\", '0' As \"TObatRngKartu_Kredit\"," .$ObatQty. " AS \"TObatRngKartu_Saldo\",'0' AS \"TObatRngKartu_JmlDebet\", '0' AS \"TObatRngKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatRngKartu_JmlSaldo\""))
					->orderBy('TObatRngKartu_Tanggal','ASC')
		                       ->orderBy('TObatRngKartu_Nomor','ASC')
		                       ->orderBy('TObatRngKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

	Route::get('/ajax-obatkartupersediaanunit', function(){
		$kuncicari 	= Request::get('key3');
		$unit = Request::get('key4');
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d'.' 00:00:01', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d'.' 23:59:59', $dt2);

		$saldoQty = DB::table('tobatrngkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatRngKartu_Saldo",0) AS "TObatRngKartu_Saldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatRngKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
						->orderBy('TObatRngKartu_Tanggal','DESC')
						->orderBy('TObatRngKartu_Nomor','DESC')
						->orderBy('TObatRngKartu_AutoNomor','DESC')
						->first();
		$ObatQty = (empty($saldoQty->TObatRngKartu_Saldo)) ? 0 : $saldoQty->TObatRngKartu_Saldo;

		$saldoJml = DB::table('tobatrngkartu AS K')
						->select(DB::raw('COALESCE("K"."TObatRngKartu_JmlSaldo",\'0\') AS "TObatRngKartu_JmlSaldo" '))
						->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatRngKartu_Tanggal', '<=',$tgl1);
								})
						->where(function ($query) use ($kuncicari) {
							$query->where('TObat_Kode', '=', $kuncicari);
		  						})
						->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
						->orderBy('TObatRngKartu_Tanggal','DESC')
						->orderBy('TObatRngKartu_Nomor','DESC')
						->orderBy('TObatRngKartu_AutoNomor','DESC')
						->first();
		$ObatJumlah = (empty($saldoJml->TObatRngKartu_JmlSaldo)) ? 0 : $saldoJml->TObatRngKartu_JmlSaldo;


		$first = DB::table('tobatrngkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->select('k.TObat_Kode', 'TObatRngKartu_Tanggal', 'TObatRngKartu_Nomor', 'TObatRngKartu_AutoNomor', 'TObatRngKartu_Keterangan','O.TObat_HargaPokok','TObatRngKartu_Debet',DB::raw('"k"."TObatRngKartu_Debet" * "O"."TObat_HargaPokok" AS "RupiahDebet"'), 'TObatRngKartu_Kredit',DB::raw('"k"."TObatRngKartu_Kredit" * "O"."TObat_HargaPokok" AS "RupiahKredit"'), 'TObatRngKartu_Saldo',DB::raw('"k"."TObatRngKartu_Saldo" * "O"."TObat_HargaPokok" AS "RupiahSaldo"'),'TObatRngKartu_JmlDebet', 'TObatRngKartu_JmlKredit', 'TObatRngKartu_JmlSaldo')
					->where(function ($query) use ($kuncicari) {
							$query->where('k.TObat_Kode', '=', $kuncicari);
		  						})
					->where(function ($query) use ($tgl1, $tgl2) {
							$query->whereBetween('TObatRngKartu_Tanggal', array($tgl1, $tgl2));
								})
					->where(function ($query) use ($unit) {
							$query->where('TUnit_Kode', '=', $unit);
		  						})
					->orderBy('TObatRngKartu_Tanggal','ASC')
		                       ->orderBy('TObatRngKartu_Nomor','ASC')
		                       ->orderBy('TObatRngKartu_AutoNomor','ASC')
					->get();

		$second = DB::table('tobatrngkartu')
					->select(DB::raw("'".$kuncicari."' As \"TObat_Kode\", '".$tgl1."' \"TObatRngKartu_Tanggal\", '' as \"TObatRngKartu_Nomor\", '0' AS \"TObatRngKartu_AutoNomor\", 'Saldo Awal' AS \"TObatRngKartu_Keterangan\",'0' as \"TObat_HargaPokok\",'0' as \"TObatRngKartu_Debet\",'0' as \"RupiahDebet\", '0' As \"TObatRngKartu_Kredit\",'0' as \"RupiahKredit\"," .$ObatQty. " AS \"TObatRngKartu_Saldo\",'0' as \"RupiahSaldo\",'0' AS \"TObatRngKartu_JmlDebet\", '0' AS \"TObatRngKartu_JmlKredit\"," .$ObatJumlah. " AS \"TObatRngKartu_JmlSaldo\""))
					->orderBy('TObatRngKartu_Tanggal','ASC')
		                       ->orderBy('TObatRngKartu_Nomor','ASC')
		                       ->orderBy('TObatRngKartu_AutoNomor','ASC')
					->limit('1')
					->get();	

		$laporankartustok =  $second->union($first);
		return Response::json($laporankartustok);
	});

// LAPORAN SALDO
	Route::get('/ajax-obatsaldogudang', function(){
		$tgl1 	= Request::get('key1');
		$kelObat = Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 23:59:59', $dt1);
		
		$lapsaldoobat = DB::table('tobatgdgkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan','O.TObat_Minimal', DB::Raw('
									(SELECT COALESCE("k2"."TObatGdgKartu_Saldo",0) 
								     FROM tobatgdgkartu k2 
									 WHERE "k2"."TObatGdgKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatGdgKartu_Tanggal" DESC, "k2"."TObatGdgKartu_AutoNomor" DESC
									 Limit 1
									) As ObatGdgAkhir
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatGdgKartu_JmlSaldo",0) 
								     FROM tobatgdgkartu k3 
									 WHERE "k3"."TObatGdgKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatGdgKartu_Tanggal" DESC, "k3"."TObatGdgKartu_AutoNomor" DESC
									 Limit 1
									) As ObatGdgJmlAkhir
								')
							)
					->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatGdgKartu_Tanggal','<=',$tgl1);
								})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

	Route::get('/ajax-obatsaldofarmasi', function(){
		$tgl1 	= Request::get('key1');
		$kelObat = Request::get('key2');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 23:59:59', $dt1);

	
		$lapsaldoobat = DB::table('tobatkmrkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan','O.TObat_Minimal', DB::Raw('
									(SELECT COALESCE("k2"."TObatKmrKartu_Saldo",0) 
								     FROM tobatkmrkartu k2 
									 WHERE "k2"."TObatKmrKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatKmrKartu_Tanggal" DESC, "k2"."TObatKmrKartu_AutoNomor" DESC
									 Limit 1
									) As ObatKmrAkhir
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatKmrKartu_JmlSaldo",0) 
								     FROM tobatkmrkartu k3 
									 WHERE "k3"."TObatKmrKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatKmrKartu_Tanggal" DESC, "k3"."TObatKmrKartu_AutoNomor" DESC
									 Limit 1
									) As ObatKmrJmlAkhir
								')
							)
					->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatKmrKartu_Tanggal','<=',$tgl1);
								})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

	Route::get('/ajax-obatsaldounit', function(){
		$tgl1 	= Request::get('key1');
		$kelObat = Request::get('key2');
		$units = Request::get('key3');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 23:59:59', $dt1);

		
		$lapsaldoobat = DB::table('tobatrngkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan','O.TObat_Minimal', DB::Raw('
									(SELECT COALESCE("k2"."TObatRngKartu_Saldo",0) 
								     FROM tobatrngkartu k2 
									 WHERE "k2"."TObatRngKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatRngKartu_Tanggal" DESC, "k2"."TObatRngKartu_AutoNomor" DESC
									 Limit 1
									) As ObatRngAkhir
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatRngKartu_JmlSaldo",0) 
								     FROM tobatrngkartu k3 
									 WHERE "k3"."TObatRngKartu_Tanggal" <= \''.$tgl1.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatRngKartu_Tanggal" DESC, "k3"."TObatRngKartu_AutoNomor" DESC
									 Limit 1
									) As ObatRngJmlAkhir
								')
							)
					->where(function ($query) use ($tgl1) {
							$query->whereDate('TObatRngKartu_Tanggal','<=',$tgl1);
								})
					->where(function ($query) use ($units) {
							$query->where('k.TUnit_Kode', '=', $units);
		  						})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')					
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

	// LAPORAN MUTASI
	Route::get('/ajax-obatmutasigudang', function(){
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');
		$kelObat = Request::get('key3');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 00:00:00', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d 23:59:59', $dt2);


		
		$lapsaldoobat = DB::table('tobatgdgkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->leftJoin(
						DB::Raw('
									( SELECT 	"OK"."TObat_Kode", SUM("OK"."TObatGdgKartu_Debet") as "ObatGdgDebet", SUM("OK"."TObatGdgKartu_JmlDebet") as "ObatGdgJmlDebet",
              							SUM("OK"."TObatGdgKartu_Kredit") as "ObatGdgKredit", SUM(
              							"OK"."TObatGdgKartu_JmlKredit") as "ObatGdgJmlKredit"
       								  FROM TObatGdgKartu AS "OK"
								       	WHERE "OK"."TObatGdgKartu_Tanggal" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\'
								       	GROUP BY "OK"."TObat_Kode"
									) As "ST"
								'),'ST.TObat_Kode','=','k.TObat_Kode'
					)
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan',DB::raw('COALESCE("ST"."ObatGdgDebet",0) As "ObatGdgDebet"'),DB::raw('COALESCE("ST"."ObatGdgJmlDebet",0) As "ObatGdgJmlDebet"'),DB::raw('COALESCE("ST"."ObatGdgKredit",0) As "ObatGdgKredit"'),DB::raw('COALESCE("ST"."ObatGdgJmlKredit",0) As "ObatGdgJmlKredit"'), DB::Raw('
									(SELECT COALESCE("k2"."TObatGdgKartu_Saldo",0) 
								     FROM tobatgdgkartu k2 
									 WHERE "k2"."TObatGdgKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatGdgKartu_Tanggal" DESC, "k2"."TObatGdgKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatGdgAkhir"
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatGdgKartu_JmlSaldo",0) 
								     FROM tobatgdgkartu k3 
									 WHERE "k3"."TObatGdgKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatGdgKartu_Tanggal" DESC, "k3"."TObatGdgKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatGdgJmlAkhir"
								')
							)
					->where(function ($query) use ($tgl2) {
							$query->whereDate('TObatGdgKartu_Tanggal','<=',$tgl2);
								})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

	Route::get('/ajax-obatmutasifarmasi', function(){
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');
		$kelObat = Request::get('key3');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 00:00:00', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d 23:59:59', $dt2);


		
		$lapsaldoobat = DB::table('tobatkmrkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->leftJoin(
						DB::Raw('
									( SELECT 	"OK"."TObat_Kode", SUM("OK"."TObatKmrKartu_Debet") as "ObatKmrDebet", SUM("OK"."TObatKmrKartu_JmlDebet") as "ObatKmrJmlDebet",
              							SUM("OK"."TObatKmrKartu_Kredit") as "ObatKmrKredit", SUM(
              							"OK"."TObatKmrKartu_JmlKredit") as "ObatKmrJmlKredit"
       								  FROM TObatKmrKartu AS "OK"
								       	WHERE "OK"."TObatKmrKartu_Tanggal" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\'
								       	GROUP BY "OK"."TObat_Kode"
									) As "ST"
								'),'ST.TObat_Kode','=','k.TObat_Kode'
					)
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan',DB::raw('COALESCE("ST"."ObatKmrDebet",0) As "ObatKmrDebet"'),DB::raw('COALESCE("ST"."ObatKmrJmlDebet",0) As "ObatKmrJmlDebet"'),DB::raw('COALESCE("ST"."ObatKmrKredit",0) As "ObatKmrKredit"'),DB::raw('COALESCE("ST"."ObatKmrJmlKredit",0) As "ObatKmrJmlKredit"'), DB::Raw('
									(SELECT COALESCE("k2"."TObatKmrKartu_Saldo",0) 
								     FROM tobatkmrkartu k2 
									 WHERE "k2"."TObatKmrKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatKmrKartu_Tanggal" DESC, "k2"."TObatKmrKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatKmrAkhir"
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatKmrKartu_JmlSaldo",0) 
								     FROM tobatkmrkartu k3 
									 WHERE "k3"."TObatKmrKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatKmrKartu_Tanggal" DESC, "k3"."TObatKmrKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatKmrJmlAkhir"
								')
							)
					->where(function ($query) use ($tgl2) {
							$query->whereDate('TObatKmrKartu_Tanggal','<=',$tgl2);
								})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

	Route::get('/ajax-obatmutasiunit', function(){
		$tgl1 	= Request::get('key1');
		$tgl2 	= Request::get('key2');
		$kelObat = Request::get('key3');
		$units = Request::get('key4');

		$dt1 		= strtotime($tgl1);
		$tgl1 		= date('Y-m-d 00:00:00', $dt1);
		$dt2 		= strtotime($tgl2);
		$tgl2 		= date('Y-m-d 23:59:59', $dt2);


		
		$lapsaldoobat = DB::table('tobatrngkartu as k')
					->leftJoin('tobat as O', 'O.TObat_Kode', '=', 'k.TObat_Kode')
					->leftJoin('tgrup as G', function($join)
                         {
                             $join->on('G.TGrup_Kode', '=', 'O.TGrup_Kode');
                             $join->on('G.TGrup_Jenis','=', DB::raw('\'OBAT\''));
                         })
					->leftJoin(
						DB::Raw('
									( SELECT 	"OK"."TObat_Kode", SUM("OK"."TObatRngKartu_Debet") as "ObatRngDebet", SUM("OK"."TObatRngKartu_JmlDebet") as "ObatRngJmlDebet",
              							SUM("OK"."TObatRngKartu_Kredit") as "ObatRngKredit", SUM(
              							"OK"."TObatRngKartu_JmlKredit") as "ObatRngJmlKredit"
       								  FROM TObatRngKartu AS "OK"
								       	WHERE "OK"."TObatRngKartu_Tanggal" BETWEEN \''.$tgl1.'\' AND \''.$tgl2.'\' AND "OK"."TUnit_Kode" = \''.$units.'\'
								       	GROUP BY "OK"."TObat_Kode"
									) As "ST"
								'),'ST.TObat_Kode','=','k.TObat_Kode'
					)
					->select(DB::Raw(' DISTINCT "k"."TObat_Kode" '), 'G.TGrup_Kode', 'G.TGrup_Nama','O.TObat_Nama','O.TObat_Satuan',DB::raw('COALESCE("ST"."ObatRngDebet",0) AS "ObatRngDebet"'),DB::raw('COALESCE("ST"."ObatRngJmlDebet",0)  AS "ObatRngJmlDebet"'),DB::raw('COALESCE("ST"."ObatRngKredit",0) AS "ObatRngKredit"'),DB::raw('COALESCE("ST"."ObatRngJmlKredit",0) AS "ObatRngJmlKredit"'), DB::Raw('
									(SELECT COALESCE("k2"."TObatRngKartu_Saldo",0) 
								     FROM tobatrngkartu k2 
									 WHERE "k2"."TObatRngKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k2"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k2"."TObat_Kode" ASC, "k2"."TObatRngKartu_Tanggal" DESC, "k2"."TObatRngKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatRngAkhir"
								'), DB::Raw('
									(SELECT COALESCE("k3"."TObatRngKartu_JmlSaldo",0) 
								     FROM tobatrngkartu k3 
									 WHERE "k3"."TObatRngKartu_Tanggal" <= \''.$tgl2.'\' AND 
									       "k3"."TObat_Kode" = "k"."TObat_Kode"
									 ORDER BY "k3"."TObat_Kode" ASC, "k3"."TObatRngKartu_Tanggal" DESC, "k3"."TObatRngKartu_AutoNomor" DESC
									 Limit 1
									) As "ObatRngJmlAkhir"
								')
							)
					->where(function ($query) use ($tgl2) {
							$query->whereDate('TObatRngKartu_Tanggal','<=',$tgl2);
								})
					->where(function ($query) use ($units) {
							$query->where('k.TUnit_Kode', '=', $units);
		  						})
					->where(function ($query) use ($kelObat) {
							$query->where('O.TGrup_Kode', '=', $kelObat)
									->orWhere(DB::Raw('\'ALL\''),'=', strtoupper($kelObat));
		  						})
					->orderBy('O.TObat_Satuan','ASC')
					->orderBy('k.TObat_Kode','ASC')
					->get();
		
		return Response::json($lapsaldoobat);	
	});

// === Pencarian Pemberian Obat Pasien dari Referensi Dokter =========

	Route::get('/ajax-getHasilApotekRefDok', function(){
		$noreg = Request::get('noreg');

		$obat = DB::table('tobatkmrdetil AS D')
						->leftJoin('tobatkmr AS O', 'D.TObatKmr_Nomor', '=', 'O.TObatKmr_Nomor')
						->select('D.*')
						->where('O.TRawatJalan_NoReg', '=', $noreg)
						->orderBy('D.id', 'ASC')
						->get();

		return Response::json($obat);
	});

