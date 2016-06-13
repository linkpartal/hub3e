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
    if(div =='addtier' & hiddenval =='1')
    {
        document.getElementById("InfoAddsociete").style.display ='none';
    }
    if(div =='addtier' & hiddenval =='0')
    {
        document.getElementById("InfoAddsociete").style.display ='block';
    }

}

function stringWith (str, prefix) {
    var ustring = str.toUpperCase();
    var uprefix = prefix.toUpperCase();
    return ustring.indexOf(uprefix) >= 0;
}

function ShowWaiter(){
    var div = document.createElement("div");
    div.setAttribute('class','popup');
    div.style.display = 'flex';
    div.style.top = '0';
    div.style.left = '0';
    div.setAttribute("id","patienter458275245");
    var div1 = document.createElement("div");
    div1.setAttribute('class','close');
    div1.style.display = 'flex';

    var img = document.createElement('img');

    img.src="data:image/gif;base64,R0lGODlhyAAUAMQfANji7cjW5sDQ49Dc6qS1yWB4lY6ftHeLpEplhr3N3bvF0pWuycrX5O/z+ODo8fb4+/z8/efu9B0+Z3qau+Tq7+ju9fDz9/n7/PX3+NDX4K/C1niYuhk7ZbDF3K/E2////yH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcDpDcmVhdGVEYXRlPSIyMDE0LTA3LTE3VDExOjQwOjU3KzA2OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNC0wNy0xN1QxMTo0OTozMyswNjowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNC0wNy0xN1QxMTo0OTozMyswNjowMCIgZGM6Zm9ybWF0PSJpbWFnZS9naWYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjBCQUQ2NUIwRDc2MTFFNEJEQzVBQ0E1NkY5RjI1RjQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjBCQUQ2NUMwRDc2MTFFNEJEQzVB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";
    img.style.margin = 'auto';
    img.style.height = '20px';
    div1.appendChild(img);
    div.appendChild(div1);
    document.body.appendChild(div);
}
function DeleteWaiter(){
    document.getElementById('patienter458275245').parentNode.removeChild(document.getElementById('patienter458275245'));
}

function NaviguerOnglet(DivOnglet,OngletToShow,button){
    if(button){
        var buttons = button.parentElement.getElementsByTagName("button");
        for(var i = 0;i <buttons.length; i++){
            buttons[i].className = buttons[i].className.replace("active","");
        }
        button.className += " active";

        if(stringWith(button.className,'NavButtonIframe')){sessionStorage.setItem('onglet_tier',button.getAttribute('id'));}
    }

    var divs = document.getElementById(DivOnglet).children;
    if ( $('#'+DivOnglet).parents("form").length == 1 ) {

        for( i = 0; i <divs.length ;i++){
            divs[i].style.visibility= 'hidden';
            divs[i].style.position= 'absolute';
        }
        document.getElementById(OngletToShow).style.visibility= 'visible';
        document.getElementById(OngletToShow).style.position= 'static';

    } else {

        for( i = 0; i <divs.length ;i++){
            divs[i].style.display = 'none';
        }
        document.getElementById(OngletToShow).style.display = 'block';
    }
}