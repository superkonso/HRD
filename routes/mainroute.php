<?php
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// ================= Pendaftaran =======================
Route::resource('pasien', 'Pendaftaran\PasiensController');
Route::resource('appointment', 'Pendaftaran\AppointmentsController');
Route::resource('poli', 'Pendaftaran\PolisController');
Route::resource('ugddaftar', 'Pendaftaran\UgddaftarController');
Route::resource('daftarinap', 'Pendaftaran\InapdaftarController');

Route::get('/autocompletepasienbynorm', array('as' => 'autocompletepasienbynorm', 'uses' => 'Pendaftaran\AppointmentsController@autocompletePasienByNoRM'));

Route::post('/editpasien', 'Pendaftaran\PasiensController@editfromajax');
Route::put('/editpasien', 'Pendaftaran\PasiensController@editfromajax');
Route::get('/editpasien', 'Pendaftaran\PasiensController@editfromajax');

// ================= Rawat Jalan =======================

Route::resource('transpoli', 'Rawatjalan\PolisController');
Route::resource('transobatjalan', 'Rawatjalan\ObattransController');
Route::resource('kasirjalan', 'Rawatjalan\KasirjalansController');

Route::get('bayarkasirjalan/{noreg}', 'Rawatjalan\KasirjalansController@jalantransbayar');
Route::get('batalkasirjalan/{noreg}', 'Rawatjalan\KasirjalansController@jalantransbatal');

// ================= Gudang Farmasi =======================

Route::resource('/farmasiorderbeli', 'Gudangfarmasi\OrderobatsController');
Route::resource('/masterobat','Gudangfarmasi\MasterobatsController');

Route::resource('orderketstd', 'Gudangfarmasi\OrderketstdController');
Route::resource('obatterima', 'Gudangfarmasi\ObatterimasController');
Route::resource('obatkeluar', 'Gudangfarmasi\ObatkeluarsController');
Route::resource('stockopnamegudang', 'Gudangfarmasi\StockopnamegudangsController');

Route::post('/createorderketstd', 'Gudangfarmasi\OrderketstdController@createfromajax');
Route::put('/createorderketstd', 'Gudangfarmasi\OrderketstdController@createfromajax');
Route::get('/createorderketstd', 'Gudangfarmasi\OrderketstdController@createfromajax');

//===================UGD================================
Route::resource('transugd','Ugd\Ugdcontroller');
Route::resource('transobatugd', 'Ugd\ObattransController');

// ================= Wewenang ==========================
Route::resource('unit', 'Wewenang\UnitsController');
Route::resource('user', 'Wewenang\UsersController');
Route::resource('userlevel','Wewenang\UserlevelsController');
Route::resource('dokter','Wewenang\PelakusController');
Route::resource('spesialis','Wewenang\SpesialisController');
Route::resource('tarifjalan','Wewenang\TarifjalansController');
Route::resource('tarifgigi','Wewenang\TarifgigisController');
Route::resource('tarifugd','Wewenang\TarifugdsController');
Route::resource('tariflain','Wewenang\TariflainsController');


Route::resource('/menuitem','Wewenang\MenuitemController');

Route::get('editprofile/{users}', 'Wewenang\UsersController@editprofile');
Route::put('updateprofile/{users}', 'Wewenang\UsersController@updateprofile');

// ================= Sample Laporan ==========================
// Route::get('/lapdaftarjalan','Pendaftaran\LapdaftarjalanController@report');
Route::resource('lapdaftarjalan', 'Pendaftaran\LapdaftarjalanController');
// ===========================================================

// ================= Pendaftaran Laporan =====================
Route::post('ctklapharianjalan','Pendaftaran\LaporansController@ctklapharianjalan');
Route::get('ctklapharianjalan','Pendaftaran\LaporansController@ctklapharianjalan');

// ===========================================================

// ================= LAPORAN TARIF WEWENANG ========================
// laporan harian pendaftaran rawat jalan
Route::post('ctktarifugd','Wewenang\TarifugdsController@ctktarifugd');
Route::get('ctktarifugd','Wewenang\TarifugdsController@ctktarifugd');

// laporan data dokter / pelaku
Route::post('ctkpelaku','Pendaftaran\LaporansController@ctklapdatadokter');
Route::get('ctkpelaku','Pendaftaran\LaporansController@ctklapdatadokter');

// laporan data unit
Route::post('ctkunit','Pendaftaran\LaporansController@ctklapdataunit');
Route::get('ctkunit','Pendaftaran\LaporansController@ctklapdataunit');

// laporan tarif rawat jalan
Route::post('ctktarifjalan','Pendaftaran\LaporansController@ctklaptarifjalan');
Route::get('ctktarifjalan','Pendaftaran\LaporansController@ctklaptarifjalan');

// laporan tarif gigi
Route::post('ctktarifgigi','Pendaftaran\LaporansController@ctklaptarifgigi');
Route::get('ctktarifgigi','Pendaftaran\LaporansController@ctklaptarifgigi');


