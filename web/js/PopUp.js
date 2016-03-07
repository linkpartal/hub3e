function hide(div)
{

    div.parentNode.style.display = 'none';
    var forms = div.parentNode.getElementsByTagName("form");
    if(forms)
    {
        forms[0].reset();
    }
}
function show(div,hiddenval){
    var popup = document.getElementById(div);

    popup.style.display = 'block';
    if(div =='addtier')
    {
        document.getElementById("EcoleBool").setAttribute("value",hiddenval);
    }
}

function stringWith (str, prefix) {
    var ustring = str.toUpperCase();
    var uprefix = prefix.toUpperCase();
    return ustring.indexOf(uprefix) >= 0;
}