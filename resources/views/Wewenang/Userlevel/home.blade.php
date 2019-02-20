@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | User Level')

@section('content_header', 'Userlevel')

@section('header_description', 'Level User')

@section('menu_desc', 'Userlevel')

@section('link_menu_desc', '/userlevel')

@section('sub_menu_desc', 'Userlevel')

@section('content')
 
@include('Partials.message')

<?php 

use SIMRS\Helpers\accessList;

?>
  

<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div class="box-header">
	          		{{-- @if(Session::has('flash_message'))
                      <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
                  	@endif --}}
		        </div>

		      <div class="box-body">

		      	<form action="/userlevel" method="post" name="userlevelpermissionform" id="userlevelpermissionform" novalidate>
    			{{csrf_field()}}

			      	<div class="col-md-12 col-sm-12 col-xs-12">
			      		<div class="form-group col-md-4 col-sm-4 col-xs-12">
			      			<label>User Level : </label>
				            <select class="form-control" name="userlevel" id="userlevel">
				            	@foreach($access as $level)
				            		<option value="{{$level->TAccess_Code}}">{{$level->TAccess_Name}}</option>
				            	@endforeach
				            </select>
			      		</div>

			      		<div class="form-group col-md-8 col-sm-8 col-xs-12" style="max-height: 400px; overflow-x: scroll;">
			      			<label>Akses Menu : </label>
			      			<span id="divMenu"></span>
			      		</div>
			      	</div>
			      	<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
				      	<button type="submit" name="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
				    </div>

	      		</form>
			      	
			  </div> <!--div class="box-body"-->

			</div> <!--div class="box box-primary"-->
		</div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
	var m 	= 0;
	var mh 	= 0;
	var mi 	= 0;

	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	fillAksesMenu('000');

      	//$('ul[id=hmenu]').hide();
      	//$('ul[id=hmenuitem]').hide();

	});

	$(function() {
          $(this).bind("contextmenu", function(e) {
              e.preventDefault();
          });
      });


	$('#userlevel').on('change', function(e){
      var kdlevel = $('#userlevel').val();

      fillAksesMenu(kdlevel);

    });



	function fillAksesMenu(levelUser){
		var isi 			= '';
		var menukode 		= '';
		var menukode_temp 	= '';
		var gantikode 		= false;

		var i 	= 0;
		var m 	= 0;
		var mh 	= 0;
		var mi 	= 0;

		document.getElementById('divMenu').innerHTML = '';

		isi += '<section class="sidebar">'
		    + '<ul style="background-color:#F5F5F5;">';

		$.get('/ajax-getmenuitembylevel?level='+levelUser, function(data){

			$.each(data, function(index, listMenuObj){

				menukode_temp 	= listMenuObj.TMenu_Kode;

				var aksesmodul 	= listMenuObj.AksesMenu;
				var aksesmenu 	= listMenuObj.Akses;
				var kodeitem 	= listMenuObj.TMenuItem_Item;
				var namamenu 	= listMenuObj.TMenu_Nama;

				if(menukode == ''){
					menukode 		= listMenuObj.TMenu_Kode;

					isi += '<ul class="treeview-menu" style="list-style:none;" id="">';

					if(aksesmodul == 1){
						isi += '<input type="checkbox" id="m'+m+'" checked="checked" value="'+kodeitem+'" onclick="changeCheckedMenu('+m+', \''+menukode+'\');"><a href="#"> <b>'+namamenu.toUpperCase()+'</b></a>';
					}else{
						isi += '<input type="checkbox" id="m'+m+'" value="'+kodeitem+'" onclick="changeCheckedMenu('+m+', \''+menukode+'\');"><a href="#"> <b>'+namamenu.toUpperCase()+'</b></a>';
					}
					
				}else{

					if(menukode == menukode_temp){
						gantikode = false;
					}else{
						gantikode = true;
						menukode  = menukode_temp;
					}
				}

				if(gantikode){
					isi += '</ul><ul class="treeview-menu" style="list-style:none;" id="">';

					if(aksesmodul == 1){
						m++;

						isi += '<input type="checkbox" id="m'+m+'" checked="checked" value="'+kodeitem+'" onclick="changeCheckedMenu('+m+', \''+menukode+'\');"><a href="#"> <b>'+namamenu.toUpperCase()+'</b></a>';
					}else{
						m++;

						isi += '<input type="checkbox" id="m'+m+'" value="'+kodeitem+'" onclick="changeCheckedMenu('+m+', \''+menukode+'\');"><a href="#"> <b>'+namamenu.toUpperCase()+'</b></a>';
					}
				}

				isi += '<li class="treeview" style="list-style:none;">';

				if(aksesmenu == 1){
					if(listMenuObj.TMenuItem_Jenis == 'H'){
						mh++;

						isi += '&emsp; <input type="checkbox" name="listMenu['+menukode+'][]" id="hm'+m+''+mh+'" checked="checked" value="'+kodeitem+'" onclick="changeCheckedHMenu('+m+', '+mh+', \''+menukode+'\');"><a href="#"> <b>'+listMenuObj.TMenuItem_Nama+'</b></a>';
					}else{
						mi++; 

						isi += '&emsp;&emsp; <input type="checkbox" name="listMenu['+menukode+'][]" id="im'+m+''+mh+''+mi+'" checked="checked" value="'+kodeitem+'"><a href="#"> <b>'+listMenuObj.TMenuItem_Nama+'</b></a>';
					}	
				}else{
					if(listMenuObj.TMenuItem_Jenis == 'H'){
						mh++;

						isi += '&emsp; <input type="checkbox" name="listMenu['+menukode+'][]" id="hm'+m+''+mh+'" value="'+kodeitem+'" onclick="changeCheckedHMenu('+m+', '+mh+', \''+menukode+'\');"><a href="#"> <b>'+listMenuObj.TMenuItem_Nama+'</b></a>';
					}else{
						mi++;

						isi += '&emsp;&emsp; <input type="checkbox" name="listMenu['+menukode+'][]" id="im'+m+''+mh+''+mi+'" value="'+kodeitem+'"><a href="#"> <b>'+listMenuObj.TMenuItem_Nama+'</b></a>';
					}
				}

				isi += '</li>';

				if(i == (data.length-1)) 
					isi += '</ul>';

				i++;

			}); // ... $.each(data, function(index, listMenuObj){

			isi += '</ul></section>';

			document.getElementById('divMenu').innerHTML = isi;

		}); // ... $.get('/ajax-getmenuitembylevel?level='+levelUser, function(data){

	}

	function changeCheckedMenu(id, menukode){

		// $.get('/ajax-checkcountsubmenu?menukode='+menukode+'&jenis=H', function(data){
		// 	var status 	= 0;
		// 	var i 		= 0;
	 //    	var num 	= 0;

	 //    	num = data;

	 //    	status = ($('#m'+id).prop('checked') ? 1 : 0);

		// 	var hm = '#hm'+id;

		// 	for(i=0; i<=num; i++){
		// 		if(status == 1){
		// 			$(hm+i).prop('checked', true);
		// 			changeCheckedHMenu(id, i, menukode);
		// 		}else{
		// 			$(hm+i).prop('checked', false);
		// 			changeCheckedHMenu(id, i, menukode);
		// 		}
		// 	}
	 //    });
	
	}

	function changeCheckedHMenu(idmenu, idhead, menukode){
		// $.get('/ajax-checkcountsubmenu?menukode='+menukode+'&jenis=M', function(data){
		// 	var status 	= 0;
		// 	var i 		= 0;
		// 	var num 	= 0;

		// 	num = data;

		// 	status = ($('#hmenu'+idmenu+''+idhead).prop('checked') ? 1 : 0);

		// 	var im = '#imenu'+idmenu+''+idhead;

		// 	for(i=0; i<=num; i++){
		// 		if(status == 1){
		// 			$(im+i).prop('checked', true);
		// 		}else{
		// 			$(im+i).prop('checked', false);
		// 		}
		// 	}
		// });
	
	}

</script>	  

@endsection