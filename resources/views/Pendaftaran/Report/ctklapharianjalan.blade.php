@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN HARIAN RAWAT JALAN')

@section('content_header', 'Laporan Harian Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'Ctkdatapoli')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">
                <div class="" style="text-align: center;">
                  <h3>LAPORAN RAWAT JALAN  <b>SMART BRIDGE</b> PERIODE</h3>
                </div>
                
                <div class="" style="text-align: center;">
                  <?php echo $searchkey1 ?>
                </div>
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

      isiData += '<table class="tablereport">';

      isiData += '<tr>'
              +'<th width="20%">No.Trans</th>'
              +'<th width="20%">Tanggal</th>'
              +'<th width="15%">Nomor RM</th>'
              +'<th width="20%">Nama Pasien</th>'
              +'<th width="20%">Unit</th>'
              +'<th width="25%">Dokter</th>'
              +'<th width="20%">Jumlah</th>'
            +'</tr>';

      $.get('/ajax-getpendaftaranpoli?key1='+key1+'&key2='+key2, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpoliObj){
            isiData += '<tr>'
                          +'<td width="20%">'+listpoliObj.TRawatJalan_NoReg+'</td>'
                          +'<td width="20%">'+listpoliObj.TRawatJalan_Tanggal+'</td>'
                          +'<td width="15%">'+listpoliObj.TPasien_NomorRM+'</td>'
                          +'<td width="20%">'+listpoliObj.TPasien_Nama+'</td>'
                          +'<td width="20%">'+listpoliObj.TUnit_Nama+'</td>'
                          +'<td width="25%">'+listpoliObj.TPelaku_NamaLengkap+'</td>'
                          +'<td style="text-align:right;" width="15%">'+listpoliObj.TRawatJalan_Jumlah+'</td>'
                      +'</tr>';
          });

        isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        window.print();
        window.window.location.href="lapdaftarjalan";
        }else{
          isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }     
      });
    }


</script> 
 @endsection