@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Data Semua Pasien')

@section('content_header', 'Data Pasien')

@section('header_description', '')

@section('menu_desc', 'Batal Daftar Pasien')

@section('link_menu_desc', '/bataldaftarpasien')

@section('sub_menu_desc', 'View')

@section('content')

  @include('Partials.message')

  <form class="form-horizontal form-label-left" action="/transobatugd" method="post" id="formtransobat" name="formtransobat"<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div class="box-header">
          		@if(Session::has('flash_message'))
		    		<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>

	        <div class="box-body">
	        	<a href="/pasien/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar Pasien Baru</a>
	        </div>
	           
		  <a href="#formsearch" id="btnPoli" onclick="cariPasien('RP','POLI');" class="btn btn-danger" data-toggle="modal"
	      style="margin:0px 2px 2px 0px;"><img src="{!! asset('images/icon/dokter-icon.png') !!}" width="20px" height="20px"> Daftar Poli</a>

	      <a href="#formsearch" id="btnUGD" onclick="cariPasien('RD','UGD');" class="btn btn-danger" data-toggle="modal" style="margin:0px 2px 2px 0px;"><img src="{!! asset('images/icon/dokter-icon.png') !!}" width="20px" height="20px"> Daftar UGD</a>

	      <a href="#formsearch" id="btnInap" onclick="cariPasien('RI','Rawat Inap');" class="btn btn-danger" data-toggle="modal" style="margin:0px 2px 2px 0px;"><img src="{!! asset('images/icon/dokter-icon.png') !!}" width="20px" height="20px"> Daftar Inap</a>

	         
            {{-- <hr> --}}
            <div class="divscroll">
                <span id="tablebody1">
                  <table class="responstable">
                    <tr>
                      <th width="100px">No Reg</th>
                      <th width="200px">No RM</th>
                      <th width="100px">Nama Pasien</th>
                      <th width="100px">Tanggal Lahir</th>
                      <th width="100px">Alamat</th>
                      <th width="100px">Telpon</th>
                      <th width="100px">Penjamin</th>
                      <th width="100px">Action</th>
                      </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      </tr>
                  </table>
            	</span>
           </div> {{-- <div class="col-md-12 col-sm-12 col-xs-12"> --}}
        </div> {{-- <div class="box-body"> --}}
    </div> {{-- <div class="box-primary"> --}}
  <!-- <div class="row"> -->

<input type="hidden" name="arrItem" id="arrItem" value="">
  </form>
<!-- JQuery 1 -->
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


  @include('Partials.errors')

  <script type="text/javascript">
  		 var arrDaftarPas  = [];
  		 var arrItem       = [];
  		 var indP = 0;
  		 var arrPas       = [];
  		 var indKon = 0;

  		$( document ).ready(function() {
			$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
	          $("#successAlert").slideUp(500);
	      	});  
	      	refreshData();
		});

	  function cariPasien(kd, txtheader){

	  document.getElementById('hasil').innerHTML = '';

	  document.getElementById('searchmodal_Title').innerHTML ='DAFTAR PASIEN '+txtheader;

      document.getElementById('searchmodal_Logo').innerHTML = '<img src="{!! asset('images/menu/aprj-icon.png') !!}" width="20" height="20">';

        document.getElementById('searchmodal_Textsearch').innerHTML = '<input type="text" id="keypas" class="form-control pull-right" onkeyup="cDaftarPasienAll( \''+kd+'\')" placeholder="Nomor Reg / Nama Pasien">';

      document.getElementById('searchmodal_Btnpilih').innerHTML = '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="choosePasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
       
        cDaftarPasienAll(kd);
    }

	$(function () {
          $('#searchkey1').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
	          refreshData();
	        });
      });

	$('#searchkey1').on('keyup', function(e){
		refreshData();
	});

	$('#searchkey2').on('keyup', function(e){
		refreshData();
	});

	  function sendArrDft(ind){
      indP = ind;
    }

     function sendArrTempPas(ind){
     indKon = ind;
    }

	function choosePasien(){
		   		arrItem.push({
		   		   ugd_noreg: arrDaftarPasien[indKon]['ugd_noreg'],
		           pasien_norm: arrDaftarPasien[indKon]['pasien_norm'],
		      	   pasien_nama: arrDaftarPasien[indKon]['pasien_nama'],
		   	       tgllahir: arrDaftarPasien[indKon]['tgllahir'],
		       	   pasien_alamat:arrDaftarPasien[indKon]['pasien_alamat'],
		           telp: arrDaftarPasien[indKon]['telp'],
		           prsh: arrDaftarPasien[indKon]['prsh'],
                });
               refreshData();
               }


	 function delItem(indKon){
      arrItem.splice(indKon, 1);
      refreshData();  
    }

    function KlikDel(kd,nama)
	{
	    var confrm = confirm("Yakin Akan Membatalkan Pendaftaran Pasien Atas Nama " + nama );

	    if(confrm==true)
			{
			$.get('/ajax-Updatestatuspas?kd='+kd, function(data){
				alert("Pembatalan Berhasil");
			});
			  return true;
			}
			else
			{
			return false;
			}
	  }

	function refreshData(){
      var refreshtable1 = '';
      refreshtable1 += '<table class="responstable">'
                    +'<tr>'
                      +'<th width="25px"></th>'
                      +'<th width="100px">No Reg</th>'
                      +'<th width="50px">No Rekam Medis</th>'
                      +'<th width="150px">Nama Pasien</th>'
                      +'<th width="70px">Tanggal Lahir</th>'
                      +'<th width="130px">Alamat</th>'
                      +'<th width="100px">Telepon</th>'
                      +'<th width="100px">Penjamin</th>'
                      +'<th width="100px">Action</th>'
                      +'</tr>';
    	var i = 0;

        $.each(arrItem, function (index, value) {
        	    	     refreshtable1 += '<tr>'
        	    	      +'<td><img src="{!! asset('images/icon/delete-icon.png') !!}" width="15" height="15" onclick="delItem('+i+');" title="Delete Item"></td>'
          				    +'<td>'+value.ugd_noreg+'</td>'
                            +'<td>'+value.pasien_norm+'</td>'
                            +'<td>'+value.pasien_nama+'</td>'
                            +'<td>'+value.tgllahir+'</td>'
                            +'<td>'+value.pasien_alamat+'</td>'
                            +'<td>'+value.telp+'</td>'
                            +'<td>'+value.prsh+'</td>'
                             +'<td>'
                             	// +'<a href="/transobatugd/'+value.ugd_noreg+'/edit">'
                             			
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" onclick="KlikDel( \''+value.ugd_noreg+'\',\''+value.pasien_nama+'\')" title="Edit">'
									+'</a>'
								+'</td>'
                            +'</tr>';
          i++;
   });
     
   

      document.getElementById('tablebody1').innerHTML = refreshtable1;
       refreshtable1 += '</table>';
}
</script>
@endsection