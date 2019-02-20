@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Data  Pasien')

@section('content_header', 'Data Informasi Pasien Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Batal Daftar Pasien')

@section('link_menu_desc', '/daftarpasien')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

  <form class="form-horizontal form-label-left" action="/poli" method="post" id="formtransobat" name="formtransobat"<div class="row">
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
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Awal" value="<?php echo date('m/d/Y'); ?>">
                </div>
             
                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey2" id="searchkey2" class="form-control pull-right" placeholder="Tanggal Akhir" value="<?php echo date('m/d/Y'); ?>">
                </div>

                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey3" id="searchkey3" class="form-control pull-right" placeholder="No Registrasi / Nomor RM / Nama Pasien">
                </div>

                <select name="Daftar" id="Daftar" class="form-control">
                   @foreach($admvars as $admvar)
                      <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                   @endforeach
                </select>

            <div class="input-group">
                  <button type="button" onclick="changeData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
             </div>
            <div class="form-group" >
              <label class="control-label col-md-1 col-sm-1 col-xs-1">Pribadi:</label>
              <div class="col-md-1 col-sm-1 col-xs-1">
                <input type="text" name="pribadi" id="pribadi" class="form-control col-md-5 col-xs-8" value=""  onchange="statuspasien();">
              </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-1">Kerjasama:</label>
              <div class="col-md-1 col-sm-1 col-xs-1">
                <input type="text" name="kerjasama" id="kerjasama" class="form-control col-md-5 col-xs-8" value="" onchange="statuspasien();">
              </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-1">Jumlah:</label>
              <div class="col-md-1 col-sm-1 col-xs-1">
                <input type="text" name="JumlahS" id="JumlahS" class="form-control col-md-5 col-xs-8" value="" onchange="statuspasien();">
              </div>
            </div>
          

           {{-- <hr> --}}
            <div class="divscroll">
                <span id="tablebody1">
                  <table class="responstable">
                    <tr>
                      <th width="800px">Status</th>
                      <th width="100px"> No RM</th>
                      <th width="100px">Nama Pasien</th>
                      <th width="100px">No Registrasi</th>
                      <th width="100px">Tanggal Kunjungan</th>
                      <th width="100px">Jam Daftar</th>
                      <th width="100px">Unit Dituju</th>
                      <th width="100px">Nama Dokter</th>
                      <th width="120px">Nama Kerjasama</th>
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
                      <td></td>
                      </tr>
                  </table>
            	</span>
           </div> {{-- <div class="col-md-12 col-sm-12 col-xs-12"> --}}
        </div> {{-- <div class="box-body"> --}}
    </div> {{-- <div class="box-primary"> --}}
  <div class="row">

<input type="hidden" name="arrItem" id="arrItem" value="">
  </form>
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

  function changeData(){
     var status = $('#Daftar').val();
      if(status == '1'){
        kd='0';
      }else if(status == '2'){
        kd='1';
      }else if(status == '3'){
        kd='9';
      }
      else{
        kd='';
       }
        refreshData(kd);
        statuspasien(kd);
       }  

      $(function () {
          $('#searchkey1, #searchkey2').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
         changeData();
          });
      });

      $('#searchkey3').on('keyup', function(e){
         changeData();
  });

    $('#Daftar').on('change', function(e){
         changeData();
  });

    $( document ).ready(function() {
        $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
              $("#successAlert").slideUp(500);
          }); 
      changeData();
      // refreshData();
    });

	  function sendArrDft(ind){
      indP = ind;
    }

     function sendArrTempPas(ind){
     indKon = ind;
    }

    function statuspasien(kd){   

      var key1  = $('#searchkey1').val();
      var key2  = $('#searchkey2').val();  

       $.get('/ajax-getpribadiinfopasienrajal?key1='+key1+'&key2='+key2+'&key3='+kd, function(data){
              $('#pribadi').val(data);  
                });

        $.get('/ajax-getprshinfopasienrajal?key1='+key1+'&key2='+key2+'&key3='+kd, function(data){
              $('#kerjasama').val(data);
              });
     
         $.get('/ajax-getjlhprshinfopasienrajal?key1='+key1+'&key2='+key2+'&key3='+kd, function(data){
              $('#JumlahS').val(data);
              });

        }


	 function delItem(indKon){
      arrItem.splice(indKon, 1);
      changeData();  
    }

	function refreshData(kd){
      var refreshtable1 = '';
      var key1  = $('#searchkey1').val();
      var key2  = $('#searchkey2').val();
      var key3  = $('#searchkey3').val();

      refreshtable1 += '<table class="responstable">'
                    +'<tr>'
                      +'<th width="80px">Status</th>'
                      +'<th width="100px">No Rekam Medis</th>'
                      +'<th width="100px">Nama Pasien</th>'
                      +'<th width="100px">No Registrasi</th>'
                      +'<th width="100px">Tanggal Kunjungan</th>'
                      +'<th width="100x">Jam Daftar</th>'
                      +'<th width="100px">Alamat Pasien</th>'
                      +'<th width="100px">Unit Dituju</th>'
                      +'<th width="100px">Nama Dokter</th>'
                      +'<th width="120px">Nama Kerjasama</th>'
                      +'</tr>';
         
     $.get('/ajax-getinfopasienrajal?key1='+key1+'&key2='+key2+'&key3='+key3+'&key4='+kd, function(data){
      
        if(data.length > 0){
           var nomor   ='';
           var prsh    ='';

             $.each(data, function(index, listpasObj){
                if(nomor == listpasObj.TPasien_NomorRM){
                   prsh    ='';
                   nsub++;

                 }else{
                   prsh    =listpasObj.TPerusahaan_Nama;
                    nsub      = 1; 

                  if(nomor == listpasObj.TPasien_NomorRM) i++;
                  
                 }

                  refreshtable1 += '<tr>'        
                      +'<td width="100px">'+listpasObj.status+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_NomorRM+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_Nama+'</td>'
                      +'<td width="100px">'+listpasObj.TRawatJalan_NoReg+'</td>'
                      +'<td width="100px">'+listpasObj.tanggal+'</td>'
                      +'<td width="100px">'+listpasObj.jam+'</td>'
                      +'<td width="100px">'+listpasObj.TPasien_Alamat+'</td>'
                      +'<td width="100px">'+listpasObj.TUnit_Nama+'</td>'
                      +'<td width="100px">'+listpasObj.TPelaku_Nama+'</td>'
                      +'<td width="100px">'+prsh+'</td>'
                      });
                  
                 refreshtable1 += '</tr>';
            
            document.getElementById('tablebody1').innerHTML = refreshtable1;
            refreshtable1 += '</table>';
        }else{
          refreshtable1 += '<tr><td colspan="10"><i>Tidak ada Data Ditemukan</i></td></tr>';
          refreshtable1 += '<table>';
          document.getElementById('tablebody1').innerHTML = refreshtable1;
        } // endif data.length 
      }); //End ajax
    }   //end function    
      
</script>
@endsection