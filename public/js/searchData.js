// ============================= Cari Data ===================================

// === Pasien ===
function cPasienLama(){
	var pasiennama = '';

	$.get('/ajax-pasienbynama?pasiennama='+pasiennama, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="100%">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, pasienObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoRM\', \''+pasienObj.TPasien_NomorRM+'\')"></td>';
			isi += '<td>'+pasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;

	});

}

function cPasienApp(){
	var pasiennama = '';

	$.get('/ajax-getpendaftaranappointment2?key='+pasiennama, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="100%">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, pasienObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoRM\', \''+pasienObj.TPasien_NomorRM+'\')"></td>';
			isi += '<td>'+pasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;

	});

}

function cdatapasienKUApp(keyword){
	var pasiennama = keyword;

	$.get('/ajax-getpendaftaranappointment2?key='+pasiennama, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="100%">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, pasienObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoRM\', \''+pasienObj.TPasien_NomorRM+'\')"></td>';
			isi += '<td>'+pasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

//});
}

function cdatapasienKU(keyword){
	var pasiennama = keyword;

	$.get('/ajax-pasienbynama?pasiennama='+pasiennama, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, pasienObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoRM\', \''+pasienObj.TPasien_NomorRM+'\')"></td>';
			isi += '<td>'+pasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+pasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

//});
}

// Pencarian Data Pasien ==========================
function cPasien(keyword){
	var key = keyword;

	$.get('/ajax-getdatapasien?key='+key, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		arrPasien = [];

		var i = 0;

		$.each(data, function(index, pasien){

			arrPasien.push({
				TPasien_NomorRM 	: pasien.TPasien_NomorRM,
				TPasien_Nama 		: pasien.TPasien_Nama,
				TAdmVar_Gender 		: pasien.TAdmVar_Gender,
				TPasien_Panggilan 	: pasien.TPasien_Panggilan,
				TPasien_Prioritas 	: pasien.TPasien_Prioritas,
				TPasien_Alamat 		: pasien.TPasien_Alamat,
				TPasien_Kelurahan 	: pasien.TPasien_Kelurahan,
				TPasien_Kecamatan 	: pasien.TPasien_Kecamatan,
				TPasien_Kota 		: pasien.TPasien_Kota,
				TPasien_NamaKota	: pasien.Nama_Kota,
				TPasien_Prov 		: pasien.TPasien_Prov,
				TPasien_RT 			: pasien.TPasien_RT,
				TPasien_RW 			: pasien.TPasien_RW,
				TWilayah2_Kode 		: pasien.TWilayah2_Kode,
				TPasien_Telp 		: pasien.TPasien_Telp,
				TPasien_HP 			: pasien.TPasien_HP,
				TPasien_TmpLahir 	: pasien.TPasien_TmpLahir,
				TPasien_TglLahir 	: pasien.TPasien_TglLahir,
				TAdmVar_Pekerjaan 	: pasien.TAdmVar_Pekerjaan,
				tadmvar_agama 		: pasien.tadmvar_agama,
				TPasien_Kerja 		: pasien.TPasien_Kerja,
				TPasien_KerjaAlamat : pasien.TPasien_KerjaAlamat,
				TAdmVar_Darah 		: pasien.TAdmVar_Darah,
				TAdmVar_Pendidikan 	: pasien.TAdmVar_Pendidikan,
				TAdmVar_Kawin 		: pasien.TAdmVar_Kawin,
				TPasien_KlgNama 	: pasien.TPasien_KlgNama,
				TPasien_KlgKerja 	: pasien.TPasien_KlgKerja,
				TAdmVar_KlgPdk 		: pasien.TAdmVar_KlgPdk,
				TAdmVar_Keluarga 	: pasien.TAdmVar_Keluarga,
				TPasien_KlgTelp 	: pasien.TPasien_KlgTelp,
				TPasien_KlgAlamat 	: pasien.TPasien_KlgAlamat,
				TPasien_NOID 		: pasien.TPasien_NOID,
				TPasien_NoMember 	: pasien.TPasien_NoMember,
				TAdmVar_Jenis  		: pasien.TAdmVar_Jenis,
				TPasien_TglInput 	: pasien.TPasien_TglInput,
				TPasien_MemberNomor : pasien.TPasien_MemberNomor,
				TPasien_Title 		: pasien.TPasien_Title
			});

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarPasien" name="daftarPasien" onclick="sendArrPasien('+i+')"></td>';
			isi += '<td>'+pasien.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+pasien.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+pasien.TPasien_Alamat+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

//});
}


function pilihPasienApp()
{
	var nomorrm = $('#tempNoRM').val();
	nomorrm 	= nomorrm.toString();

	document.getElementById('pasiennorm').value = nomorrm;
	fillPasienApp();
	$('#tempNoRM').val('');
}


function pilihPasien()
{
	var nomorrm = $('#tempNoRM').val();
	nomorrm 	= nomorrm.toString();

	document.getElementById('pasiennorm').value = nomorrm;
	fillPasien();
	$('#tempNoRM').val('');
}

// === end of Pasien ===

// ============================ Poliklinik =============================
function cTransPoli(){

	$.get('/ajax-transpoliall', function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="table table-bordered table-striped">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Trans</th>';
		isi += '<th class="column-title">Tanggal Transaksi</th>';
		isi += '<th class="column-title">Unit</th>';
		isi += '<th class="column-title">Nomor RM</th>';
		isi += '<th class="column-title">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, poliObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoTrans\', \''+poliObj.TRawatJalan_NoReg+'\')"></td>';
			isi += '<td>'+poliObj.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+poliObj.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+poliObj.TUnit_Nama+'</td>';
			isi += '<td>'+poliObj.TPasien_NomorRM+'</td>';
			isi += '<td>'+poliObj.TPasien_Nama+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});
}

function cdatapoliKU(keyword){
	//$('#cdatapoli').on('keyup', function(e){
	var kuncicari = keyword;

	$.get('/ajax-polisearch?kuncicari='+kuncicari, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="table table-bordered table-striped">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Trans</th>';
		isi += '<th class="column-title">Tanggal Transaksi</th>';
		isi += '<th class="column-title">Unit</th>';
		isi += '<th class="column-title">Nomor RM</th>';
		isi += '<th class="column-title">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, poliObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" name="rdPasien" id="rdPasien" onclick="clickRd(\'tempNoTrans\', \''+poliObj.TRawatJalan_NoReg+'\')"></td>';
			isi += '<td>'+poliObj.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+poliObj.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+poliObj.TUnit_Nama+'</td>';
			isi += '<td>'+poliObj.TPasien_NomorRM+'</td>';
			isi += '<td>'+poliObj.TPasien_Nama+'</td>';
			isi += '</tr>';
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});
//});
}

function pilihPoli()
{
	var notrans = $('#tempNoTrans').val();	

	fillTransPoli(notrans);
	$('#tempNoTrans').val('');
}

function clickRd(getID, valData){
	$('#'+getID).val(valData);
}

// ============================ end of Poli =================================

// === Daftar Inap ===
function CPasienInap(){

	var key1 = $('#searchkey1').val();
	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-trawatinapsearch?key='+key1, function(data){
		
		var isi = '';
		var nomorrm = '';
		var i = 0;
		arrDaftarInap = [];
		
		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Reg</th>';
		isi += '<th class="column-title">Pasien No. RM</th>';
		isi += '<th class="column-title">Pasien Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '<th class="column-title">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, daftarinapObj){

			arrDaftarInap.push({
		        inap_noreg 		: daftarinapObj.TRawatInap_NoAdmisi, 
		        pasien_norm 	: daftarinapObj.TPasien_NomorRM,
		        pasien_nama 	: daftarinapObj.TPasien_Nama,
		        TTmpTidur_Kode 	: daftarinapObj.TTmpTidur_Kode,
		        TTmpTidur_Nama 	: daftarinapObj.TTmpTidur_Nama,
		        pasien_umurthn 	: daftarinapObj.TRawatInap_UmurThn,
		        pasien_umurbln 	: daftarinapObj.TRawatInap_UmurBln,
		        pasien_umurhr 	: daftarinapObj.TRawatInap_UmurHr,
		        pasien_alamat 	: daftarinapObj.TPasien_Alamat,
		        pasien_gender 	: daftarinapObj.TAdmVar_Gender,
		        inap_tgl 		: daftarinapObj.TRawatInap_TglMasuk,
		        penjamin 		: daftarinapObj.TPerusahaan_Nama,
		        kota 			: daftarinapObj.TWilayah2_Nama,
		        penjamin_kode 	: daftarinapObj.TPerusahaan_Kode,
		        pelaku_kode 	: daftarinapObj.TPelaku_Kode,
		        pelaku_nama 	: daftarinapObj.TPelaku_NamaLengkap
		      });
			

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrDft('+i+')"></td>';
			isi += '<td width="20%">'+daftarinapObj.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+daftarinapObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarinapObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarinapObj.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+daftarinapObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;
		});
		
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}
// End Inap

// === Daftar Ugd ===
function cDaftarUgd(){

	var key1 = $('#searchkey1').val();
	// var key2 = $('#searchkey2').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-ugdsearch?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';
		var i = 0;
		arrDaftarUgd = [];
		
		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Reg</th>';
		isi += '<th class="column-title">Pasien No. RM</th>';
		isi += '<th class="column-title">Pasien Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '<th class="column-title">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		$.each(data, function(index, daftarugdObj){

			arrDaftarUgd.push({
		        ugd_noreg 		: daftarugdObj.TRawatUGD_NoReg,
		        pasien_norm 	: daftarugdObj.TPasien_NomorRM,
		        pasien_nama 	: daftarugdObj.TPasien_Nama,
		        pasien_umurthn 	: daftarugdObj.TRawatUGD_PasienUmurThn,
		        pasien_umurbln 	: daftarugdObj.TRawatUGD_PasienUmurBln,
		        pasien_umurhr 	: daftarugdObj.TRawatUGD_PasienUmurHr,
		        pasien_alamat 	: daftarugdObj.TPasien_Alamat,
		        pasien_gender 	: daftarugdObj.TAdmVar_Gender,
		        ugd_tgl 		: daftarugdObj.TRawatUGD_Tanggal,
		        penjamin 		: daftarugdObj.TPerusahaan_Nama,
		        kota 			: daftarugdObj.TWilayah2_Nama,
		        penjamin_kode 	: daftarugdObj.TPerusahaan_Kode,
		        pelaku_kode 	: daftarugdObj.TPelaku_Kode,
		        pelaku_nama 	: daftarugdObj.TPelaku_NamaLengkap
		      });
			
			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarugd" name="daftarugd" onclick="sendArrDft('+i+')"></td>';
			isi += '<td width="20%">'+daftarugdObj.TRawatUGD_NoReg+'</td>';
			isi += '<td>'+daftarugdObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarugdObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarugdObj.TRawatUGD_Tanggal+'</td>';
			isi += '<td>'+daftarugdObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;
		});
		
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}
// End UGD


// === Daftar Pasien ALL ===
function cDaftarPasienAll(kd){

	var key1 = $('#keypas').val();

	$.get('/ajax-pasiendaftarAll?key1='+kd+'&key2='+key1, function(data){
		
		var isi 		= '';
		var nomorrm 	= '';
		
	    arrPas      	= [];

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Reg</th>';
		isi += '<th class="column-title">Pasien No. RM</th>';
		isi += '<th class="column-title">Pasien Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '<th class="column-title">Alamat</th>';
		isi += '<th class="column-title">No Telp</th>';
		isi += '<th class="column-title">Penjamin</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i			= 0;
		arrDaftarPasien = [];
		$.each(data, function(index, daftarpasienObj){
		
			arrDaftarPasien.push({
				ID 				: daftarpasienObj.id,
		        ugd_noreg 		: daftarpasienObj.noreg,
		        pasien_norm 	: daftarpasienObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpasienObj.TPasien_Nama,
		        tgllahir 		: daftarpasienObj.TPasien_TglLahir,
		        pasien_alamat 	: daftarpasienObj.TPasien_Alamat,
		        telp 			: daftarpasienObj.TPasien_Telp,
		        prsh 			: daftarpasienObj.TPerusahaan_Nama,
		   
		      });
			
			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpas" name="daftarpas" onclick="sendArrTempPas('+i+')"></td>';
			isi += '<td width="20%">'+daftarpasienObj.noreg+'</td>';
			isi += '<td>'+daftarpasienObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarpasienObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarpasienObj.TPasien_TglLahir+'</td>';
			isi += '<td>'+daftarpasienObj.TPasien_Alamat+'</td>';
			isi += '<td>'+daftarpasienObj.TPasien_Telp+'</td>';
			isi += '<td>'+daftarpasienObj.TPerusahaan_Nama+'</td>';
			isi += '</tr>';
			i++;

		});
		
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}
// End Pasien ALL

// === Daftar Poli ===
function cDaftarPoli(){

	var key1 = $('#searchkey1').val();
	// var key2 = $('#searchkey2').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-polisearch?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Reg</th>';
		isi += '<th width="75px">Pasien No. RM</th>';
		isi += '<th width="150px">Pasien Nama</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="100px">Unit</th>';
		isi += '<th width="150px">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, daftarpoliObj){

			arrDaftarPoli.push({
		        jalan_noreg 	: daftarpoliObj.TRawatJalan_NoReg,
		        pasien_norm 	: daftarpoliObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpoliObj.TPasien_Nama,
		        pasien_umurthn 	: daftarpoliObj.TRawatJalan_PasienUmurThn,
		        pasien_umurbln 	: daftarpoliObj.TRawatJalan_PasienUmurBln,
		        pasien_umurhr 	: daftarpoliObj.TRawatJalan_PasienUmurHr,
		        pasien_alamat 	: daftarpoliObj.TPasien_Alamat,
		        pasien_gender 	: daftarpoliObj.TAdmVar_Gender,
		        jalan_tgl 	 	: daftarpoliObj.TRawatJalan_Tanggal,
		        kota 			: daftarpoliObj.TWilayah2_Nama,
		        unit_kode 		: daftarpoliObj.TUnit_Kode,
		        unit_nama 		: daftarpoliObj.TUnit_Nama,
		        penjamin 		: daftarpoliObj.TPerusahaan_Nama,
		        penjamin_kode 	: daftarpoliObj.TPerusahaan_Kode,
		        pelaku_kode 	: daftarpoliObj.TPelaku_Kode,
		        pelaku_nama 	: daftarpoliObj.TPelaku_NamaLengkap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpoliObj.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+daftarpoliObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+daftarpoliObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarpoliObj.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+daftarpoliObj.TUnit_Nama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpoliObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

// === Daftar Poli untuk Transaksi Obat Alkes Jalan ===
function cDaftarPoliOHP(){

	var key1 = $('#searchkey1').val();
	// var key2 = $('#searchkey2').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	//polisearchOHP 
	$.get('/ajax-vpasienOHP?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Reg</th>';
		isi += '<th width="75px">Pasien No. RM</th>';
		isi += '<th width="150px">Pasien Nama</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="100px">Unit</th>';
		isi += '<th width="150px">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, daftarpoliObj){

			arrDaftarPoli.push({
		        jalan_noreg 	: daftarpoliObj.TRawatJalan_NoReg,
		        pasien_norm 	: daftarpoliObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpoliObj.TPasien_Nama,
		        pasien_umurthn 	: daftarpoliObj.TRawatJalan_PasienUmurThn,
		        pasien_umurbln 	: daftarpoliObj.TRawatJalan_PasienUmurBln,
		        pasien_umurhr 	: daftarpoliObj.TRawatJalan_PasienUmurHr,
		        pasien_alamat 	: daftarpoliObj.TPasien_Alamat,
		        pasien_gender 	: daftarpoliObj.TAdmVar_Gender,
		        jalan_tgl 		: daftarpoliObj.TRawatJalan_Tanggal,
		        kota 			: daftarpoliObj.TWilayah2_Nama,
		        unit_kode 		: daftarpoliObj.TUnit_Kode,
		        unit_nama 		: daftarpoliObj.TUnit_Nama,
		        penjamin 		: daftarpoliObj.TPerusahaan_Nama,
		        penjamin_kode 	: daftarpoliObj.TPerusahaan_Kode,
		        pelaku_kode 	: daftarpoliObj.TPelaku_Kode,
		        pelaku_nama 	: daftarpoliObj.TPelaku_NamaLengkap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpoliObj.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+daftarpoliObj.TPasien_NomorRM+'</td>';
			isi += '<td>'+daftarpoliObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarpoliObj.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+daftarpoliObj.TUnit_Nama+'</td>';
			isi += '<td>'+daftarpoliObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar UGD untuk Transaksi Obat Alkes Jalan ===
function cDaftarUgdOHP(){

	var key1 = $('#searchkey1').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-ugdsearchOHP?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">Nomor Reg</th>';
		isi += '<th class="column-title">Pasien No. RM</th>';
		isi += '<th class="column-title">Pasien Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '<th class="column-title">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarUgd = [];

		$.each(data, function(index, daftarugdObj){

			arrDaftarUgd.push({
		        ugd_noreg 		: daftarugdObj.TRawatUGD_NoReg,
		        pasien_norm 	: daftarugdObj.TPasien_NomorRM,
		        pasien_nama 	: daftarugdObj.TPasien_Nama,
		        pasien_umurthn 	: daftarugdObj.TRawatUGD_PasienUmurThn,
		        pasien_umurbln 	: daftarugdObj.TRawatUGD_PasienUmurBln,
		        pasien_umurhr 	: daftarugdObj.TRawatUGD_PasienUmurHr,
		        pasien_alamat 	: daftarugdObj.TPasien_Alamat,
		        pasien_gender 	: daftarugdObj.TAdmVar_Gender,
		        jalan_tgl 		: daftarugdObj.TRawatUGD_Tanggal,
		        kota 			: daftarugdObj.TWilayah2_Nama,
		        penjamin 		: daftarugdObj.TPerusahaan_Nama,
		        penjamin_kode 	: daftarugdObj.TPerusahaan_Kode,
		        pelaku_kode 	: daftarugdObj.TPelaku_Kode,
		        pelaku_nama 	: daftarugdObj.TPelaku_NamaLengkap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarugd" name="daftarugd" onclick="sendArrDft('+i+')"></td>';
			isi += '<td width="20%">'+daftarugdObj.TRawatUGD_NoReg+'</td>';
			isi += '<td>'+daftarugdObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarugdObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarugdObj.TRawatUGD_Tanggal+'</td>';
			isi += '<td>'+daftarugdObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;
		});
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar UGD ===
function cTarifUgd(keyword, kdTarif){
	$.get('/ajax-tarifugdsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="200px">Tarif Nama</th>';
		isi += '<th width="150px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;
		arrTarifUgd = [];
		$.each(data, function(index, trfugdObj){
			arrTarifUgd.push({
		        tarifkode 		: trfugdObj.TTarifIGD_Kode,
		        tarifnama 		: trfugdObj.TTarifIGD_Nama,
		        tarif 			: parseFloat(trfugdObj.TTarifIGD_Jalan).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfugdObj.TTarifIGD_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfugdObj.TTarifIGD_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfugdObj.TTarifIGD_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfugdObj.TTarifIGD_RSFT).toFixed(2),
		        tarifjenis 		: parseFloat(trfugdObj.TTarifVar_Kelompok).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarugd" name="daftarugd" onclick="sendArrUgd('+i+')"></td>';
			isi += '<td>'+trfugdObj.TTarifIGD_Kode+'</td>';
			isi += '<td>'+trfugdObj.TTarifIGD_Nama+'</td>';
			isi += '<td>'+formatRibuan(trfugdObj.TTarifIGD_Jalan)+'</td>';
			isi += '</tr>';
			i++;
		});
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Jalan ===
function cTarifJalan(keyword, kdTarif){
	$.get('/ajax-tarifjalansearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifJalan = [];

		$.each(data, function(index, trfjalanObj){

			arrTarifJalan.push({
		        tarifkode 		: trfjalanObj.TTarifJalan_Kode,
		        tarifnama 		: trfjalanObj.TTarifJalan_Nama,
		        tarif 			: parseFloat(trfjalanObj.TTarifJalan_Jalan).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfjalanObj.TTarifJalan_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfjalanObj.TTarifJalan_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfjalanObj.TTarifJalan_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfjalanObj.TTarifJalan_RSFT).toFixed(2),
		        tarifjenis 		: trfjalanObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrJalan('+i+')"></td>';
			isi += '<td>'+trfjalanObj.TTarifJalan_Kode+'</td>';
			isi += '<td style="text-align:left;">'+trfjalanObj.TTarifJalan_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(trfjalanObj.TTarifJalan_Jalan)+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Lain ===
function cTarifLain(keyword, kdTarif){

	$.get('/ajax-tariflainsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifJalan = [];

		$.each(data, function(index, trfjalanObj){

			arrTarifJalan.push({
		        tarifkode 		: trfjalanObj.TTarifLain_Kode,
		        tarifnama 		: trfjalanObj.TTarifLain_Nama,
		        tarif 			: parseFloat(trfjalanObj.TTarifLain_Jalan).toFixed(2),
		        tarifdokterpt 	: 0.0,
		        tarifdokterft 	: 0.0,
		        tarifrspt 		: parseFloat(trfjalanObj.TTarifLain_Jalan).toFixed(2),
		        tarifrsft 		: parseFloat(trfjalanObj.TTarifLain_Jalan).toFixed(2),
		        tarifjenis 		: 'LAIN',
		        kelas1 			: parseFloat(trfjalanObj.TTarifLain_Kelas1).toFixed(2),
		        kelas2 			: parseFloat(trfjalanObj.TTarifLain_Kelas2).toFixed(2),
		        kelas3 			: parseFloat(trfjalanObj.TTarifLain_Kelas3).toFixed(2),
		        vvip 			: parseFloat(trfjalanObj.TTarifLain_Utama).toFixed(2),
		        vip 			: parseFloat(trfjalanObj.TTarifLain_VIP).toFixed(2),
		        jalan 			: parseFloat(trfjalanObj.TTarifLain_Jalan).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrJalan('+i+')"></td>';
			isi += '<td>'+trfjalanObj.TTarifLain_Kode+'</td>';
			isi += '<td style="text-align:left;">'+trfjalanObj.TTarifLain_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(trfjalanObj.TTarifLain_Jalan)+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Gigi ===
function cTarifGigi(keyword, kdTarif){

	$.get('/ajax-tarifgigisearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifJalan = [];

		$.each(data, function(index, trfjalanObj){

			arrTarifJalan.push({
		        tarifkode 		: trfjalanObj.TTarifGigi_Kode,
		        tarifnama 		: trfjalanObj.TTarifGigi_Nama,
		        tarif 			: parseFloat(trfjalanObj.TTarifGigi_Jumlah).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfjalanObj.TTarifGigi_JasaDokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfjalanObj.TTarifGigi_JasaDokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfjalanObj.TTarifGigi_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfjalanObj.TTarifGigi_RSFT).toFixed(2),
		        tarifjenis 		: 'GIGI'
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrJalan('+i+')"></td>';
			isi += '<td>'+trfjalanObj.TTarifGigi_Kode+'</td>';
			isi += '<td style="text-align:left;">'+trfjalanObj.TTarifGigi_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(trfjalanObj.TTarifGigi_Jumlah).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

function cObatHarga(keyword, kdObat, penjamin){

	var isi 		= '';
	var nomorrm 	= '';
	var Margin 		= 0
	var harga 		= 0;
	var HNA_PPN 	= 0.0; 

		arrMargin 	= [];
		arrObat 	= [];

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';

	$.get('/ajax-marginobat?kdpenjamin='+penjamin, function(data){
					Margin 	= data;

			}); 

  	$.get('/ajax-obathargasearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="150px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		$.each(data, function(index, obatObj){
			HNA_PPN = obatObj.TObat_HNA_PPN;
				
				harga = (Margin * parseFloat(HNA_PPN)+ parseFloat(HNA_PPN));

				harga = parseFloat(harga).toFixed(2);
				
				arrObat.push({
			        ID 				: obatObj.id,
			        Kode 			: obatObj.TObat_Kode,
			        TGrup_Kode 		: obatObj.TGrup_Kode,
			        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
			        Nama 			: obatObj.TObat_Nama,
			        NamaGenerik 	: obatObj.TObat_NamaGenerik,
			        Satuan 			: obatObj.TObat_Satuan,
			        Satuan2 		: obatObj.TObat_Satuan2,
			        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
			        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
			        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
			        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
			        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
			        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
			        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
			        JualFaktor 		: obatObj.TObat_JualFaktor,
			        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
			        HargaJualUmum 	: harga
				});
					
				isi += '<tr class="even pointer">';
				isi += '<td><input type="radio" id="daftarobat" name="daftarobat" onclick="sendArrObat('+i+')"></td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(harga)+'</td>';
				isi += '</tr>';

				i++;
				
		}); // $.each(data, function(index, obatObj){
				
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
	
}

function cObatHargaMulti(keyword, kdObat, penjamin, unitKode){
	
	var isi 		= '';
	var nomorrm 	= '';
	var Margin 		= 0
	var harga 		= 0;
	var HNA_PPN 	= 0.0; 

	arrMargin 	= [];
	arrObat 	= [];
	arrTempObat = [];


	isi += '<table id="datatable1" class="responstable">';
	isi += '<thead>';
	
	$.get('/ajax-marginobat?kdpenjamin='+penjamin, function(data){
		
		Margin 	= data;

		$.get('/ajax-obatgrupmutasisearch?kuncicari='+keyword+'&kdObat='+kdObat+'&kdUnit='+unitKode, function(data){

			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Kode Obat</th>';
			isi += '<th width="200px">Nama Obat</th>';
			isi += '<th width="150px">Harga</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			$.each(data, function(index, obatObj){
				HNA_PPN = obatObj.TObat_HNA_PPN;
					
				harga = (Margin * parseFloat(HNA_PPN)+ parseFloat(HNA_PPN));

				harga = parseFloat(harga).toFixed(2);
				
				arrObat.push({
			        ID 				: obatObj.id,
			        Kode 			: obatObj.TObat_Kode,
			        TGrup_Kode 		: obatObj.TGrup_Kode,
			        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
			        Nama 			: obatObj.TObat_Nama,
			        NamaGenerik 	: obatObj.TObat_NamaGenerik,
			        Satuan 			: obatObj.TObat_Satuan,
			        Satuan2 		: obatObj.TObat_Satuan2,
			        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
			        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
			        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
			        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
			        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
			        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
			        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
			        JualFaktor 		: obatObj.TObat_JualFaktor,
			        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
			        HargaJualUmum 	: harga
				});
					
				isi += '<tr class="even pointer">';
				isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(harga)+'</td>';
				isi += '</tr>';

				i++;
					
			}); // $.each(data, function(index, obatObj){
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;

		}); // ... $.get('/ajax-obatgrupmutasisearch?

	}); // ... $.get('/ajax-marginobat?
	
}

function cObatRacikHargaMulti(keyword, kdObat, penjamin, unitKode){

	var isi 		= '';
	var nomorrm 	= '';
	var Margin 		= 0
	var harga 		= 0;
	var HNA_PPN 	= 0.0; 

	arrObat 		= [];
	arrMargin 		= [];
	arrTempObatRacik = [];

	isi += '<table id="datatable1" class="responstable">';
	isi += '<thead>';

	$.get('/ajax-marginobat?kdpenjamin='+penjamin, function(data){
		
		Margin 	= data;

		$.get('/ajax-obatgrupmutasisearch?kuncicari='+keyword+'&kdObat='+kdObat+'&kdUnit='+unitKode, function(data){

			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Kode Obat</th>';
			isi += '<th width="200px">Nama Obat</th>';
			isi += '<th width="150px">Harga</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			$.each(data, function(index, obatObj){
				HNA_PPN = obatObj.TObat_HNA_PPN;
					
				harga = (Margin * parseFloat(HNA_PPN)+ parseFloat(HNA_PPN));

				harga = parseFloat(harga).toFixed(2);
				
				arrObat.push({
			        ID 				: obatObj.id,
			        Kode 			: obatObj.TObat_Kode,
			        TGrup_Kode 		: obatObj.TGrup_Kode,
			        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
			        Nama 			: obatObj.TObat_Nama,
			        NamaGenerik 	: obatObj.TObat_NamaGenerik,
			        Satuan 			: obatObj.TObat_Satuan,
			        Satuan2 		: obatObj.TObat_Satuan2,
			        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
			        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
			        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
			        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
			        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
			        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
			        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
			        JualFaktor 		: obatObj.TObat_JualFaktor,
			        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
			        HargaJualUmum 	: harga
				});
					
				isi += '<tr class="even pointer">';
				isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObatRacik(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(harga)+'</td>';
				isi += '</tr>';

				i++;
					
			}); // $.each(data, function(index, obatObj){
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;

		}); // ... $.get('/ajax-obatgrupmutasisearch?

	}); // ... $.get('/ajax-marginobat?
	
}

// === Daftar Obat ===
function cObat(keyword, kdObat){

	$.get('/ajax-obatsearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="150px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarobat" name="daftarobat" onclick="sendArrObat('+i+')"></td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA_PPN).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Obat Dengan Multi Choice ===
function cObatMulti(keyword, kdObat){

	$.get('/ajax-obatsearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="150px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        GdJml_PPN 		: parseFloat(obatObj.TObat_GdJml_PPN).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        RpJml_PPN 		: parseFloat(obatObj.TObat_RpJml_PPN).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA_PPN).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Obat Dengan Multi Choice Untuk SO/Retur unit (dengan batasan grupmutasi) ===
function cObatMultiOpUnit(keyword, kdObat, kdUnit, notrans, tgl){

	$.get('/ajax-obatsearchmutasi?kuncicari='+keyword+'&kdObat='+kdObat+'&kdUnit='+kdUnit+'&notrans='+notrans+'&tgl='+tgl, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="150px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        GdJml_PPN 		: parseFloat(obatObj.TObat_GdJml_PPN).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        RpJml_PPN 		: parseFloat(obatObj.TObat_RpJml_PPN).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
		        Stok 			: obatObj.Stok,
		        Jumlah 			: parseFloat(obatObj.Jumlah).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA_PPN).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});
}


// === Daftar Obat Untuk Pembelian/Order ===
function cObatBeli(keyword, kdObat){

	$.get('/ajax-obatsearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="100px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat = [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarobat" name="daftarobat" onclick="sendArrObat('+i+')"></td>';
			isi += '<td>'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});
        
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Obat Untuk Pembelian/Order Multi ===
function cObatBeliMulti(keyword, kdObat){

	$.get('/ajax-obatsearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Obat</th>';
		isi += '<th width="200px">Nama Obat</th>';
		isi += '<th width="100px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        GdJml_PPN 		: parseFloat(obatObj.TObat_GdJml_PPN).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        RpJml_PPN 		: parseFloat(obatObj.TObat_RpJml_PPN).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
			isi += '<td>'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA_PPN).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Barang Untuk Pembelian/Order Multi ===
function cbarangBeliMulti(keyword, kdBarang){

	$.get('/ajax-barangsearch?kuncicari='+keyword+'&kdBarang='+kdBarang, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Barang</th>';
		isi += '<th width="200px">Nama Barang</th>';
		isi += '<th width="100px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, barangObj){

			arrObat.push({
		        ID 				: barangObj.id,
		        Kode 			: barangObj.TStok_Kode,
		        TGrup_Kode_Gol 	: barangObj.TGrup_id_Log,
		        Nama 			: barangObj.TStok_Nama,
		        Satuan 			: barangObj.TStok_Satuan,
		        Satuan2 		: barangObj.TStok_Satuan2,
		        SatuanFaktor 	: barangObj.TStok_SatuanFaktor,
		        Merk 			: barangObj.TStok_Merk,
		        Harga 			: parseFloat(barangObj.TStok_Harga).toFixed(2),
		        Memo 			: barangObj.TStok_Memo,
		        StokMin 		: barangObj.TStok_Minimal,
		        Stoktercatat 	: barangObj.TStok_Qty,
		        HargaBeli 		: parseFloat(barangObj.TStok_HargaBeli).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarbarang'+i+'" name="daftarbarang'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+barangObj.TStok_Kode+'\')"></td>';
			isi += '<td>'+barangObj.TStok_Kode+'</td>';
			isi += '<td style="text-align:left;">'+barangObj.TStok_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(barangObj.TStok_Harga).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

function cbaranglogpakai(keyword){
	$.get('/ajax-barangpemakaiansearch?kuncicari='+keyword, function(data){
		var isi = '';
		var nomorrm = '';
		var hargarata2 = 0;

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Barang</th>';
		isi += '<th width="200px">Nama Barang</th>';
		isi += '<th width="50px">Stok Qty</th>';
		isi += '<th width="100px">Harga Rata2</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, barangObj){

		  	if (barangObj.jmlsaldo == 0) {
                hargaRata = 0;
            } else if (barangObj.stoksaldo == 0) {
                hargaRata = barangObj.jmlsaldo / 1;
            } else {
                hargaRata = barangObj.jmlsaldo / barangObj.stoksaldo;
            }

			arrObat.push({
		        Kode 			: barangObj.TStok_id,
		        Nama 			: barangObj.TStok_Nama,
		        Satuan 			: barangObj.TStok_Satuan,
		        Merk 			: barangObj.TStok_Merk,
		        Hargarata2 		: parseFloat(hargaRata).toFixed(0),
		        Stok 			: barangObj.stoksaldo
		      });



			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarbarang'+i+'" name="daftarbarang'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+barangObj.TStok_Kode+'\')"></td>';
			isi += '<td>'+barangObj.TStok_id+'</td>';
			isi += '<td style="text-align:left;">'+barangObj.TStok_Nama+'</td>';
			isi += '<td>'+barangObj.stoksaldo+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(hargaRata.toFixed(0))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Barang Untuk Pembelian/Order Multi ===
function cBarangBeliMulti(keyword, kdObat){

	$.get('/ajax-barangsearch?kuncicari='+keyword+'&kdObat='+kdObat, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Barang</th>';
		isi += '<th width="200px">Nama Barang</th>';
		isi += '<th width="100px">Harga</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrObat 	= [];
		arrTempObat = [];

		$.each(data, function(index, obatObj){

			arrObat.push({
		        ID 				: obatObj.id,
		        Kode 			: obatObj.TObat_Kode,
		        TGrup_Kode 		: obatObj.TGrup_Kode,
		        TGrup_Kode_Gol 	: obatObj.TGrup_Kode_Gol,
		        Nama 			: obatObj.TObat_Nama,
		        NamaGenerik 	: obatObj.TObat_NamaGenerik,
		        Satuan 			: obatObj.TObat_Satuan,
		        Satuan2 		: obatObj.TObat_Satuan2,
		        SatuanFaktor 	: obatObj.TObat_SatuanFaktor,
		        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
		        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
		        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
		        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
		        GdJml_PPN 		: parseFloat(obatObj.TObat_GdJml_PPN).toFixed(2),
		        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
		        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
		        RpJml_PPN 		: parseFloat(obatObj.TObat_RpJml_PPN).toFixed(2),
		        JualFaktor 		: parseFloat(obatObj.TObat_JualFaktor).toFixed(2),
		        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
			isi += '<td>'+obatObj.TObat_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(obatObj.TObat_HNA_PPN).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// === Daftar Supplier ===
function cSupplier(keyword, kdSupplier){

	$.get('/ajax-suppliersearch?kuncicari='+keyword+'&kdSupplier='+kdSupplier, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Supplier</th>';
		isi += '<th width="150px">Nama Supplier</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrSupplier = [];

		$.each(data, function(index, suppObj){

			arrSupplier.push({
		        ID 		: suppObj.id,
		        Kode 	: suppObj.TSupplier_Kode,
		        Nama 	: suppObj.TSupplier_Nama,
		        Alamat 	: suppObj.TSupplier_Alamat,
		        Telp 	: suppObj.TSupplier_Telepon,
		        Kota 	: suppObj.TSupplier_Kota,
		        Tempo 	: suppObj.TSupplier_Tempo,
		        IDRS 	: suppObj.IDRS
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarsupp" name="daftarsupp" onclick="sendArrSupplier('+i+')"></td>';
			isi += '<td>'+suppObj.TSupplier_Kode+'</td>';
			isi += '<td>'+suppObj.TSupplier_Nama+'</td>';
			isi += '<td>'+suppObj.TSupplier_Alamat+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Supplier Logistik===
function cSupplierlogistik(keyword, kdSupplier){

	$.get('/ajax-supplierlogistiksearch?kuncicari='+keyword+'&kdSupplier='+kdSupplier, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Supplier</th>';
		isi += '<th width="150px">Nama Supplier</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrSupplier = [];

		$.each(data, function(index, suppObj){

			arrSupplier.push({
		        ID 		: suppObj.id,
		        Kode 	: suppObj.TSupplier_Kode,
		        Nama 	: suppObj.TSupplier_Nama,
		        Alamat 	: suppObj.TSupplier_Alamat,
		        Telp 	: suppObj.TSupplier_Telepon,
		        Kota 	: suppObj.TSupplier_Kota,
		        Tempo 	: suppObj.TSupplier_Tempo,
		        IDRS 	: suppObj.IDRS
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarsupp" name="daftarsupp" onclick="sendArrSupplier('+i+')"></td>';
			isi += '<td>'+suppObj.TSupplier_Kode+'</td>';
			isi += '<td>'+suppObj.TSupplier_Nama+'</td>';
			isi += '<td>'+suppObj.TSupplier_Alamat+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Supplier Logistik===
function cStoklogistik(keyword, kdStok){

	$.get('/ajax-barangsearch?kuncicari='+keyword+'&kdStok='+kdStok, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Kode Barang</th>';
		isi += '<th width="150px">Nama Barang</th>';
		isi += '<th width="250px">Satuan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrBarang = [];

		$.each(data, function(index, barangObj){

			arrBarang.push({
		        ID 		: barangObj.id,
		        Kode 	: barangObj.TStok_Kode,
		        Nama 	: barangObj.TStok_Nama,
		        Merk 	: barangObj.TStok_Merk,
		        Harga 	: barangObj.TStok_Harga
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarbrg" name="daftarbrg" onclick="sendArrBarang('+i+')"></td>';
			isi += '<td>'+barangObj.TStok_Kode+'</td>';
			isi += '<td>'+barangObj.TStok_Nama+'</td>';
			isi += '<td>'+barangObj.TStok_Satuan+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Order Frm filter Sisa > 0 ===
function cOrder(keyword, tgl1, tgl2){

	$.get('/ajax-orderfrmfiltersisasearch?key='+keyword+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){

		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Order</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Supplier</th>';
		isi += '<th width="250px">Keterangan</th>';
		isi += '<th width="50px">Tempo</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrOrder = [];

		$.each(data, function(index, orderObj){

			var StdKet = (orderObj.TOrderKetStd_Nama == null ? '-' : orderObj.TOrderKetStd_Nama );

			arrOrder.push({
		        ID 			: orderObj.id,
		        Nomor 		: orderObj.TOrderFrm_Nomor,
		        Tgl 		: orderObj.TOrderFrm_Tgl,
		        Supp_Kode 	: orderObj.TSupplier_Kode,
		        Supp_Nama 	: orderObj.TSupplier_Nama,
		        Std_Kode 	: orderObj.TOrderKetStd_Kode,
		        Std_Nama 	: orderObj.TOrderKetStd_Nama,
		        Tempo 		: orderObj.TOrderFrm_BayarHr
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarsupp" name="daftarsupp" onclick="sendArrOrder('+i+')"></td>';
			isi += '<td>'+orderObj.TOrderFrm_Nomor+'</td>';
			isi += '<td>'+orderObj.TOrderFrm_Tgl+'</td>';
			isi += '<td style="text-align:left;">'+orderObj.TSupplier_Nama+'</td>';
			isi += '<td style="text-align:left;">'+StdKet+'</td>';
			isi += '<td style="text-align:left;">'+orderObj.TOrderFrm_BayarHr+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================== Daftar Transaksi Penerimaan ====================================
function cPenerimaan(keyword, tgl1, tgl2){

	$.get('/ajax-terimafrmsearch?key1='+tgl1+'&key2='+tgl2+'&key3='+keyword, function(data){

		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Penerimaan</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Supplier</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTerima = [];

		$.each(data, function(index, terimaObj){

			arrTerima.push({
		        ID 			: terimaObj.id,
		        Nomor 		: terimaObj.TTerimaFrm_Nomor,
		        Tgl 		: terimaObj.TTerimaFrm_Tgl,
		        Supp_Kode 	: terimaObj.TSupplier_Kode,
		        Supp_Nama 	: terimaObj.TSupplier_Nama, 
		        Supp_Alamat : terimaObj.TSupplier_Alamat, 
		        Supp_Kota 	: terimaObj.TSupplier_Kota, 
		        OrderNomor 	: terimaObj.TOrderFrm_Nomor,
		        ReffNo 		: terimaObj.TTerimaFrm_ReffNo,
		        ReffTgl 	: terimaObj.TTerimaFrm_ReffTgl,
		        JTempo 		: terimaObj.TTerimaFrm_JTempo,
		        JatuhTempo 	: terimaObj.TTerimaFrm_JTempo,
		        DiscJenis 	: terimaObj.TTerimaFrm_DiscJenis,
		        TipeBayar 	: terimaObj.TTerimaFrm_DiscJenis,
		        Jumlah 		: parseFloat(terimaObj.TTerimaFrm_Jumlah).toFixed(2)
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarsupp" name="daftarsupp" onclick="sendArrTerima('+i+')"></td>';
			isi += '<td>'+terimaObj.TTerimaFrm_Nomor+'</td>';
			isi += '<td>'+terimaObj.TTerimaFrm_Tgl+'</td>';
			isi += '<td style="text-align:left;">'+terimaObj.TSupplier_Nama+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}
// ============================== End Of Daftar Transaksi Penerimaan ====================================

// === Daftar Keterangan Std ===
function cStdKet(keyword){

	$.get('/ajax-orderketstdsearch?kuncicari='+keyword, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Kode</th>';
		isi += '<th width="150px">Nama</th>';
		isi += '<th width="250px">Keterangan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrStdKet = [];

		$.each(data, function(index, ketObj){

			arrStdKet.push({
		        ID 			: ketObj.id,
		        Kode 		: ketObj.TOrderKetStd_Kode,
		        Nama 		: ketObj.TOrderKetStd_Nama,
		        Keterangan 	: ketObj.TOrderKetStd_Keterangan
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarsupp" name="daftarsupp" onclick="sendArrKeterangan('+i+')"></td>';
			isi += '<td>'+ketObj.TOrderKetStd_Kode+'</td>';
			isi += '<td style="text-align:left;">'+ketObj.TOrderKetStd_Nama+'</td>';
			isi += '<td style="text-align:left;">'+ketObj.TOrderKetStd_Keterangan+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Keterangan Std ===
function cStdTemplate(keyword, jenis){

	$.get('/ajax-stdtemplatesearch?key='+keyword+'&jenis='+jenis, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Kode</th>';
		isi += '<th width="150px">Nama</th>';
		isi += '<th width="100%px">Keterangan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrStdTemp = [];

		$.each(data, function(index, dt){

			arrStdTemp.push({
		        ID 			: dt.id,
		        Kode 		: dt.Kode,
		        Jenis 		: dt.Jenis,
		        Nama 		: dt.Nama,
		        Keterangan 	: dt.Keterangan
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarstdtempl" onclick="sendArrStdTemplt('+i+')"></td>';
			isi += '<td>'+dt.Kode+'</td>';
			isi += '<td style="text-align:left;">'+dt.Nama+'</td>';
			isi += '<td style="text-align:left;">'+dt.Keterangan+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Poli ===
function cDaftarIkb(){

	var key1 = $('#searchkey1').val();

	$.get('/ajax-getpendaftaranikb?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">No Admisi</th>';
		isi += '<th class="column-title">No. RM</th>';
		isi += '<th class="column-title">Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarInap = [];
	
		$.each(data, function(index, daftarInapObj){
			arrDaftarInap.push({
		        Inap_noreg 		: daftarInapObj.TRawatInap_NoAdmisi,
		        PasBaru 		: daftarInapObj.TRawatInap_PasBaru,
		        pasien_norm 	: daftarInapObj.TPasien_NomorRM,
		        pasien_nama 	: daftarInapObj.TPasien_Nama,
		        pasien_alamat 	: daftarInapObj.TPasien_Alamat,
		        inap_tgl 		: daftarInapObj.TRawatInap_TglMasuk,
		        pasien_gender 	: daftarInapObj.TAdmVar_Gender,
		        pelaku_kode 	: daftarInapObj.TPelaku_Kode,
		        pelaku_nama 	: daftarInapObj.TPelaku_NamaLengkap,
		        TTmpTidur_Nama 	: daftarInapObj.TTmpTidur_Nama,
		        TTmpTidur_Kode 	: daftarInapObj.TTmpTidur_Kode,
		        umurHari 		: parseInt(daftarInapObj.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(daftarInapObj.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(daftarInapObj.TRawatInap_UmurThn),
		        penjamin 		: daftarInapObj.TPerusahaan_Nama,
		        penjamin_kode 	: daftarInapObj.TPerusahaan_Kode,
		        Kelas_Nama 		: daftarInapObj.TKelas_Nama,
		        Kelas_Kode 		: daftarInapObj.TKelas_Kode,
		        PasienKota 		: daftarInapObj.TWilayah2_Nama
		      });
		
			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrDft('+i+')"></td>';
			isi += '<td width="20%">'+daftarInapObj.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+daftarInapObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarInapObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarInapObj.TRawatInap_TglMasuk+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

// === Daftar Poli ===
function cDaftarInap(){

	var key1 = $('#searchkey1').val();

	$.get('/ajax-caripendaftaraninap?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th></th>';
		isi += '<th class="column-title">No Admisi</th>';
		isi += '<th class="column-title">No. RM</th>';
		isi += '<th class="column-title">Nama</th>';
		isi += '<th class="column-title">Tanggal</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarInap = [];
	
		$.each(data, function(index, daftarInapObj){
			arrDaftarInap.push({
		        Inap_noreg 		: daftarInapObj.TRawatInap_NoAdmisi,
		        PasBaru 		: daftarInapObj.TRawatInap_PasBaru,
		        pasien_norm 	: daftarInapObj.TPasien_NomorRM,
		        pasien_nama 	: daftarInapObj.TPasien_Nama,
		        pasien_alamat 	: daftarInapObj.TPasien_Alamat,
		        inap_tgl 		: daftarInapObj.TRawatInap_TglMasuk,
		        pasien_alamat 	: daftarInapObj.TPasien_Alamat,
		        pasien_gender 	: daftarInapObj.TAdmVar_Gender,
		        pelaku_kode 	: daftarInapObj.TPelaku_Kode,
		        pelaku_nama 	: daftarInapObj.TPelaku_NamaLengkap,
		        TTmpTidur_Nama 	: daftarInapObj.TTmpTidur_Nama,
		        TTmpTidur_Kode 	: daftarInapObj.TTmpTidur_Kode,
		        umurHari 		: parseInt(daftarInapObj.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(daftarInapObj.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(daftarInapObj.TRawatInap_UmurThn),
		        penjamin 		: daftarInapObj.TPerusahaan_Nama,
		        penjamin_kode 	: daftarInapObj.TPerusahaan_Kode,
		        Kelas_Nama 		: daftarInapObj.TKelas_Nama,
		        Kelas_Kode 		: daftarInapObj.TKelas_Kode,
		        PasienKota 		: daftarInapObj.TWilayah2_Nama 
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrDft('+i+')"></td>';
			isi += '<td width="20%">'+daftarInapObj.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+daftarInapObj.TPasien_NomorRM+'</td>';
			isi += '<td width="30%">'+daftarInapObj.TPasien_Nama+'</td>';
			isi += '<td>'+daftarInapObj.TRawatInap_TglMasuk+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}
// === Daftar tarif IKB ===
function cTarifIkb(keyword, kdTarif,kls){
	$.get('/ajax-tarifviewikb?kuncicari='+keyword+'&kdTarif='+kdTarif+'&kls='+kls, function(data){

		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="100px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifIkb = [];

		$.each(data, function(index, trfikbObj){

			arrTarifIkb.push({
		        tarifkode 		: trfikbObj.TTarifIRB_Kode,
		        tarifnama 		: trfikbObj.TTarifIRB_Nama,
		        tarif 			: parseFloat(trfikbObj.TTarifIRB).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfikbObj.TTarifIRB_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfikbObj.TTarifIRB_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfikbObj.TTarifIRB_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfikbObj.TTarifIRB_RSFT).toFixed(2),
		     
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarikb" name="daftarikb" onclick="sendArrIkb('+i+')"></td>';
			isi += '<td>'+trfikbObj.TTarifIRB_Kode+'</td>';
			isi += '<td>'+trfikbObj.TTarifIRB_Nama+'</td>';
			isi += '<td>'+trfikbObj.TTarifIRB+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}


// === Daftar tarif radiologi ===
function cTarifRadiologiJalan(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Jalan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarif 			: parseFloat(trfradObj.TTarifRad_Jalan).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td style="text-align:left;">'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(trfradObj.TTarifRad_Jalan).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ===================== Daftar tarif Fisioterapi =============================
function cTarifFisioSearch(keyword, kdTarif){
	$.get('/ajax-tariffisiosearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Jalan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifFisio = [];

		$.each(data, function(index, fisio){

			arrTarifFisio.push({
		        tarifkode 		: fisio.TTarifFisio_Kode,
		        tarifnama 		: fisio.TTarifFisio_Nama,
		        tarif 			: parseFloat(fisio.TTarifFisio_Jalan).toFixed(2),
		        tarifdokterpt 	: 0,
		        tarifdokterft 	: 0,
		        tarifrspt 		: parseFloat(fisio.TTarifFisio_Jalan).toFixed(2),
		        tarifrsft 		: parseFloat(fisio.TTarifFisio_Jalan).toFixed(2),
		        tarifjenis 		: fisio.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarFisio" name="daftarFisio" onclick="sendArrFis('+i+')"></td>';
			isi += '<td>'+fisio.TTarifFisio_Kode+'</td>';
			isi += '<td style="text-align:left;">'+fisio.TTarifFisio_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(fisio.TTarifFisio_Jalan).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif radiologi ===
function cTarifRadiologikelas1(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas I</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarifkelas1 	: parseFloat(trfradObj.TTarifRad_Kelas1).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td>'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trfradObj.TTarifRad_Kelas1).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif radiologi ===
function cTarifRadiologikelas2(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas II</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarifkelas2 	: parseFloat(trfradObj.TTarifRad_Kelas2).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td>'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trfradObj.TTarifRad_Kelas2).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif radiologi ===
function cTarifRadiologikelas3(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas III</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarifkelas3 	: parseFloat(trfradObj.TTarifRad_Kelas3).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td>'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trfradObj.TTarifRad_Kelas3).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif radiologi ===
function cTarifRadiologiVIP(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">VIP</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarifvip 		: parseFloat(trfradObj.TTarifRad_Utama).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td>'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trfradObj.TTarifRad_Utama).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif radiologi ===
function cTarifRadiologiVVIP(keyword, kdTarif){
	$.get('/ajax-tarifradsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">VVIP</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifRad = [];

		$.each(data, function(index, trfradObj){

			arrTarifRad.push({
		        tarifkode 		: trfradObj.TTarifRad_Kode,
		        tarifnama 		: trfradObj.TTarifRad_Nama,
		        tarifVVIP 		: parseFloat(trfradObj.TTarifRad_VIP).toFixed(2),
		        tarifdokterpt 	: parseFloat(trfradObj.TTarifRad_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trfradObj.TTarifRad_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trfradObj.TTarifRad_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trfradObj.TTarifRad_RSFT).toFixed(2),
		        tarifjenis 		: trfradObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrRad('+i+')"></td>';
			isi += '<td>'+trfradObj.TTarifRad_Kode+'</td>';
			isi += '<td>'+trfradObj.TTarifRad_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trfradObj.TTarifRad_VIP).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar Poli ===
function cDaftarRadiografer(){

	var key1 = $('#searchkey1').val();

	$.get('/ajax-caripetugasradiologi?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="10%"></th>';
		isi += '<th width="30%" class="column-title">Kode Radiografer</th>';
		isi += '<th width="60%" class="column-title">Nama Radiografer</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, daftarPetugasRadObj){

			arrDaftarPoli.push({
		        PetugasRad_Kode 		: daftarPetugasRadObj.TPelaku_Kode,
		        PetugasRad_Nama 		: daftarPetugasRadObj.TPelaku_Nama,
		        PetugasRad_NamaLengkap 	: daftarPetugasRadObj.TPelaku_NamaLengkap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td ><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrDft('+i+')"></td>';
			isi += '<td >'+daftarPetugasRadObj.TPelaku_Kode+'</td>';
			isi += '<td style="text-align:left;">'+daftarPetugasRadObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cHasilRadiologi(){

	var key2 = $('#searchkey1').val();
    var tgl1 = $('#tglsearch3').val();
    var tgl2 = $('#tglsearch4').val();

	$.get('/ajax-getradtranshasil?key2='+key2+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="150px">No.Trans</th>';
		isi += '<th width="75px">No. RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '<th width="150px">Tindakan</th>';

		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
				noreg 			: daftarpasienObj.TRad_NoReg,
		      	norontgen 		: daftarpasienObj.TRad_Nomor,
		        pasien_norm 	: daftarpasienObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpasienObj.TRad_PasienNama,
		        pasien_alamat 	: daftarpasienObj.TRad_PasienAlamat,
		        pasien_gender 	: daftarpasienObj.TRad_PasienGender,
		        usiathn 		: daftarpasienObj.TRad_PasienUmurThn,
		        usiabln 		: daftarpasienObj.TRad_PasienUmurBln,
		        usiahr 			: daftarpasienObj.TRad_PasienUmurHr,
		        penjamin_kode 	: daftarpasienObj.TPerusahaan_Kode,
		        penjamin 		: daftarpasienObj.TPerusahaan_Nama,
		        pelaku 			: daftarpasienObj.TPelaku_NamaLengkap,
		        TTmpTidur_Nama 	: daftarpasienObj.TTmpTidur_Nama,
				Kelas_Nama 		: daftarpasienObj.TKelas_Keterangan,
		        PasienKota 		: daftarpasienObj.TRad_PasienKota,
		        PasienJenis 	: daftarpasienObj.TRad_Jenis,
		        detiltrans 		: daftarpasienObj.TRadDetil_Nama,
		        pelakudetail 	: daftarpasienObj.pelakudetil,
		        hasil 			: daftarpasienObj.TRadDetil_Hasil,
		        idrad 			: daftarpasienObj.id,
		        diagnosa 		: daftarpasienObj.TRad_Catatan,
		        dokterPJ 		: daftarpasienObj.TRad_DokterBaca,
		        iddetilrad 		: daftarpasienObj.dettilid
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpasienradiologi" name="daftarpasienradiologi" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TRad_Nomor+'</td>';
			isi += '<td style="text-align:left;">'+(daftarpasienObj.TPasien_NomorRM== null ? '' :daftarpasienObj.TPasien_NomorRM)+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRad_PasienNama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRadDetil_Nama+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cTransRadiologi(keyword, tgl1, tgl2){
	var keyword = $('#searchkey1').val();
    var tgl1    = $('#tglsearch1').val();
    var tgl2    = $('#tglsearch2').val();

	$.get('/ajax-getradiologitranslengkap?key='+keyword+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">No.Trans</th>';
		isi += '<th width="75px">No. RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
				noreg 			: daftarpasienObj.TRad_NoReg,
		      	norontgen 		: daftarpasienObj.TRad_Nomor,
		        pasien_norm 	: daftarpasienObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpasienObj.TRad_PasienNama,
		        pasien_alamat 	: daftarpasienObj.TRad_PasienAlamat,
		        pasien_gender 	: daftarpasienObj.TRad_PasienGender,
		        usiathn 		: daftarpasienObj.TRad_PasienUmurThn,
		        usiabln 		: daftarpasienObj.TRad_PasienUmurBln,
		        usiahr 			: daftarpasienObj.TRad_PasienUmurHr,
		        penjamin_kode 	: daftarpasienObj.TPerusahaan_Kode,
		        penjamin 		: daftarpasienObj.TPerusahaan_Nama,
		        pelaku 			: daftarpasienObj.TPelaku_NamaLengkap,
		        TTmpTidur_Nama 	: daftarpasienObj.TTmpTidur_Nama,
				Kelas_Nama 		: daftarpasienObj.TKelas_Keterangan,
		        PasienKota 		: daftarpasienObj.TRad_PasienKota,
		        idrad 			: daftarpasienObj.id,
		        diagnosa 		: daftarpasienObj.TRad_Catatan,
		        dokterPJ 		: daftarpasienObj.TRad_DokterBaca,
		        PasienJenis 	: daftarpasienObj.TRad_Jenis
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpasienradiologi" name="daftarpasienradiologi" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TRad_Nomor+'</td>';
			isi += '<td style="text-align:left;">'+(daftarpasienObj.TPasien_NomorRM== null ? '' :daftarpasienObj.TPasien_NomorRM)+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRad_PasienNama+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function craddetail(){

	var key1 = $('#norontgen').val();
	var searchkey1 = $('#searchkey1').val();

	$.get('/ajax-getraddetail?key1='+key1+'&searchkey1='+searchkey1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">No.Rontgen</th>';
		isi += '<th width="75px">Kode</th>';
		isi += '<th width="200px">Nama</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
				iddetilrad 		: daftarpasienObj.id,
				kode 			: daftarpasienObj.TTarifRad_Kode,
		      	nama 			: daftarpasienObj.TRadDetil_Nama,
		      	pelakudetail 	: daftarpasienObj.TPelaku_NamaLengkap

		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftartindakan" name="daftartindakan" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TRad_Nomor+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TTarifRad_Kode+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRadDetil_Nama+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cstandarhasil(){

	var key1 = $('#searchkey1').val();
	var isi = '';
	$.get('/ajax-getstandarhasilradiologi?key1='+key1, function(data){
		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Kode</th>';
		isi += '<th width="75px">Nama</th>';
		isi += '<th width="200px">Keterangan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
				kode 	: daftarpasienObj.TRadStandar_Kode,
		      	hasil 	: daftarpasienObj.TRadStandar_Hasil
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="listhasil" name="listhasil" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TRadStandar_Kode+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRadStandar_Nama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TRadStandar_Hasil+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}


// ============================ Transaksi Rawat Jalan (Poli dan UGD) untuk Unit Farmasi =============================
function cTransJalanFAR(key){

	$.get('/ajax-vrawatjalanobatfarmasisearch?key='+key, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Trans</th>';
		isi += '<th width="100px">Tanggal Transaksi</th>';
		isi += '<th width="100px">Unit</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			arrTempTrans.push({
		        noTrans 		: data.TRawatJalan_NoReg,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatJalan_PasienUmurHr), 
		        umurBulan 		: parseInt(data.TRawatJalan_PasienUmurBln),
		        umurTahun 		: parseInt(data.TRawatJalan_PasienUmurThn),
		        alamat 			: data.TPasien_Alamat,
		        unitKode 		: data.TUnit_Kode,
		        unitNama 		: data.TUnit_Nama,
		        ruang 			: '',
		        ruangkode 		: '',
		        kelas 			: 'J', 
		        jenisPasien 	: 'J', 
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: 0, 
		        RDNomor 		: data.RDNomor, 
			    ReffApotek 		: data.ReffApotek,
			    ReffAlergi 		: data.ReffAlergi,
			    ReffPelaku 		: data.ReffPelaku
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+data.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+data.TUnit_Nama+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================ Transaksi Rawat Jalan (Poli dan UGD) untuk Retur Unit Farmasi =============================
function cTransJalanRetur(key){

	$.get('/ajax-vrawatjalanreturobatsearch?key='+key, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Rawat Jalan</th>';
		isi += '<th width="100px">Tanggal Transaksi</th>';
		isi += '<th width="100px">Unit</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			arrTempTrans.push({
		        noTrans 		: data.TRawatJalan_NoReg,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatJalan_PasienUmurHr), 
		        umurBulan 		: parseInt(data.TRawatJalan_PasienUmurBln),
		        umurTahun 		: parseInt(data.TRawatJalan_PasienUmurThn),
		        alamat 			: data.TPasien_Alamat,
		        unitKode 		: data.TUnit_Kode,
		        unitNama 		: data.TUnit_Nama,
		        ruang 			: '',
		        ruangkode 		: '',
		        kelas 			: 'J', 
		        jenisPasien 	: 'J', 
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: 0
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+data.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+data.TUnit_Nama+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================ Search Transaksi Rawat Inap =============================
function cDaftarInapAdmisi(key, jenis, tgl1, tgl2){

	$.get('/ajax-trawatinaptagihansearch?key='+key+'&jenis='+jenis+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="50px">Status</th>';
		isi += '<th width="100px">Nomor Admisi</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '<th width="100px">Ruang</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			NomorAdmisiInap = data.TRawatInap_NoAdmisi;

			var lamainap = 0;
			var msPerDay = 8.64e7;

			var tglmasuk 	= new Date(data.TRawatInap_TglMasuk);
			var tglSkg 		= new Date();

			lamainap = Math.round((tglSkg - tglmasuk) / msPerDay);

			arrTempTrans.push({
				transID 		: data.id,
				status 			: data.Status,
		        noTrans 		: data.TRawatInap_NoAdmisi,
		        nomornota 		: data.TRawatInap_NomorNota,
		        tglmasuk 		: data.TRawatInap_TglMasuk,
		        tglkeluar 		: data.TRawatInap_TglKeluar,
		        noRM 			: data.TPasien_NomorRM,
		        pasBaru 		: data.TRawatInap_PasBaru,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        pasienTelp 		: data.TPasien_Telp,
		        pasienHP 		: data.TPasien_HP,
		        nomorkuitansi 	: data.TKasir_Nomor,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(data.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(data.TRawatInap_UmurThn),
		        alamat 			: data.TPasien_Alamat,
		        kota 			: data.TWilayah2_Nama,
		        unitKode 		: data.TTmpTidur_Kode,
		        unitNama 		: data.TTmpTidur_Nama,
		        ruang 			: data.TTmpTidur_Nama,
		        ruangkode 		: data.TTmpTidur_Kode,
		        kelas 			: data.TTmpTidur_KelasKode, 
		        jenisPasien 	: 'I', 
		        statusbayar 	: data.TRawatInap_StatusBayar,
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        verifikasi 		: data.TRawatInap_Verifikasi, 
		        lamainap 		: lamainap

		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.Status+'</td>';
			isi += '<td>'+data.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+data.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '<td>'+data.TTmpTidur_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================ Search Transaksi Rawat Jalan dengan Status dan Tanggal =============================
function cDaftarJalanReg(key, jenis, tgl1, tgl2){

	$.get('/ajax-trawatjalantagihansearch?key='+key+'&jenis='+jenis+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="50px">Status</th>';
		isi += '<th width="100px">Nomor Reg</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '<th width="100px">Unit</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			var tgl 	= new Date(data.TPasien_TglLahir);
			var tgllhr 	= tgl.getFullYear()+'-'+(tgl.getMonth()+1)+'-'+tgl.getDate();

			arrTempTrans.push({

				transID 		: data.id,
				status 			: data.Status,
		        noTrans 		: data.TRawatJalan_NoReg,
		        tglTrans 		: data.TRawatJalan_Tanggal,
		        caradaftar 		: data.TRawatJalan_CaraDaftar,
		        asalpasien 		: data.TRawatJalan_AsalPasien,
		        ketsumber 		: data.TRawatJalan_KetSumber,
		        rujukandari		: data.TRawatJalan_RujukanDari,
		        pulangcara 		: '1',
		        biayadaftar 	: data.TRawatJalan_Daftar,
		        diagpoli 		: data.TrawatJalan_DiagPoli,
		        noRM 			: data.TPasien_NomorRM,
		        pasBaru 		: data.TRawatJalan_PasBaru,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienTgllahir 	: tgllhr,
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        pasienTelp 		: data.TPasien_Telp,
		        pasienHP 		: data.TPasien_HP,
		        nomorkuitansi 	: data.TKasir_Nomor,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatJalan_PasienUmurThn), 
		        umurBulan 		: parseInt(data.TRawatJalan_PasienUmurBln),
		        umurTahun 		: parseInt(data.TRawatJalan_PasienUmurHr),
		        alamat 			: data.TPasien_Alamat,
		        kota 			: data.TWilayah2_Nama,
		        unitKode 		: data.TUnit_Kode,
		        unitNama 		: data.TUnit_Nama,
		        kelas 			: 'J', 
		        jenisPasien 	: 'J', 
		        statusbayar 	: data.TRawatJalan_Status,
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        verifikasi 		: data.TRawatInap_Verifikasi,
		        sepnomorkartu 	: data.TSep_NOKAPST,
		        sepnomor 		: data.TSep_Nomor

		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.Status+'</td>';
			isi += '<td>'+data.TRawatJalan_NoReg+'</td>';
			isi += '<td>'+data.TRawatJalan_Tanggal+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '<td>'+data.TUnit_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================ Transaksi Rawat Inap untuk Unit Farmasi =============================
function cTransInapFAR(key){

	$.get('/ajax-trawatinapsearch?key='+key, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Admisi</th>';
		isi += '<th width="100px">Tanggal Transaksi</th>';
		isi += '<th width="100px">Ruang</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			var lamainap = 0;
			var msPerDay = 8.64e7;

			var tglmasuk 	= new Date(data.TRawatInap_TglMasuk);
			var tglSkg 		= new Date();

			lamainap = Math.round((tglSkg - tglmasuk) / msPerDay);

			arrTempTrans.push({
		        noTrans 		: data.TRawatInap_NoAdmisi,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(data.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(data.TRawatInap_UmurThn),
		        alamat 			: data.TPasien_Alamat,
		        unitKode 		: data.TTmpTidur_Kode,
		        unitNama 		: data.TTmpTidur_Nama,
		        ruang 			: data.TTmpTidur_Nama,
		        ruangkode 		: data.TTmpTidur_Kode,
		        kelas 			: data.TTmpTidur_KelasKode, 
		        jenisPasien 	: 'J', 
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: lamainap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+data.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+data.TTmpTidur_Nama+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}
// ========= Cari Daftar Poli UGD ===============
	function cDaftarPoliUGD(){

		var key1 = $('#searchkey1').val();
		// var key2 = $('#searchkey2').val();

		var today = new Date();

		var d = today.getDate();
		var m = today.getMonth()+1;
		var Y = today.getFullYear();

		var key2 = m+'/'+d+'/'+Y; 

		$.get('/ajax-vrawatjalanPOLIUGDsearchrad?key1='+key1, function(data){
			var isi = '';
			var nomorrm = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor Reg</th>';
			isi += '<th width="75px">Pasien No. RM</th>';
			isi += '<th width="150px">Pasien Nama</th>';
			isi += '<th width="100px">Tanggal</th>';
			isi += '<th width="100px">Unit</th>';
			isi += '<th width="150px">Dokter</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			arrDaftarPoli = [];

			$.each(data, function(index, daftarpoliObj){
					arrDaftarPoli.push({

			        jalan_noreg 	: daftarpoliObj.TRawatJalan_NoReg,
			        pasien_norm 	: daftarpoliObj.TPasien_NomorRM,
			        pasien_nama 	: daftarpoliObj.TPasien_Nama,
			        pasien_umurthn 	: daftarpoliObj.TRawatJalan_PasienUmurThn,
			        pasien_umurbln 	: daftarpoliObj.TRawatJalan_PasienUmurBln,
			        pasien_umurhr 	: daftarpoliObj.TRawatJalan_PasienUmurHr,
			        pasien_alamat 	: daftarpoliObj.TPasien_Alamat,
			        pasien_gender 	: daftarpoliObj.TAdmVar_Gender,
			        jalan_tgl 		: daftarpoliObj.TRawatJalan_Tanggal,
			        kota 			: daftarpoliObj.TWilayah2_Nama,
			        unit_kode 		: daftarpoliObj.TUnit_Kode,
			        unit_nama 		: daftarpoliObj.TUnit_Nama,
			        penjamin 		: daftarpoliObj.TPerusahaan_Nama,
			        penjamin_kode 	: daftarpoliObj.TPerusahaan_Kode,
			        pelaku_kode 	: daftarpoliObj.TPelaku_Kode,
			        pelaku_nama 	: daftarpoliObj.TPelaku_NamaLengkap,
			        PasBaru 		: daftarpoliObj.TRawatJalan_PasBaru
			      });

				isi += '<tr class="even pointer">';
				isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
				isi += '<td>'+daftarpoliObj.TRawatJalan_NoReg+'</td>';
				isi += '<td>'+daftarpoliObj.TPasien_NomorRM+'</td>';
				isi += '<td style="text-align:left;">'+daftarpoliObj.TPasien_Nama+'</td>';
				isi += '<td>'+daftarpoliObj.TRawatJalan_Tanggal+'</td>';
				isi += '<td>'+daftarpoliObj.TUnit_Nama+'</td>';
				isi += '<td style="text-align:left;">'+daftarpoliObj.TPelaku_NamaLengkap+'</td>';
				isi += '</tr>';
				i++;

			});

			isi += '</tbody>';
			isi += '</table>';

			document.getElementById('hasil').innerHTML = isi;
		});

	}

// ========= Cari Daftar Rawat Jalan untuk Pasien Radiologi ===============
	function cDaftarJalanRadiologi(){

		var key1 = $('#searchkey1').val();

		var today = new Date();

		var d = today.getDate();
		var m = today.getMonth()+1;
		var Y = today.getFullYear();

		var key2 = m+'/'+d+'/'+Y; 

		$.get('/ajax-vrawatjalanPOLIUGDsearchrad?key1='+key1, function(data){
			var isi = '';
			var nomorrm = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor Reg</th>';
			isi += '<th width="75px">Pasien No. RM</th>';
			isi += '<th width="150px">Pasien Nama</th>';
			isi += '<th width="100px">Tanggal</th>';
			isi += '<th width="100px">Unit</th>';
			isi += '<th width="150px">Dokter</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			arrDaftarPoli = [];

			$.each(data, function(index, daftarpoliObj){
					arrDaftarPoli.push({

			        jalan_noreg 	: daftarpoliObj.TRawatJalan_NoReg,
			        pasien_norm 	: daftarpoliObj.TPasien_NomorRM,
			        pasien_nama 	: daftarpoliObj.TPasien_Nama,
			        pasien_umurthn 	: daftarpoliObj.TRawatJalan_PasienUmurThn,
			        pasien_umurbln 	: daftarpoliObj.TRawatJalan_PasienUmurBln,
			        pasien_umurhr 	: daftarpoliObj.TRawatJalan_PasienUmurHr,
			        pasien_alamat 	: daftarpoliObj.TPasien_Alamat,
			        pasien_gender 	: daftarpoliObj.TAdmVar_Gender,
			        jalan_tgl 		: daftarpoliObj.TRawatJalan_Tanggal,
			        kota 			: daftarpoliObj.TWilayah2_Nama,
			        unit_kode 		: daftarpoliObj.TUnit_Kode,
			        unit_nama 		: daftarpoliObj.TUnit_Nama,
			        penjamin 		: daftarpoliObj.TPerusahaan_Nama,
			        penjamin_kode 	: daftarpoliObj.TPerusahaan_Kode,
			        pelaku_kode 	: daftarpoliObj.TPelaku_Kode,
			        pelaku_nama 	: daftarpoliObj.TPelaku_NamaLengkap,
			        PasBaru 		: daftarpoliObj.TRawatJalan_PasBaru,
			        RDNomor 		: daftarpoliObj.RDNomor, 
			        ReffRad 		: daftarpoliObj.ReffRad,
			        ReffPelaku 		: daftarpoliObj.ReffPelaku

			      });

				isi += '<tr class="even pointer">';
				isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
				isi += '<td>'+daftarpoliObj.TRawatJalan_NoReg+'</td>';
				isi += '<td>'+daftarpoliObj.TPasien_NomorRM+'</td>';
				isi += '<td style="text-align:left;">'+daftarpoliObj.TPasien_Nama+'</td>';
				isi += '<td>'+daftarpoliObj.TRawatJalan_Tanggal+'</td>';
				isi += '<td>'+daftarpoliObj.TUnit_Nama+'</td>';
				isi += '<td style="text-align:left;">'+daftarpoliObj.TPelaku_NamaLengkap+'</td>';
				isi += '</tr>';
				i++;

			});

			isi += '</tbody>';
			isi += '</table>';

			document.getElementById('hasil').innerHTML = isi;
		});

	}

// === Daftar Poli ===
function cDaftarPasienLama(){

	var key1 = $('#searchkey1').val();
	// var key2 = $('#searchkey2').val();

	$.get('/ajax-pasienTanpaRegistrasi?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
		      
		        pasien_norm 	: daftarpasienObj.TPasien_NomorRM,
		        pasien_nama 	: daftarpasienObj.TPasien_Nama,
		        pasien_tgllahir : daftarpasienObj.TPasien_TglLahir,
		        pasien_alamat 	: daftarpasienObj.TPasien_Alamat,
		        pasien_gender 	: daftarpasienObj.TAdmVar_Gender,
		        PasienKota 		: daftarpasienObj.TWilayah2_Nama,
		        penjamin_kode 	: '0-0000',
		        penjamin 		: 'Reguler/Umum',
		        
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarPasien" name="daftarPasien" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cDaftarPasienSurat(){

	var key1 = $('#searchkey1').val();

	$.get('/ajax-pasienTanpaRegistrasi?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '<th width="250px">Alamat</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			arrDaftarPasien.push({
		      
		        pasien_norm 		: daftarpasienObj.TPasien_NomorRM,
		        pasien_nama 		: daftarpasienObj.TPasien_Nama,
		        pasien_tgllahir 	: daftarpasienObj.TPasien_TglLahir,
		        pasien_alamat 		: daftarpasienObj.TPasien_Alamat,
		        pasien_gender 		: daftarpasienObj.TAdmVar_Gender,
		        telepon 			: daftarpasienObj.TPasien_Telp,
		        PasienKota 			: daftarpasienObj.TWilayah2_Nama,
		        PasienAgama 		: daftarpasienObj.TAdmVar_Agama,
		        PasienPekerjaan 	: daftarpasienObj.TPasien_Kerja,
		        PasienGoldarah 		: daftarpasienObj.TAdmVar_Darah,
		        Pasien_klgnama 		: daftarpasienObj.TPasien_KlgNama,
		        penjamin_kode 		: '0-0000',
		        penjamin 			: 'Reguler/Umum',
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarPasien" name="daftarPasien" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TPasien_Nama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TPasien_Alamat+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

// Search Trans Lab
	function cDaftarPoliLab(){

		var key1 = $('#searchkey1').val();
		// var key2 = $('#searchkey2').val();

		var today = new Date();

		var d = today.getDate();
		var m = today.getMonth()+1;
		var Y = today.getFullYear();

		var key2 = m+'/'+d+'/'+Y; 

		$.get('/ajax-vrawatjalanPOLIUGDsearchlab?key1='+key1+'&key2='+key2, function(data){
			var isi = '';
			var nomorrm = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor Reg</th>';
			isi += '<th width="75px">Pasien No. RM</th>';
			isi += '<th width="150px">Pasien Nama</th>';
			isi += '<th width="100px">Tanggal</th>';
			isi += '<th width="100px">Unit</th>';
			isi += '<th width="150px">Dokter</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			arrDaftarPoli = [];

			$.each(data, function(index, daftarpoliObj){
					arrDaftarPoli.push({

			        jalan_noreg 	: daftarpoliObj.TRawatJalan_NoReg,
			        pasien_norm 	: daftarpoliObj.TPasien_NomorRM,
			        pasien_nama 	: daftarpoliObj.TPasien_Nama,
			        pasien_umurthn 	: daftarpoliObj.TRawatJalan_PasienUmurThn,
			        pasien_umurbln 	: daftarpoliObj.TRawatJalan_PasienUmurBln,
			        pasien_umurhr 	: daftarpoliObj.TRawatJalan_PasienUmurHr,
			        pasien_alamat 	: daftarpoliObj.TPasien_Alamat,
			        pasien_gender 	: daftarpoliObj.TAdmVar_Gender,
			        jalan_tgl 		: daftarpoliObj.TRawatJalan_Tanggal,
			        kota 			: daftarpoliObj.TWilayah2_Nama,
			        unit_kode 		: daftarpoliObj.TUnit_Kode,
			        unit_nama 		: daftarpoliObj.TUnit_Nama,
			        penjamin 		: daftarpoliObj.TPerusahaan_Nama,
			        penjamin_kode 	: daftarpoliObj.TPerusahaan_Kode,
			        pelaku_kode 	: daftarpoliObj.TPelaku_Kode,
			        pelaku_nama 	: daftarpoliObj.TPelaku_NamaLengkap,
			        PasBaru 		: daftarpoliObj.TRawatJalan_PasBaru,
			        RDNomor 		: daftarpoliObj.RDNomor, 
			        ReffLab 		: daftarpoliObj.ReffLab,
			        ReffPelaku 		: daftarpoliObj.ReffPelaku
			      });

				isi += '<tr class="even pointer">';
					isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
					isi += '<td>'+daftarpoliObj.TRawatJalan_NoReg+'</td>';
					isi += '<td>'+daftarpoliObj.TPasien_NomorRM+'</td>';
					isi += '<td style="text-align:left;">'+daftarpoliObj.TPasien_Nama+'</td>';
					isi += '<td>'+daftarpoliObj.TRawatJalan_Tanggal+'</td>';
					isi += '<td>'+daftarpoliObj.TUnit_Nama+'</td>';
					isi += '<td style="text-align:left;">'+daftarpoliObj.TPelaku_NamaLengkap+'</td>';
				isi += '</tr>';
				i++;

			});

			isi += '</tbody>';
			isi += '</table>';

			document.getElementById('hasil').innerHTML = isi;
		});

	}

// =============== Cari Obat berdasarkan Transaksi ============================

function cObatByTransMulti(keyword, notrans, penjamin){

	var isi 		= '';
	var nomorrm 	= '';
	var Margin 		= 0
	var harga 		= 0;
	var HNA_PPN 	= 0.0; 

	arrMargin 	= [];
	arrObat 	= [];
	arrTempObat = [];


	isi += '<table id="datatable1" class="responstable">';
	isi += '<thead>';

	$.get('/ajax-marginobat?kdpenjamin='+penjamin, function(data){
		
		Margin 	= data;

		$.get('/ajax-obatkmrdetilbynotrans?kuncicari='+keyword+'&notrans='+notrans, function(data){

			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor Transaksi</th>';
			isi += '<th width="100px">Kode Obat</th>';
			isi += '<th width="200px">Nama Obat</th>';
			isi += '<th width="100px">Banyak</th>';
			isi += '<th width="150px">Harga</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			$.each(data, function(index, obatObj){
				HNA_PPN = obatObj.TObat_HNA_PPN;
					
				harga = ((Margin * parseFloat(HNA_PPN))+ parseFloat(HNA_PPN));

				harga = parseFloat(harga).toFixed(2);
				
				arrObat.push({
			        ID 				: obatObj.id,
			        KmrNomor 		: obatObj.TObatKmr_Nomor,
			        Kode 			: obatObj.TObat_Kode,
			        Nama 			: obatObj.TObat_Nama,
			        NamaGenerik 	: obatObj.TObat_NamaGenerik,
			        Satuan 			: obatObj.TObatKmrDetil_Satuan,
			        Satuan2 		: obatObj.TObat_Satuan2,
			        SatuanFaktor 	: obatObj.TObatKmrDetil_Faktor,
			        Jumlah 			: obatObj.TObatKmrDetil_Banyak,
			        DiskonPrs 		: obatObj.TObatKmrDetil_DiskonPrs,
			        Diskon 			: obatObj.TObatKmrDetil_Diskon,
			        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
			        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
			        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
			        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
			        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
			        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
			        JualFaktor 		: obatObj.TObat_JualFaktor,
			        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
			        HargaJualUmum 	: harga
				});
					
				isi += '<tr class="even pointer">';
				isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
				isi += '<td style="text-align:left;">'+obatObj.TObatKmr_Nomor+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObatKmrDetil_Banyak+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(harga)+'</td>';
				isi += '</tr>';

				i++;
					
			}); // $.each(data, function(index, obatObj){
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;

		}); // ... $.get('/ajax-obatgrupmutasisearch?

	}); // ... $.get('/ajax-marginobat?
	
}

// ============= End Cari Obat berdasarkan Transaksi ==========================



// =============== Cari Obat Retur berdasarkan Transaksi ============================

function cObatReturByTransMulti(keyword, notrans, penjamin){

	var isi 		= '';
	var nomorrm 	= '';
	var Margin 		= 0
	var harga 		= 0;
	var HNA_PPN 	= 0.0; 

	arrMargin 	= [];
	arrObat 	= [];
	arrTempObat = [];


	isi += '<table id="datatable1" class="responstable">';
	isi += '<thead>';

	$.get('/ajax-marginobat?kdpenjamin='+penjamin, function(data){
		
		Margin 	= data;

		$.get('/ajax-obatkmrdetilbynotrans?kuncicari='+keyword+'&notrans='+notrans, function(data){

			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor Transaksi</th>';
			isi += '<th width="100px">Kode Obat</th>';
			isi += '<th width="200px">Nama Obat</th>';
			isi += '<th width="100px">Banyak</th>';
			isi += '<th width="150px">Harga</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';

			var i = 0;

			$.each(data, function(index, obatObj){
				HNA_PPN = obatObj.TObat_HNA_PPN;
					
				harga = ((Margin * parseFloat(HNA_PPN))+ parseFloat(HNA_PPN));

				harga = parseFloat(harga).toFixed(2);
				
				arrObat.push({
			        ID 				: obatObj.id,
			        KmrNomor 		: obatObj.TObatKmr_Nomor,
			        Kode 			: obatObj.TObat_Kode,
			        Nama 			: obatObj.TObat_Nama,
			        NamaGenerik 	: obatObj.TObat_NamaGenerik,
			        Satuan 			: obatObj.TObatKmrDetil_Satuan,
			        Satuan2 		: obatObj.TObat_Satuan2,
			        SatuanFaktor 	: obatObj.TObatKmrDetil_Faktor,
			        Jumlah 			: parseFloat(obatObj.TObatKmrDetil_Banyak).toFixed(2),
			        DiskonPrs 		: parseFloat(obatObj.TObatKmrDetil_DiskonPrs).toFixed(2),
			        Diskon 			: parseFloat(obatObj.TObatKmrDetil_Diskon).toFixed(2),
			        HargaPokok 		: parseFloat(obatObj.TObat_HargaPokok).toFixed(2),
			        HargaBeli 		: parseFloat(obatObj.TObat_HNA).toFixed(2),
			        GdQty 			: parseFloat(obatObj.TObat_GdQty).toFixed(2),
			        GdJml 			: parseFloat(obatObj.TObat_GdJml).toFixed(2),
			        RpQty 			: parseFloat(obatObj.TObat_RpQty).toFixed(2),
			        RpJml 			: parseFloat(obatObj.TObat_RpJml).toFixed(2),
			        JualFaktor 		: obatObj.TObat_JualFaktor,
			        HargaJual 		: parseFloat(obatObj.TObat_HNA_PPN).toFixed(2),
			        HargaJualUmum 	: harga,
			        Retur 			: parseFloat(obatObj.Retur).toFixed(2)
				});

				var sisa = parseFloat(obatObj.TObatKmrDetil_Banyak) - parseFloat(obatObj.Retur);
					
				isi += '<tr class="even pointer">';
				isi += '<td><input type="checkbox" id="daftarobat'+i+'" name="daftarobat'+i+'" onchange="sendArrTempObat(this.id, '+i+', \''+obatObj.TObat_Kode+'\')"></td>';
				isi += '<td style="text-align:left;">'+obatObj.TObatKmr_Nomor+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Kode+'</td>';
				isi += '<td style="text-align:left;">'+obatObj.TObat_Nama+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(sisa)+'</td>';
				isi += '<td style="text-align:left;">'+formatRibuan(harga)+'</td>';
				isi += '</tr>';

				i++;
					
			}); // $.each(data, function(index, obatObj){
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;

		}); // ... $.get('/ajax-obatgrupmutasisearch?
	}); // ... $.get('/ajax-marginobat?
	
}

// ============= End Cari Obat Retur berdasarkan Transaksi ==========================




//================== REGION LABORATORIUM =======================================
// === Daftar tarif laboratorium ===
function cTarifLaboratoriumJalan(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100%">Tarif Nama</th>';
		isi += '<th width="125px">Jalan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarif 			: parseFloat(trflabObj.TTarifLab_Jalan).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td style="text-align:left;">'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(parseFloat(trflabObj.TTarifLab_Jalan).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif laboratorium ===
function cTarifLaboratoriumkelas1(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas I</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarifkelas1 	: parseFloat(trflabObj.TTarifLab_Kelas1).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td>'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trflabObj.TTarifLab_Kelas1).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif laboratorium ===
function cTarifLaboratoriumkelas2(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas II</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarifkelas2 	: parseFloat(trflabObj.TTarifLab_Kelas2).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td>'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trflabObj.TTarifLab_Kelas2).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif laboratorium ===
function cTarifLaboratoriumkelas3(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">Kelas III</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarifkelas3 	: parseFloat(trflabObj.TTarifLab_Kelas3).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td>'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trflabObj.TTarifLab_Kelas3).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// === Daftar tarif laboratorium ===
function cTarifLaboratoriumVIP(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">VIP</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarifvip 		: parseFloat(trflabObj.TTarifLab_Utama).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td>'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trflabObj.TTarifLab_Utama).toFixed(2))+'</td>';
			isi += '</tr>';
			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});
}

// === Daftar tarif laboratorium ===
function cTarifLaboratoriumVVIP(keyword, kdTarif){
	$.get('/ajax-tariflabsearch?kuncicari='+keyword+'&kdTarif='+kdTarif, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="100px">Tarif Nama</th>';
		isi += '<th width="150px">VVIP</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLab = [];

		$.each(data, function(index, trflabObj){

			arrTarifLab.push({
		        tarifkode 		: trflabObj.TTarifLab_Kode,
		        tarifnama 		: trflabObj.TTarifLab_Nama,
		        tarifVVIP 		: parseFloat(trflabObj.TTarifLab_VIP).toFixed(2),
		        tarifdokterpt 	: parseFloat(trflabObj.TTarifLab_DokterPT).toFixed(2),
		        tarifdokterft 	: parseFloat(trflabObj.TTarifLab_DokterFT).toFixed(2),
		        tarifrspt 		: parseFloat(trflabObj.TTarifLab_RSPT).toFixed(2),
		        tarifrsft 		: parseFloat(trflabObj.TTarifLab_RSFT).toFixed(2),
		        tarifjenis 		: trflabObj.TTarifVar_Kelompok
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrLab('+i+')"></td>';
			isi += '<td>'+trflabObj.TTarifLab_Kode+'</td>';
			isi += '<td>'+trflabObj.TTarifLab_Nama+'</td>';
			isi += '<td>'+formatRibuan(parseFloat(trflabObj.TTarifLab_VIP).toFixed(2))+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// =============== Daftar Poli =================================
function cDaftarPetugasLab(){

	var key1 = $('#searchkey1').val();

	$.get('/ajax-caripetugaslaboratorium?key1='+key1, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="10%"></th>';
		isi += '<th width="30%" class="column-title">Kode Petugas Lab</th>';
		isi += '<th width="60%" class="column-title">Nama Petugas Lab</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, daftarPetugasLabObj){

			arrDaftarPoli.push({
		        PetugasLab_Kode 		: daftarPetugasLabObj.TPelaku_Kode,
		        PetugasLab_Nama 		: daftarPetugasLabObj.TPelaku_Nama,
		        PetugasLab_NamaLengkap 	: daftarPetugasLabObj.TPelaku_NamaLengkap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td ><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrDft('+i+')"></td>';
			isi += '<td >'+daftarPetugasLabObj.TPelaku_Kode+'</td>';
			isi += '<td style="text-align:left;">'+daftarPetugasLabObj.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cTransLaboratorium(){

	var key2 = $('#searchkey1').val();

	$.get('/ajax-getlaboratoriumtranslengkap?key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="75px">No.Trans</th>';
		isi += '<th width="75px">No. RM</th>';
		isi += '<th width="200px">Nama Pasien</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPasien = [];

		$.each(data, function(index, daftarpasienObj){

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpasienlaboratorium" name="daftarpasienlaboratorium" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+daftarpasienObj.TLab_Nomor+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TPasien_id+'</td>';
			isi += '<td style="text-align:left;">'+daftarpasienObj.TLab_PasienNama+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;
	});

}

function cPeriksaLaboratorium(){

	var key1 = $('#keytrflab').val();
	var key2 = $('#kelLab').val();

	$.get('/ajax-getlabperiksa?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="5%"></th>';
		isi += '<th width="10%">Kode </th>';
		isi += '<th width="25%">Nama Pemeriksaan </th>';
		isi += '<th width="20%">Satuan</th>';
		isi += '<th width="40%">Harga Normal</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';
		
		var i = 0;

		arrDaftarPemeriksaan = [];
		arrTempDaftarPemeriksaan = [];

		$.each(data, function(index, daftarpemeriksaanObj){

			arrDaftarPemeriksaan.push({
		        status 				: daftarpemeriksaanObj.status,
		        periksa_kode 		: daftarpemeriksaanObj.TLabPeriksa_Kode,
		        periksa_nama 		: daftarpemeriksaanObj.TLabPeriksa_Nama,
		        periksa_satuan 		: daftarpemeriksaanObj.TLabPeriksa_Satuan,
		        periksa_harganorm 	: daftarpemeriksaanObj.TLabPeriksa_HargaNorm,
		        hasil_numeric 		: daftarpemeriksaanObj.TLabPeriksa_Numeric,
		        hasil_metode 		: daftarpemeriksaanObj.TLabPeriksa_Metode
		      });
			
			isi += '<tr class="even pointer">';
			isi += '<td><input type="checkbox" id="daftarpemeriksaanlaboratorium'+i+'" name="daftarpemeriksaanlaboratorium'+i+'" onchange="sendArrTempLabPeriksa(this.id, '+i+', \''+daftarpemeriksaanObj.TLabPeriksa_Kode+'\')"></td>';
			isi += '<td style="text-align:left;">'+daftarpemeriksaanObj.TLabPeriksa_Kode+'</td>';
			isi += '<td style="text-align:left;">'+daftarpemeriksaanObj.TLabPeriksa_Nama+'</td>';
			isi += '<td style="text-align:left;">'+daftarpemeriksaanObj.TLabPeriksa_Satuan+'</td>';
			isi += '<td style="text-align:left;">'+daftarpemeriksaanObj.TLabPeriksa_HargaNorm+'</td>';
			isi += '</tr>';
			i++;

		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil_combo').innerHTML = isi;
	});

}
//================== END REGION LABORATORIUM =======================================


//========================== KAMAR OPERASI ==========================================
// cari tarif operasi
function cTarifOperasi(kelas){
	var keyword = $('#searchkey1').val();

	$.get('/ajax-tarifoperasisearch?kuncicari='+keyword+'&kelas='+kelas, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="200px">Tarif Nama</th>';
		isi += '<th width="150px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;
		arrTarifOp = [];
		$.each(data, function(index, operasi){
			arrTarifOp.push({
		        kode 	: operasi.kode,
		        nama 	: operasi.nama,
		        tarifop : parseFloat(operasi.tarif).toFixed(0)		       	
		      });
			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarugd" name="daftarugd" onclick="sendArrOperasi('+i+')"></td>';
			isi += '<td>'+operasi.kode+'</td>';
			isi += '<td>'+operasi.nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(operasi.tarif)+'</td>';
			isi += '</tr>';
			i++;
		});
		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

//cari pasien untuk trans ohp
function cDaftarOpOHP(){

	var key1 = $('#searchkey1').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-opsearchohp?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Reg</th>';
		isi += '<th width="75px">Pasien No. RM</th>';
		isi += '<th width="150px">Pasien Nama</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, pasienOp){

			arrDaftarPoli.push({
		        jalan_noreg 	: pasienOp.TRawatInap_Nomor,
		        pasien_norm 	: pasienOp.TPasien_NomorRM,
		        pasien_nama 	: pasienOp.TPasien_Nama,
		        pasien_umurthn 	: pasienOp.TBedah_PasienUmurThn,
		        pasien_umurbln 	: pasienOp.TBedah_PasienUmurBln,
		        pasien_umurhr 	: pasienOp.TBedah_PasienUmurHr,
		        pasien_alamat 	: pasienOp.TPasien_Alamat,
		        pasien_gender 	: pasienOp.TAdmVar_Gender,
		        jalan_tgl 		: pasienOp.TBedah_Tanggal,
		        kota 			: pasienOp.TWilayah2_Nama,
		        unit_kode 		: pasienOp.TUnit_Kode,
		        unit_nama 		: pasienOp.TUnit_Nama,
		        penjamin 		: pasienOp.TPerusahaan_Nama,
		        penjamin_kode 	: pasienOp.TPerusahaan_Kode,
		        pelaku_kode 	: pasienOp.TPelaku_Kode_Op,
		        pelaku_nama 	: pasienOp.TPelaku_NamaLengkap,
		        TTNomor 		: pasienOp.TTmpTidur_Nomor,
		        KelasKode 		: pasienOp.TBedah_KelasKode
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+pasienOp.TRawatInap_Nomor+'</td>';
			isi += '<td>'+pasienOp.TPasien_NomorRM+'</td>';
			isi += '<td>'+pasienOp.TPasien_Nama+'</td>';
			isi += '<td>'+pasienOp.TBedah_Tanggal+'</td>';
			isi += '<td>'+pasienOp.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}
//===================================================================================

//cari pasien untuk trans ohp
function cDaftarLabOHP(){

	var key1 = $('#searchkey1').val();

	var today = new Date();

	var d = today.getDate();
	var m = today.getMonth()+1;
	var Y = today.getFullYear();

	var key2 = m+'/'+d+'/'+Y; 

	$.get('/ajax-labsearchohp?key1='+key1+'&key2='+key2, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Reg</th>';
		isi += '<th width="75px">Pasien No. RM</th>';
		isi += '<th width="150px">Pasien Nama</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Dokter</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrDaftarPoli = [];

		$.each(data, function(index, pasienLab){

			arrDaftarPoli.push({
		        jalan_noreg 	: pasienLab.TLab_NoReg,
		        pasien_norm 	: pasienLab.TPasien_NomorRM,
		        pasien_nama 	: pasienLab.TPasien_Nama,
		        pasien_umurthn 	: pasienLab.TLab_PasienUmurThn,
		        pasien_umurbln 	: pasienLab.TLab_PasienUmurBln,
		        pasien_umurhr 	: pasienLab.TLab_PasienUmurHr,
		        pasien_alamat 	: pasienLab.TPasien_Alamat,
		        pasien_gender 	: pasienLab.TAdmVar_Gender,
		        jalan_tgl 		: pasienLab.TLab_Tanggal,
		        kota 			: pasienLab.TWilayah2_Nama,
		        unit_kode 		: pasienLab.TUnit_Kode,
		        unit_nama 		: pasienLab.TUnit_Nama,
		        penjamin 		: pasienLab.TPerusahaan_Nama,
		        penjamin_kode 	: pasienLab.TPerusahaan_Kode,
		        pelaku_kode 	: pasienLab.TPelaku_Kode,
		        pelaku_nama 	: pasienLab.TPelaku_NamaLengkap,
		        TTNomor 		: pasienLab.TTmpTidur_Nomor,
		        KelasKode 		: pasienLab.TLab_KelasKode
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarpoli" name="daftarpoli" onclick="sendArrDft('+i+')"></td>';
			isi += '<td>'+pasienLab.TLab_NoReg+'</td>';
			isi += '<td>'+pasienLab.TPasien_NomorRM+'</td>';
			isi += '<td>'+pasienLab.TPasien_Nama+'</td>';
			isi += '<td>'+pasienLab.TLab_Tanggal+'</td>';
			isi += '<td>'+pasienLab.TPelaku_NamaLengkap+'</td>';
			isi += '</tr>';

			i++;

		});

		isi += '</tbody>';
		isi += '</table>';
		document.getElementById('hasil').innerHTML = isi;
	});
}

// =========== Cari Data Farmasi Berdasarkan Admisi Inap ====================
function cFarmasiByAdmisi(noreg){

	$.get('/ajax-farmasibyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Obat</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;
		var totRetur 	= 0;

		if(data.length > 0){
			$.each(data, function(index, farmObj){

				isi += '<tr class="even pointer">';
				isi += '<td>'+farmObj.TObatKmr_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+farmObj.TObat_Nama+'</td>';
				isi += '<td>'+formatRibuan(farmObj.TObatKmrDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Harga)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(farmObj.TObatKmrDetil_Jumlah);
				totPribadi 	+= parseFloat(farmObj.TObatKmrDetil_Pribadi);
				totAsuransi += parseFloat(farmObj.TObatKmrDetil_Asuransi);
				totDisc 	+= parseFloat(farmObj.TObatKmrDetil_Diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		$.get('/ajax-getreturobatfarmasi?noreg='+noreg, function(data){

			$.each(data, function(index, value){
				totRetur = parseFloat(value.Jumlah);
			});

			document.getElementById('total1').value = formatRibuan(total);
			document.getElementById('total2').value = formatRibuan(totPribadi);
			document.getElementById('total3').value = formatRibuan(totAsuransi);
			document.getElementById('total4').value = formatRibuan(totDisc);
			document.getElementById('total5').value = formatRibuan(totRetur);

			document.getElementById('rinciHasil').innerHTML = isi;
		});
	});
}
//===================================================================================

// =========== Cari Data Obat dan Alkes Berdasarkan Admisi Inap ====================
function cObatAlkesByAdmisi(noreg){

	$.get('/ajax-obatalkesbyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Obat</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, farmObj){

				isi += '<tr class="even pointer">';
				isi += '<td>'+farmObj.TObatKmr_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+farmObj.TObat_Nama+'</td>';
				isi += '<td>'+formatRibuan(farmObj.TObatKmrDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Harga)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(farmObj.TObatKmrDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(farmObj.TObatKmrDetil_Jumlah);
				totPribadi 	+= parseFloat(farmObj.TObatKmrDetil_Pribadi);
				totAsuransi += parseFloat(farmObj.TObatKmrDetil_Asuransi);
				totDisc 	+= parseFloat(farmObj.TObatKmrDetil_Diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Tindakan Kamar Operasi Berdasarkan Admisi Inap ====================
function cOperasiByAdmisi(noreg){

	$.get('/ajax-operasibyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, bedahObj){

				isi += '<tr class="even pointer">';
				isi += '<td>'+bedahObj.TBedah_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+bedahObj.TTarifIBS_Nama+'</td>';
				isi += '<td>'+formatRibuan(bedahObj.TBedahDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(bedahObj.TBedahDetil_Tarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(bedahObj.TBedahDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(bedahObj.TBedahDetil_Diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(bedahObj.TBedahDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(bedahObj.TBedahDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(bedahObj.TBedahDetil_Jumlah);
				totPribadi 	+= parseFloat(bedahObj.TBedahDetil_Pribadi);
				totAsuransi += parseFloat(bedahObj.TBedahDetil_Asuransi);
				totDisc 	+= parseFloat(bedahObj.TBedahDetil_Diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Tindakan Medis Inap Berdasarkan Admisi Inap ====================
function cInapTransByAdmisi(noreg){

	$.get('/ajax-inaptransbyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, tmsObj){

				isi += '<tr class="even pointer">';
				isi += '<td>'+tmsObj.TransTanggal+'</td>';
				isi += '<td style="text-align:left;">'+tmsObj.TTarifInap_Nama+'</td>';
				isi += '<td>'+formatRibuan(tmsObj.TransBanyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransTarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransJumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransDiskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransPribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransAsuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(tmsObj.TransJumlah);
				totPribadi 	+= parseFloat(tmsObj.TransPribadi);
				totAsuransi += parseFloat(tmsObj.TransAsuransi);
				totDisc 	+= parseFloat(tmsObj.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Kamar Tindakan Inap Berdasarkan Admisi Inap ====================
function cKamarTindByAdmisi(noreg){

	$.get('/ajax-kamartindbyadmisi?inapnoadmisi='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, tmsObj){

				isi += '<tr class="even pointer">';
				isi += '<td>'+tmsObj.TransTanggal+'</td>';
				isi += '<td style="text-align:left;">'+tmsObj.TTarifInap_Nama+'</td>';
				isi += '<td>'+formatRibuan(tmsObj.TransBanyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransTarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransJumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransDiskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransPribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(tmsObj.TransAsuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(tmsObj.TransJumlah);
				totPribadi 	+= parseFloat(tmsObj.TransPribadi);
				totAsuransi += parseFloat(tmsObj.TransAsuransi);
				totDisc 	+= parseFloat(tmsObj.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Tindakan Medis Inap Berdasarkan Admisi Inap ====================
function cBersalinByAdmisi(noreg){

	$.get('/ajax-kamarbersalinbyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, irbObj){

				var diskon = irbObj.TIRBDetil_Diskon + irbObj.TIRBDetil_DiskonRS + irbObj.TIRBDetil_DiskonDokter;

				isi += '<tr class="even pointer">';
				isi += '<td>'+irbObj.TIRB_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+irbObj.TTarifIRB_Nama+'</td>';
				isi += '<td>'+formatRibuan(irbObj.TIRBDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(irbObj.TIRBDetil_Tarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(irbObj.TIRBDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(irbObj.TIRBDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(irbObj.TIRBDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(irbObj.TIRBDetil_Jumlah);
				totPribadi 	+= parseFloat(irbObj.TIRBDetil_Pribadi);
				totAsuransi += parseFloat(irbObj.TIRBDetil_Asuransi);
				totDisc 	+= parseFloat(diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Lab Inap Berdasarkan Admisi Inap ====================
function cLabByAdmisi(noreg){

	$.get('/ajax-labbyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, labObj){

				var diskon = labObj.TLabDetil_Diskon;

				isi += '<tr class="even pointer">';
				isi += '<td>'+labObj.TLab_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+labObj.TLabDetil_Nama+'</td>';
				isi += '<td>'+formatRibuan(labObj.TLabDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(labObj.TLabDetil_Tarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(labObj.TLabDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(labObj.TLabDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(labObj.TLabDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(labObj.TLabDetil_Jumlah);
				totPribadi 	+= parseFloat(labObj.TLabDetil_Pribadi);
				totAsuransi += parseFloat(labObj.TLabDetil_Asuransi);
				totDisc 	+= parseFloat(diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Lab Inap Berdasarkan Admisi Inap ====================
function cRadByAdmisi(noreg){

	$.get('/ajax-radbyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, radObj){

				var diskon = radObj.TRadDetil_Diskon;

				isi += '<tr class="even pointer">';
				isi += '<td>'+radObj.TRad_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+radObj.TRadDetil_Nama+'</td>';
				isi += '<td>'+formatRibuan(radObj.TRadDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(radObj.TRadDetil_Tarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(radObj.TRadDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(radObj.TRadDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(radObj.TRadDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(radObj.TRadDetil_Jumlah);
				totPribadi 	+= parseFloat(radObj.TRadDetil_Pribadi);
				totAsuransi += parseFloat(radObj.TRadDetil_Asuransi);
				totDisc 	+= parseFloat(diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Rehabilitasi Medik (Fisio) Berdasarkan Admisi Inap ====================
function cFisioByAdmisi(noreg){

	$.get('/ajax-fisiobyadmisisearch?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="150px">Nama Tindakan</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="80px">Harga</th>';
		isi += '<th width="80px">Jumlah</th>';
		isi += '<th width="80px">Potongan</th>';
		isi += '<th width="80px">Bayar Sendiri</th>';
		isi += '<th width="80px">Ditanggung</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, fisioObj){

				var diskon = fisioObj.TFisioDetil_Diskon;

				isi += '<tr class="even pointer">';
				isi += '<td>'+fisioObj.TFisio_Tanggal+'</td>';
				isi += '<td style="text-align:left;">'+fisioObj.TFisioDetil_Nama+'</td>';
				isi += '<td>'+formatRibuan(fisioObj.TFisioDetil_Banyak)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(fisioObj.TFisioDetil_Tarif)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(fisioObj.TFisioDetil_Jumlah)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(diskon)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(fisioObj.TFisioDetil_Pribadi)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(fisioObj.TFisioDetil_Asuransi)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(fisioObj.TFisioDetil_Jumlah);
				totPribadi 	+= parseFloat(fisioObj.TFisioDetil_Pribadi);
				totAsuransi += parseFloat(fisioObj.TFisioDetil_Asuransi);
				totDisc 	+= parseFloat(diskon);

			});
		}else{
			isi += '<tr><td colspan="8" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Ruang Perawatan Berdasarkan Admisi Inap ====================
function cRuangRawatByAdmisi(noreg){

	$.get('/ajax-ruangrawatbyadmisisearch?inapnoadmisi='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="150px">Kamar</th>';
		isi += '<th width="125px">Tgl Masuk</th>';
		isi += '<th width="125px">Tgl Keluar</th>';
		isi += '<th width="75px">Lama</th>';
		isi += '<th width="100px">Tarif Kamar</th>';
		isi += '<th width="100px">Jumlah</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;

		if(data.length > 0){
			$.each(data, function(index, kmrObj){

				var diskon = kmrObj.TFisioDetil_Diskon;

				isi += '<tr class="even pointer">';
				isi += '<td>'+kmrObj.TTmpTidur_Nama+'</td>';
				isi += '<td>'+kmrObj.MutasiTgl+'</td>';
				isi += '<td>'+kmrObj.SmpDenganTgl+'</td>';
				isi += '<td>'+formatRibuan(kmrObj.LamaInap)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(kmrObj.TTmpTidur_Harga)+'</td>';
				isi += '<td style="text-align:right;">'+formatRibuan(kmrObj.Jumlah)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(kmrObj.Jumlah);

			});
		}else{
			isi += '<tr><td colspan="6" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// ============================ Search Transaksi Rawat Inap =============================
function cDaftarPasienInap(keyword){

	$.get('/ajax-trawatinapbysearch?keyword='+keyword, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="50px">Status</th>';
		isi += '<th width="100px">Nomor Admisi</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '<th width="100px">Ruang</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			NomorAdmisiInap = data.TRawatInap_NoAdmisi;

			var lamainap = 0;
			var msPerDay = 8.64e7;

			var tglmasuk 	= new Date(data.TRawatInap_TglMasuk);
			var tglSkg 		= new Date();

			lamainap = Math.round((tglSkg - tglmasuk) / msPerDay);

			arrTempTrans.push({
				status 			: data.Status,
		        noTrans 		: data.TRawatInap_NoAdmisi,
		        nomornota 		: data.TRawatInap_NomorNota,
		        tglmasuk 		: data.TRawatInap_TglMasuk,
		        tglkeluar 		: data.TRawatInap_TglKeluar,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        nomorkuitansi 	: data.TKasir_Nomor,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(data.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(data.TRawatInap_UmurThn),
		        alamat 			: data.TPasien_Alamat,
		        kota 			: data.TWilayah2_Nama,
		        unitKode 		: data.TTmpTidur_Kode,
		        unitNama 		: data.TTmpTidur_Nama,
		        ruangNama		: data.TRuang_Nama,
		        ruang 			: data.TTmpTidur_Nama,
		        ruangkode 		: data.TTmpTidur_Kode,
		        kelas 			: data.TTmpTidur_KelasKode, 
		        kelasNama		: data.TKelas_Keterangan, 
		        jenisPasien 	: 'I', 
		        statusbayar 	: data.TRawatInap_StatusBayar,
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        dokterJenis 	: data.TPelaku_Jenis,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: lamainap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.Status+'</td>';
			isi += '<td>'+data.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+data.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '<td>'+data.TTmpTidur_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// ============================ Search Transaksi Rawat Inap Yang Sudah Bayar =============================
function cRawatInapBayar(keyword){
	$.get('/ajax-trawatinapstatusbyr?keyword='+keyword, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Nomor Admisi</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '<th width="100px">Ruang</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			NomorAdmisiInap = data.TRawatInap_NoAdmisi;

			var lamainap = 0;
			var msPerDay = 8.64e7;

			var tglmasuk 	= new Date(data.TRawatInap_TglMasuk);
			var tglSkg 		= new Date();

			lamainap = Math.round((tglSkg - tglmasuk) / msPerDay);

			arrTempTrans.push({
				status 			: data.Status,
		        noTrans 		: data.TRawatInap_NoAdmisi,
		        nomornota 		: data.TRawatInap_NomorNota,
		        tglmasuk 		: data.TRawatInap_TglMasuk,
		        tglkeluar 		: data.TRawatInap_TglKeluar,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        nomorkuitansi 	: data.TKasir_Nomor,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(data.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(data.TRawatInap_UmurThn),
		        sisatagihan 	: parseFloat(data.TRawatInap_Piutang),
		        alamat 			: data.TPasien_Alamat,
		        kota 			: data.TWilayah2_Nama,
		        unitKode 		: data.TTmpTidur_Kode,
		        unitNama 		: data.TTmpTidur_Nama,
		        ruangNama		: data.TRuang_Nama,
		        ruang 			: data.TTmpTidur_Nama,
		        ruangkode 		: data.TTmpTidur_Kode,
		        kelas 			: data.TTmpTidur_KelasKode, 
		        kelasNama		: data.TKelas_Keterangan, 
		        jenisPasien 	: 'I', 
		        statusbayar 	: data.TRawatInap_StatusBayar,
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        dokterJenis 	: data.TPelaku_Jenis,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: lamainap, 
		        kasirpiutang 	: data.TKasir_TagPiutang
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+data.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '<td>'+data.TTmpTidur_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}


// ============================ Search Daftar Rawat Inap yang sudah Verifikasi =============================
function cDaftarPasienInapVerifikasi(key, jenis, tgl1, tgl2){

	$.get('/ajax-trawatinapisverif?key='+key+'&jenis='+jenis+'&tgl1='+tgl1+'&tgl2='+tgl2, function(data){
		var isi = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="50px">Status</th>';
		isi += '<th width="100px">Nomor Admisi</th>';
		isi += '<th width="100px">Tanggal</th>';
		isi += '<th width="75px">Nomor RM</th>';
		isi += '<th width="150px">Nama Pasien</th>';
		isi += '<th width="100px">Ruang</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTempTrans = [];

		$.each(data, function(index, data){

			NomorAdmisiInap = data.TRawatInap_NoAdmisi;

			var lamainap = 0;
			var msPerDay = 8.64e7;

			var tglmasuk 	= new Date(data.TRawatInap_TglMasuk);
			var tglSkg 		= new Date();

			lamainap = Math.round((tglSkg - tglmasuk) / msPerDay);

			arrTempTrans.push({
				status 			: data.Status,
		        noTrans 		: data.TRawatInap_NoAdmisi,
		        nomornota 		: data.TRawatInap_NomorNota,
		        tglmasuk 		: data.TRawatInap_TglMasuk,
		        tglkeluar 		: data.TRawatInap_TglKeluar,
		        noRM 			: data.TPasien_NomorRM,
		        pasienNama 		: data.TPasien_Nama, 
		        pasienAlamat 	: data.TPasien_Alamat,
		        pasienKota 		: data.TWilayah2_Nama,
		        nomorkuitansi 	: data.TKasir_Nomor,
		        jmltrans 		: data.TRawatInap_Jumlah,
		        jkKode 			: data.TAdmVar_Gender,
		        jk 				: data.TAdmVar_Gender,
		        umurHari 		: parseInt(data.TRawatInap_UmurHr), 
		        umurBulan 		: parseInt(data.TRawatInap_UmurBln),
		        umurTahun 		: parseInt(data.TRawatInap_UmurThn),
		        alamat 			: data.TPasien_Alamat,
		        kota 			: data.TWilayah2_Nama,
		        unitKode 		: data.TTmpTidur_Kode,
		        unitNama 		: data.TTmpTidur_Nama,
		        ruang 			: data.TTmpTidur_Nama,
		        ruangkode 		: data.TTmpTidur_Kode,
		        kelas 			: data.TTmpTidur_KelasKode, 
		        jenisPasien 	: 'I', 
		        statusbayar 	: data.TRawatInap_StatusBayar,
		        dokterKode 		: data.TPelaku_Kode,
		        dokterNama 		: data.TPelaku_NamaLengkap,
		        dokterJenis 	: data.TPelaku_Jenis,
		        penjaminJenis 	: data.TAdmVar_Nama,
		        penjaminKode 	: data.TPerusahaan_Kode,
		        penjaminNama 	: data.TPerusahaan_Nama,
		        lamainap 		: lamainap
		      });

			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarjalan" name="daftarjalan" onclick="sendArrTrans('+i+')"></td>';
			isi += '<td>'+data.Status+'</td>';
			isi += '<td>'+data.TRawatInap_NoAdmisi+'</td>';
			isi += '<td>'+data.TRawatInap_TglMasuk+'</td>';
			isi += '<td>'+data.TPasien_NomorRM+'</td>';
			isi += '<td style="text-align:left;">'+data.TPasien_Nama+'</td>';
			isi += '<td>'+data.TTmpTidur_Nama+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}


// === Daftar Inap ===
function cTarifInap(keyword, kdtarif, kelas, kdpelaku){
	$.get('/ajax-tarifinapsearch?keyword='+keyword+'&kdtarif='+kdtarif, function(data){
		var isi 	= '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="200px">Tarif Nama</th>';
		isi += '<th width="150px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifInap = [];

		$.each(data, function(index, obj){

			var tarif = 0;

			arrTarifInap.push({
		        tarifkode 		: obj.TTarifInap_Kode,
		        tarifvarkode 	: obj.TTarifVar_Kode,
		        tarifvarkel 	: obj.TTarifVar_Kelompok,
		        tarifnama 		: obj.TTarifInap_Nama,
		        dokterptvip		: parseFloat(obj.TTarifInap_DokterPTVIP).toFixed(2),
		        dokterftvip		: parseFloat(obj.TTarifInap_DokterFTVIP).toFixed(2),
		        rsptvip			: parseFloat(obj.TTarifInap_RSPTVIP).toFixed(2),
		        rsftvip			: parseFloat(obj.TTarifInap_RSFTVIP).toFixed(2),
		        tarifvip		: parseFloat(obj.TTarifInap_VIP).toFixed(2),

		        dokterptutama	: parseFloat(obj.TTarifInap_DokterPTUtama).toFixed(2),
		        dokterftutama	: parseFloat(obj.TTarifInap_DokterFTUtama).toFixed(2),
		        rsptutama		: parseFloat(obj.TTarifInap_RSPTUtama).toFixed(2),
		        rsftutama		: parseFloat(obj.TTarifInap_RSFTUtama).toFixed(2),
		        tarifutama		: parseFloat(obj.TTarifInap_Utama).toFixed(2),

		        dokterptkelas1	: parseFloat(obj.TTarifInap_DokterPTKelas1).toFixed(2),
		        dokterftkelas1	: parseFloat(obj.TTarifInap_DokterFTKelas1).toFixed(2),
		        rsptkelas1		: parseFloat(obj.TTarifInap_RSPTKelas1).toFixed(2),
		        rsftkelas1		: parseFloat(obj.TTarifInap_RSFTKelas1).toFixed(2),
		        tarifkelas1		: parseFloat(obj.TTarifInap_Kelas1).toFixed(2),

		        dokterptkelas2	: parseFloat(obj.TTarifInap_DokterPTKelas2).toFixed(2),
		        dokterftkelas2	: parseFloat(obj.TTarifInap_DokterFTKelas2).toFixed(2),
		        rsptkelas2		: parseFloat(obj.TTarifInap_RSPTKelas2).toFixed(2),
		        rsftkelas2		: parseFloat(obj.TTarifInap_RSFTKelas2).toFixed(2),
		        tarifkelas2		: parseFloat(obj.TTarifInap_Kelas2).toFixed(2),

		        dokterptkelas3	: parseFloat(obj.TTarifInap_DokterPTKelas3).toFixed(2),
		        dokterftkelas3	: parseFloat(obj.TTarifInap_DokterFTKelas3).toFixed(2),
		        rsptkelas3		: parseFloat(obj.TTarifInap_RSPTKelas3).toFixed(2),
		        rsftkelas3		: parseFloat(obj.TTarifInap_RSFTKelas3).toFixed(2),
		        tarifkelas3		: parseFloat(obj.TTarifInap_Kelas3).toFixed(2)
		        
		      });

			tarif = ((kelas == '10') ? parseFloat(obj.TTarifInap_Kelas1) : (kelas == '20') ? parseFloat(obj.TTarifInap_Kelas2) : (kelas == '30') ? parseFloat(obj.TTarifInap_Kelas3) : (kelas == 'UI') ? parseFloat(obj.TTarifInap_VIP).toFixed(2) : (kelas == 'VII') ? parseFloat(obj.TTarifInap_Utama) : parseFloat(obj.TTarifInap_Kelas1));


			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarinap" name="daftarinap" onclick="sendArrInap('+i+')"></td>';
			isi += '<td>'+obj.TTarifInap_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obj.TTarifInap_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(tarif)+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// =========== Cari Data Tindakan Medis Berdasarkan Admisi Inap ====================
function cTindMedisByAdmisi(noreg){

	$.get('/ajax-gettindakanmedis?inapnoadmisi='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="125px">Tanggal</th>';
		isi += '<th width="125px">Tindakan</th>';
		isi += '<th width="125px">Dokter</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="100px">Tarif</th>';
		isi += '<th width="100px">Potongan</th>';
		isi += '<th width="100px">Jumlah</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, data){

				var diskon = data.TFisioDetil_Diskon;

				isi += '<tr class="even pointer">';
				isi 	+= '<td>'+data.TransTanggal+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TTarifInap_Nama+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TPelaku_NamaLengkap+'</td>';
				isi 	+= '<td style="text-align:center;">'+formatRibuan(data.TransBanyak)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransTarif)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransDiskon)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransJumlah)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(data.TransJumlah);
				totPribadi 	+= parseFloat(data.TransPribadi);
				totAsuransi += parseFloat(data.TransAsuransi);
				totDisc 	+= parseFloat(data.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="6" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// =========== Cari Data Visite Dokter Berdasarkan Admisi Inap ====================
function cVisiteDokterByAdmisi(noreg){

	$.get('/ajax-getvisitedokterbyadmisi?noadmisi='+noreg, function(data){
		var isi 	= '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="125px">Tanggal</th>';
		isi += '<th width="125px">Dokter</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="100px">Tarif</th>';
		isi += '<th width="100px">Potongan</th>';
		isi += '<th width="100px">Jumlah</th>';
		isi += '<th width="150px">Keterangan</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){

			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';
				isi 	+= '<td>'+data.TransTanggal+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TPelaku_NamaLengkap+'</td>';
				isi 	+= '<td style="text-align:center;">'+formatRibuan(data.TransBanyak)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransTarif)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransDiskon)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransJumlah)+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TransKeterangan+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(data.TransJumlah);
				totPribadi 	+= parseFloat(data.TransPribadi);
				totAsuransi += parseFloat(data.TransAsuransi);
				totDisc 	+= parseFloat(data.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="7" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================


// =========== Cari Data Diagnostik Inap Berdasarkan Admisi Inap ====================
function cDiagnostikByAdmisi(noreg){

	$.get('/ajax-getdiagnostikinapbyadmisi?inapnoadmisi='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="125px">Tanggal</th>';
		isi += '<th width="125px">Tindakan</th>';
		isi += '<th width="125px">Dokter</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="100px">Tarif</th>';
		isi += '<th width="100px">Potongan</th>';
		isi += '<th width="100px">Jumlah</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';
				isi 	+= '<td>'+data.TransTanggal+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TTarifInap_Nama+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TPelaku_NamaLengkap+'</td>';
				isi 	+= '<td style="text-align:center;">'+formatRibuan(data.TransBanyak)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransTarif)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransDiskon)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransJumlah)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(data.TransJumlah);
				totPribadi 	+= parseFloat(data.TransPribadi);
				totAsuransi += parseFloat(data.TransAsuransi);
				totDisc 	+= parseFloat(data.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="6" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

// === Daftar Tarif Lain sesuai Kode Pelaku dan Kelas ===
function cTarifLainSearchAll(keyword, kdtarif, kelas, kdpelaku){
	$.get('/ajax-tariflainsearchnonKode?keyword='+keyword, function(data){
		var isi 	= '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="30px"></th>';
		isi += '<th width="100px">Tarif Kode</th>';
		isi += '<th width="200px">Tarif Nama</th>';
		isi += '<th width="150px">Tarif</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i = 0;

		arrTarifLain = [];

		$.each(data, function(index, obj){

			var tarif = 0;

			arrTarifLain.push({
		        tarifkode 		: obj.TTarifLain_Kode,
		        tarifvarkode 	: obj.TTarifVar_Kode,
		        tarifvarkel 	: 'LAIN',
		        tarifnama 		: obj.TTarifLain_Nama,
		        dokterptvip		: 0,
		        dokterftvip		: 0,
		        rsptvip			: parseFloat(obj.TTarifLain_VIP).toFixed(2),
		        rsftvip			: parseFloat(obj.TTarifLain_VIP).toFixed(2),
		        tarifvip		: parseFloat(obj.TTarifLain_VIP).toFixed(2),

		        dokterptutama	: 0,
		        dokterftutama	: 0,
		        rsptutama		: parseFloat(obj.TTarifLain_Utama).toFixed(2),
		        rsftutama		: parseFloat(obj.TTarifLain_Utama).toFixed(2),
		        tarifutama		: parseFloat(obj.TTarifLain_Utama).toFixed(2),

		        dokterptkelas1	: 0,
		        dokterftkelas1	: 0,
		        rsptkelas1		: parseFloat(obj.TTarifLain_Kelas1).toFixed(2),
		        rsftkelas1		: parseFloat(obj.TTarifLain_Kelas1).toFixed(2),
		        tarifkelas1		: parseFloat(obj.TTarifLain_Kelas1).toFixed(2),

		        dokterptkelas2	: 0,
		        dokterftkelas2	: 0,
		        rsptkelas2		: parseFloat(obj.TTarifLain_Kelas2).toFixed(2),
		        rsftkelas2		: parseFloat(obj.TTarifLain_Kelas2).toFixed(2),
		        tarifkelas2		: parseFloat(obj.TTarifLain_Kelas2).toFixed(2),

		        dokterptkelas3	: 0,
		        dokterftkelas3	: 0,
		        rsptkelas3		: parseFloat(obj.TTarifLain_Kelas3).toFixed(2),
		        rsftkelas3		: parseFloat(obj.TTarifLain_Kelas3).toFixed(2),
		        tarifkelas3		: parseFloat(obj.TTarifLain_Kelas3).toFixed(2)
		        
		      });

			tarif = ((kelas == '10') ? parseFloat(obj.TTarifLain_Kelas1) : (kelas == '20') ? parseFloat(obj.TTarifLain_Kelas2) : (kelas == '30') ? parseFloat(obj.TTarifLain_Kelas3) : (kelas == 'UI') ? parseFloat(obj.TTarifLain_VIP).toFixed(2) : (kelas == 'VII') ? parseFloat(obj.TTarifLain_Utama) : parseFloat(obj.TTarifLain_Kelas1));


			isi += '<tr class="even pointer">';
			isi += '<td><input type="radio" id="daftarLain" name="daftarLain" onclick="sendArrLain('+i+')"></td>';
			isi += '<td>'+obj.TTarifLain_Kode+'</td>';
			isi += '<td style="text-align:left;">'+obj.TTarifLain_Nama+'</td>';
			isi += '<td style="text-align:right;">'+formatRibuan(tarif)+'</td>';
			isi += '</tr>';

			i++;
		});

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('hasil').innerHTML = isi;

	});

}

// =========== Cari Data Transaksi Lain-lain Inap Berdasarkan No Admisi ====================
function cLainLainInapByAdmisi(noreg){

	$.get('/ajax-gettranslaininapbyadmisi?inapnoadmisi='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
		isi += '<thead>';
		isi += '<tr>';
		isi += '<th width="125px">Tanggal</th>';
		isi += '<th width="125px">Tindakan</th>';
		isi += '<th width="125px">Dokter</th>';
		isi += '<th width="75px">Banyak</th>';
		isi += '<th width="100px">Tarif</th>';
		isi += '<th width="100px">Potongan</th>';
		isi += '<th width="100px">Jumlah</th>';
		isi += '</tr>';
		isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var total 		= 0;
		var totPribadi 	= 0;
		var totAsuransi = 0;
		var totDisc 	= 0;

		if(data.length > 0){
			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';
				isi 	+= '<td>'+data.TransTanggal+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TTarifInap_Nama+'</td>';
				isi 	+= '<td style="text-align:left;">'+data.TPelaku_NamaLengkap+'</td>';
				isi 	+= '<td style="text-align:center;">'+formatRibuan(data.TransBanyak)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransTarif)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransDiskon)+'</td>';
				isi 	+= '<td style="text-align:right;">'+formatRibuan(data.TransJumlah)+'</td>';
				isi += '</tr>';

				i++;

				total 		+= parseFloat(data.TransJumlah);
				totPribadi 	+= parseFloat(data.TransPribadi);
				totAsuransi += parseFloat(data.TransAsuransi);
				totDisc 	+= parseFloat(data.TransDiskon);

			});
		}else{
			isi += '<tr><td colspan="6" style="text-aling:center;"><i>Tidak Ada Data Transaksi Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('total1').value = formatRibuan(total);
		document.getElementById('total2').value = formatRibuan(totPribadi);
		document.getElementById('total3').value = formatRibuan(totAsuransi);
		document.getElementById('total4').value = formatRibuan(totDisc);

		document.getElementById('rinciHasil').innerHTML = isi;
	});
}
//===================================================================================

//=========== pencarian data transaksi saat kas/bank entry ===========================
function cTransModul(text,jenistr, jenis, tgl1, tgl2){
	$.get('/ajax-kasinputsearch?tgl1='+tgl1+'&tgl2='+tgl2+'&jenistr='+jenistr, function(data){
			var isi = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Nomor</th>';
			isi += '<th width="100%">Keterangan</th>';
			isi += '<th width="100px">Jumlah</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';
			var i = 0;
			arrItem= [];

			if (data.length>0) {
				$.each(data, function(index, inv){
					arrItem.push({
				        tanggal 	: inv.TKas_Nomor,
				        keterangan 	: inv.TKas_Nama      	
				      });

					isi += '<tr class="even pointer">';
					isi += '<td><input type="checkbox" name="cbitem" id="cbitem" onclick="sendArr('+i+')"></td>';
					isi += '<td>'+inv.TKas_Nomor+'</td>';
					isi += '<td style="text-align:left;">'+inv.TKas_Nama+'</td>';
					isi += '<td style="text-align:right;">'+formatRibuan(inv.TKas_Jumlah)+'</td>';
					isi += '</tr>';
					i++;
				});
			} else {
				 isi += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
			}
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;
		});
}
//====================== pencarian penjamin untuk search di create invoice =======================
function cariPenjamin(text){
	$.get('/ajax-penjamin?key='+text, function(data){
			var isi = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Kode</th>';
			isi += '<th width="100%">Nama</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';
			var i = 0;
			arrPenjamin= [];

			if (data.length>0) {
				$.each(data, function(index, penjamin){
					arrPenjamin.push({
				        kodepenjamin 	: penjamin.TPerusahaan_Kode,
				        namapenjamin 	: penjamin.TPerusahaan_Nama,
				        coa 			: penjamin.TPerkiraan_Kode,
				        namacoa 		: penjamin.TPerkiraan_Nama
				      });

					isi += '<tr class="even pointer">';
					isi += '<td><input type="radio" name="rbitem" id="rbitem" onclick="sendArr('+i+')"></td>';
					isi += '<td>'+penjamin.TPerusahaan_Kode+'</td>';
					isi += '<td style="text-align:left;">'+penjamin.TPerusahaan_Nama+'</td>';
					isi += '</tr>';
					i++;
				});
			} else {
				 isi += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
			}
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;
		});
}

//===== cari detail invoice atas penjamin ==============================
function cariInvoice(penjamin, tgl1, tgl2, key){
	$.get('/ajax-invsearch?penjamin='+penjamin+'&tgl1='+tgl1+'&tgl2='+tgl2+'&key='+key, function(data){
			var isi = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">No. Reg</th>';
			isi += '<th width="100%">Nama Pasien</th>';
			isi += '<th width="100px">Jumlah</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';
			var i = 0;
			arrItemInv = [];

			if (data.length>0) {
				$.each(data, function(index, inv){
					arrItemInv.push({
						no_reg 			: inv.no_reg,
						pasien_nama 	: inv.pasien_nama,
						pasien_nomor_rm : inv.pasien_nomor_rm,
						piutang_bayar 	: inv.piutang_bayar,
						piutang_jenis 	: inv.piutang_jenis,
						piutang_jumlah 	: inv.piutang_jumlah,
						piutang_nomor 	: inv.piutang_nomor,
						piutang_tanggal : inv.piutang_tanggal,
						prsh_kode 		: inv.prsh_kode 	
				      });

					isi += '<tr class="even pointer">';
					isi += '<td><input type="checkbox" name="cbitem'+i+'" id="cbitem'+i+'" onchange="sendArrTempItem(this.id, '+i+', \''+inv.piutang_nomor+'\')"></td>';
					isi += '<td>'+inv.no_reg+'</td>';
					isi += '<td style="text-align:left;">'+inv.pasien_nama+'</td>';
					isi += '<td style="text-align:right;">'+formatRibuan(inv.piutang_jumlah)+'</td>';
					isi += '</tr>';
					i++;
				});
			} else {
				 isi += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
			}
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;
		});
}

// =========== Cari Hasil Lab dari Referensi Dokter ====================
function cHasilLabRefDok(noreg){

	$.get('/ajax-getHasilLabRefDok?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
				isi += '<tr>';
					isi += '<th width="100%px">Lab Nama</th>';
					isi += '<th width="200px">Periksa Nama</th>';
					isi += '<th width="100px">Hasil</th>';
					isi += '<th width="125px">Nilai Normal</th>';
					isi += '<th width="100px">Satuan</th>';
					isi += '<th width="125px">Keterangan</th>';
				isi += '</tr>';
			isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;
		var templabkode = '';

		if(data.length > 0){
			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';

					if(i > 0 && data.TLab_Kode == templabkode){
						isi 	+= '<td></td>';
					}else{
						isi 	+= '<td><b>'+data.TLabDetil_Nama+'</b></td>';
					}

					isi 	+= '<td style="text-align:left;">'+data.TLabPeriksa_Nama+'</td>';
					isi 	+= '<td>'+data.TLabHasil_Hasil+'</td>';
					isi 	+= '<td style="text-align:left;">'+data.TLabHasil_HargaNorm+'</td>';
					isi 	+= '<td>'+data.TLabHasil_Satuan+'</td>';
					isi 	+= '<td style="text-align:left;">'+data.TLabHasil_Keterangan+'</td>';
				isi += '</tr>';

				templabkode = data.TLab_Kode;

				i++;

			});
		}else{
			isi += '<tr><td colspan="6" style="text-aling:center;"><i>Hasil Lab Tidak Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('bodyHasil').innerHTML 		= isi;
	});
}
//===================================================================================

// =========== Cari Hasil Lab dari Referensi Dokter ====================
function cHasilRadRefDok(noreg){

	$.get('/ajax-getHasilRadRefDok?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
				isi += '<tr>';
					isi += '<th width="200px">Pemeriksaan Radiologi</th>';
					isi += '<th width="100%">Baca Hasil</th>';
				isi += '</tr>';
			isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;

		if(data.length > 0){
			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';
					isi 	+= '<td style="text-align:left;">'+data.TRadDetil_Nama+'</td>';
					isi 	+= '<td style="text-align:left;"><textarea class="form-control" rows="7" style="resize:none;">'+data.TRadDetil_Hasil+'</textarea></td>';
				isi += '</tr>';

				i++;

			});
		}else{
			isi += '<tr><td colspan="2" style="text-aling:center;"><i>Hasil Radiologi Tidak Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('bodyHasil').innerHTML 		= isi;
	});
}
//===================================================================================

// =========== Cari Pembarian Obat Pasien dari Referensi Dokter ====================
function cHasilApotekRefDok(noreg){

	$.get('/ajax-getHasilApotekRefDok?noreg='+noreg, function(data){
		var isi = '';
		var nomorrm = '';

		isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
				isi += '<tr>';
					isi += '<th width="100px">Kode Obat</th>';
					isi += '<th width="100%">Nama Obat</th>';
					isi += '<th width="75px">Banyak</th>';
					isi += '<th width="100px">Satuan</th>';
				isi += '</tr>';
			isi += '</thead>';
		isi += '<tbody>';

		var i 			= 0;

		if(data.length > 0){
			$.each(data, function(index, data){

				isi += '<tr class="even pointer">';
					isi 	+= '<td>'+data.TObat_Kode+'</td>';
					isi 	+= '<td style="text-align:left;">'+data.TObat_Nama+'</td>';
					isi 	+= '<td>'+data.TObatKmrDetil_Banyak+'</td>';
					isi 	+= '<td>'+data.TObatKmrDetil_Satuan+'</td>';
				isi += '</tr>';

				i++;

			});
		}else{
			isi += '<tr><td colspan="4" style="text-aling:center;"><i>Hasil Radiologi Tidak Ditemukan</i></td></tr>';
		}

		isi += '</tbody>';
		isi += '</table>';

		document.getElementById('bodyHasil').innerHTML 		= isi;
	});
}
//===================================================================================

//====================== pencarian dokter  untuk jasa dokter =======================
function cariPelaku(text){
	$.get('/ajax-getdatapelaku?key='+text, function(data){
			var isi = '';

			isi += '<table id="datatable1" class="responstable">';
			isi += '<thead>';
			isi += '<tr>';
			isi += '<th width="30px"></th>';
			isi += '<th width="100px">Kode Dokter</th>';
			isi += '<th width="100%">Nama Dokter</th>';
			isi += '</tr>';
			isi += '</thead>';
			isi += '<tbody>';
			var i = 0;
			arrJasaDokter= [];

			if (data.length>0) {
				$.each(data, function(index, dokter){
					arrJasaDokter.push({
				        kodedokter 		: dokter.TPelaku_Kode,
				        namadokter 		: dokter.TPelaku_Nama,
				        coa 			: dokter.TPerkiraan_Kode,
				        namacoa 		: dokter.TPerkiraan_Nama,
				        pelakujasa 		: dokter.TPelaku_Jasa,
						pelakujasa2 	: dokter.TPelaku_Jasa2,
						pelakujasa3 	: dokter.TPelaku_Jasa3,
						pelakujasakhusus: dokter.TPelaku_JasaKhusus,
						pelakutunjket 	: dokter.TPelaku_TunjKet,
						pelakutunjjumlah: dokter.TPelaku_TunjJumlah,
						pelakujenis 	: dokter.TPelaku_Jenis,
						pelakujenisnama : dokter.PelakuJenisNama,
				      });

					isi += '<tr class="even pointer">';
					isi += '<td><input type="radio" name="rbitem" id="rbitem" onclick="sendArr('+i+')"></td>';
					isi += '<td>'+dokter.TPelaku_Kode+'</td>';
					isi += '<td style="text-align:left;">'+dokter.TPelaku_Nama+'</td>';
					isi += '</tr>';
					i++;
				});
			} else {
				 isi += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
			}
					
			isi += '</tbody>';
			isi += '</table>';
			document.getElementById('hasil').innerHTML = isi;
		});
}

