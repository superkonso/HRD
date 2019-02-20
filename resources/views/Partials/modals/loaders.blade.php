<style type="text/css">
	#loaderc {
	  position: absolute;
	  left: 50%;
	  top: 50%;
	  z-index: 0;
	  width: 150px;
	  height: 150px;
	  margin: 0 0 0 -75px;
	  border: 16px solid #f3f3f3;
	  border-radius: 50%;
	  border-top: 16px solid #3498db;
	  width: 120px;
	  height: 120px;
	  -webkit-animation: spin 2s linear infinite;
	  animation: spin 2s linear infinite;
	}

	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}

	/* Add animation to "page content" */
	.animate-bottom {
	  position: relative;
	  -webkit-animation-name: animatebottom;
	  -webkit-animation-duration: 1s;
	  animation-name: animatebottom;
	  animation-duration: 1s
	}

	@-webkit-keyframes animatebottom {
	  from { bottom:-100px; opacity:0 } 
	  to { bottom:0px; opacity:1 }
	}

	@keyframes animatebottom { 
	  from{ bottom:-100px; opacity:0 } 
	  to{ bottom:0; opacity:1 }
	}

	#myDiv {
	  display: none;
	  text-align: center;
	  background: transparent;
	}


	// Define vars we'll be using
	$brand-success: #5cb85c;
	$loader-size: 8em;
	$check-height: $loader-size/2;
	$check-width: $check-height/2;
	$check-left: ($loader-size/6 + $loader-size/12);
	$check-thickness: 2px;
	$check-color: $brand-success;

	.circle-loader {
	  margin: 0 0 30px 10px;
	  border: $check-thickness solid rgba(0, 0, 0, 0.2);
	  border-left-color: $check-color;
	  animation-name: loader-spin;
	  animation-duration: 1s;
	  animation-iteration-count: infinite;
	  animation-timing-function: linear;
	  position: relative;
	  display: inline-block;
	  vertical-align: top;
	}

	.circle-loader,
	.circle-loader:after {
	  border-radius: 50%;
	  width: $loader-size;
	  height: $loader-size;
	}

	.load-complete {
	  -webkit-animation: none;
	  animation: none;
	  border-color: $check-color;
	  transition: border 500ms ease-out;
	}

	.checkmark {
	  display: none;
	  
	  &.draw:after {
	    animation-duration: 800ms;
	    animation-timing-function: ease;
	    animation-name: checkmark;
	    transform: scaleX(-1) rotate(135deg);
	  }
	  
	  &:after {
	    opacity: 1;
	    height: $check-height;
	    width: $check-width;
	    transform-origin: left top;
	    border-right: $check-thickness solid $check-color;
	    border-top: $check-thickness solid $check-color;
	    content: '';
	    left: $check-left;
	    top: $check-height;
	    position: absolute;
	  }
	}

	@keyframes loader-spin {
	  0% {
	    transform: rotate(0deg);
	  }
	  100% {
	    transform: rotate(360deg);
	  }
	}

	@keyframes checkmark {
	  0% {
	    height: 0;
	    width: 0;
	    opacity: 1;
	  }
	  20% {
	    height: 0;
	    width: $check-width;
	    opacity: 1;
	  }
	  40% {
	    height: $check-height;
	    width: $check-width;
	    opacity: 1;
	  }
	  100% {
	    height: $check-height;
	    width: $check-width;
	    opacity: 1;
	  }
	}
</style>

<div class="modal fade" id="loader" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="loader" aria-hidden="true" style="width: 100%; height: 100vh; background:transparent; padding-top: 20%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div id="loaderc" style="display:initial;"></div>	
			{{-- <div style="display:none;" id="myDiv" class="animate-bottom">
			  <button type="ok" id="oke" data-dismiss="modal" class="btn btn-danger">Close</button>
			</div>	 --}}
			{{-- <div style="display:none;" id="myDiv"> --}}
				<div id="myDiv" class="circle-loader">
				  <div class="checkmark draw"></div>
				</div>
				{{-- <button id="toggle" type="button" class="btn btn-success">Toggle Completed</button> --}}
			{{-- </div> --}}
        </div>
    </div>
</div>
