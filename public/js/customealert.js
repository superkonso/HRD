function closeDialog(modalid){
      $('#'+modalid).modal('hide');
    }

function showDialog(modalid, msg){
    document.getElementById('msgModal').innerHTML = msg;
    $('#'+modalid).modal('show');
}
