function validate(fld) {
	
	var tags = fld.name.split('_');
	
	var studid = tags[1];
	var clsid = tags[2];
	var hidid = 'c_' + studid;
	
	var clsarr = document.getElementById(hidid).value.split('-');
	var nn = clsarr.length;
	for (jj=0; jj<nn; jj++) {
		if (clsarr[jj]==clsid) return true;
	}

	return false;
}

function clsclicked(fld) {
	
	if (''==fld.value) {		
		
		if (validate(fld)) {
			
			fld.value='X';	
		} else {
			//alert ("Student is not eligible for this class");
		}
	} else if ('X' == fld.value) {
		fld.value='';	
	} 
	
}

function clskeyup(fld) {
//	alert('keyup');
//	var e = window.event;
//	if(e.keyCode == 9) return; // tab

	if (fld.value=='') return;
	var val = fld.value.toUpperCase() ;

		if (validate(fld)) {
			fld.value='X';	
		} else {
			fld.value='';
			//alert ("Student is not eligible for this class");
		}
	
	//fld.value = val ;

}