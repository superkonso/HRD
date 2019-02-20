@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Dokter')

@section('content_header', 'Edit Dokter')

@section('header_description', '')

@section('menu_desc', 'Dokter')

@section('link_menu_desc', '/dokter')

@section('sub_menu_desc', 'Edit')

@section('content')

@include('Partials.message')

<div class="row">
	    <div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="box box-primary">
	            <div class="box-header">
	                @if(Session::has('flash_message'))
	                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
	                @endif
	                <h3 class="box-title">Form Edit Dokter</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/dokter/{{$pelakus->id}}" method="post" novalidate>
		                {{csrf_field()}}
		                {{method_field('PUT')}}
    				           					           
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Dokter </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" disabled="disabled" name="kode" placeholder="Kode Dokter" required="required" value="{{$pelakus->TPelaku_Kode}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Dokter <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Dokter" value="{{$pelakus->TPelaku_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="namalengkap">Nama Lengkap
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="namalengkap" class="form-control col-md-7 col-xs-12" name="namalengkap" placeholder="Nama Lengkap Dokter" value="{{$pelakus->TPelaku_NamaLengkap}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
				            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamat">Alamat</label>
				            <div class="col-md-6 col-sm-6 col-xs-12">
				             <textarea id="alamat" class="form-control" name="alamat" rows="2" style="resize:none;")>{{$pelakus->TPelaku_Alamat}}</textarea>
				            </div>
			          	</div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="alamat">Kota
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kota" class="form-control col-md-7 col-xs-12" name="kota" placeholder="Kota" value="{{$pelakus->TPelaku_Kota}}" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="telepon">Telepon
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="alamat" class="form-control col-md-7 col-xs-12" name="telepon" placeholder="Telepon" value="{{$pelakus->TPelaku_Telepon}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 1</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select id="unit" name="unit" class="form-control col-md-7 col-xs-12">  
	                          	@foreach($units as $unit)
	                          		<option value="{{$unit->TUnit_Kode}}" @if(!empty($pelakus->TUnit_Kode)) @if($unit->TUnit_Kode==$pelakus->TUnit_Kode) selected="selected" @endif @endif)>{{$unit->TUnit_Nama}}</option>
	                          	@endforeach                          	  	                           
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 2</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="unit2" class="form-control col-md-7 col-xs-12">
	                          	@foreach($units as $unit2)
	                          		<option value="{{$unit2->TUnit_Kode}}" @if(!empty($pelakus->TUnit_Kode2)) @if($unit2->TUnit_Kode==$pelakus->TUnit_Kode2) selected="selected" @endif @endif)>{{$unit2->TUnit_Nama}}</option>
	                          	@endforeach            
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 3</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="unit3" class="form-control col-md-7 col-xs-12">
	                          	@foreach($units as $unit3)
	                          		<option value="{{$unit3->TUnit_Kode}}" @if(!empty($pelakus->TUnit_Kode3)) @if($unit3->TUnit_Kode==$pelakus->TUnit_Kode3) selected="selected" @endif @endif)>{{$unit3->TUnit_Nama}}</option>
	                          	@endforeach               
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Spesialis</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="spesial" class="form-control col-md-7 col-xs-12">
	                          	@foreach($spesials as $spes)
	                          		<option value="{{$spes->TSpesialis_Kode}}" @if(!empty($pelakus->TSpesialis_Kode)) @if ($spes->TSpesialis_Kode==$pelakus->TSpesialis_Kode) selected="selected" @endif @endif>{{$spes->TSpesialis_Nama}}</option>
	                          	@endforeach              
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Jenis</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select name="jenis" class="form-control col-md-7 col-xs-12">
		                     	<option value="FT1" @if(!empty($pelakus->TPelaku_Jenis)) @if ("FT1"==$pelakus->TPelaku_Jenis) selected="selected" @endif @endif>Full Time</option>
		                     	<option value="PT1" @if(!empty($pelakus->TPelaku_Jenis)) @if ("PT1"==$pelakus->TPelaku_Jenis) selected="selected" @endif @endif>Part Time</option>
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Status</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select name="status" class="form-control col-md-7 col-xs-12">
		                     <option value="0" @if(!empty($pelakus->TPelaku_Status)) @if($pelakus->TPelaku_Status=="0") selected="selected" @endif @endif> Non Aktif</option>
		                     <option value="1" @if(!empty($pelakus->TPelaku_Status)) @if($pelakus->TPelaku_Status=="1") selected="selected" @endif @endif> Aktif</option>

		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                     <label class="control-label col-md-3 col-sm-3 col-xs-12" for="perkiraan">Perkiraan Kode 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="perkiraan" name="perkiraan" class="form-control col-md-7 col-xs-12">
		                     	@foreach($perkkodes as $pk)
	                          		<option value="{{$pk->TPerkiraan_Kode}}" @if(!empty($tarif->TPerkiraan_Kode)) @if ($pk->TPerkiraan_Kode==$tarif->TPerkiraan_Kode) selected="selected" @endif @endif > {{$pk->TPerkiraan_Kode .' - '. $pk->TPerkiraan_Nama}}</option>
	                          	@endforeach      
		                     </select>
		                    </div>
		                </div>

		                <div class="ln_solid"></div>

	                    <div class="row">
						    <div class="col-md-12 col-sm-12 col-xs-12">
						    <div class="form-group">
						      <div class="box-body">
						        <div class="col-md-12 col-md-offset-5">
						           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
						          <a href="/dokter" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
						        </div>
						      </div>
						    </div>
						  </div>
						</div>

	                </form>
	            </div>
	        </div>
	    </div>

	  
</div>

@endsection