@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN DATA DOKTER')

@section('content_header', 'Dokter')

@section('header_description', 'Data Dokter')

@section('menu_desc', 'Dokter')

@section('link_menu_desc', '/dokter')

@section('sub_menu_desc', 'Ctk Daftar Dokter')

@section('content')

@include('Partials.message')
    
<div class="row">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
                <div style="text-align: center;">
                    <h3>LAPORAN DATA DOKTER <br> <b>SMART BRIDGE</b></h3>
                </div>
            <div class="box-body">
                <div id="searchkey1">
                    <span id="tablebody1"></span>
                </div>
            </div>  
        </div> 
    </div> 
</div> 
<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    $( document ).ready(function() {
        $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();

    });

    $('#searchkey1').on('keyup', function(e){
        refreshData();
    });

    function refreshData(){
        var isiData = '';

        isiData += '<table class="tablereport">';
        isiData += '<thead>'
                        +'<th width="15%">Kode Dokter</th>'
                        +'<th width="25%">Nama Dokter</th>'
                        +'<th width="25%">Alamat</th>'
                        +'<th width="20%">Telepon</th>'
                        +'<th width="15%">Unit</th>'
                    +'</thead>';

        $.get('/ajax-getdatadokterprint', function(data){

            if(data.length > 0){
                $.each(data, function(index, listpelakuObj){
                    isiData += '<tr>'
                                +'<td>'+listpelakuObj.TPelaku_Kode+'</td>'
                                +'<td style="text-align:left;">'+listpelakuObj.TPelaku_NamaLengkap+'</td>'
                                +'<td style="text-align:left;">'+listpelakuObj.TPelaku_Kota+'</td>'
                                +'<td style="text-align:left;">'+listpelakuObj.TPelaku_Telepon+'</td>'
                                +'<td>'+listpelakuObj.TSpesialis_Nama+'</td>'
                            +'</tr>';
                });

                isiData += '</table>';
                document.getElementById('tablebody1').innerHTML = isiData;
                window.print();
                window.window.location.href="dokter"
            }else{

                isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
                isiData += '<table>';
                document.getElementById('tablebody1').innerHTML = isiData;
            }           
        });
        
    }
</script>

@endsection