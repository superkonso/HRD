@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Kartu Pasien')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">

  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
          <div class="box-body">
              <div class="form-group">
                   <div class="" style="text-align: center;">
                   <div style="" id="key1">
                <span id="tablebody1"></span>
              </div>
    </div>
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

     var key1  = '{{$id}}';

      $.get('/ajax-cetakpasien?key='+key1, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpasien){
            isiData += '<tr>'
                  +'<br><font size="7"> <td style="text-align:center;">'+listpasien.TPasien_NomorRM+'</td></br></font>'
                  +'<br><font size="7"><td style="text-align:center;">'+listpasien.TPasien_Nama+'</td></br></font>'
               
                      +'</tr>';
          });

         document.getElementById('tablebody1').innerHTML = isiData;
        window.print();
        window.window.location.href="/pasien";

       
        }       
      });
    }

    </script> 
    
@endsection