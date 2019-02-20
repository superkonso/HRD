<style type="text/css">
	#myProgress {
	  width: 100%;
	  background-color: #ddd;
	  left: 50%;
	  top: 50%;
	}

	#myProgBar {
	  width: 10%;
	  height: 30px;
	  background-color: #4CAF50;
	  text-align: center;
	  line-height: 30px;
	  color: white;
	}
</style>

<div class="modal fade" id="progressbar" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="progressbar" aria-hidden="true" style="width: 100%; height: 150px; background:transparent; top: 50%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div id="myProgress">
			  <div id="myProgBar">5%</div>
			</div>
			<div class="labelProg" style="text-align: center;">
				<span id="ket">Loading......</span>
				<span id="btn"></span>
			</div>			
        </div>
    </div>
</div>

