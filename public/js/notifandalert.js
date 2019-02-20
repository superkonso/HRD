function showSuccess(tmr, title, msg, closeClick){
	swal({
        title : title,
        text  : msg,
        icon  : 'success',
        timer : tmr,
        closeOnClickOutside : closeClick
      });
}

function showWarning(tmr, title, msg, closeClick){
	swal({
        title : title,
        text  : msg,
        icon  : 'warning',
        timer : tmr,
        closeOnClickOutside : closeClick
      });
}

function showError(tmr, title, msg, closeClick){
	swal({
        title : title,
        text  : msg,
        icon  : 'error',
        timer : tmr,
        closeOnClickOutside : closeClick
      });
}

function showInfo(tmr, title, msg, closeClick){
	swal({
        title : title,
        text  : msg,
        icon  : 'info',
        timer : tmr,
        closeOnClickOutside : closeClick
      });
}