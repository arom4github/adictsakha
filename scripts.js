
function ch_right_dict(){
        ch_right_dict_(0);
}
function ch_back_dict(){
        ch_back_dict_(0);
}
function AdvSearch_r(){
        ch_right_dict_(1);
}
function AdvSearch_b(){
        ch_back_dict_(1);
}
function parse_adv_form(){
    str = "&sex=";
    // search for sex
    for(i=0; i<document.forms[0].sex.length; i++){
            if(document.forms[0].sex[i].checked)
                    str += document.forms[0].sex[i].value;
    }
    // search for age
    //alert(str);
    /*
    age = document.forms[0].age_from.value;
    str += "&af=";
    if(age.length >0){
            if(age > 3 && age <= 90){
                    str += age;
            }else{
                    alert("age_from_error");
                    return "";
            }
    }
    age = document.forms[0].age_to.value;
    str += "&at="
    if(age.length >0){
            if(age > 3 && age <= 90){
                    str += age;
            }else{
                    alert("age_to_error");
                    return "";
            }
    }
    */
    //alert(str);
    // serch for edu
    /*
    checked = 0;
    str += "&edu=";
    for(i=0; i<document.forms[0].edu.length; i++){
            if(document.forms[0].edu[i].checked){
                    if(checked == 0){
                            checked = 1;
                            str += document.forms[0].edu[i].value;
                    }else{
                            str += "," + document.forms[0].edu[i].value;
                    }
            }
    }
    */
    // search for spec
    //spec = document.forms[0].spec.value;
    //if(spec.length >0){
    //        str += "&spec=" + spec.toLowerCase();
    //}
    str += '&spec=';
    str += document.forms[0].spec.value;

    // search for city
    var city_o = document.forms[0].city;
    //alert(city_o);
    //alert(city_o.value);
    //if(city_o.value.length >0){
            str += "&city=" + encodeURIComponent(city_o.value.toLowerCase());
    //}
    //if(document.forms[0].base.checked){
     //       str += "&base=1";
    //}else{
            str += "&base=0";
    //}
    //if(document.forms[0].nl.value != ""){
    //        str += '&nl=';
    //        str += document.forms[0].nl.value;
    //}
    //if(document.forms[0].chr.value != ""){
            str += '&chr=';
            str += encodeURIComponent(document.forms[0].chr.value);
    //}
            str += '&reg=';
            str += document.forms[0].reg.value;

            str += '&sort=';
            str += document.forms[0].sort.value;
    return str;
}

function dict_request(str){
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null){
            alert ("Your browser does not support AJAX!");
            return;
    }
    var url="include/dict_queries.php?"+str;
   // alert(url);
    xmlHttp.onreadystatechange=stateChanged_data;
    xmlHttp.open("GET",url,true);
    //xmlHttp.setRequestHeader("charset", "utf-8");
    xmlHttp.send(null);
}

function chDict(ch){
    document.forms[0].chr.value = ch;
    AdvSearch_r();
}

function chDict_st(from ,to){
	str = 'test=';
	str += document.forms[0].test.value;
	str += parse_adv_form();
	str += '&st=1&srf='+from+'&srt='+to;
	dict_request(str);
}

function chDict_rs(from ,to){
	str = 'test=';
	str += document.forms[0].test.value;
	str += parse_adv_form();
	str += '&rs=1&srf='+from+'&srt='+to;
	dict_request(str);
}

function ch_right_dict_(adv){
    str = 'test=';
    str += document.forms[0].test.value;
    if(adv){
            str += parse_adv_form();
    }// if adv
    dict_request(str);
}

function ch_list_dict_(adv){
    var obj;
    obj = document.getElementById('anketa');
    str = 'test=';
    str += document.forms[0].test.value;
    str += "&ank="+obj.innerHTML;
    
    if(adv){
            str += parse_adv_form();
    }// if adv
    //alert();
    dict_request(str);
}

//function ch_back_dict_(adv){
//    str = '&test=';
//    str += document.forms[0].test.value;
//    if(adv){
//            str += parse_adv_form();
//    }// if adv
//    s_on_add(0, 'back', str);
//}

function edit_city(city){
    var tmp = "";
    tmp = window.prompt("Substitute all '"+city+"' with", city);
    if(tmp != null){
            str = '&test=';
            str += document.forms[0].test.value;
            str +='&act=ch&f='+city+'&t='+tmp;
            s_on_add(3, 'city', str);
    }
}
function edit_region(id, name, cname){
        var obj = document.getElementById("ereg"+id);
        if(edit_reg == 1){
            alert("You should press 'cancel' or  'submit' before continue");
            return;
        }
        edit_reg = 1;

        if(!obj){
            alert("Error1. Ask for administrator support.");
            return;
        }
        str = "";
        for(i=0; i<reg_list.length-1; i++){
            str += "<option value='"+(i+1)+"' ";
            if(reg_list[i] == name) str += "selected";
            str +=">"+reg_list[i]+"</option>";
        }
        str += "<option value='-1'";
        if(reg_list[i] == name) str += "selected";
        str +=">"+reg_list[i]+"</option>";
        str1 = "&test="+document.forms[0].test.value+"&act=chr&f="+cname.replace(/'/g, "\\'")+"&t=";

        obj.innerHTML="<select name='region' onChange=\"s_on_add(3, 'city','"+str1+"'+this.value);\">"+
                        str+"</select> "+
                    "<input type='button' value='Cancel' "+
                            "onClick='document.getElementById(\"ereg\"+"+id+").innerHTML=\""+name+"\"; edit_reg=0;'>";
}
function edit_spec(spec){
        var tmp = "";
        tmp = window.prompt("Substitute all '"+spec+"' with", spec);
        if(tmp != null){
                str = '&test=';
                str += document.forms[0].test.value;
                str +='&act=ch&f='+spec+'&t='+tmp;
                s_on_add(3, 'spec', str);
        }
}

function del_used_keys(){
        str = '&del_keys=1';
        s_on_add(1, 'used', str);
}

function test_create(){
        str = '&lang=';
        str += document.forms[0].lang.value;
        str += '&descr=';
        str += document.forms[0].descr.value;
        s_on_add(2, 'tlist', str);
}

function del_test(id){
        str = '&del_test=1&test=';
        str += id;
        s_on_add(2, 'tlist', str);
}

function gen_keys(){
        str = '&lang=';
        str += document.forms[0].lang.value;
        str += '&num=';
        str += document.forms[0].nkeys.value;
        s_on_add(1, 'unused', str);
}
function ch_cities(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'city', str);
}

function ch_langs(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'lang', str);
}

function ch_specs(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'spec', str);
}

function s_on(id, str){
        s_on_add(id, str, "");
}
function s_on_add(id, str, add){
//        var obj;
//        var m_ids = new Array(
//                                                new Array('right', 'back'),
//                                                new Array('used', 'unused'),
//                                                new Array('tlist', 'tlist'),
//                                                new Array('city', 'lang', 'spec')
//                                        );
//        for(i=0; i<m_ids.length; i++){
//                obj = document.getElementById('sm_'+m_ids[id][i]);
//                if(obj) { obj.className = "smenu_def"; }
//        }
//        obj = document.getElementById('sm_'+str);
//        obj.className = "smenu_act";
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null){
                alert ("Your browser does not support AJAX!");
                return;
        }
        var url="include/dict_queries.php?p="+str + add;
        alert(url);
        xmlHttp.onreadystatechange=stateChanged_data;
        xmlHttp.open("GET",url,true);
        xmlHttp.send(null);
}
function on(str){
        var obj;
        var m_ids = new Array('dict',
            'keys',
            'tests', 'lists');
        var sm_ids =  new Array('right',
            'unused',
            'tlist', 'city');
        var id = 0;
        var str1 = 'unused';
        for(i=0; i<m_ids.length; i++){
                obj = document.getElementById(m_ids[i]);
                obj.style.display = "none";
                obj = document.getElementById('m_'+m_ids[i]);
                obj.className = "menu_def";
                if(str == m_ids[i]){
                        id = i;
                        str1 = sm_ids[i];
                }
        }
        obj = document.getElementById(str);
        obj.style.display = "";
        obj = document.getElementById('m_'+str);
        obj.className = "menu_act";
        s_on(id, str1);
}

function showAdv(){
        var obj;
        obj = document.getElementById('adv_search');
        if(obj){
                if(obj.style.display == 'none')
                        obj.style.display = '';
                else
                        obj.style.display = 'none';
        }
}
function getAnketa(diff){
    var obj1;
    var obj2;
    obj1 = document.getElementById('anketa');
    obj2 = document.getElementById('anketas');
    //alert(obj1);
    //alert(obj2);
    chg = 0;
    if(obj1 && obj2){
        prev = parseInt(obj1.innerHTML);
        nAnk = parseInt(obj2.innerHTML);
        res = prev + diff;
        //alert(res);
        if(res>0 && res <= nAnk){
            obj1.innerHTML = res;
        }
        if(res<=0){
            obj1.innerHTML = "1";
        }
        if(res>nAnk){
            obj1.innerHTML = nAnk;
        }
        if(prev != parseInt(obj1.innerHTML)){
            chg = 1;
        }
    }else{
        alert("Javascript error");
    }
    if(chg){
        ch_list_dict_(1);
    }
}

function getCookie(name) {
        var prefix = name + "="
        var cookieStartIndex = document.cookie.indexOf(prefix)
        if (cookieStartIndex == -1)
                return null
        var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length)
        if (cookieEndIndex == -1)
                cookieEndIndex = document.cookie.length
        return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex))
}

//function setVal(str1){
//    var cookie = getCookie(str1+"_s_criteria");
//    var f = document.forms[0];
//    if(cookie == null) return;
//    var m = cookie.toString().split(":");
//    if(m[0] == "M") f.sex[0].checked = true;
//    if(m[0] == "F") f.sex[1].checked = true;
//    if(m[0] == "E") f.sex[2].checked = true;
//    f.age_from.value = m[1];    f.age_to.value = m[2];
//    var e = m[3].split(",");
//    for(i=0; i<f.edu.length; i++){ f.edu[i].checked = false;}
//    for(i=0; i<e.length; i++)
//       f.edu[e[i]].checked = true;
//    f.spec.value = m[4];
//    f.city.value = m[5];
//    f.nl.value = m[6];
//    f.base.checked = (m[7])?true:false;
//    f.reg.value = m[9];
//}

function my_print(str){
// TODO: styles
   var html = "<html><head>"+
        "<title>Search results</title>"+
        "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"+
        "<script type=\"text/javascript\" language=\"JavaScript1.2\">"+
        "function setVal(cookie){"+
        "var f = document.forms[0];"+
        "if(cookie == null) return;"+
        "var m = cookie.toString().split(\":\");"+
        "if(m[0] == \"M\") f.sex[0].checked = true;"+
        "if(m[0] == \"F\") f.sex[1].checked = true;"+
        "if(m[0] == \"E\") f.sex[2].checked = true;"+
        "f.age_from.value = m[1];    f.age_to.value = m[2];"+
        "var e = m[3].split(\",\");"+
        "for(i=0; i<f.edu.length; i++){ f.edu[i].checked = false;}"+
        "for(i=0; i<e.length; i++)"+
        "   f.edu[e[i]-1].checked = true;"+
        "f.spec.value = m[4];"+
        "f.city.value = m[5];"+
        "f.nl.value = m[6];"+
        //"f.base.checked = (m[7]==1)?true:false;"+
        "f.reg.value = m[9];"+
        "}"+

        "</script>"+
        "</head>"+
        "<body onLoad=\"javascript:setVal('"+getCookie(str+"_s_criteria")+"'); window.print();\">"+
        "<form>"+document.getElementById("s_criteria").innerHTML+"</form>"+
        document.getElementById("results").innerHTML+
        "</body></html>";
    var win = window.open("", "Search results");
    win.document.open();
    win.document.write(html);
    win.document.close();
}

function search_word(){
	document.forms[0].chr.value = document.forms[1].stimul.value;	
	AdvSearch_r();
}
