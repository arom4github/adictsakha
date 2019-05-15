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

function stateChanged_data() {
        if (xmlHttp.readyState==1){
                document.getElementById("results").innerHTML="Loading...";
        }
        if (xmlHttp.readyState==2){
                document.getElementById("results").innerHTML="Loading...1";
        }
        if (xmlHttp.readyState==3){
                document.getElementById("results").innerHTML="Loading...2";
        }
        if (xmlHttp.readyState==4){
                var resp = xmlHttp.responseText.replace(/:([0-9]+)/g, "&nbsp;($1)");
                resp = resp.replace(/{f ([^{}]+)}/g, "<td valign=\"top\" class=\"nchk\">$1</td>");
                resp = resp.replace(/{t ([^{}]+)}/g, "<td valign=\"top\">$1</td>");
                resp = resp.replace(/{([^{}]+)}/g, "<td valign=\"top\">$1</td>");
                resp = resp.replace(/\\([0-9]+)\\/g, "<b>$1</b>");
                resp = resp.replace(/\|/g, "<br>");
                document.getElementById("results").innerHTML=resp;
//              alert(xmlHttp.responseText.length+":"+resp.length);
                resp = xmlHttp.responseText.match(/<!-- ([0-9]+) -->/g);
                var obj = document.getElementById("anketas");
                if(obj) obj.innerHTML=resp[1].toString().match(/[0-9]+/);
                //alert(resp);
                obj = document.getElementById("anketa");
                if(obj) obj.innerHTML=resp[0].toString().match(/[0-9]+/);
        }
//        edit_reg=0;
}

