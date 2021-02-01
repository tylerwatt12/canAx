<?php
$msMenuItem[] =	Array('href' => '#', 'onClick' => '$showMenu = 0;', 'value' => 'Cancel');
$msMenuItem[] =	Array('href' => '#', 'onClick' => 'window.location.href=window.location.href', 'value' => 'Refresh');
$msMenuItem[] =	Array('href' => '/sysctl.php?cmd=shutdown', 'onClick' => '', 'value' => 'Power Off');
$msMenuItem[] =	Array('href' => '/sysctl.php?cmd=reboot', 'onClick' => '', 'value' => 'Reboot');
$msMenuItem[] =	Array('href' => '/?page=timer', 'onClick' => '', 'value' => 'Mode: Timer');
$msMenuItem[] =	Array('href' => '/?page=modern', 'onClick' => '', 'value' => 'Mode: Modern');
$msMenuItem[] =	Array('href' => '/?page=f1', 'onClick' => '', 'value' => 'Mode: F1');
$msMenuItem[] =	Array('href' => '/?page=debug', 'onClick' => '', 'value' => 'Mode: Debug');
$msMenuItem[] =	Array('href' => '/sysctl.php?cmd=flip', 'onClick' => '', 'value' => 'Flip');
$msMenuItem[] =	Array('href' => '/sysctl.php?cmd=rec', 'onClick' => '', 'value' => 'Record');
$msMenuItem[] =	Array('href' => '/sysctl.php?cmd=can', 'onClick' => '', 'value' => 'Emulate/Live');

?>
<!-- CONTROLLER MODULE UI -->
<div id="ms-overlay">
	<a id="ms-titlebar" class="ms-overlay-button"></a>
	<a class="ms-overlay-button" href="/">RELOAD</a>
	<a class="ms-overlay-button" onclick="$showMenu = 1;" ontouchevent="$showMenu = 1;">MENU</a>
</div>
<div id="mode-switcher">
	<div>
		<a id="ms-menuTitle">Main Menu</a>
		<h2>
			<?php
				$msMenuItem = array_reverse($msMenuItem,TRUE);
				foreach ($msMenuItem as $ID => $arr) {
					echo "<a ";
					if(!empty($arr['onClick'])){ echo "onClick='{$arr['onClick']}' ";}
					echo"href='{$arr['href']}' id='ms-menuItem{$ID}' class='ms-menuItem'>{$arr['value']}</a><br>\n";
				}
			?>
		</h2>
	</div>
	<div id="ms-touchControls">
		<h1 onclick="msCurrentSettingCtl('+');" class="ms-touchControl">+</h1>
		<h1 onclick="msCurrentSettingCtl('-');" class="ms-touchControl">-</h1>
		<h1 onclick="msCurrentSettingCtl('go');" class="ms-touchControl">Go</h1>
	</div>
	<div id="ms-menuInstructions">
		<h3>Use Cruise +/- select</h3>
		<h3>Cruise Cancel to confirm action</h3>
	</div>
</div>
<!-- END CONTROLLER MODULE UI -->
<!-- LOAD CONTROLLER MODULE -->
<script type="text/javascript">
	$msSettingMaxOption = <?php echo count($msMenuItem); ?>;
</script>
<script src="/modules/mode-switcher/mode-switcher.js"></script>
<!-- END CONTROLLER MODULE -->