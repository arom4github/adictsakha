<?php include('include/dict.js.php'); ?>
<?php
function uni_strsplit($string, $split_length=1)
{
    preg_match_all('`.`u', $string, $arr);
    $arr = array_chunk($arr[0], $split_length);
    $arr = array_map('implode', $arr);
    return $arr;
}
?>
<div class="dict_content">
    <div id="dict_menu">
        <?php
            $right_class = "dict";
            $back_class = "dict";
            $list_class = "dict";

            if($dict == "right"){
                $right_class .= "_act";
            }
            if($dict == "back"){
                $back_class .= "_act";
            }
            if($dict == "list"){
                $list_class .= "_act";
            }
        ?>
		<table border=0 width="100%" cellpadding=0 cellspacing=0>
		<td>
		<table id="nav-tbl">
        <td><a href="dictright" class="<?php echo $right_class; ?>"><?php echo $locale['right']; ?></a></td>
        <td><a href="dictback"  class="<?php echo $back_class; ?>"><?php echo $locale['back']; ?></a></td>
        <td><a href="dictlist"  class="<?php echo $list_class; ?>"><?php echo $locale['anketas']; ?></a></td>
        <td><a href="#" class="dict" onClick="show_modal('db');"><?php echo "Select DB" ?></a></td>
		</table>
		</td>
		<td>
		<img src="imgs/document-print.png" alt="print" align="right" width="20px" border="0"
                 onclick="my_print('<?php echo "{$dict}_{$test}"; ?>');">
		</td>
		</table>
		<?php 
			$rows = db_get_tests();
        		for($i=0; $i<count($rows); $i++){
                		if($rows[$i][3] == $_COOKIE['test']) 
					echo "{$rows[$i][0]} \"{$rows[$i][2]}\" ({$locale[$rows[$i][1]]})";
        		}
		
		?>
    </div>
    <div class="search_criteria" id="s_criteria">
    <form action="" enctype="application/x-www-form-urlencoded">
        <fieldset class="fs">
            <legend class="search_criteria"><?php echo $locale['s_criteria']; ?></legend>
            <?php include 'include/search_creteria.php';?>
        </fieldset>
    </form>
    </div>
    <div class="abc">
	<form action="" onsubmit="search_word(); return false;">
        <?php
        if($dict == "right"){
		?>
		<div id='rabc_order' class='abc_in'>
		<?php
	    $abc = "АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЭЮЯ";
	    if($_COOKIE['test'] == 1) {$abc = "АБДИКМНОӨСТУҮХЧЫЭЮ";}
	    if($_COOKIE['test'] == 2) {$abc = "АБВГДИКЛМНҤОПСТУХЧЭЮЯ";}
	    //$abc1= "абвгдежзийклмнопрстуфхцчшщыэюя";
	    $aabc = uni_strsplit($abc);
	    $len = mb_strlen($abc, "UTF-8");
            for($i=0; $i<$len; $i++){
              //    echo "<span  class=\"abc_link\" onclick=\"chDict('".(ord($abc[2*$i])*256 + ord($abc[2*$i+1]))."')\">".$aabc[$i]."</span>";
                  echo "<span  class=\"abc_link\" onclick=\"chDict('{$aabc[$i]}')\">".$aabc[$i]."</span>";
            }
	?>
	      </div>
              <div id='rword_order' class='abc_in'>
		<?php echo "{$locale['stimul']}:";?> 
		<input type="text" name="stimul" value=""/>
            	<input type="button" value="search" onclick="search_word();">
	      </div>
       
        <?php
	}
	if($dict == "back"){
	?>
			<div id='abc_order' class='abc_in'>
        	<?php   
			//$num = "?0123456789";
            		//for($i=0; $i<strlen($num); $i++){
                  	//	echo "<span  class=\"abc_link\" onclick=\"chDict('".(ord($num[$i]))."')\">".$num[$i]."</span>";
            		//}		
	    		$abc = "?АБВГҔДЕЖЗИЙКЛМНҤОӨПРСҺТУҮФХЦЧШЩЫЭЮЯ";
	   		$aabc = uni_strsplit($abc);
	    		$len = mb_strlen($abc, "UTF-8");
            		for($i=0; $i<$len; $i++){
                  		//echo "<span  class=\"abc_link\" onclick=\"chDict('".(ord($abc[2*$i])*256 + ord($abc[2*$i+1]))."')\">".$aabc[$i]."</span>";
                  		echo "<span  class=\"abc_link\" onclick=\"chDict('{$aabc[$i]}')\">".$aabc[$i]."</span>";
            		}		
	?>
			</div>
            <div id='word_order' class='abc_in'>
				<?php echo "{$locale['resp']}:";?> <input type="text" name="stimul" value=""/>
            	<input type="button" value="search" onclick="search_word();">
			</div>
			<div id='stim_order' class='abc_in'>
				Количество стимулов: 
					<span  class="abc_link" onclick="chDict_st(350, 200);">350-200</span>&nbsp;
					<span  class="abc_link" onclick="chDict_st(199, 150);">199-150</span>&nbsp;
					<span  class="abc_link" onclick="chDict_st(149, 100);">149-100</span>&nbsp;
					<span  class="abc_link" onclick="chDict_st(90, 50);">99-50</span>&nbsp;
					<span  class="abc_link" onclick="chDict_st(49, 1);">49-1</span>&nbsp;
			</div>
			<div id='resp_order' class='abc_in'>
				Количество откликов:
					<span  class="abc_link" onclick="chDict_rs(3000, 2000);">3000-2000</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(1999, 1500);">1999-1500</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(1499, 1000);">1499-1000</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(999, 750);">999-750</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(749, 500);">749-500</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(499, 250);">499-250</span>&nbsp;
					<span  class="abc_link" onclick="chDict_rs(249, 1);">249-1</span>&nbsp;
			</div>
        <?php
	}
        if($dict == "list"){
        ?>
            <span class="abc_link" onClick="getAnketa(-100);">&lt;&lt;&lt;</span>&nbsp;
            <span class="abc_link" onClick="getAnketa(-10);">&lt;&lt;</span>&nbsp;
            <span class="abc_link" onClick="getAnketa(-1);">Пред.</span>&nbsp;
            <span id="anketa">1</span> из <span id="anketas">1</span>&nbsp;
            <span class="abc_link" onClick="getAnketa(+1);">След.</span>&nbsp;
            <span class="abc_link" onClick="getAnketa(+10);">&gt;&gt;</span>&nbsp;
            <span class="abc_link" onClick="getAnketa(+100);">&gt;&gt;&gt;</span>
        <?php
        }
        ?>
            		</form>
    </div>
    <div class="searc_result">
        <fieldset class="fs">
            <legend class="search_result"><?php echo $locale['s_result']; ?></legend>
            <div id="results">
                Выберите первую букву стимула/реакция<br>
            </div>
        </fieldset>
    </div>
</div>
<div id='mask'></div> 
<div id="bdict_selector" class="bdict_selector">
	Выбор порядка представления информации:
	<ul>
		<li><a href="#" name="abc" class="order">В алфавитном порядке реакций</a> 
		<li><a href="#" name="stim" class="order">По количеству стимулов</a>
		<li><a href="#" name="resp" class="order">По количеству реакций</a>
		<li><a href="#" name="word" class="order">По отдельному слову</a>
	</ul>
</div>
<div id="rdict_selector" class="rdict_selector">
	Выбор порядка представления информации:
	<ul>
		<li><a href="#" name="rabc" class="order">В алфавитном порядке реакций</a> 
		<li><a href="#" name="rword" class="order">По отдельному слову</a>
	</ul>
</div>

<div id="db_selector" class="db_selector">
	Выбор базы данных словарей
	<ul>
<?php
	$rows = db_get_tests();
        $test = 0;
        for($i=0; $i<count($rows); $i++){
                echo "<li><a href=\"#\" class=\"db_link\" name=\"{$rows[$i][3]}\">{$rows[$i][0]} \"{$rows[$i][2]}\" ({$locale[$rows[$i][1]]})</a>";
        }
?>		
	</ul>
</div>
