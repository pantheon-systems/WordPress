function nice_r_toggle(pfx, id){
    var el = document.getElementById(pfx+'_v'+id);
    if(el){
        if(el.style.display==='block'){
            el.style.display = 'none';
            document.getElementById(pfx+'_a'+id).innerHTML = '&#9658;';
        }else{
            el.style.display = 'block';
            document.getElementById(pfx+'_a'+id).innerHTML = '&#9660;';
        }
    }
}