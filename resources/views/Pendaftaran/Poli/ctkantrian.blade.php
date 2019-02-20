@extends('layouts.print_standar')

@section('content')

<?php 

use SIMRS\Helpers\formattanggal;

date_default_timezone_set("Asia/Bangkok"); 

?>

<table width="100%" style="border:solid 1px black; font-size: 11px;">
    <tr>
        <td colspan="2" style="text-align: center;">SMART BRIDGE</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;">Nomor Urut Poliklinik</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;"><h2>{{$rawatjalans->TRawatJalan_NoUrut}}</h2></td>
    </tr>
    <tr>
        <td width="30%">&nbsp;Nomor RM</td>
        <td width="100%">: {{$rawatjalans->TPasien_NomorRM}}</td>
    </tr>
    <tr>
        <td width="30%">&nbsp;Nama Pasien</td>
        <td width="100%">: {{$rawatjalans->TPasien_Nama}}</td>
    </tr>
    <tr>
        <td width="30%">&nbsp;Dokter</td>
        <td width="100%">: {{$rawatjalans->TPelaku_NamaLengkap}}</td>
    </tr>
    <tr>
        <td width="30%">&nbsp;Unit</td>
        <td width="100%">: {{$rawatjalans->TUnit_Nama}}</td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <tr>
        <td colspan="2" width="100%">&nbsp;Kartu Dicetak pada : {{date('d-m-Y')}} ({{date('H:i:s')}})</td>
    </tr>
</table>

<style type="text/css" media="print">
  @page { 
        size: auto; 
        margin:0;
        width: 80mm;
        height: 80mm;
    }

    @media print {

        html, body{
            width: 80mm;
            height: 80mm;
            display: block;
            margin: 0 auto;
        }
        
     }

     @media screen and projection {
        a {
            display:inline;
            width: 80mm;
            height: 80mm;
        }
      }
</style>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>

    $( document ).ready(function() {

        printPages();

    });

    function printPages(){
        window.print();
        setTimeout(function(){window.location.href="/poli"} , 1000);  
    }

</script>

@endsection


