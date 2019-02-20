@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName)

@section('content_header', '')

@section('content')

	<div class="col-lg-12 col-xs-12" style="background: linear-gradient(#d5e8f2, #ECEFF4); border-top-left-radius: 7px; border-top-right-radius: 7px; height: 200px;">
		<div style="text-align:center;">
			<div>
				<h3><font style="background: -webkit-linear-gradient(#103e44, #031f23); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 3px 3px 5px #66b7c9;">Selamat Datang Di Sistem Human Resource Terpadu</font></h3>
				<h2><font style="background: -webkit-linear-gradient(#103e44, #031f23); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 3px 3px 5px #66b7c9;"><strong>SMART MEDISOFT</strong></font></h2>
			</div>
		</div>
	</div>

	<div class="row">

	    <div class="col-lg-3 col-xs-6">
	      <div class="small-box bg-red">
	        <div class="inner">
	          <h3>{{$jmlkaryawan}}</h3>
	          <p>Data Karyawan</p>
	        </div>
	        <div class="icon">
	          <i class="fa fa-ambulance"></i>
	        </div>
	        <a href="/datakaryawan" class="small-box-footer">View Karyawan <i class="fa fa-arrow-circle-right"></i></a>
	      </div>
	    </div>
	</div>
	<div>

		<?php 
			//echo session()->get('jajal'); 
		?>

	</div>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
{{-- <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script> --}}

<!-- Auto Complete Search Asset -->
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>

<!-- Modal Searching Pasien Lama -->
<script src="{{ asset('js/searchData.js') }}"></script>

@endsection