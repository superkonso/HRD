@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Laporan Informasi Appointment Per Dokter')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">

                <div class="" style="text-align: center;">
                  <h3>INFORMASI APPOINTMENT DOKTER  </h3>
                </div>
                
                <div class="" style="text-align: center;">
                  a/n  <?php echo $key4 ?>
                </div>
              </div>

              <div style="" id="key1">
                <span id="tablebody1"></span>
              </div>
      </div> <!--div class="box-body"-->
    </div> <!--div class="box box-primary"-->
  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->

<style type="text/css" media="print">
  @page { size: landscape; }
</style>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>

  $( document ).ready(function() {
        refreshData();
  });

function refreshData(){ 
     var isiData = '';

     var key1  = '{{$key1}}';
     var key2  = '{{$key2}}';
     var key3  = '{{$key3}}';

     isiData += '<table class="tablereport-font11">';

      isiData += '<tr>'
              +'<th width="17%">No Appointment</th>' 
              +'<th width="10%">RM </th>'
              +'<th width="20%">Nama Pasien </th>'
              +'<th width="10%">Tanggal Janji</th>' 
              +'<th width="8%">Jam Janji </th>'
              +'<th width="5%">Gender </th>'
              +'<th width="15%">Telpon</th>' 
              +'<th width="15%">Tanggal Lahir </th>'
            +'</tr>';

      $.get('/ajax-getappointment?key1='+key1+'&key2='+key2+'&key3='+key3, function(data){
        if(data.length > 0){
          $.each(data, function(index, listpasien){
            isiData += '<tr>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_NoJan+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TPasien_NomorRM+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_Nama+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_TglJanji+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_JamJanji+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_Gender+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TJanjiJalan_PasienTelp+'</td>'
                  +'<td style="text-align:left;">'+listpasien.TPasien_TglLahir+'</td>'
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