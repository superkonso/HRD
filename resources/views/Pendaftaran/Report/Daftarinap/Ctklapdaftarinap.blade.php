@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | daftarinap')

@section('content_header', 'Laporan Pendaftaran Pasien Inap')

@section('header_description', '')

@section('menu_desc', 'Daftarinap')

@section('link_menu_desc', '/daftarinap')

@section('sub_menu_desc', 'Ctkdatadaftarinap')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">
                <div class="" style="text-align: center;">
                  <h3>LAPORAN PENDAFTARAN PASIEN INAP  <b>SMART BRIDGE</b> PERIODE</h3>
                </div>
                
                <div class="" style="text-align: center;">
                  <?php echo $searchkey1 ?> sampai  <?php echo $searchkey2 ?> 
                  <div class="" style="text-align: center;">
                
              </div>

              <div style="" id="searchkey2">
                <span id="tablebody1"></span>
              </div>

      </div> <!--div class="box-body"-->

    </div> <!--div class="box box-primary"-->
  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->


<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
 <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

<script>
  $(function () {
          $('#searchkey1').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
            refreshData();
          });

      });

  $('#searchkey2').on('keyup', function(e){
    refreshData();
  });

  $( document ).ready(function() {

    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();

  });

    function refreshData(){
      var isiData = '';

      var key1  = '{{$searchkey1}}';
      var key2  = '{{$searchkey2}}';
      var key3  = '{{$searchkey3}}';
      var keyS  = '{{$searchkey4}}';
      var key5  = '{{$searchkey5}}';
      var key6  = '{{$searchkey6}}';

      var key4 ='';

      if(keyS=='1'){
            key4 ='0';
          }else if(keyS=='2'){
             key4 ='1';
          }else{
             key4 ='';
          }

      isiData += '<table class="tablereport">';

      isiData += '<tr>'
                  +'<th class="column-title" width="12%">Tanggal Masuk</th>'
                  +'<th class="column-title" width="10%">Kamar</th>'
                  +'<th class="column-title" width="18%">Dokter</th>'
                  +'<th class="column-title" width="10%">No Reg</th>'
                  +'<th class="column-title" width="5%">No RM</th>'
                  +'<th class="column-title" width="5%">Status</th>'
                  +'<th class="column-title" width="15%">Nama Pasien</th>'
                  +'<th class="column-title" width="19%">Alamat</th>'
                  +'<th class="column-title" width="6%">Gender</th>'
                  +'<th class="column-title" width="5%">Umur</th>'
                  +'<th class="column-title" width="17%">Penanggung jawab</th>'
                  +'<th class="column-title" width="10%">Penjamin</th>'
                  +'<th class="column-title" width="5%">Kelas</th>'
                +'</tr>';

          $.get('/ajax-getdaftarinap?key1='+key1+'&key2='+key2+'&key3='+key3+'&key4='+key4+'&key5='+key5+'&key6='+key6, function(data){

            if(data.length > 0){
                $.each(data, function(index, listappObj){

                 isiData += '<tr>'
                      +'<td class="" width="10%">'+listappObj.TRawatInap_TglMasuk+'</td>'
                          +'<td class="" >'+listappObj.TRuang_Nama+'</td>'
                          +'<td class="" >'+listappObj.TPelaku_NamaLengkap+'</td>'
                          +'<td class="" >'+listappObj.TRawatInap_NoAdmisi+'</td>'
                          +'<td class="" >'+listappObj.TPasien_NomorRM+'</td>'
                          +'<td class="" >'+listappObj.TRawatInap_PasBaru+'</td>'
                          +'<td class="" >'+listappObj.TPasien_Nama+'</td>'
                          +'<td class="" >'+listappObj.TPasien_Alamat+'</td>'
                          +'<td class="" >'+listappObj.TAdmVar_Gender+'</td>'
                          +'<td class="" >'+listappObj.TRawatInap_UmurThn+'</td>'
                          +'<td class="" >'+listappObj.TPasien_KlgNama+'</td>'
                          +'<td class="" >'+listappObj.TPerusahaan_Nama+'</td>'
                          +'<td class="" >'+listappObj.TKelas_Nama+'</td>'
                      +'</tr>';
              });
          
        isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        window.print();
        window.window.location.href="lapdaftarinap";
        }else{

          isiData += '<tr><td colspan="13"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
    }


</script> 
 @endsection