<script type="text/javascript" language="javascript">
    function do_onLoad(){
    }
    function add_ch(str){
	var obj = document.getElementById('i_text');
	if(obj == null) {alert('Some error. Try to reload page'); return;}
	obj.value = obj.value + str;
    }
    
    function corp_request(str){
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null){
                alert ("Your browser does not support AJAX!");
                return;
        }
        var url="/corpora/include/corp_queries.php?s="+str;
        xmlHttp.onreadystatechange=stateChanged_corp;
        xmlHttp.open("GET",url,true);
        //xmlHttp.setRequestHeader("charset", "utf-8");
        xmlHttp.send(null);
    }
    function get_list(){
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
                alert ("Your browser does not support AJAX!");
                return;
        }
        var url="/corpora/include/texts_queries.php";
        xmlHttp.onreadystatechange=stateChanged_texts;
        xmlHttp.open("GET",url,true);
        xmlHttp.send(null);
    }
    function do_search(){
	var obj = document.getElementById('i_text');
	if(obj == null) {alert('Some error. Try to reload page'); return;}
	if(obj.value == "") {alert('Введите слово'); return;}
//	alert('searching for "'+obj.value+'"');
	corp_request(obj.value);
    }
</script>
<div id="search_area">
	<form method="get" onSubmit="do_search(); return false;">
	<table border=0 width="100%">
	<tr>
		<td>
		<input type="button" class="add_button" value="ҕ" onClick="add_ch('ҕ')">
		<input type="button" class="add_button" value="ҥ" onClick="add_ch('ҥ')">
		<input type="button" class="add_button" value="ү" onClick="add_ch('ү')">
		<input type="button" class="add_button" value="ө" onClick="add_ch('ө')">
		<input type="button" class="add_button" value="һ" onClick="add_ch('һ')">
		</td>
	</tr>
	<tr><td>
	Слово:  <input type="text" id="i_text">
		<input type="submit" value="Найти">
	</td></tr>
	<tr><td><input type="button" class="list_button" value="список текстов" onClick="get_list();"></td></tr>
	</table>
	</form>
</div>
<div id="search_res">
</div>

