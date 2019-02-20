function pembulatan(nilai){

	if(parseFloat(nilai).toFixed(2) == 0.00){
		nilai = 0;
	}else{
		nilai = Math.ceil(parseFloat(nilai).toFixed(2)/100)*100;
	}

	return nilai;
}

function getpembulatan(nilai){
	var nilaiPembulatan = 0;
	var riil_nilai 		= 0;

	if(parseFloat(nilai).toFixed(2) == 0.00){
		nilaiPembulatan = 0;
	}else{
		riil_nilai 		= parseFloat(nilai).toFixed(2);
		nilai 			= Math.ceil(parseFloat(nilai).toFixed(2)/100)*100;

		nilaiPembulatan = parseFloat(nilai - riil_nilai).toFixed(2);
	}

	return nilaiPembulatan;
}

// Create ulang pembulatan, disebagian file function pembulatan tidak mau dipanggil
// Mungkin karena nama function sama dengan nama file js
function pembulatanangka(nilai){

	if(parseFloat(nilai).toFixed(2) == 0.00){
		nilai = 0;
	}else{
		nilai = Math.ceil(parseFloat(nilai).toFixed(2)/100)*100;
	}

	return nilai;
}