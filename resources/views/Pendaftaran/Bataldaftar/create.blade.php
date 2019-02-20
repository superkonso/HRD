@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Data Semua Pasien')

@section('content_header', 'Data Pasien')

@section('header_description', '')

@section('menu_desc', 'Batal Daftar Pasien')

@section('link_menu_desc', '/bataldaftarpasien')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
      <div class="box-header">
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
              @endif
       </div>

	        <div class="input-group">
             <div class="input-group-addon" style="background-color: #167F92;">
               <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                </div>
                  <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Transaksi" value="<?php echo date('m/d/Y'); ?>">
                </div>

               <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20"> 
                  </div>
                  <input type="text" id="searchkey2" class="form-control pull-right" placeholder="Nomor Reg / Nomor RM / Nama Pasien">
                </div>
        
              <select name="Daftar" id="Daftar" width="20" height="20" class="form-control col-md-5 col-xs-12" onchange="changeData();">
                @foreach($admvars as $admvar)
                    <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                 @endforeach
              </select>
           
              <div class="input-group">
                  <button type="button" onclick="changeData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
             </div>

           {{-- <hr> --}}
            <div class="divscroll">
                <span id="tablebody1">
                  <table class="responstable">
                    <tr>
                      <th width="100px">No Reg</th>
                      <th width="100px"> No RM</th>
                      <th width="100px">Nama Pasien</th>
                      <th width="100px">Tanggal Lahir</th>
                      <th width="100px">Alamat</th>
                      <th width="100px">Telpon</th>
                      <th width="100px">Penjamin</th>
                      <th width="100px">Action</th>
                      </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      </tr>
                  </table>
                  </span>
           </div> {{-- <div class="col-md-12 col-sm-12 col-xs-12"> --}}
        </div> {{-- <div class="box-body"> --}}
    </div> {{-- <div class="box-primary"> --}}
  <div class="row">

<input type="hidden" name="arrItem" id="arrItem" value="">

<!-- JQuery 1 -->
  @include('Partials.modals.searchmodal')

  @include('Partials.alertmodal')
  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

   <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

   <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>


  @include('Partials.errors')

  <script type="text/javascript">
  		 var arrDaftarPas  = [];
  		 var arrItem       = [];
  		 var indP = 0;
  		 var arrPas       = [];
  		 var indKon = 0;

  	$( document ).ready(function() {
  			$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
  	          $("#successAlert").slideUp(500);
  	      }); 
      changeData();
	    // refreshData();
		});

	  function cariPasien(kd, txtheader){

	  document.getElementById('hasil').innerHTML = '';

	  document.getElementById('searchmodal_Title').innerHTML ='DAFTAR PASIEN '+txtheader;

      document.getElementById('searchmodal_Logo').innerHTML = '<img src="{!! asset('images/menu/aprj-icon.png') !!}" width="20" height="20">';

        document.getElementById('searchmodal_Textsearch').innerHTML = '<input type="text" id="keypas" class="form-control pull-right" onkeyup="cDaftarPasienAll( \''+kd+'\')" placeholder="Nomor Reg / Nama Pasien">';

      document.getElementById('searchmodal_Btnpilih').innerHTML = '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="choosePasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
       
        cDaftarPasienAll(kd);
    }

      $(function () {
          $('#searchkey1').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
            // refreshData();
            changeData();
          });
      });

      $('#searchkey2').on('keyup', function(e){
          changeData();
          // refreshData();
  });

	  function sendArrDft(ind){
      indP = ind;
    }

     function sendArrTempPas(ind){
     indKon = ind;
    }

     function changeData(){
     var status = $('#Daftar').val();
      if(status == '1'){
        kd='RP';
      }else if(status == '2'){
        kd='RD';
      }else if(status == '3'){
        kd='RI';
       }
      refreshData(kd);
    }

	function choosePasien(){
		   		arrItem.push({
		   		    ugd_noreg: arrDaftarPasien[indKon]['ugd_noreg'],
		           pasien_norm: arrDaftarPasien[indKon]['pasien_norm'],
		      	   pasien_nama: arrDaftarPasien[indKon]['pasien_nama'],
		   	       tgllahir: arrDaftarPasien[indKon]['tgllahir'],
		       	   pasien_alamat:arrDaftarPasien[indKon]['pasien_alamat'],
		           telp: arrDaftarPasien[indKon]['telp'],
		           prsh: arrDaftarPasien[indKon]['prsh'],
                });
               refreshData();
               }


	 function delItem(indKon){
      arrItem.splice(indKon, 1);
      refreshData();  
    }

    function KlikDel(kd,nama,status)
	  {
     $.get('/ajax-CekNoReg?kd='+kd, function(data){
        if(data == 0){
              if (status==0) {
                var msgCfrm = document.createElement("span");

                 msgCfrm.innerHTML = '<table>'
                                    + '<tr>'
                                    + '<td colspan="3">Yakin Akan Membatalkan Pendaftaran Pasien a/n : <br><br>'
                                    + '</td>'
                                    + '</tr>'
                                    + '<tr>'
                                    + '<td style="text-align:left">Nomor Registrasi</td>'
                                    + '<td>: </td>'
                                    + '<td style="text-align:left">'+kd+'</td>'
                                    + '</tr>'
                                    + '<tr>'
                                    + '<td style="text-align:left">Nama Pasien</td>'
                                    + '<td>: </td>'
                                    + '<td style="text-align:left">'+nama+'</td>'
                                  + '</tr>'
              swal({
                title     : 'Batal Daftar Pasien',
                content   : msgCfrm,
                icon      : "warning",
                buttons   : true,
                dangerMode: true,
              })
              .then((willDelete) => {
                if (willDelete) {
                var stts = '9';
                $.get('/ajax-Updatestatuspas?kd='+kd+'&key='+stts, function(data){
                   showSuccess(2000, 'Sukses',"Pembatalan Berhasil",true);
                   changeData(); 
                   return true;
                });
              } else {
            
              }
            });
              }else if (status == 9) {
                var msgCfrm = document.createElement("span");
                msgCfrm.innerHTML = '<table>'
                                        + '<tr>'
                                        + '<td colspan="3">Apakah Akan Mengaktifkan Kembali Pasien a/n : <br><br>'
                                        + '</td>'
                                        + '</tr>'
                                        + '<tr>'
                                        + '<td style="text-align:left">Nomor Registrasi</td>'
                                        + '<td>: </td>'
                                        + '<td style="text-align:left">'+kd+'</td>'
                                        + '</tr>'
                                        + '<tr>'
                                        + '<td style="text-align:left">Nama Pasien</td>'
                                        + '<td>: </td>'
                                        + '<td style="text-align:left">'+nama+'</td>'
                                      + '</tr>'
              swal({
                title     : 'Aktif Kan Kembali Pasien',
                content   : msgCfrm,
                icon      : "warning",
                buttons   : true,
                dangerMode: true,
              })
              .then((willDelete) => {
                if (willDelete) {
                var stts = '0';
                $.get('/ajax-Updatestatuspas?kd='+kd+'&key='+stts, function(data){
                  showSuccess(2000, 'Sukses',"Pasien Kembali Aktif",true);
                  changeData(); 
                  return true;
                });
              } else {  
                        
              }
              });
                 
              }//end status
           
        }else{
           showWarning(2000, '','Pembatalan Tidak Dapat Dilakukan, Pasien sudah Melakukan Transaksi',true);
              return false;
        }//end data
        });//end ajax CekNoReg
      			
	  }

	function refreshData(kd){
      var refreshtable1 = '';
      var key2  = $('#searchkey1').val();
      var key3  = $('#searchkey2').val();

      refreshtable1 += '<table class="responstable">'
                    +'<tr>'
                      +'<th width="100px">No Reg</th>'
                      +'<th width="50px">No Rekam Medis</th>'
                      +'<th width="150px">Nama Pasien</th>'
                      +'<th width="70px">Tanggal Lahir</th>'
                      +'<th width="130px">Alamat</th>'
                      +'<th width="100px">Telepon</th>'
                      +'<th width="100px">Penjamin</th>'
                      +'<th width="125px">Status</th>'
                      +'<th width="100px">Action</th>'
                      +'</tr>';
  
      $.get('/ajax-pasiendaftarAll?key1='+kd+'&key2='+key2+'&key3='+key3, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpasObj){
            
          refreshtable1 += '<tr>'
                      +'<td width="100px">'+listpasObj.noreg+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_NomorRM+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_Nama+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_TglLahir+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_Alamat+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_Telp+'</td>'
                      +'<td width="100px">'+listpasObj.TPerusahaan_Nama+'</td>'
                      +'<td width="100px">'+listpasObj.status2+'</td>'
                      +'<td width="50px">'
                      +'<img src="{!! asset('images/icon/batal-bayar-icon.png') !!}" width="20" height="20" onclick="KlikDel( \''+listpasObj.noreg+'\',\''+listpasObj.TPasien_Nama+'\',\''+listpasObj.status+'\',\''+listpasObj.status+'\')" title=Batal>' 
                      +'</a>'
                      +'</td>'
                      +'</tr>';
                  refreshtable1 += '</tr>';
          });

       document.getElementById('tablebody1').innerHTML = refreshtable1;
       refreshtable1 += '</table>';
        }else{

          refreshtable1 += '<tr><td colspan="9"><i>Tidak ada Data Ditemukan</i></td></tr>';
          refreshtable1 += '<table>';
          document.getElementById('tablebody1').innerHTML = refreshtable1;
        } // endif data.length 
      }); //End ajax
    }   //end function    
      
</script>
@endsection