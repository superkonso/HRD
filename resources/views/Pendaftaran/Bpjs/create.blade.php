@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | SEP BPJS')

@section('content_header', 'Create SEP BPJS')

@section('header_description', '')

@section('menu_desc', 'SEP BPJS')

@section('link_menu_desc', '/sepbpjs')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

  <?php
    date_default_timezone_set("Asia/Bangkok");
  ?>

  <!-- ============================================== Content ================================================== -->
  <div class="row">

    <form class="form-horizontal form-label-left" action="/sepbpjs" method="post" id="formsepbpjs" name="formsepbpjs" data-parsley-validate>

      <!-- Token -->
      {{csrf_field()}}

      {{ Form::hidden('pasien_id', '0', array('id' => 'pasien_id')) }}
      {{ Form::hidden('transID', '0', array('id' => 'transID')) }}
      {{ Form::hidden('tempNoRM', '', array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', '', array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('temptStatus', 'C', array('id' => 'temptStatus')) }}
      {{ Form::hidden('tempjnspas', '', array('id' => 'tempjnspas')) }}

    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ===================================== Data Peserta =========================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Peserta</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">No Kartu</label>
              <div class="col-md-6 col-sm-6 col-xs-9">
                <input type="text" name="noka" id="noka" class="form-control col-md-7 col-xs-12" value="" placeholder="No BPJS / NIK">
              </div>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <a href="#formsearch" id="caripasienlama" onclick="cariPeserta();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Cari</a>
              </div>
            </div>
			       <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" readonly="">
              </div>
            </div>
			       <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="jk" id="jk" class="form-control col-md-7 col-xs-12" value="" readonly="">
              </div>
            </div>
            <div class="form-group">
            	<label class="control-label col-md-3 col-sm-3 col-xs-3">Pisat</label>
              <div class="col-md-3 col-sm-3 col-xs-3">                
                  <input type="text" name="pisat" class="form-control " id="pisat" value="" readonly>
              </div>  
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl. Lahir</label>
              <div class="col-md-3 col-sm-3 col-xs-3">                 
                  <input type="text" name="tgllahir" class="form-control " id="tgllahir" value="" readonly>
              </div>              
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">PPK Tk. I</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="ppktk1" id="ppktk1" class="form-control col-md-7 col-xs-12" readonly="">
              </div>
            </div>           
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Peserta</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="peserta" id="peserta" class="form-control" readonly="">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelas Rawat</label>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="klsrawat" id="klsrawat" class="form-control col-md-7 col-xs-12" readonly>
              </div>
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl. Cetak Kartu</label>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="cetakkartu" id="cetakkartu" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">TAT</label>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="tat" id="tat" class="form-control col-md-7 col-xs-12" readonly>
              </div>
              <label class="control-label col-md-3 col-sm-3 col-xs-3">TMT</label>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="tmt" id="tmt" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Asuransi COB</label>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="pesertacob" id="pesertacob" class="form-control" readonly="">
              </div>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="checkbox" name="cob" id="cob">  COB
              </div>
            </div>
           </div>
      </div>
      <!-- ====================================== End Data Peserta ==================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Riwayat SEP</h3>
        </div>
        <div class="box-body">
          <div class="divscroll">
                      <span id="tabelhistory">
                        <table class="responstable">
                          <tr>
                            <th width="150px">SEP</th>
                            <th width="100px">Tanggal SEP</th>
                            <th width="100px">Diagnosa</th>
                            <th width="100px">Poli</th>
                            <th width="100px">RI/RJ</th>
                            <th width="100px">Klaim</th>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        </table>
                      </span>
                  </div>
        </div>
      </div>
  </div>

<input type="hidden" name="nokartu" id="nokartu" value="">
<input type="hidden" name="nik" id="nik" value="">
<input type="hidden" name="kicd" id="kicd" value="">
<input type="hidden" name="arrItem" id="arrItem" value="">

    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ====================================== SEP ================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Surat Eligibilitas Peserta</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Perawatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="jnsrawat" id="jnsrawat" class="form-control col-md-7 col-xs-12">
               		<option value="2">Rawat Jalan</option>
                  <option value="1">Rawat Inap</option>
              </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelas Perawatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="kelasrawat" id="kelasrawat" class="form-control col-md-7 col-xs-12">
               		<option value="1">Kelas I</option>
                    <option value="2">Kelas II</option>
                    <option value="3">Kelas III</option>
              </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              	<select name="rujukfaskes" id="rujukfaskes" class="form-control col-md-4 col-xs-12">
               		<option value="1">Rujukan Faskes I</option>
                  <option value="2">Rujukan Faskes II</option>
              	</select>
              </div>

            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="norujukan" id="norujukan" class="form-control" placeholder="Nomor Rujukan">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>            
              <div class="col-md-9 col-sm-9 col-xs-9">
                <a id="carirujukan" onclick="cariRujukan();" class="btn btn-primary" data-toggle="modal">Cari</a>
                <a href="" id="queryrujukan" onclick="queryRujukan();" class="btn btn-primary" data-toggle="modal">Query</a>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="nomorrm" id="nomorrm" class="form-control" placeholder="Nomor RM RS">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Asal Rujuan</label>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="kdasalruujukan" id="kdasalruujukan" class="form-control">                
              </div>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="asalruujukan" id="asalruujukan" class="form-control">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Rujukan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="input-group date">
              <div class="input-group-addon">
                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                </div>
                <input type="text" name="tglrujukan" class="form-control pull-right" id="datepicker" value="<?php echo date('m/d/Y'); ?>">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor SEP</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="sep" id="sep" class="form-control">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl SEP</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="input-group date">
              <div class="input-group-addon">
                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                </div>
                <input type="text" name="tglsep" class="form-control pull-right" id="datepicker2" value="<?php echo date('m/d/Y'); ?>">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Diagnosa Awal</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="icd" id="icd" class="form-control" placeholder="Kode ICD">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="diagnosa" id="diagnosa" class="form-control" placeholder="Nama ICD">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Poli Tujuan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="poli" id="poli" class="form-control col-md-7 col-xs-12">
                  <option value="0"> </option>
                @foreach($refpoli as $poli)
                  <option value="{{$poli->TRefPoli_Kode}}"><?php echo $poli->TRefPoli_Kode.' - '.$poli->TRefPoli_Nama;?></option>
                @endforeach
              </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Catatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="catatan" id="catatan" class="form-control">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">
                <input type="checkbox" name="ckasus" id="ckasus" value="0">  Kasus</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kasus" id="kasus" class="form-control" placeholder="lokasi Laka">
              </div>
            </div>

          </div>
      </div>
      <!-- ================================== End SEP ======================================= -->    
    </div> <!--div class="col-md-6 col-sm-12 col-xs-12"-->
  </div> <!-- row -->


  <div class="row">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
      <div class="box">
        <div class="box-body">
        	<div class="cold-md-6 col-sm-6 col-xs-6">
    			   <div class="col-md-12 col-md-offset-4">
{{-- 		            <button id="save" name="submit" type="submit" value="cari" class="btn btn-primary"><img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20"> Cari</button> --}}
                <a name="cari" value="cari" class="btn btn-success" onclick="sepbpjs();"><img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20"> Cari</a>
		            <button id="edit" type="submit" name="submit" value="edit" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Edit</button>
		            <button name="submit" class="btn btn-primary" value="cetak" ><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Cetak</button>
	           </div>
    		  </div>	
          <div class="cold-md-6 col-sm-6 col-xs-6">
    			  <div class="col-md-12 col-md-offset-4">
{{--                 <button id="save" name="submit" type="submit" value="save" class="btn btn-primary"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button> --}}
		            <a type="" name="" value="save" class="btn btn-success" onclick="sepbpjs();"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</a>
		            <a href="" class="btn btn-danger" onclick="resetForm();"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
	          </div>
    		  </div>	
        </div>
      </div>
    </div>
  </div>
  <!-- ================== -->

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

  <!-- ============================================= End Of Content ====================================== -->

  @include('Partials.alertmodal')

  <script type="text/javascript">
    var arrItem   = [];
    var newdata   = false;

    $(function () {
      //Date picker
        $('#datepicker').datepicker({
          autoclose: true
    });

        $('#datepicker2').datepicker({
          autoclose: true
        });
    });

    $( document ).ready(function() {

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);       
      }); 

      refreshTable();         

    });

    // ---------------------------------------------------------------------------------
    $( "#icd" ).autocomplete({
        source: '{!!URL::route('autocompleteicd')!!}',
        minLength: 1,
        autoFocus:true,
        select: function(e, ui) {     
          $("#kicd").val(ui.item.id);
      }
    });

    $('#noka').on('change', function(e){
      cariPeserta();
    });
    
    $('#icd').on('change', function(e){
      $('#icd').val($("#kicd").val());
      cariIcd();
      $("#kicd").val('');
    });

    function cariPeserta(){
      var pasiennoka = $('#noka').val();
      if (pasiennoka.length > 6) {
        $.get('/ajax-caripesertabpjs?noka='+pasiennoka, function(data){
          if(data['metadata']['message']!="OK"){
            showDialog('modalWarning','Code: '+data['metadata']['code'] + '. ' +data['metadata']['message']);

            $('#noka').val(''); 
            document.getElementById('nama').value = '';
            document.getElementById('jk').value = '';
            document.getElementById('pisat').value = '';
            document.getElementById('tgllahir').value =  '';
            document.getElementById('ppktk1').value =  '';
            document.getElementById('peserta').value =  '';
            document.getElementById('klsrawat').value =  '';
            document.getElementById('cetakkartu').value =  '';
            document.getElementById('tat').value = '';
            document.getElementById('tmt').value = '';
            document.getElementById('pesertacob').value = '';
            document.getElementById('cob').value = 0;
            document.getElementById('asalruujukan').value =  '';
            $('#nik').val(''); 
            $('#nokartu').val(''); 

          }else{

            document.getElementById('nik').value = data['response']['peserta']['nik'];
            document.getElementById('nokartu').value = data['response']['peserta']['noKartu'];
            document.getElementById('nama').value = data['response']['peserta']['nama'];
            document.getElementById('jk').value = (data['response']['peserta']['sex']==='L' ? 'Laki-laki':'Perempuan');
            document.getElementById('pisat').value = namapisat(data['response']['peserta']['pisa']);
            document.getElementById('tgllahir').value = moment(data['response']['peserta']['tglLahir']).format('DD/MM/YYYY');
            document.getElementById('ppktk1').value = data['response']['peserta']['provUmum']['kdProvider']+' - '+data['response']['peserta']['provUmum']['nmProvider'];
            document.getElementById('peserta').value = data['response']['peserta']['jenisPeserta']['nmJenisPeserta'];
            document.getElementById('klsrawat').value = data['response']['peserta']['kelasTanggungan']['nmKelas'];
            document.getElementById('cetakkartu').value = moment(data['response']['peserta']['tglCetakKartu']).format('DD/MM/YYYY');
            document.getElementById('tat').value = moment(data['response']['peserta']['tglTAT']).format('DD/MM/YYYY');
            document.getElementById('tmt').value = moment(data['response']['peserta']['tglTMT']).format('DD/MM/YYYY');
            document.getElementById('kdasalruujukan').value = data['response']['peserta']['provUmum']['kdProvider']
            document.getElementById('asalruujukan').value = data['response']['peserta']['provUmum']['nmProvider'];

            cariRiwayatSep();

          } // else if(data > 0){
        });
      } else {
        $('#noka').val(''); 
        document.getElementById('nama').value = '';
        document.getElementById('jk').value = '';
        document.getElementById('pisat').value = '';
        document.getElementById('tgllahir').value =  '';
        document.getElementById('ppktk1').value =  '';
        document.getElementById('peserta').value =  '';
        document.getElementById('klsrawat').value =  '';
        document.getElementById('cetakkartu').value =  '';
        document.getElementById('tat').value = '';
        document.getElementById('tmt').value = '';
        document.getElementById('pesertacob').value = '';
        document.getElementById('cob').value = 0;
        document.getElementById('asalruujukan').value =  '';
        $('#nik').val(''); 
        $('#nokartu').val(''); 
      }
      
    }

    function namapisat(kode){
      $.get('/ajax-getnamapisat?kode='+kode, function(data){        
          document.getElementById('pisat').value =data.trim();        
      });
    }

    function cariIcd(){
      var kode = $('#icd').val();
      if (kode != '') {
        $.get('/ajax-getnamaicd?kode='+kode, function(data){        
          document.getElementById('diagnosa').value = data;        
        });
      }else{
         document.getElementById('diagnosa').value ='';     
      }      
    }

    function sepbpjs(){
      event.preventDefault();  
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content') 
          }
      });
      $.ajax({
        url: '/insertsep',
        type: "POST",
        data: {
          'newdata': 'true',
          'tglSep': $('#datepicker2').val(),
          'noKartu': $('#nokartu').val(),
          'tglRujukan': $('#datepicker').val(),
          'noRujukan': $('#norujukan').val(),
          'ppkRujukan': $('#kdasalruujukan').val(),
          'jnsPelayanan': $('#jnsrawat').val(),
          'diagAwal': $('#icd').val(),
          'poliTujuan': $('#poli').val(),
          'klsRawat': $('#klsrawat').val(),
          'noMR': $('#nomorrm').val(),
        },
        dataType: "JSON",
        // beforeSend:
        //     function () {
        //         $(".loader").show();
        //     }
        // ,
        success: function (data) {
            // $(".loader").hide();
            if (data['metadata']['code'] == '200') {
              swal({
                title: 'SEP Berhasil',
                text: data['response'],
                icon: 'success',
                timer: 2000
              });
              setTimeout(function () {
                document.getElementById('sep').value = data['response'];
                }, 1000);
            }else {
              swal({
                title: 'SEP Gagal',
                text: data['metadata']['message'],
                icon: 'warning',
                button: 'OK',
                timer: 4000
              });
              setTimeout(function () {
                 document.getElementById('sep').value = '';
              }, 1000);
            }
        }
      });
    }

    function cariRiwayatSep(){
      var kartu = $('#nokartu').val();
      
      arrItem = [];

      $.get('/ajax-caririwayat?noka='+kartu, function(riwayat){
        if (riwayat.length > 1) {
          if (riwayat['metadata']['message']=="OK") {
            var data = riwayat['response']['list'];
            $.each(data, function(index,value){            
              arrItem.push({
                    sep: value.noSEP,
                    tanggalsep: value.tglSEP,
                    diagnosasep: value.diagnosa.kodeDiagnosa,
                    poli: value.poliTujuan.kdPoli,
                    rirj: value.jnsPelayanan, 
                    klaim: value.biayaTagihan, 
                  });
              //refreshTable();
            });
          }
        }
      });
      refreshTable();
    }

    function refreshTable(){
      var refreshtablehistory = '';

      refreshtablehistory += '<table class="responstable">'
                            +'<tr>'
                              +'<th width="130px">SEP</th>'
                              +'<th width="100px">Tanggal SEP</th>'
                              +'<th width="200px">Diagnosa</th>'
                              +'<th width="100px">Poli</th>'
                              +'<th width="100px">RI/RJ</th>'
                              +'<th width="100px">Klaim</th>'
                            +'</tr>';                 
      var i = 0;

      $.each(arrItem, function (index, value) {

        refreshtablehistory += '<tr  ondblclick="clickGrid('+i+', 1,1);">'
                            +'<td style="text-align:left;">'+value.sep+'</td>'
                            +'<td style="text-align:center;">'+value.tanggalsep+'</td>'
                            +'<td style="text-align:left;">'+value.diagnosasep+'</td>'
                            +'<td style="text-align:center;">'+value.poli+'</td>'
                            +'<td style="text-align:left;">'+value.rirj+'</td>'
                            +'<td style="text-align:left;">'+value.klaim+'</td>'
                          +'</tr>';
        i++;
      });

      document.getElementById('arrItem').value = JSON.stringify(arrItem);

      refreshtablehistory += '</table>';

      document.getElementById('tabelhistory').innerHTML = refreshtablehistory;
    }

    function clickGrid(ind){
      var sep = arrItem[ind]['sep'];
      $.get('/ajax-detilriwayat?sep='+sep, function(driwayat){
        if (driwayat['metadata']['message']!="OK") {
          showDialog('modalWarning','Code: '+driwayat['metadata']['code'] + '. ' +driwayat['metadata']['message']);
          return false;
        }else{
          
          document.getElementById('nomorrm').value = data['response']['peserta']['noMr'];
          document.getElementById('sep').value = data['response']['noSep'];
          document.getElementById('diagnosa').value = data['response']['diagAwal']['nmDiag'];
          document.getElementById('icd').value = data['response']['diagAwal']['kdDiag'];
          document.getElementById('poli').value = data['response']['peserta']['poliTujuan']['kdPoli'];
          document.getElementById('kasus').value = data['response']['lakaLantas']['keterangan'];
          document.getElementById('jnsrawat').selectedvalue = (data['response']['diagAwal']['jnsPelayanan'] === 'Inap' ? 'RI': 'RJ');
          document.getElementById('kelasrawat').selectedvalue = data['response']['diagAwal']['kdKelas'];
          document.getElementById('catatan').value = data['response']['catatan'];
          document.getElementById('norujukan').value = data['response']['noRujukan'];

          var datepicker = driwayat['response']['tglRujukan'], 
          dateParts = datepicker.match(/(\d+)/g)
          realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);  
          $('#datepicker').datepicker('setDate',realDate );

          var datepicker2 = driwayat['response']['tglSep'], 
          dateParts2 = datepicker2.match(/(\d+)/g)
          realDate2 = new Date(dateParts2[0], dateParts2[1] - 1, dateParts2[2]);  
          $('#datepicker2').datepicker('setDate',realDate2 );

        }
      });
    }

    function cariRujukan() {
      var tingkat = $('#rujukfaskes').val();
      var nomor   = $('#nomorrujukan').val();

      $.get('/ajax-rujukan?nomorrujukan='+nomor+'$tingkat='+tingkat, function(rujukan){
        if (rujukan['metadata']['message']!="OK") {
          showDialog('modalWarning','Code: '+rujukan['metadata']['code'] + '. ' +rujukan['metadata']['message']);
          return false;
        }else{
          
          // document.getElementById('nomorrm').value = data['response']['peserta']['noMr'];
          // document.getElementById('sep').value = data['response']['noSep'];
          // document.getElementById('diagnosa').value = data['response']['diagAwal']['nmDiag'];
          // document.getElementById('icd').value = data['response']['diagAwal']['kdDiag'];
          // document.getElementById('poli').value = data['response']['peserta']['poliTujuan']['kdPoli'];
          // document.getElementById('kasus').value = data['response']['lakaLantas']['keterangan'];
          // document.getElementById('jnsrawat').selectedvalue = (data['response']['diagAwal']['jnsPelayanan'] === 'Inap' ? 'RI': 'RJ');
          // document.getElementById('kelasrawat').selectedvalue = data['response']['diagAwal']['kdKelas'];
          // document.getElementById('catatan').value = data['response']['catatan'];
          // document.getElementById('norujukan').value = data['response']['noRujukan'];

          // var datepicker = rujukan['response']['tglRujukan'], 
          // dateParts = datepicker.match(/(\d+)/g)
          // realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);  
          // $('#datepicker').datepicker('setDate',realDate );

          // var datepicker2 = rujukan['response']['tglSep'], 
          // dateParts2 = datepicker2.match(/(\d+)/g)
          // realDate2 = new Date(dateParts2[0], dateParts2[1] - 1, dateParts2[2]);  
          // $('#datepicker2').datepicker('setDate',realDate2 );
        }
      });
    }

    function queryRujukan() {
      showDialog('modalWarning','menu belum tersedia');
      return false;
    }
  </script>

@endsection