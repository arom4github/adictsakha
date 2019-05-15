<?php



$Last_key_lang = "";
$Test_id = "";

function connect($host, $port, $db, $user, $pass, $enc){
	$conn = pg_connect("host={$host} port={$port} dbname={$db} user={$user} password={$pass}");
	return $conn;
}

function disconnect($conn){
	@pg_close($conn);
}

function db_check_key($key){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	global $Last_key_lang;
	global $Test_id;
	$Test_id = 1;
	$Last_key_lang = 'fr';
	return 0;
	
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "select lang, id from keys where used='false' and key='{$key}'");
		if(!$result) return 1;
		if(pg_numrows($result) != 1){
			disconnect($conn);
			return 2;
		}
		$res = pg_fetch_array($result, 0);
		$id = $res[1];
		$Last_key_lang = $res[0];
		$result = @pg_exec ($conn, "select max(id) from tests where lang='{$Last_key_lang}'");
		if(!$result) return 1;
		if(pg_numrows($result) != 1){
			disconnect($conn);
			return 3;
		}
		$res = pg_fetch_array($result, 0);
		$Test_id = $res[0];	
		if($Test_id == ""){
			disconnect($conn);
			return 3;
		}
		$result = @pg_exec ($conn, "update keys set used='true' where id={$id}");
		if(!$result) {disconnect($conn); return 1;}
		$cmdtuples = pg_cmdtuples ($result);
		pg_freeresult($result);
		if($cmdtuples == 1) {
			disconnect($conn); 
			return 0;
		}
		disconnect($conn);
		return 2;
	}else{
		return 1;
	}
	return 3;
}

function db_save_words($words){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	global $_SESSION;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	$new_user = "";
	$new_words = "";
	
	$new_user = "INSERT into users (age, sex, edu, spec, lang_n, key, city, id_t, nationality) ".
				"Values ('{$_SESSION['age']}', {$_SESSION['sex']}, {$_SESSION['edu']}, '{$_SESSION['spec']}', ".
					"'{$_SESSION['lang_n']}', '{$_SESSION['key']}', '{$_SESSION['city']}', {$_SESSION['test_id']}, {$_SESSION['nationality']});";
	write_log($new_user);
	preg_match_all("|([0-9]+):([^;]+);|", $_POST['words'], $out, PREG_PATTERN_ORDER);
	for($i=0; $i<count($out[0]); $i++){
			write_log("INSERT into resp (id_w, id_u, word) VALUES ({$out[1][$i]}, lid, '{$out[2][$i]}');");
	}
	
	
	if($conn){
		preg_match_all("|([0-9]+):([^;]+);|", $_POST['words'], $out, PREG_PATTERN_ORDER);
		$result = @pg_exec ($conn, "BEGIN WORK");
		if(!$result){disconnect($conn); return 5;}
		pg_freeresult($result);
		
		$str = "INSERT into users (age, sex, edu, spec, lang_n, key, city, id_t, nationality) ".
				"Values ('{$_SESSION['age']}', {$_SESSION['sex']}, {$_SESSION['edu']}, '{$_SESSION['spec']}', ".
					"'{$_SESSION['lang_n']}', '{$_SESSION['key']}', '{$_SESSION['city']}', {$_SESSION['test_id']}, {$_SESSION['nationality']})";
		//echo $str;
		$result = @pg_exec ($conn, $str);
		if(!$result){ @pg_exec ($conn, "ROLLBACK"); disconnect($conn); return 4;}
		
		$result = @pg_exec ($conn, "select max(id) from users where key='{$_SESSION['key']}'");
		if(pg_numrows($result) != 1){
			@pg_exec ($conn, "ROLLBACK");
			disconnect($conn);
			return 1;
		}
		$res = pg_fetch_array($result, 0);
		$lid = $res[0];	
		
		for($i=0; $i<count($out[0]); $i++){
			$str = "INSERT into resp (id_w, id_u, word) ";
			$str .= "VALUES ({$out[1][$i]}, {$lid}, '".trim($out[2][$i])."')";
			$result = @pg_exec ($conn, $str);
			if(!$result){ @pg_exec ($conn, "ROLLBACK"); disconnect($conn); return 2;}
			pg_freeresult($result);
		}
		$result = @pg_exec ($conn, "COMMIT");
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_get_words($lang){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select id, word from dict where lang='{$lang}' order by random() limit 100");
		//$result = @pg_exec ($conn, "select id, word from 
		//				(select dict.id as id, dict.word as word, count(resp.word) as cnt from 
		//				    resp inner join dict on dict.id=resp.id_w 
		//					where dict.lang='{$lang}' 
		//					group by dict.word, dict.id order by cnt limit 500) 
		//				as foo order by random() limit 100");
		if(!$result){
		    write_log("GET_WORDS: no words available");
		    disconnect($conn); 
		    return Array();
		}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		write_log("GET_WORDS: ".pg_numrows($result)." selected");
		return $res;
	}else{
		write_log("GET_WORDS: db connection error");
		return Array();
	}
	return Array();
}

function db_get_unused_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select key, lang from keys where used='F'");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();

}

function db_get_used_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select key, lang from keys where used='T'");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();

}
function db_edit_city($test, $from, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set city='{$to}' where city='{$from}' and id_t={$test}");
		//echo( "update users set city=lower('{$to}') where lower(city)='{$from}' and id_t={$test}");
		//pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}
/*
function db_edit_region($test, $for, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set region={$to} where city='{$for}' and id_t={$test}");
		//write_log("");
		//echo( "update users set city=lower('{$to}') where lower(city)='{$from}' and id_t={$test}");
		//pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_edit_spec($test, $from, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set spec=lower('{$to}') where lower(spec)='{$from}' and id_t={$test}");
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}
function db_gen_keys($lang, $num){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		for($i=0; $i<$num; $i++){
			$result = @pg_exec ($conn, "insert into keys (lang, key) values('{$lang}',substring(md5(RANDOM()),1,7));");
			pg_freeresult($result);
		}
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_del_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		
		$result = @pg_exec ($conn, "delete from keys where used='t';");
		pg_freeresult($result);
		
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}
*/
function db_get_tests(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "Select dstart, lang, description, tests.id, count(tests.id) ".
					    "from users left join tests on  users.id_t = tests.id ".
					    "group by  tests.id, tests.description, tests.dstart, ".
					    "tests.active, tests.lang order by dstart");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_get_lang($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select lower(lang_n) as a, count(lang_n) as c from users where id_t={$test} group by a order by c desc");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}


function db_get_specs($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "select lower(spec) as a, count(spec) as c from users where id_t={$test} group by a order by c desc, a");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}
function db_get_specs_list($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "select lower(spec) as a from users where id_t={$test} group by a order by a");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}
function db_get_cities($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "select city as a, count(city) as c, from users where id_t={$test} group by a, order by c desc, a");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_test_create($lang, $descr){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
			$description = addslashes($descr);
			$result = @pg_exec ($conn, "insert into tests (lang, dstart, description) values ".
					"('{$lang}', now(), '{$description}');");
			pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_test_delete($id){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "delete from tests where id={$id}");
		pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function ccmp($aa, $bb){
	preg_match("/^((un|une|le|la|les) )?(.*)$/i", $aa[0], $a);
	preg_match("/^((un|une|le|la|les) )?(.*)$/i", $bb[0], $b);
	
	$res = strcasecmp($a[3], $b[3]);
	return $res;
}

function rcmp($aa, $bb){
	if($aa[1] == $bb[1]) return 0;
	return ($aa[1] < $bb[1])? 1: -1;
}

function db_right_dict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_order){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
//		if(strlen($af)>0) $search .= "AND users.age > $af ";
//		if(strlen($at)>0) $search .= "AND users.age < $at ";
//		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
                if(strlen($chr)>0) {
		   preg_match_all('`.`u', mb_strtolower($chr, "UTF-8"), $arr);
		   $arr = array_chunk($arr[0], 1);
		   $arr = array_map('implode', $arr);

		      if($arr[0] == 'е' && strlen($chr) == 2){
			$search .= "AND  lower(dict.word) SIMILAR  TO '(е|ё)%'  ";
		      }else{
			$search .= "AND lower(dict.word) like '".mb_strtolower($chr, "UTF-8")."%' ";
		      }

                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";
                if($nat != 0) $search.= "AND users.nationality = {$nat} ";
		
		$req = "select resp.word as rw, dict.word, count(resp.word) as cnt 
						from resp inner join dict on dict.id=resp.id_w  
						inner join users on users.id=resp.id_u 
						where dict.test={$test} {$search}
						group by dict.word, rw 
						order by dict.word, cnt desc, rw;";
 		//echo $req."<br>-{$city}-<br>";
		$result = pg_exec ($conn, $req); 
		if(!$result){disconnect($conn); return Array();}
		$str = "";
		$word = "";
		$num = -1;
		$cnt = Array(0,0,0,0);
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
			if($word == ""){
				$word = $arr[1];
				if($arr[0] != '-'){
					$str = $arr[0];
					$num = $arr[2];
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$cnt[2] = ($arr[2] == 1)?1:0;
					$cnt[3] = 0;
				}else{
					$str = "";
					$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
					$num = $arr[2];
				}
			}else{
				if($word == $arr[1]){
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){
							$str .= " \\{$num}\\; ";
						}
						$str .= ", $arr[0]";
						$cnt[0] += $arr[2];
						$cnt[1] += 1;
						$cnt[2] += ($arr[2] == 1)?1:0;
						$num = $arr[2];
					}else{
						$cnt[0] += $arr[2];
						$cnt[3] += $arr[2];
						$num = $arr[2];
					}
				}else{
					$str = preg_replace("/^, /", "", $str);
					$str = preg_replace("/; , /", "; ", $str);
					$str .= " \\{$num}\\";
					array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
					$num = -1;
					$word = $arr[1];
					$str = "";
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){
							$str .= " \\{$num}\\; ";
						}
						$str .= ", $arr[0]";
						$cnt[0] = $arr[2];
						$cnt[1] = 1;
						$cnt[2] = ($arr[2] == 1)?1:0;
						$cnt[3] = 0;
						$num = $arr[2];
					}else{
						$str = "";
						$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
						$num = $arr[2];
					}
				}
			}
		}
		$str = preg_replace("/^, /", "", $str);
		$str = preg_replace("/; , /", "; ", $str);
		$str .= " \\{$num}\\";
		if($word != "") array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
		if($sort_order==0) {
			usort($res, "rcmp");
		}else{
			usort($res, "ccmp");
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}


function db_back_dict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_order, $sr, $srf, $srt){
//function db_back_dict($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		$search1 = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		//if(strlen($af)>0) $search .= "AND users.age > $af ";
		//if(strlen($at)>0) $search .= "AND users.age < $at ";
		//if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if($sr == "0"){
                 if(strlen($chr)>0) {
		   preg_match_all('`.`u', mb_strtolower($chr, "UTF-8"), $arr);
		   $arr = array_chunk($arr[0], 1);
		   $arr = array_map('implode', $arr);

		   if($arr[0] == '?'){
		      $search .= "AND lower(resp.word) NOT SIMILAR TO 
					'(а|б|в|г|ҕ|д|е|ё|ж|з|и|й|к|л|м|н|ҥ|о|ө|п|р|с|һ|т|у|ү|ф|х|ц|ч|ш|щ|ы|э|ю|я)%' ";
		   }else{
		      if($arr[0] == 'е' && strlen($chr) == 2){
			$search .= "AND  lower(resp.word) SIMILAR  TO '(е|ё)%'  ";
		      }else{
		   	$search .= "AND lower(resp.word) like '".mb_strtolower($chr, "UTF-8")."%' ";   
		      }
		   }
		
	        }
			}else{
	//			if($sr == 1){ // stimulus count are limited
	//				$search1 .= "HAVING ((count(dict.word) <= {$srf}) AND (count(dict.word) >= {$srt})) ";
	//			}
			}
        	if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
		//Commented base dict
		//if($base == 1) $search.= "AND dict.base='T' ";
                if($nat != 0) $search.= "AND users.nationality = {$nat} ";
		//$search .= "AND resp.checked='f' ";
	
		//$result = pg_exec ($conn, "select dict.word, lower(resp.word) as rw, count(dict.word) as cnt 
		$req = "select dict.word, resp.word as rw,  count(dict.word) as cnt, resp.checked as ch 
							from resp inner join dict on dict.id=resp.id_w  
								inner join users on users.id=resp.id_u 
							where dict.test={$test} and resp.word<>'-' {$search} 
							group by rw, dict.word, ch {$search1} order by rw, cnt desc, dict.word;";
//		echo $req;
		$result = pg_exec ($conn, $req);
		if(!$result){disconnect($conn); return Array();}
		$str = "";
		$word = "";
		$chk = 0;
		$cnt = Array(0,0);
		$num = -1;
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
			if($word == ""){
				$num = $arr[2];
				$word = $arr[1];
				$chk = $arr[3];
				$str = "$arr[0]";
				$cnt[0] = $arr[2];
				$cnt[1] = 1;
			}else{
				if($word == $arr[1]){
					if(($num != $arr[2]) && ($str !="")){
						$str .= " \\{$num}\\; ";
					}
					$str .= ", $arr[0]";
					$cnt[0] += $arr[2];
					$cnt[1] += 1;
					$num = $arr[2];
				}else{
					$str .= " \\{$num}\\; ";
					$str = preg_replace("/; , /", "; ", $str);
					if($sr == "0") 
						array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					if($sr == 1 && $cnt[1] >= $srt && $cnt[1] <= $srf)
						array_push($res, Array($word, $cnt[1],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					if($sr == 2 && $cnt[0] >= $srt && $cnt[0] <= $srf)
						array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					$word = $arr[1];
					$chk = $arr[3];
					$str = $arr[0];
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$num = $arr[2];
				}
			}
		}
		$str .= " \\{$num}\\; ";
		$str = preg_replace("/; , /", "; ", $str);
		if($word != ""){ 
			if($sr == "0") 
				array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
			if($sr == 1 && $cnt[1] >= $srt && $cnt[1] <= $srf)
				array_push($res, Array($word, $cnt[1],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
			if($sr == 2 && $cnt[0] >= $srt && $cnt[0] <= $srf)
				array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
		}
		if($sort_order == 0) {	
			usort($res, "rcmp");
		}else{
			usort($res, "ccmp");
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_get_user_ank($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $ank, $nat){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND lower(users.lang_n) = '{$nl}' ";
		//if(strlen($af)>0) $search .= "AND users.age > $af ";
		//if(strlen($at)>0) $search .= "AND users.age < $at ";
		//if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
                if($nat != 0) $search.= "AND users.nationality = {$nat} ";
                
		//$result = @pg_exec ($conn, "select id, sex, spec, lang_n, age, edu, city, region from users
                //                                where users.id_t={$test} {$search}
                //                                order by users.id limit 1 offset {$ank};");
		$result = pg_exec ($conn, "select id, sex, m_status, lang_n, city, nationality, faculty from users
                                                where users.id_t={$test} {$search}
                                                order by users.id limit 1 offset {$ank};");
                $userid = "";
		if(!$result){disconnect($conn); return Array();}
		$arr = Array();
		if( pg_numrows($result) == 1){
			$arr = pg_fetch_array($result, $i);
                        array_push($res, $arr);
                        $userid = $arr[0];
		}else{
                    return Array();
                }
                $result = @pg_exec ($conn, "select count(id) from users where users.id_t={$test} {$search};");
                if( pg_numrows($result) == 1){
			$arr = pg_fetch_array($result, $i);
                        array_push($res, $arr);
		}else{
                    return Array();
                }
		$result = @pg_exec ($conn, "select resp.word, resp.checked, dict.word, dict.base
                                                from resp inner join users on users.id = resp.id_u
                                                          inner join dict on resp.id_w=dict.id
                                                where users.id={$userid};");
                for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
                        array_push($res, $arr);
                }
		return $res;
	}else{
		return Array();
	}
	return Array();
}

?>
