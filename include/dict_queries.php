<?php

header('Content-type: text/html; charset=utf-8');

$lang = "";
$t_id = 1;
if(isset($_COOKIE["test"]))
	$t_id = $_COOKIE["test"];

if(isset($_COOKIE["pref_lang"])){
	$lang = $_COOKIE["pref_lang"];
}

if(($lang != "ru") && ($lang != "fr")){
	$lang = "ru";
}

require_once 'config.php';
require_once 'db.php';
require_once 's_criteria_class.php';
require_once 'lang_'.$lang.'.php';

$dict = "";
if(isset($_COOKIE["dict"])) $dict = $_COOKIE["dict"];
if(($dict != "right") && ($dict != "back") && ($dict != "list")){
    echo "Error: Check if cookeis is enabled in you brouser\n";
    return;
}

// parse parameters
$s_cr = new s_criteria_class();
if(isset($_COOKIE["{$dict}_{$t_id}_s_criteria"]))
        $s_cr->parse($_COOKIE["{$dict}_{$t_id}_s_criteria"]);

//TODO: check if parameters are valide
if(isset($_GET["sex"])) $s_cr->sex = $_GET["sex"];
if(isset($_GET["af"])) $s_cr->age_from = $_GET["af"];
if(isset($_GET["at"])) $s_cr->age_to = $_GET["at"];
if(isset($_GET["edu"])) $s_cr->edu = $_GET["edu"];
if(isset($_GET["spec"])) $s_cr->spec = $_GET["spec"];
if(isset($_GET["nl"])) $s_cr->lang = $_GET["nl"];
if(isset($_GET["city"])) $s_cr->city = $_GET["city"];
//if(isset($_GET["base"])) $s_cr->base = $_GET["base"];
if(isset($_GET["chr"])) $s_cr->chr = $_GET["chr"];
if(isset($_GET["nat"])) $s_cr->reg = $_GET["nat"];
if(isset($_GET["sort"])) $s_cr->sort = $_GET["sort"];
if(isset($_GET["st"])) $s_cr->sr = 1;
if(isset($_GET["rs"])) $s_cr->sr = 2;
if(isset($_GET["srf"])) $s_cr->srf = $_GET["srf"];
if(isset($_GET["srt"])) $s_cr->srt = $_GET["srt"];

if($s_cr->chr == '') $s_cr->chr = 'a';

$anketa = 1;
if(isset($_GET["ank"])) $anketa = $_GET["ank"];
$anketa = intval($anketa);

//var_dump($_COOKIE);
setcookie("{$dict}_{$t_id}_s_criteria", $s_cr->cookie(), 0, "/");
//echo "{$dict}_{$t_id}_s_criteria = ".$s_cr->cookie();
//var_dump($_GET);

//var_dump($_GET);
if($dict == "right")
    getRightDict($t_id, $s_cr->sex, $s_cr->age_from, $s_cr->age_to, $s_cr->edu, $s_cr->spec, $s_cr->city, $s_cr->base, $s_cr->lang, $s_cr->chr, $s_cr->nat, $s_cr->sort);
if($dict == "back")
    getBackDict($t_id, $s_cr->sex, $s_cr->age_from, $s_cr->age_to, $s_cr->edu, $s_cr->spec, $s_cr->city, $s_cr->base, $s_cr->lang, $s_cr->chr, $s_cr->nat, $s_cr->sort, $s_cr->sr, $s_cr->srf, $s_cr->srt);
if($dict == "list")
    getAnketa($t_id, $s_cr->sex, $s_cr->age_from, $s_cr->age_to, $s_cr->edu, $s_cr->spec, $s_cr->city, $s_cr->base, $s_cr->lang, $anketa, $s_cr->nat);
// query functions


function getRightDict($t_id, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_crt){
global $locale;
?>
<table width="100%" border=1 class="result">
    <tr><td>&nbsp;</td>
        <td width=150px><b><?php echo $locale['stimul']; ?></b> <img src="imgs/sort.png" alt="sort" border="0" class="sort" onClick="document.forms[0].sort.value=1; AdvSearch_r();"></td>
        <td><b><?php echo $locale['resp']; ?></b> <img src="imgs/sort.png" alt="sort" border="0" class="sort" onClick="document.forms[0].sort.value=0; AdvSearch_r();"></td></tr>
<?php                                                                                                                                                  
        $rows = db_right_dict($t_id, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_crt);
        for($i=0; $i<count($rows); $i++){                                                                                                              
                echo "<tr>{".($i+1)."}{{$rows[$i][0]}}{{$rows[$i][2]}}</tr>\n";                                                                          
        }                                                               
?>                                                                                                                                                     
</table>
<?php
}

function getBackDict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_crt, $sr, $srf, $srt){
global $locale;
$click = "AdvSearch_r();";
if($sr == "1")
	$click = "chDict_st($srf, $srt)";
if($sr == "2")
	$click = "chDict_rs($srf, $srt)";
?>
<table width="100%" border=1 class="result">
<tr><td>&nbsp;</td><td><b><?php echo $locale['resp']; ?></b><img src="imgs/sort.png" alt="sort" border="0" class="sort" onClick="document.forms[0].sort.value=1; <?php echo $click; ?>"></td>
<td><b><?php echo $locale['stimul']; ?></b><img src="imgs/sort.png" alt="sort" border="0" class="sort" onClick="document.forms[0].sort.value=0; <?php echo $click; ?>"></td>
<?php
        $rows = db_back_dict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $nat, $sort_crt, $sr, $srf, $srt);

        for($i=0; $i<count($rows); $i++){
                echo "<tr>{".($i+1)."}{{$rows[$i][3]} {$rows[$i][0]}}{{$rows[$i][2]}}</tr>\n";
        }
?>                     
</table>
<?php
}

function getAnketa($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $ank, $nat){
    global $locale;
    $rows = db_get_user_ank($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $ank-1, $nat);
    //echo "db_get_user_ank($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, ".($ank-1).");";
    if(count($rows)<1){
        echo "<!-- 0 --> <!-- 0 --> No data available!";
        return;
    }
    if($rows[0][1] == 'm'){
        echo "<img src=\"imgs/male.png\" width=\"70px\" height=\"70px\" align=\"left\" alt=\"Male\">";
    }
    if($rows[0][1] == 'f'){
        echo "<img src=\"imgs/female.png\" width=\"70px\" height=\"70px\" align=\"left\" alt=\"Female\">";
    }
    if($rows[0][1] == 'u'){
        echo "<img src=\"imgs/sex-unknown.png\" width=\"70px\" height=\"70px\" align=\"left\" alt=\"unknown\">";
    }

    echo "<b>Откуда родом</b>:  {$rows[0][4]} <br>
	  <b>Национальность</b>: {$locale["nat".$rows[0][5]]}<br>
          <b>{$locale['lang']}</b>: ";
    if(isset($locale[$rows[0][3]])){
	echo $locale[$rows[0][3]];
    }else{
	echo $rows[0][3];
    }
    echo "<br>
          <b>Факультет</b>: {$rows[0][6]}<br>
	  <b>Женат\Замужем</b>: {$locale["m_st".$rows[0][2]]}<br>\n";
    //      <b>{$locale['edu']}</b>: {$locale['edu_'.$rows[0][5]]}";
    echo "<!-- {$ank} -->";
    echo "<!-- {$rows[1][0]} -->";
    echo "<table width=\"50%\" border=1 class=\"result\">
        <tr><td width=\"50px\">&nbsp;</td><td><b>{$locale['stimul']}</b></td><td><b>{$locale['resp']}</b></td>
		<!--<td><b>Base dict</b></td>--></tr>\n";

    for($i=2; $i<count($rows); $i++){
          echo "<tr><td>".($i-1)."</td><td>{$rows[$i][2]}</td><td>{$rows[$i][0]}</td></tr>";//<td>{$rows[$i][3]}</td></tr>";
    }
    echo "</table>";
}
?>
