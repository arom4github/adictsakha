/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function GetXmlHttpObject() {
  var xmlHttp;

  try {
    // Для всех, кроме IE6 и позже
    xmlHttp = new XMLHttpRequest();
  }
  catch(e) {
    var xmlVers = new Array ("MSXML2.XMLHTTP.6.0",
            "MSXML2.XMLHTTP.5.0",
            "MSXML2.XMLHTTP.4.0",
            "MSXML2.XMLHTTP.3.0",
            "MSXML2.XMLHTTP",
            "Microsoft.XMLHTTP");
    // Перебираем возможные варианты, пока не получится
    for (var i=0; i<xmlVers.length && !xmlHttp; i++) {
      try {
        xmlHttp = new ActiveXObject(xmlVers[i]);
      }
      catch(e) {}
    }
  }

  if (!xmlHttp) {
    alert ("Ошибка создания xmlHttp");
    return false;
  }

  return xmlHttp;
}

function GetXmlHttpObject_() {
  var xmlHttp=null;
  try {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  } catch (e) {
    // Internet Explorer
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}

function stateChanged_corp() {
        if (xmlHttp.readyState==1){
                document.getElementById("search_res").innerHTML="Loading...";
        }
        if (xmlHttp.readyState==2){
                document.getElementById("search_res").innerHTML="Loading...1";
        }
        if (xmlHttp.readyState==3){
                document.getElementById("search_res").innerHTML="Loading...2";
        }
        if (xmlHttp.readyState==4){
                var resp = xmlHttp.responseText;
                var obj = document.getElementById("search_res");
		if(obj) obj.innerHTML = resp;
		else alert("Error on page");
        }
}

function stateChanged_texts() {
        if (xmlHttp.readyState==1){
                document.getElementById("search_res").innerHTML="Loading...";
        }
        if (xmlHttp.readyState==2){
                document.getElementById("search_res").innerHTML="Loading...1";
        }
        if (xmlHttp.readyState==3){
                document.getElementById("search_res").innerHTML="Loading...2";
        }
        if (xmlHttp.readyState==4){
                var resp = xmlHttp.responseText;
                var obj = document.getElementById("search_res");
		if(obj) obj.innerHTML = resp;
		else alert("Error on page");
        }
}

