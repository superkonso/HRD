<script src="{{ asset('js/sweetalert.min.js') }}"></script>
@if(session()->has('message'))
	<h4>
		{{-- <p class="alert alert-success" id="successAlert">{{session()->get('message')}}</p> --}}
		<script>
			swal({
	            title : '',
	            text  : '{{session()->get('message')}}',
	            icon  : 'success',
	            timer : 3000,
	            closeOnClickOutside : true
	          });
		</script>
	</h4>
@endif

@if(session()->has('validate'))
	<h4>
		<script>
			swal({
	            title : '',
	            text  : '{{session()->get('validate')}}',
	            icon  : 'error',
	            timer : 3000,
	            closeOnClickOutside : true
	          });
		</script>
	</h4>
@endif