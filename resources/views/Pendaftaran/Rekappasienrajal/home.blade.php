@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Laporan Rekap Kunjungan Kamar Bersalin')

@section('content_header', 'Laporan Rekap Pasien Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<div class="row">
  <form action="/ctklaprekapjalan" method="post" id="formrekap" data-parsley-validate >
     {{csrf_field()}}
  
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
      <div class="box-header">          
      </div>
          <div class="box-body">
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

                  <select name="Daftar" id="Daftar" class="form-control">
                   @foreach($admvars as $admvar)
                      <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                   @endforeach
                  </select>
                  
                <div class="input-group">
                  <button type="button" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
                    <div class="form-group pull-right">
                    <button type="submit" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>
                      </div>
                </div>
              </div>
              <div style="overflow-x: scroll;">
              <span id="tablebody1"></span>
              </div>
          </div> <!--div class="box-body"-->
    </div> <!--div class="box box-primary"-->
  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</form>

</div> <!--div class="row"-->

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
  $(function () {
          $('#searchkey1, #searchkey2').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
            refreshData();
          });
      });

 $('#searchkey3').on('keyup', function(e){
         refreshData();
  });

  $('#Daftar').on('change', function(e){
         refreshData();
  });

  $( document ).ready(function() {

    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  
         refreshData();
  });


function refreshData(kd){ 
      var isiData = '';
      var Nama = '';
      var key1  = $('#searchkey1').val();
      var key2  = $('#searchkey2').val();
      var key3  = $('#Daftar').val();

      isiData += '<table class="responstable">';
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
       
           $.each(data, function(index, listdftdObj){
            n         = i;
            i++;
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
        }else{

          isiData += '<tr><td colspan="16"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
    }


</script> 

@endsection