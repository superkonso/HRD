@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | daftarinap')

@section('content_header', 'Laporan Pendaftaran Pasien Inap')

@section('header_description', '')

@section('menu_desc', 'Daftarinap')

@section('link_menu_desc', '/daftarinap')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<div class="row">
  <form action="ctklapdaftarinap" method="post" id="formdaftarinap" data-parsley-validate >
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
                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey3" id="searchkey3" class="form-control pull-right" placeholder="Nomor Admisi / Nomor RM / Nama Pasien">
                </div>

                  <select name="Status" id="Status" class="form-control">
                   @foreach($admvars as $admvar)
                      <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                   @endforeach
                  </select>

                   <select name="Ruang" id="Ruang" class="form-control">
                   @foreach($ruangs as $ruang)
                      <option value="{{$ruang->TAdmVar_Kode}}">{{$ruang->TAdmVar_Nama}}</option>
                   @endforeach
                  </select>

                   <select name="Kelas" id="Kelas" class="form-control">
                   @foreach($kelas as $kls)
                      <option value="{{$kls->TAdmVar_Kode}}">{{$kls->TAdmVar_Nama}}</option>
                   @endforeach
                  </select>

                   <div class="input-group">
                  <button type="button" onclick="changestatus();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
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
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

    <script>

    function changestatus(kelas){
     var status = $('#Status').val();
     var kelas =  $('#Kelas').val();
     var ruang =  $('#Ruang').val();

         if(status=='1'){
            key4 ='0';
          }else if(status=='2'){
             key4 ='1';
          }else{
             key4 ='';
          }
         
       refreshData(key4,kelas,ruang);
    }
    
      $(function () {
        $('#searchkey1, #searchkey2').datepicker({
            autoclose: true
          })
              .on('changeDate', function(en) {
                changestatus();
               });

          });

      $('#searchkey2').on('keyup', function(e){
          changestatus();
                });

       $('#searchkey3').on('keyup', function(e){
          changestatus();
                 });

         $('#Kelas').on('change', function(e){
         changestatus();
      });

           $('#Ruang').on('change', function(e){
         changestatus();
      });

      $( document ).ready(function() {

        $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
              $("#successAlert").slideUp(500);
            });  

             changestatus();
         
      });

       $('#Status').on('change', function(e){
        changestatus();
            
      });

        function refreshData(key4,kelas,ruang){
          var isiData = '';

          var key1  = $('#searchkey1').val();
          var key2  = $('#searchkey2').val();
          var key3  = $('#searchkey3').val();
          var key4  = key4;
          var key5  = ruang;
          var key6  = kelas;
          
          isiData += '<table class="responstable">';

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
            }else{

              isiData += '<tr><td colspan="13"><i>Tidak ada Data Ditemukan</i></td></tr>';
              isiData += '<table>';
              document.getElementById('tablebody1').innerHTML = isiData;
            }     
          });
        }

</script> 
    
@endsection