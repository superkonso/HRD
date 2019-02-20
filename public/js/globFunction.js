function hitungUmur(tgllahir)
{

	var now 		= new Date();
	var tgllahir 	= new Date(tgllahir);
	var age 		= {};
	

	var nowYear 	= now.getFullYear();
	var nowMonth 	= now.getMonth() + 1;
	var nowDate 	= now.getDate();

	var lhrYear		= tgllahir.getFullYear();
	var lhrMonth	= tgllahir.getMonth() + 1;
	var lhrDate 	= tgllahir.getDate();

	var yearAge 	= nowYear - lhrYear;

	if (nowMonth >= lhrMonth)
	  var monthAge = nowMonth - lhrMonth;
	else {
	  yearAge--;
	  var monthAge = 12 + nowMonth - lhrMonth;
	}

	if (nowDate >= lhrDate)
	  var dateAge = nowDate - lhrDate;
	else {
	  monthAge--;
	  var dateAge = 31 + nowDate - lhrDate;

	  if (monthAge < 0) {
	    monthAge = 11;
	    yearAge--;
	  }
	}

	age = {
		    years: yearAge,
		    months: monthAge,
		    days: dateAge
		    };

	return age;
}

function hitungUmurInsertForm(idtgl, idumurthn, idumurbln, idumurhari)
{
		var now 		  = new Date();
		var tgllahir 	= new Date(document.getElementById(idtgl).value);		

		var nowYear 	= now.getFullYear();
		var nowMonth 	= now.getMonth() + 1;
		var nowDate 	= now.getDate();

		var lhrYear		= tgllahir.getFullYear();
		var lhrMonth	= tgllahir.getMonth() + 1;
		var lhrDate 	= tgllahir.getDate();

		var yearAge 	= nowYear - lhrYear;

		if (nowMonth >= lhrMonth)
		  var monthAge = nowMonth - lhrMonth;
		else {
		  yearAge--;
		  var monthAge = 12 + nowMonth - lhrMonth;
		}

		if (nowDate >= lhrDate)
		  var dateAge = nowDate - lhrDate;
		else {
		  monthAge--;
		  var dateAge = 31 + nowDate - lhrDate;

		  if (monthAge < 0) {
		    monthAge = 11;
		    yearAge--;
		  }
		}

    document.getElementById(idumurthn).value  = yearAge;
    document.getElementById(idumurbln).value  = monthAge;
    document.getElementById(idumurhari).value = dateAge;

}

function resetForm()
{
  $('form').get(0).reset();
}

function formatRibuan(angka){
	var angkaR = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	return angkaR;
}

function changeFormat(id, number){
  var numberFormat = 0.0;

  numberFormat = parseFloat($('#'+id).val().toString().replace(/,/g, ''));

  if(isNaN(numberFormat) || numberFormat == ''){
      numberFormat = 0;
  }else{

  }

  $('#'+id).val(formatRibuan(numberFormat));
}


// ================================ Auto Wilayah =======================================================================
	function getKota(kota, kec, kel){
		var kdProv 	= $('#provinsi').val();
        kdProv 		= kdProv.substr(0,2);

        $.get('/ajax-getkota?kdprov='+kdProv, function(data){

          $('#kota').empty();

          $.each(data, function(index, kotaObj){

          	if(kotaObj.TWilayah2_Kode == kota){
          		$('#kota').append('<option value="'+kotaObj.TWilayah2_Kode+'" selected="selected">'+kotaObj.TWilayah2_Nama+'</option>');
          	}else{
          		$('#kota').append('<option value="'+kotaObj.TWilayah2_Kode+'">'+kotaObj.TWilayah2_Nama+'</option>');
          	}

          });

          getKecamatan(kec, kel);

        });
	}

	function getKecamatan(kecamatan, kelurahan){
		var kdkota 	= $('#kota').val();
        kdkota 		= kdkota.substr(0,4);

        $.get('/ajax-getkecamatan?kdKec='+kdkota, function(data){

          $('#kecamatan').empty();

          $.each(data, function(index, kecamatanObj){
          	if(kecamatanObj.TWilayah2_Kode == kecamatan){
          		$('#kecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'" selected="selected">'+kecamatanObj.TWilayah2_Nama+'</option>');
          	}else{
          		$('#kecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'">'+kecamatanObj.TWilayah2_Nama+'</option>');
          	}
          });

          getKelurahan(kelurahan);

        });

	}

	function getKelurahan(kdkel){
	        var kdkec 	= $('#kecamatan').val();
	        kdkec 		= kdkec.substr(0,6);

	        $.get('/ajax-getkelurahan?kdkec='+kdkec, function(data){

	          $('#kelurahan').empty();

	          $.each(data, function(index, kelurahanObj){
	          	if(kelurahanObj.TWilayah2_Kode == kdkel){
	          		$('#kelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'" selected="selected">'+kelurahanObj.TWilayah2_Nama+'</option>');
	          	}else{
	          		$('#kelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'">'+kelurahanObj.TWilayah2_Nama+'</option>');
	          	}
	          });

	        });

	}


      $('#provinsi').on('change', function(e){
        var kdProv 	= $('#provinsi').val();
        kdProv 		= kdProv.substr(0,2);

        $.get('/ajax-getkota?kdprov='+kdProv, function(data){

          $('#kota').empty();
          $('#kecamatan').empty();
          $('#kelurahan').empty();

          $.each(data, function(index, kotaObj){
            $('#kota').append('<option value="'+kotaObj.TWilayah2_Kode+'">'+kotaObj.TWilayah2_Nama+'</option>');
          });

        });

      });

      $('#kota').on('change', function(e){
        var kdKec 	= $('#kota').val();
        kdKec 		= kdKec.substr(0,4);

        $.get('/ajax-getkecamatan?kdKec='+kdKec, function(data){

          $('#kecamatan').empty();
          $('#kelurahan').empty();

          $.each(data, function(index, kecamatanObj){
            $('#kecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'">'+kecamatanObj.TWilayah2_Nama+'</option>');
          });

        });

      });

      $('#kecamatan').on('change', function(e){
        var kdkec 	= $('#kecamatan').val();
        kdkec 		= kdkec.substr(0,6);

        $.get('/ajax-getkelurahan?kdkec='+kdkec, function(data){

          $('#kelurahan').empty();

          $.each(data, function(index, kelurahanObj){
            $('#kelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'">'+kelurahanObj.TWilayah2_Nama+'</option>');
          });

        });

      });

    // ============================= change combo wilayah (modal) ================== 

    function getKota2(kota, kec, kel){
		var kdProv 	= $('#mprovinsi').val();
        kdProv 		= kdProv.substr(0,2);

        $.get('/ajax-getkota?kdprov='+kdProv, function(data){

          $('#mkota').empty();

          $.each(data, function(index, kotaObj){

          	if(kotaObj.TWilayah2_Kode == kota){
          		$('#mkota').append('<option value="'+kotaObj.TWilayah2_Kode+'" selected="selected">'+kotaObj.TWilayah2_Nama+'</option>');
          	}else{
          		$('#mkota').append('<option value="'+kotaObj.TWilayah2_Kode+'">'+kotaObj.TWilayah2_Nama+'</option>');
          	}

          });

          getKecamatan2(kec, kel);

        });
	}

	function getKecamatan2(kecamatan, kelurahan){
		var kdkota 	= $('#mkota').val();
        kdkota 		= kdkota.substr(0,4);

        $.get('/ajax-getkecamatan?kdKec='+kdkota, function(data){

          $('#mkecamatan').empty();

          $.each(data, function(index, kecamatanObj){
          	if(kecamatanObj.TWilayah2_Kode == kecamatan){
          		$('#mkecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'" selected="selected">'+kecamatanObj.TWilayah2_Nama+'</option>');
          	}else{
          		$('#mkecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'">'+kecamatanObj.TWilayah2_Nama+'</option>');
          	}
          });

          getKelurahan2(kelurahan);

        });

	}

	function getKelurahan2(kdkel){
	        var kdkec 	= $('#mkecamatan').val();
	        kdkec 		= kdkec.substr(0,6);

	        $.get('/ajax-getkelurahan?kdkec='+kdkec, function(data){

	          $('#mkelurahan').empty();

	          $.each(data, function(index, kelurahanObj){
	          	if(kelurahanObj.TWilayah2_Kode == kdkel){
	          		$('#mkelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'" selected="selected">'+kelurahanObj.TWilayah2_Nama+'</option>');
	          	}else{
	          		$('#mkelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'">'+kelurahanObj.TWilayah2_Nama+'</option>');
	          	}
	          });

	        });

	}


      $('#mprovinsi').on('change', function(e){
        var kdProv 	= $('#mprovinsi').val();
        kdProv 		= kdProv.substr(0,2);

        $.get('/ajax-getkota?kdprov='+kdProv, function(data){

          $('#mkota').empty();
          $('#mkecamatan').empty();
          $('#mkelurahan').empty();

          $.each(data, function(index, kotaObj){
            $('#mkota').append('<option value="'+kotaObj.TWilayah2_Kode+'">'+kotaObj.TWilayah2_Nama+'</option>');
          });

        });

      });

      $('#mkota').on('change', function(e){
        var kdKec 	= $('#mkota').val();
        kdKec 		= kdKec.substr(0,4);

        $.get('/ajax-getkecamatan?kdKec='+kdKec, function(data){

          $('#mkecamatan').empty();
          $('#mkelurahan').empty();

          $.each(data, function(index, kecamatanObj){
            $('#mkecamatan').append('<option value="'+kecamatanObj.TWilayah2_Kode+'">'+kecamatanObj.TWilayah2_Nama+'</option>');
          });

        });

      });

      $('#mkecamatan').on('change', function(e){
        var kdkec 	= $('#mkecamatan').val();
        kdkec 		= kdkec.substr(0,6);

        $.get('/ajax-getkelurahan?kdkec='+kdkec, function(data){

          $('#mkelurahan').empty();

          $.each(data, function(index, kelurahanObj){
            $('#mkelurahan').append('<option value="'+kelurahanObj.TWilayah2_Kode+'">'+kelurahanObj.TWilayah2_Nama+'</option>');
          });

        });

      });

    // ===================================================================================================


    // ======================== Format Angka Ribuan ============================

      function formatRibuan(numb) {
        var angka = numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return angka;
      }

    // =========================================================================
    //=========== untuk update pilihan coa ketika jurnal terima logistik
    function getcoajurnallog(perlu){
        $.get('/ajax-getcoajlog?perlu='+perlu, function(data){

          $('#perkterima').empty();

          $.each(data, function(index, coa){
            $('#perkterima').append('<option value="'+coa.TPerkiraan_Kode+'" selected="selected">'+coa.TPerkiraan_Kode+' - '+coa.TPerkiraan_Nama+'</option>');     
          });

        });
      }


