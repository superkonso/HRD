@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | User Activity')

@section('content_header', 'USER ACTIVITY')

@section('header_description', '')

@section('menu_desc', 'activity')

@section('link_menu_desc', '/useractivity')

@section('sub_menu_desc', 'User Activity')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

  <div class="row font-medium">
   {{ Form::hidden('Kode', '', array('id' => 'Kode', 'name' => 'Kode')) }}
   {{ Form::hidden('Nama', '', array('id' => 'Nama', 'name' => 'Nama')) }}
  
  <div class="row">
    <!-- <span id="formaction"> -->
  <form class="form-horizontal form-label-left" action="/appointment" method="post" name="formappointment" id="formappointment" data-parsley-validate onsubmit="return">
  <!-- </span> -->
        {{method_field('PUT')}} 
        <!-- Token -->
      {{csrf_field()}}

      <?php date_default_timezone_set("Asia/Bangkok"); ?>
      <!-- ===================================== Data Pasien =========================================== -->
      <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="col-md-4 col-sm-12 col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
                <h3 class="box-title">Daftar User</h3>
              </div>
              <!-- /.box-header -->
        <div class="box-body" >
                  <div class="form-group">
                    <div class="divscroll" >
                      <span id="tablebody1">
                      
                      </span>
                    </div>
                  </div>          
          </div>  {{-- box Body --}}
      </div> {{-- Box Success --}}
    </div> {{--Col 6--}}

        <!-- ===================================== Data Pasien =========================================== -->
    <div class="col-md-8 col-sm-12 col-xs-12">
      <div class="box box-success">
        <div class="box-body">
                     <div class="form-group">
                    <div class="divscroll">
                      <span id="tablebodyhasil">
                  
                      </span>
                    </div>
                  </div>
          </div>  {{-- box Body --}}
      </div> {{-- Box Success --}}
    </div> {{--Col 6--}}
    </div>
  </form>
  </div>

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
  <script type="text/javascript">

      var arrTrans = [];
      var arrTransDetil = [];
      var arrRiwayat = [];
      
      var indTrans  = 0;
      var indTransDetil = 0;
      var indRiwayat = 0;

      var indGrid   = 0;

      $(function () {
          $('#searchkey1').datepicker({
            autoclose: true,
            dateFormat: 'm/d/Y'
          });
          
          $('#searchkey2').datepicker({
            autoclose: true,
            dateFormat: 'm/d/Y'
          });
      });

    $('#searchkey1').on('keyup', function(e){
    refreshData();
    refreshDataHasil();
    });

  $('#searchkey2').on('keyup', function(e){
    refreshData();
    refreshDataHasil();
    });

  $('#kuncicari').on('keyup', function(e){
    refreshData();
    refreshDataHasil();
    });

  $( document ).ready(function() {
    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();
        refreshDataHasil();
      });

  function refreshData(){
      var isiData = '';
      var key   = $('#kuncicari').val();
    
      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th width="35%">ID User </th>'
              +'<th width="55%">Nama User </th>'
            +'</tr>';
  
        $.get('/ajax-getusers?key='+key, function(data){
        if(data.length > 0){
          $.each(data, function(index, listPelaku){
            isiData += '<tr onclick="refreshDataHasil(\''+listPelaku.id+'\');">'
                  +'<td style="text-align:center;">'+listPelaku.id+'</td>'
                      +'<td style="text-align:left;">'+listPelaku.username+'</td>'
                  +'</tr>';
                
          });

        isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="6"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
   }

    function refreshDataHasil(Kd){
      var isiData = '';
      var key    = Kd;

  $.get('/ajax-getlogbook?key='+Kd, function(data){
        if(data.length > 0){
          $.each(data, function(index, listPelaku){
          $('#Nama').val(listPelaku.TLogBook_LogMenuNama);
           $('#Kode').val(listPelaku.TUsers_id);        
            });
            }
      });

      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th width="10%">User ID</th>' 
              +'<th width="10%">Komputer</th>'
              +'<th width="20%">Tanggal : Jam </th>'
              +'<th width="30%">Menu Item</th>' 
              +'<th width="15%">No Bukti </th>'
              +'<th width="35%">Keterangan </th>'
            +'</tr>';

      $.get('/ajax-getlogbook?key='+key, function(data){
        if(data.length > 0){
          $.each(data, function(index, listlogbook){
            isiData += '<tr>'
                  +'<td style="text-align:center;">'+listlogbook.TUsers_id+'</td>'
                  +'<td style="text-align:left;">'+listlogbook.TLogBook_LogIPAddress+'</td>'
                  +'<td style="text-align:left;">'+listlogbook.TLogBook_LogDate+'</td>'
                  +'<td style="text-align:left;">'+listlogbook.TLogBook_LogMenuNama+'</td>'
                  +'<td style="text-align:left;">'+listlogbook.TLogBook_LogNoBukti+'</td>'
                  +'<td style="text-align:left;">'+listlogbook.TLogBook_LogKeterangan+'</td>'
                  +'</tr>';
                      
                });

            isiData += '</table>';
        document.getElementById('tablebodyhasil').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="6"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebodyhasil').innerHTML = isiData;
        }       
      });   
    }
 
  </script>
@endsection