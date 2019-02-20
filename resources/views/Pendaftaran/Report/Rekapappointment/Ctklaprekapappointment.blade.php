@extends('layouts.main')
  
@section('title', Auth::User()->cPanel->TCpanel_AppName.' | appointment ')

@section('content_header', 'Laporan Pendaftaran Appointment')

@section('header_description', '')

@section('menu_desc', 'appointment')

@section('link_menu_desc', '/appointment')

@section('sub_menu_desc', 'dataappointment')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">
                <div class="" style="text-align: center;">
                  <h3>LAPORAN PENDAFTARAN APPOINTMENT  <b>SMART BRIDGE</b> PERIODE</h3>
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
                  +'<th class="column-title" width="10%">No.Trans</th>'
                  +'<th class="column-title" width="10%">Nomor RM</th>'
                  +'<th class="column-title" width="15%">Nama Pasien</th>'
                  +'<th class="column-title" width="15%">Unit</th>'
                  +'<th class="column-title" width="20%">Dokter</th>'
                  +'<th class="column-title" width="10%">Jam</th>'
                  +'<th class="column-title" width="20%">Keterangan</th>'
                +'</tr>';

      $.get('/ajax-getdaftarappointment?key1='+key1+'&key2='+key2, function(data){

            if(data.length > 0){
               var Keterangan   ='';
               var nomor   ='';
               var dokter    ='';

              $.each(data, function(index, listappObj){

                 if(nomor== listappObj.TJanjiJalan_NoJan){
                      Keterangan     = '';
                      dokter         ='';
                       nsub++;

                     }else{
                      Keterangan=listappObj.TJanjiJalan_Keterangan;
                      dokter=listappObj.TPelaku_NamaLengkap;
                      nsub      = 1;  

                       if(nomor == listappObj.TJanjiJalan_NoJan) i++;
                     }

                 isiData += '<tr>'
                      +'<td class="" width="10%">'+listappObj.TJanjiJalan_NoJan+'</td>'
                          +'<td class="" >'+listappObj.TPasien_NomorRM+'</td>'
                          +'<td class="" >'+listappObj.TJanjiJalan_Nama+'</td>'
                          +'<td class="" >'+listappObj.TUnit_Nama+'</td>'
                          +'<td class="" >'+listappObj.tpelaku_namalengkap+'</td>'
                          +'<td class="" >'+listappObj.TJanjiJalan_JamJanji+'</td>'
                          +'<td class="" >'+listappObj.tjanjijalan_keterangan+'</td>'
                      +'</tr>';
            
              });
          
        isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        window.print();
        window.window.location.href="lapdaftarappointment";
        }else{

          isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
    }


</script> 
@endsection