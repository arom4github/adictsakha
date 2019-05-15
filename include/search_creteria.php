<?php $test = 1;

if(isset($_COOKIE["test"]))
	$test = $_COOKIE["test"];

$dict = $_COOKIE["dict"];
if(isset($_GET["dict"])) $dict = $_GET["dict"];
if($dict !="right" && $dict !="back" && $dict != "list") $dict="none";

$s_cr = new s_criteria_class();
if(isset($_COOKIE["{$dict}_{$test}_s_criteria"])){
    $s_cr->parse($_COOKIE["{$dict}_{$test}_s_criteria"]);
}
//var_dump($_COOKIE);
?>
<form action="">
    <table width=100% border=0 class="criteria">
	<tr><td valign=top align=right rowspan=2><?php echo $locale['sex']; ?>:</td>
		<td valign=top rowspan=2>
			<input type=radio name="sex" value='M' <?php echo($s_cr->sex == 'M')?"checked":""; ?>/><?php echo $locale['sex_m']; ?><br>
			<input type=radio name="sex" value='F' <?php echo($s_cr->sex == 'F')?"checked":""; ?>/><?php echo $locale['sex_f']; ?><br>
			<input type=radio name="sex" value='E' <?php echo($s_cr->sex == 'E' ||
				($s_cr->sex != 'M' && $s_cr->sex != 'F'))?"checked":""; ?>><?php echo $locale['donotcare']; ?>
		</td>
		<td valign="middle" align=right><?php echo $locale['city']; ?>:
		</td>
		<td>
		    <input type=text name="city" value="<?php echo $s_cr->city?>">
		</td>

<!--		<td valign=top align=right><?php echo $locale['edu']; ?>:</td>
		<td valign=top>
			<?php
				$arr = explode(",", $s_cr->edu);
				for($i=1; $i<6; $i++){
					echo "<input type=checkbox name=\"edu\" value=\"{$i}\" ";
					echo ((in_array($i, $arr))?"checked":"") ."> ".$locale['edu_'.$i]."<br>";
				}
			?>
		</td>
-->
		<td valign=top><!-- <input type=checkbox name="base" <?php echo ($s_cr->base)?"checked":"" ?>> Base dict only -->&nbsp;
		</td>
		</tr>
		<tr>
                    <td valign="middle" align=right><?php echo $locale['region']; ?>:</td>
                    <td>
                        <select name="reg">
		<?php 
			$k = -1;
                        $i = 1;
                        while(isset($locale["reg".$i])){
				echo "<option value=\"".($i)."\"";
				if($s_cr->reg == $i) { $k =$i; echo " selected"; }
				echo ">";
				echo $locale["reg".$i];
				echo "</option>";
                                $i++;
			}
			if($k == -1){ echo "<option value=\"0\" selected>{$locale['donotcare']}</optino>";}
			else{ echo "<option value=\"\">{$locale['donotcare']}</optino>";}
		?>
                        </select>
                    </td>
		<td>&nbsp;</td>
		</tr>
		<tr>
<!--
		<td valign="middle" align=right><?php echo $locale['age']; ?>: </td>
		<td valign="middle">
			<?php echo $locale['from']; ?> <input type=text name="age_from" value="<?php echo $s_cr->age_from; ?>" maxlength=2 style="width:40px">
			<?php echo $locale['to'];   ?> <input type=text name="age_to" value="<?php echo $s_cr->age_to; ?>" maxlength=2 style="width:40px">
			
		</td>
-->
		<td valign="middle" align=right><?php echo $locale['spec']; ?>:</td>
		<td valign="middle" colspan=3>
                    	<select name="spec">
                        <?php $rows = db_get_specs_list($test);
                                $k = -1;
                                for($i=0; $i<count($rows); $i++){
                                        echo "<option value=\"{$rows[$i][0]}\"";
                                        if($s_cr->spec == $rows[$i][0]) { $k =$i; echo " selected"; }
                                        echo ">{$rows[$i][0]}</option>";
                                }
                                if($k == -1){ echo "<option value=\"\" selected>{$locale['donotcare']}</optino>";}
                                else{ echo "<option value=\"\">{$locale['donotcare']}</optino>";}
                        ?>
                        </select>
		</td>	
		<td><input type=button value="<?php echo $locale['renew']; ?>" onClick="AdvSearch_r();">
		</td>
		</tr>
<!--
		<tr>
		<td valign="middle" align=right><?php echo $locale['lang']; ?>:</td>
		<td>
			<select name="nl">
		<?php $rows = db_get_lang($test);
			$k = -1;
			for($i=0; $i<count($rows); $i++){
				echo "<option value=\"{$rows[$i][0]}\"";
				if($s_cr->lang == $rows[$i][0]) { $k =$i; echo " selected"; }
				echo ">";
				if(isset($locale[$rows[$i][0]])){
					echo $locale[$rows[$i][0]]." (".$rows[$i][0].")";
				}else{
					echo $rows[$i][0];
				}
				echo "</option>";
			}
			if($k == -1){ echo "<option value=\"\" selected>{$locale['donotcare']}</optino>";}
			else{ echo "<option value=\"\">{$locale['donotcare']}</optino>";}
		?>
		</select>
		</td>
		<td valign="middle" align=right> <?php echo $locale['city']; ?>: 
		</td>
		<td>
		    <input type=text name="city" value="<?php echo $s_cr->city?>"> 
		</td>
                    <td valign="bottom" align="right">
                        <input type=button value="<?php echo $locale['renew']; ?>" onClick="AdvSearch_r();">
                    </td>
                </tr>
-->
	</table>
    <input type="hidden" name="test" value="<?php echo $test; ?>">
    <input type="hidden" name="chr" value="<?php  echo $s_cr->chr; ?>">
	<input type="hidden" name="sort" value="<?php  echo $s_cr->sort; ?>">
</form>

