@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | INFO RUANG')

@section('content_header', 'INFO RUANG')

@section('header_description', '')

@section('menu_desc', 'inforuang')

@section('link_menu_desc', '/inforuang')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

	<div class="row">
		<!-- <span id="formaction"> -->
	<form class="form-horizontal form-label-left" method="post" name="formruang" id="formruang" data-parsley-validate >
	<!-- </span> -->
      	{{method_field('PUT')}} 
	      <!-- Token -->
    	{{csrf_field()}}

      <?php date_default_timezone_set("Asia/Bangkok"); ?>

		<div class="form-group col-md-12 col-sm-12 col-xs-12">
	    <div class="box box-primary">
	      	<div class="box-header">
	            <h3 class="box-title">Pencarian</h3>
	            @if(Session::has('flash_message'))
	            	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
	        	@endif
	    	</div>

	    	<div class="input-group">
                    <div class="input-group-addon" style="background-color: #167F92; color:white;">
                      <label for="combo_control" class="control-label">Status</label>
                    </div>
                    <span id="">
                        <select name="status" id="status" class="form-control col-md-7 col-xs-12"> 
                              <option value="ALL">Semua Kamar</option>
                              <option value="1">TT Kosong</option>
                              <option value="2">Kamar Penuh</option>
                        </select>
                    </span>
                </div>

                <div class="input-group">
                    <div class="input-group-addon" style="background-color: #167F92; color:white;">
                      <label for="combo_control" class="control-label">Ruang</label>
                    </div>
                    <span id="">
                        <select name="ruang" id="ruang" class="form-control col-md-7 col-xs-12"> 
                              <option value="ALL">SEMUA RUANG</option>
                          @foreach($ruang as $ruang) 
                              <option value="{{$ruang->TRuang_Kode}}">{{$ruang->TRuang_Nama}}</option>
                          @endforeach 
                        </select>
                    </span>
                </div>

                 <div class="input-group">
                    <div class="input-group-addon" style="background-color: #167F92; color:white;">
                      <label for="combo_control" class="control-label">Kelas</label>
                    </div>
                    <span id="">
                        <select name="kls" id="kls" class="form-control col-md-7 col-xs-12"> 
                              <option value="ALL">SEMUA RUANG</option>
                         @foreach($kls as $kls) 
                              <option value="{{$kls->TKelas_Kode}}">{{$kls->TKelas_Nama}}</option>
                          @endforeach 
                        </select>
                    </span>
                </div>

{{-- 	             <div style="overflow-x: scroll;">
	        		<span id="tablebody1"></span>
	        	</div> --}}
	       
	    </div>  <!--div class="box box-primary"-->
	  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
	
  <!-- ===================================== Data Perusahaan=========================================== -->
		<div class="col-md-8 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Ruang Kamar</h3>
	            </div>
	            <!-- /.box-header -->
				<div class="box-body">
			              <div class="form-group">
				            <div class="divscroll">
				            	<div style="height:220px;">
				            		<span id="tablebody1" >
	            					</span>
				            	</div>	            				
	            			</div>
			            </div>

			            <div class="form-group">
			            	<div class="divscroll">
			            		<div style="height:220px;">
		            				<span id="tablebodyriwayat">
		            		
		            				</span>
	            				</div>
	            			</div>
			            </div>

				</div>  {{-- box Body --}}
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}

		    <!-- ===================================== Data Pasien =========================================== -->
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Bor Ruang</h3>
	            </div>
	            <!-- /.box-header -->
				<div class="box-body">
			            <div class="form-group">
			                  		<div style="height:450px;" >
		            				<span id="tablebody2">
		            				</span>
	            			</div>
			            </div>	        
			    </div>  {{-- box Body --}}
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}
	</form>

	</div>
	
	<!-- JQuery 1 -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

	<!-- Auto Complete Search Asset -->
	<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.js') }}"></script>

	<!-- Modal Searching Pasien Lama -->
	<script src="{{ asset('js/searchData.js') }}"></script>
	<script type="text/javascript">

	    var arrTrans      = [];
	    var arrTransDetil = [];
	    var arrHasil      = [];
	    var arrkamar      = [];
	
	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();
        refreshDataKls();
      	refreshDataKamar();
	});

	  $('#status').on('change', function(e){
    refreshData();
    refreshDataKls();
    refreshDataKamar();
  });
	    $('#ruang').on('change', function(e){
    refreshData();
    refreshDataKls();
    refreshDataKamar();
  });
	      $('#kls').on('change', function(e){
    refreshData();
    refreshDataKls();
    refreshDataKamar();

  });

	function refreshData(){
    	var isiData = '';
        var key1  = $('#status').val();
        var key2  = $('#ruang').val();
        var key3  = $('#kls').val();

    	isiData += '<table class="table table-bordered">';

    	isiData += '<tr>'    					
    					+'<th width="25%">Ruang/ Kelas</th>'
    					+'<th width="15%">Tarif Kamar</th>'
    					+'<th width="15%">Daya Tampung</th>'
    					+'<th width="15%">Terisi</th>'
    					+'<th width="15%">Kosong</th>'
    					+'<th width="15%">Terisi (%)</th>'
    				+'</tr>';
		$.get('/ajax-getvkamarrekap?key1='+key1+'&key2='+key2+'&key3='+key3, function(data){
    		var i=0;
    		if(data.length > 0){
    			$.each(data, function(index, listruang){
    				var kosong = (listruang.DayaTampung) - (listruang.JmlTerisi);
    				var perisi = ((listruang.JmlTerisi*100) / listruang.DayaTampung);

   	    			isiData += 
		    			'<tr onclick="GetInfoKamar(\''+listruang.NomorKamar+'\');">'
    					
    						+'<td id="KdPrsh'+i+'" style="text-align:left;">'+listruang.Kamar+'</td>'
				            +'<td style="text-align:left;">'+listruang.HargaKamar+'</td>'
				            +'<td style="text-align:left;">'+listruang.DayaTampung+'</td>'
				             +'<td style="text-align:left;">'+listruang.JmlTerisi+'</td>'
				            +'<td style="text-align:left;">'+kosong+'</td>'
				             +'<td style="text-align:left;">'+parseFloat(perisi).toFixed(2)+' %'+'</td>'
				        +'</tr>';		    		
					i++;
    			});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{
    			isiData += '<tr><td colspan="2"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}  
    	});
    }

      function refreshDataKls(){
      var isiData = '';
        var key1  = $('#status').val();
        var key2  = $('#ruang').val();
        var key3  = $('#kls').val();

      isiData += '<table class="table table-bordered">';

    $.get('/ajax-getvinfoBOR?key1='+key1, function(data){
        var i=0;
        if(data.length > 0){
          $.each(data, function(index, listruang){
            var kosong = (listruang.DayaTampung) - (listruang.JmlTerisi);
            var perisi = ((listruang.JmlTerisi*100) / listruang.DayaTampung);

              isiData += 
              '<tr>'
                     +'<td style="text-align:center;">'+listruang.TKelas_Nama+'</td>'
                     +'<td style="text-align:center;">'+listruang.JmlTerisi+'</td>'
                     +'<td style="text-align:center;">'+parseFloat(perisi).toFixed(2)+' %'+'</td>'
                +'</tr>';           
          i++;
          });

          isiData += '</table>';
        document.getElementById('tablebody2').innerHTML = isiData;
        }else{
          isiData += '<tr><td colspan="3"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody2').innerHTML = isiData;
        }  
      });
    }


      function GetInfoKamar(Kd){
    	// Isi ke ArrHasil
    	
    		$.get('/ajax-getvinfokamarrekap2?key='+Kd, function(data){
    		arrkamar = [];
    		if(data.length > 0){
    			$.each(data, function(index, listKmr){
    				arrkamar.push({
    				  NamaTT: listKmr.TTmpTidur_Nama,
    				  NamaPas: listKmr.TPasien_Nama,
    				  Umur: listKmr.PasienUmur,
		              TglMasuk: listKmr.TRawatInap_TglMasuk,
		            
		            });
   			    });
    		} else {
    			arrkamar = []
    		}
    			// document.getElementById('arrkamar').value = JSON.stringify(arrkamar);
    			refreshDataKamar();
			});			
    }

       function refreshDataKamar(){
    	var isiData = '';    	
    	isiData += '<form name="formkamar" id="formkamar" action="" method="POST">'
    	isiData += '<table class="table table-bordered">';

    	isiData += '<tr>'
    					+'<th width="30%">Nama TT.</th>'
    					+'<th width="40%">Nama Pasien</th>' 
    					+'<th width="10%">Umur</th>'
    					+'<th width="20%">Tanggal Masuk </th>'
    				+'</tr>';
		
    	 	if (arrkamar.length > 0){
    	 			var i = 0;
    	 			$.each(arrkamar, function (index, value) {
    	 				isiData += '<tr>'
	    						+'<td style="text-align:left;">'+value.NamaTT+'</td>'
	    						+'<td style="text-align:left;">'+value.NamaPas+'</td>'
	    						+'<td style="text-align:left;">'+value.Umur+'</td>'
	    						+'<td style="text-align:left;">'+value.TglMasuk+'</td>'
					            +'</tr>';
	    				i++;
    	 			});
	    			
    			// document.getElementById('arrkamar').value = JSON.stringify(arrkamar);
    			  document.getElementById('tablebodyriwayat').innerHTML = isiData;
    			isiData += '</table>';
    			isiData += '</form>';
				document.getElementById('tablebodyriwayat').innerHTML = isiData;
    		}else{
				// document.getElementById('arrkamar').value = JSON.stringify(arrkamar);
				 document.getElementById('tablebodyriwayat').innerHTML = isiData;
    			isiData += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '</table>';
    			isiData += '</form>';
    			document.getElementById('tablebodyriwayat').innerHTML = isiData;
    		}
   }
	</script>
@endsection