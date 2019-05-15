<?php include("include/init.php");?>
<?php include("include/header.php"); ?>
<div class="top_line">
<!--	<a href="ru"><img border=0 src="<?php echo $basedir; ?>imgs/ru-flag.png" alt="ru"/></a>
	<a href="fr"><img border=0 src="<?php echo $basedir; ?>imgs/fr-flag.png" alt="fr"/></a>
-->
</div>
<div class="menu_bg">
	<div id="navigator">
	<ul id="nav-menu">
		<li <?php if($page=="about"){echo "id=\"nav-menu-act\"";}?>><a href="about"><?php echo $locale['about']; ?></a></li>
		<li <?php if($page=="authors"){echo "id=\"nav-menu-act\"";}?>><a href="authors"><?php echo $locale['authors']; ?></a></li>
		<li <?php if($page=="dict"){echo "id=\"nav-menu-act\"";}?>><a href="dict"><?php echo $locale['dict']; ?></a></li>
		<!-- <li <?php if($page=="experiment"){echo "id=\"nav-menu-act\"";}?>><a href="experiment"><?php echo $locale['experiments']; ?></a></li>  -->
		<li <?php if($page=="corpora"){echo "id=\"nav-menu-act\"";}?>><a href="/corpora/corp">Корпус</a></li> 
		<!-- <li></li> -->
	</ul>
	</div>
</div>
<div class="content">
<?php 
	if( ($page=="about")
		||($page=="authors")
		||($page=="dict")
		||($page=="experiment")){
			include("pages_".$lang."/".$page.".php");
		}
	  else
	  {
	  		include("pages_".$lang."/unknown.php");
	  }
		?>
</div>
<!--<div class="footer">footer</div> -->
<?php include("include/footer.php"); ?>
