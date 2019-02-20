@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Edit Menu Item')

@section('content_header', 'Edit Menu')

@section('header_description', '')

@section('menu_desc', 'Menu Item')

@section('link_menu_desc', '/menuitem')

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
	                <h3 class="box-title">Menu Baru</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/menuitem/{{$menuitems->id}}" method="post" novalidate>
		                {{csrf_field()}}
		                {{method_field('PUT')}}
		           		
		           		<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok Menu</label>
						    <div class="col-md-6 col-sm-6 col-xs-12">
						      <select id="kelompok" name="kelompok" class="form-control col-md-7 col-xs-12">  
						      	@foreach($menus as $menu)
						      		<option value="{{$menu->TMenu_Kode}}" @if(!empty($menuitems->TMenu_Kode)) @if($menu->TMenu_Kode == $menuitems->TMenu_Kode) selected = selected @endif @endif>{{$menu->TMenu_Nama}}</option>
						      	@endforeach                          	  	                           
						      </select>
						    </div>
			          	</div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Menu Kode</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="" required="required" value="{{$menuitems->TMenuItem_Item}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Menu<span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama" value="{{$menuitems->TMenuItem_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="link">Menu Link</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                        <input type="text" id="link" class="form-control col-md-6 col-xs-12" name="link" placeholder="Link (input dengan tanda / di depan link)" value="{{$menuitems->TMenuItem_Link}}" required="required" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Jenis</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select name="jenis" class="form-control col-md-7 col-xs-12">
		                     	<option value="H" @if(!empty($menuitems->TMenuItem_Jenis)) @if("H"==$menuitems->TMenuItem_Jenis) selected = selected @endif @endif>Header Menu</option>
		                     	<option value="M" @if(!empty($menuitems->TMenuItem_Jenis)) @if("M"==$menuitems->TMenuItem_Jenis) selected = selected @endif @endif>Menu Item</option>
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
				            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="logo">Logo</label>
				            <div class="col-md-6 col-sm-6 col-xs-12">
		                    <div><img width="50px" height="50px" src="@if(is_null($menuitems->TMenuItem_Logo) or $menuitems->TMenuItem_Logo == '') {{ asset('images/menu') }}/list-menu-icon-small.png @else {{ asset('images/menu') }}\{{$menuitems->TMenuItem_Logo}} @endif"></img></div><br>
		                        <input type="file" class="file" name="logo" id="logo">
		                    </div>
			          	</div>
                   		           
		                <div class="ln_solid"></div>

	                    <div class="row">
						    <div class="col-md-12 col-sm-12 col-xs-12">
						    <div class="form-group">
						      <div class="box-body">
						        <div class="col-md-12 col-md-offset-5">
						           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
						          <a href="/menuitem" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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