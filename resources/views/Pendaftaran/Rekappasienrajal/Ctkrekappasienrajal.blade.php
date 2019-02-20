@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Rekap pasien Rawat Jalan')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">

                <div class="" style="text-align: center;">
                  <h3>ANALISA REKAP PASIEN RAWAT JALAN  </h3>
                </div>
                
                <div class="" style="text-align: center;">
                  PERIODE  <?php echo $key1 ?> S/D <?php echo $key2 ?>
                </div>
              </div>

              <div style="" id="searchkey2">
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

function refreshData(kd){ 
      var isiData = '';

     var key1  = '{{$key1}}';
     var key2  = '{{$key2}}';
     var key3  = '{{$key3}}';

      isiData += '<table class="tablereport-font11">';

      if(key3=='1'){
        isiData += '<colgroup>'
              +'<col style="width:100px;">'
              +'<col style="width:150%;">'
              +'<col style="width:50%;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
            +'</colgroup>';

            isiData += '<tr>'
              +'<th rowspan="2">No</th>'
              +'<th rowspan="2">Dokter</th>'
              +'<th rowspan="2">Jumlah</th>'
              +'<th colspan="2">Jenis Kunjungan</th>'
              +'<th colspan="2">Jenis Pelayanan</th>'
              +'<th colspan="9">Umur Pasien</th>'
              +'</tr>'

              +'<tr>'
              +'<th>Pria</th>'
              +'<th>Wanita</th>'
              +'<th>Lama</th>'
              +'<th>Baru</th>'
              +'<th>0-6hr</th>'
              +'<th>7-28th</th>'
              +'<th>28-1th</th>'
              +'<th>1-4th</th>'
              +'<th>5-14th</th>'
              +'<th>15-24th</th>'
              +'<th>25-44th</th>'
              +'<th>45-64th</th>'
              +'<th> >64th</th>'
              +'</tr>';
          }else{

          isiData += '<colgroup>'
              +'<col style="width:100px;">'
              +'<col style="width:150%;">'
              +'<col style="width:50%;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
              +'<col style="width:50px;">'
            +'</colgroup>';

            isiData += '<tr>'
              +'<th rowspan="2">No</th>'
              +'<th rowspan="2">Unit</th>'
              +'<th rowspan="2">Jumlah</th>'
              +'<th colspan="2">Jenis Kunjungan</th>'
              +'<th colspan="2">Jenis Pelayanan</th>'
              +'<th colspan="9">Umur Pasien</th>'
              +'</tr>'

              +'<tr>'
              +'<th>Pria</th>'
              +'<th>Wanita</th>'
              +'<th>Lama</th>'
              +'<th>Baru</th>'
              +'<th>0-6hr</th>'
              +'<th>7-28th</th>'
              +'<th>28-1th</th>'
              +'<th>1-4th</th>'
              +'<th>5-14th</th>'
              +'<th>15-24th</th>'
              +'<th>25-44th</th>'
              +'<th>45-64th</th>'
              +'<th> >64th</th>'
              +'</tr>';
          }

    $.get('/ajax-getrekappasienrajal?key1='+key1+'&key2='+key2+'&key3='+key3, function(data){
      if(data.length > 0){

        var i = 1;
        var nomor   ='';
        var tgltrans  ='';
        var noreg     ='';
        var norm      ='';
        var nmpas     ='';
        var Pria    ='';
        var Wanita    ='';
        var Jumlah    ='';

        $.each(data, function(index, listdftdObj){
            n         = i;
            i++;
            Pria   = listdftdObj.Pria;
            Wanita = listdftdObj.Wanita;
            Jumlah = (Pria)+(Wanita);

               isiData += '<tr>'
                  +'<td style="text-align:left;">'+'<center>'+n+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+listdftdObj.Nama+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.jumlah+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.pria+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.wanita+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.lama+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.baru+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur1+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur2+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur3+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur4+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur5+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur6+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur7+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur8+'</center>'+'</td>'
                  +'<td style="text-align:left;">'+'<center>'+listdftdObj.umur9+'</center>'+'</td>'
                    +'</tr>';
              });


          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        window.print();
        window.window.location.href="laprekapjalan";
        }else{

          isiData += '<tr><td colspan="16"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
    }


    </script> 
    
@endsection