function incTotalRides (fld) {
	//alert("this=" + fld.name);
	
	var fldname = fld.name; // hid5_11-B
	var pair = fldname.split('_');
	var totalrow_fld = document.getElementById('totalrow_' + pair[0]);
	var totalcol_fld = document.getElementById('totalcol_' + pair[1]);
	
	var cntlist = count_X (pair[0], pair[1]);
	//alert ('total=' + cnt);
	//total_fld.value= cnt;
	totalrow_fld.innerHTML = cntlist[0];
	//alert (totalcol_fld.innerHTML);
	var prevhtml = totalcol_fld.innerHTML.split('|')[1];

	totalcol_fld.innerHTML = '<br/>' + cntlist[1] + '|' +  prevhtml;
}

function count_X(hid, sect) {
	var LL = hid.length ;
	var frm = document.forms["drawform"];
	//alert (LL + frm);
	var nn = frm.elements.length;
	var rowcnt  = 0;
	var colcnt  = 0;
	for (jj=0; jj<nn; jj++) {
		var elem = frm.elements[jj];
		if (elem.name.substr(0,LL) == hid) {
			if (elem.value == 'X') {
				rowcnt ++;
			}
		}
		if ((elem.name.split('_'))[1] == sect) {
			if (elem.value == 'X') {
				colcnt ++;
			}
		}
			
	}
	var list = new Array(2);
	list[0] = rowcnt;
	list[1] = colcnt;
	return list;
	return rowcnt;
}

function sectclicked(fld) {
	if (''==fld.value) {
		fld.value='X';	
	} else if ('X' == fld.value) {
		fld.value='A';	
	} else {
		fld.value = '';
	}
	incTotalRides( fld  );
}

function sectkeyup(fld) {
	var val = fld.value.toUpperCase() ;
	if ('X' != val && 'A' != val) {
		val = '';
	} 
	fld.value = val ;
	incTotalRides( fld  );
	
	
}