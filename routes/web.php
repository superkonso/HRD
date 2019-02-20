<?php

use SIMRS\Helpers\autoNumberTransUnit;
use SIMRS\Helpers\autoNumberTrans;
use SIMRS\Helpers\autoNumber;

Route::get('/', 'Auth\LoginController@showLoginForm')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// ****************************** HRD / PERSONALIA ***********************************************
// *****************************************************************************************

	Route::resource('datakaryawan', 'Hrd\HrdController');
	Route::get('karyawankeluar/{id}', 'Hrd\HrdController@karyawanskeluar');
	Route::put('updatekaryawankeluar/{id}', 'Hrd\HrdController@updatekaryawanskeluar');
	Route::get('karyawancuti/{id}', 'Hrd\HrdController@karyawanscuti');
	Route::put('updatekaryawancuti/{id}', 'Hrd\HrdController@updatekaryawanscuti');
	Route::get('karyawanjabatan/{id}', 'Hrd\HrdController@karyawansjabatan');
	Route::put('updatekaryawanjabatan/{id}', 'Hrd\HrdController@updatekaryawansjabatan');
	
	// ************************************ USER PROFIL *****************************************
// ******************************************************************************************
	Route::get('editprofile/{users}', 'Wewenang\UsersController@editprofile');
	Route::put('updateprofile/{users}', 'Wewenang\UsersController@updateprofile');

// ******************************************************************************************
// ************************************* AUTOCOMPLETE ***************************************
// ******************************************************************************************
	Route::get('/autocompletepasien', array('as' => 'autocompletepasien', 'uses' => 'SearchController@autocompletepasien'));
	Route::get('/autocompletepasienbynorm', array('as' => 'autocompletepasienbynorm', 'uses' => 'Pendaftaran\AppointmentsController@autocompletePasienByNoRM'));
	Route::get('/autocompletepesertabpjs', array('as' => 'autocompletepesertabpjs', 'uses' => 'Pendaftaran\SepbpjsController@autocompletepesertabpjs'));
	Route::get('/autocompleteicd', array('as' => 'autocompleteicd', 'uses' => 'Pendaftaran\SepbpjsController@autocompleteicd'));
	Route::get('/autocompleteKodeOp', array('as' => 'autocompleteKodeOp', 'uses' => 'Kamaroperasi\TransoperasiControllers@autocompleteKodeOp'));
	Route::get('/autocompleteNamaOp', array('as' => 'autocompleteNamaOp', 'uses' => 'Kamaroperasi\TransoperasiControllers@autocompleteNamaOp'));
	Route::get('/autocompleteperkiraan', array('as' => 'autocompleteperkiraan', 'uses' => 'SearchController@perkiraanbykode'));
	Route::get('/autocompleteunit', array('as' => 'autocompleteunit', 'uses' => 'SearchController@unitbyname'));
	Route::get('/autocompleteicd', array('as' => 'autocompleteicd', 'uses' => 'SearchController@kodeicd'));
	Route::get('/autocompleteicopim', array('as' => 'autocompleteicopim', 'uses' => 'SearchController@geticopim'));
	Route::get('/autocompleteicopimrm', array('as' => 'autocompleteicopimrm', 'uses' => 'SearchController@geticopimrm'));


// ******************************************************************************************
// *************************************** WEWENANG *****************************************
// ******************************************************************************************
	Route::resource('unit', 'Wewenang\UnitsController');
	Route::resource('user', 'Wewenang\UsersController');
	Route::resource('userlevel','Wewenang\UserlevelsController');
	Route::resource('dokter','Wewenang\PelakusController');
	Route::resource('spesialis','Wewenang\SpesialisController');
	Route::resource('tarifjalan','Wewenang\TarifjalansController');
	Route::resource('tarifgigi','Wewenang\TarifgigisController');
	Route::resource('tarifugd','Wewenang\TarifugdsController');
	Route::resource('tariflain','Wewenang\TariflainsController');
	Route::resource('tariflab','Wewenang\TariflabsController');
	Route::resource('administrasi', 'Wewenang\AdministrasisController');
	Route::resource('aktvar', 'Wewenang\AktvarsController');
	Route::resource('tarifvar', 'Wewenang\TarifvarsController');
	Route::resource('wilayah', 'Wewenang\WilayahsController');
	Route::resource('rmvar', 'Wewenang\RmvarsController');
	Route::resource('tarifinap','Wewenang\TarifinapsController');	
	Route::resource('tariffisio','Wewenang\TariffisiosController');
	Route::resource('tarifhd','Wewenang\TarifhdsController');
	Route::resource('tarifrad','Wewenang\TarifradsController');
	Route::resource('tarifikb','Wewenang\TarifikbsController');
	Route::resource('tarifibs','Wewenang\TarifibssController');
	Route::resource('useractivity','Wewenang\UseractivityController');
	Route::resource('rspanel','Wewenang\RspanelController');
	Route::resource('/menuitem','Wewenang\MenuitemController');

	
	// === Laporan
	Route::post('ctktarifugd','Wewenang\TarifugdsController@ctktarifugd');
	Route::get('ctktarifugd','Wewenang\TarifugdsController@ctktarifugd');

	Route::post('ctkpelaku','Pendaftaran\LaporansController@ctklapdatadokter');
	Route::get('ctkpelaku','Pendaftaran\LaporansController@ctklapdatadokter');

	Route::post('ctkunit','Pendaftaran\LaporansController@ctklapdataunit');
	Route::get('ctkunit','Pendaftaran\LaporansController@ctklapdataunit');

	Route::post('ctkspesialis','Wewenang\SpesialisController@ctkspesialis');
	Route::get('ctkspesialis','Wewenang\SpesialisController@ctkspesialis');

	Route::post('ctktarifjalan','Pendaftaran\LaporansController@ctklaptarifjalan');
	Route::get('ctktarifjalan','Pendaftaran\LaporansController@ctklaptarifjalan');

	Route::post('ctktarifgigi','Pendaftaran\LaporansController@ctklaptarifgigi');
	Route::get('ctktarifgigi','Pendaftaran\LaporansController@ctklaptarifgigi');

	Route::post('ctktariflainlain','Pendaftaran\LaporansController@ctklaptariflainlain');
	Route::get('ctktariflainlain','Pendaftaran\LaporansController@ctklaptariflainlain');

	Route::post('ctktariflab','Pendaftaran\LaporansController@ctklaptariflab');
	Route::get('ctktariflab','Pendaftaran\LaporansController@ctklaptariflab');

	Route::post('ctkwilayah','Wewenang\WilayahsController@ctkwilayah');
	Route::get('ctkwilayah','Wewenang\WilayahsController@ctkwilayah');

	Route::post('ctkadministrasi','Wewenang\AdministrasisController@ctkadministrasi');
	Route::get('ctkadministrasi','Wewenang\AdministrasisController@ctkadministrasi');

	Route::post('ctkaktvar','Wewenang\AktvarsController@ctkaktvar');
	Route::get('ctkaktvar','Wewenang\AktvarsController@ctkaktvar');

	Route::post('ctktarifvar','Wewenang\TarifvarsController@ctktarifvar');
	Route::get('ctktarifvar','Wewenang\TarifvarsController@ctktarifvar');

	Route::post('ctkrmvar','Wewenang\RmvarsController@ctkrmvar');
	Route::get('ctkrmvar','Wewenang\RmvarsController@ctkrmvar');

	Route::post('ctktarifinap','Wewenang\TarifinapsController@ctktarifinap');
	Route::get('ctktarifinap','Wewenang\TarifinapsController@ctktarifinap');

	Route::post('ctktariffisio','Wewenang\TariffisiosController@ctktariffisio');
	Route::get('ctktariffisio','Wewenang\TariffisiosController@ctktariffisio');

	Route::post('ctktarifhd','Wewenang\TarifhdsController@ctktarifhd');
	Route::get('ctktarifhd','Wewenang\TarifhdsController@ctktarifhd');

	Route::post('ctktarifrad','Wewenang\TarifradsController@ctktarifrad');
	Route::get('ctktarifrad','Wewenang\TarifradsController@ctktarifrad');

	Route::post('ctktarifikb','Wewenang\TarifikbsController@ctktarifikb');
	Route::get('ctktarifikb','Wewenang\TarifikbsController@ctktarifikb');

	Route::post('ctktarifibs','Wewenang\TarifibssController@ctktarifibs');
	Route::get('ctktarifibs','Wewenang\TarifibssController@ctktarifibs');


// ******************************************************************************************
// ************************************ SAMPLE LAPORAN **************************************
// ******************************************************************************************
	Route::resource('lapdaftarjalan', 'Pendaftaran\LapdaftarjalanController');
	Route::resource('lapdaftarugd', 'Pendaftaran\LapdaftarugdController');
	Route::resource('lapvisitetinddokter', 'Ugd\VisitetindakandokterController');
	
	// Route::post('lapvisitetinddokter','Ugd\VisitetindakandokterController@ctkvisitetindakandokter');

	Route::resource('datatables', 'Sample\DatatablesController');



// ******************************************************************************************
// ************************************ INCLUDE ROUTES AJAX *********************************
// ******************************************************************************************
// ** Penambahan Routeajax, disusun Order by Name, Supaya Tidak Duplicate ** //

include 'routeajax/access.php';
include 'routeajax/admvar.php';
include 'routeajax/aktvar.php';
include 'routeajax/aruskas.php';
include 'routeajax/autonumber.php';
include 'routeajax/bpjs.php';
include 'routeajax/bukubesar.php';
include 'routeajax/cuti.php';
include 'routeajax/fisio.php';
include 'routeajax/gettagihan.php';
include 'routeajax/hrd.php';
include 'routeajax/ikb.php';
include 'routeajax/inaptrans.php';
include 'routeajax/info.php';
include 'routeajax/invoice.php';
include 'routeajax/jalantrans.php';
include 'routeajax/janjijalan.php';
include 'routeajax/jurnal.php';
include 'routeajax/jasadokter.php';
include 'routeajax/kamar.php';
include 'routeajax/kamarbersalin.php';
include 'routeajax/kamaroperasi.php';
include 'routeajax/karyawan.php';
include 'routeajax/kas.php';
include 'routeajax/kasir.php';
include 'routeajax/laboratoriumtrans.php';
include 'routeajax/labarugi.php';
include 'routeajax/logbook.php';
include 'routeajax/logistik.php';
include 'routeajax/margin.php';
include 'routeajax/menu.php';
include 'routeajax/mutasi.php';
include 'routeajax/neraca.php';
include 'routeajax/obat.php';
include 'routeajax/obatrngkartu.php';
include 'routeajax/obatgdgmts.php';
include 'routeajax/obatkmr.php';
include 'routeajax/obatkmrretur.php';
include 'routeajax/obatkmrdetil.php';
include 'routeajax/obatkmrkartu.php';
include 'routeajax/obatmts.php';
include 'routeajax/obatopname.php';
include 'routeajax/orderfrm.php';
include 'routeajax/orderketstd.php';
include 'routeajax/pabrik.php';
include 'routeajax/pasien.php';
include 'routeajax/pasiendaftarAll.php';
include 'routeajax/pelaku.php';
include 'routeajax/perkiraan.php';
include 'routeajax/perusahaan.php';
include 'routeajax/radiologitrans.php';
include 'routeajax/rawatinap.php';
include 'routeajax/rawatjalan.php';
include 'routeajax/rawatugd.php';
include 'routeajax/realisasiorder.php';
include 'routeajax/reffdokter.php';
include 'routeajax/rekammedisinap.php';
include 'routeajax/returfrm.php';
include 'routeajax/rmvar.php';
include 'routeajax/supplier.php';
include 'routeajax/tariffisio.php';
include 'routeajax/tarifgigi.php';
include 'routeajax/tarifhd.php';
include 'routeajax/tarifibs.php';
include 'routeajax/tarifikb.php';
include 'routeajax/tarifinap.php';
include 'routeajax/tarifjalan.php';
include 'routeajax/tariflain.php';
include 'routeajax/tariflab.php';
include 'routeajax/tarifradiologi.php';
include 'routeajax/tarifugd.php';
include 'routeajax/tarifvar.php';
include 'routeajax/terimafrm.php';
include 'routeajax/terimafrmdetil.php';
include 'routeajax/ttmptidur.php';
include 'routeajax/ugd.php';
include 'routeajax/unit.php';
include 'routeajax/surat.php';
include 'routeajax/spesialis.php';
include 'routeajax/stdtemplate.php';
include 'routeajax/users.php';
include 'routeajax/verifikasi.php';
include 'routeajax/vinaptrans.php';
include 'routeajax/vjalantrans.php';
include 'routeajax/vkasirlain.php';
include 'routeajax/vlapApjalan.php';
include 'routeajax/vobatharga.php';
include 'routeajax/vordersisa.php';
include 'routeajax/vpakaiobatjalan.php';
include 'routeajax/vrawatjalan.php';
include 'routeajax/vrekapperiksajalan.php';
include 'routeajax/wilayah.php';

// ** Mohon Penambahan Routeajax, disusun Order by Name, Supaya Tidak Duplicate ** //
