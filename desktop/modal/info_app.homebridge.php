<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}

$PluginToSend = homebridge::PluginToSend();

$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend),homebridge::discovery_eqLogic($PluginToSend));
	$eqLogics = $sync_new[1];
	$cmds = $sync_new[0];
	
	$sync_array = array(
		'eqLogics' => $eqLogics['eqLogics'],
		'cmds' => $cmds['cmds'],
		'objects' => homebridge::delete_object_eqlogic_null(homebridge::discovery_object(),$eqLogics['eqLogics'])
	);
	function validateJSON($toValidate) {
		if(json_encode(json_decode($toValidate)) === null) {
			return 'JSON <i class="fa fa-times" style="color:#FA5858;"> '.json_last_error().'</i>';
		} else {
			return 'JSON <i class="fa fa-check" style="color:#94ca02;"></i>';
		}
	}
?>
<!--<button class="btn" data-clipboard-target=".copyAll"><i class="fa fa-copy" alt="Copier tout dans le presse-papier" title="Copier tout dans le presse-papier"> Copier tout</i></button>-->

<h3>{{Environnement NodeJS :}} <a class="btn" data-clipboard-target=".nodejs"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="nodejs copyAll" style='overflow: auto; with:90%;height:200px;'>
<?php
	$nodeVer=shell_exec("node -v");
	$nodejsVer=shell_exec("nodejs -v");
	if($nodeVer == $nodejsVer) :
?>
Version NodeJS : <?=$nodeVer?>
<?php
	else :
?>
<span style='color:red'>
node -v : <?=$nodeVer?>
ls -l node : <?=shell_exec("ls -l `which node`")?>
nodejs -v : <?=$nodejsVer?>
ls -l nodejs : <?=shell_exec("ls -l `which nodejs`")?>
</span>
<?php
	endif;
?>
Version NPM : <?=shell_exec("npm -v")?>
Prefix Global : <?=shell_exec("npm prefix -g")?>
Root Global : <?=shell_exec("npm root -g")?>
Architecture : <?=shell_exec("arch")?>
Linux : <?=shell_exec("lsb_release -d -s")?>
</pre>

<h3>{{Pièces :}} (<?=validateJSON($sync_array['objects'])?>) <a class="btn" data-clipboard-target=".piece"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="piece copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['objects'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Périphériques :}} (<?=validateJSON($sync_array['eqLogics'])?>)<a class="btn" data-clipboard-target=".eqLogics"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="eqLogics copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['eqLogics'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Commandes :}} (<?=validateJSON($sync_array['cmds'])?>)<a class="btn" data-clipboard-target=".cmds"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="cmds copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['cmds'],JSON_PRETTY_PRINT); ?></pre>

<?php
	$otherPlatform = file_get_contents(dirname(__FILE__) . '/../../data/otherPlatform.json');
?>
<h3>{{Autres Plateformes :}} (<?=validateJSON('['.str_replace('|',','$otherPlatform)).']'?>)<a class="btn" data-clipboard-target=".otherPlatform"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="otherPlatform copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($otherPlatform,JSON_PRETTY_PRINT); ?></pre>

<h3>{{Environnement Avahi :}} <a class="btn" data-clipboard-target=".avahi"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="avahi copyAll" style='overflow: auto; with:90%;height:200px;'>
<?=shell_exec("avahi-browse _hap._tcp -t -v -r -p")?>

<?=shell_exec("ps aux | grep avahi | grep -v grep")?>

<?=shell_exec("ps aux | grep dbus | grep -v grep")?>
</pre>

<h3>{{Environnement IP :}} <a class="btn" data-clipboard-target=".ip"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="ip copyAll" style='overflow: auto; with:90%;height:200px;'>
<?=shell_exec("ip addr")?>

<?=shell_exec("ip route")?>

<?=network::getNetworkAccess('internal')?>
</pre>

<?php
	$configJson = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/config.json");
?>
<h3>{{config.json :}} (<?=validateJSON($configJson)?>)<a class="btn" data-clipboard-target=".configJson"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="configJson copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($configJson),JSON_PRETTY_PRINT)?></pre>

<h3>{{DB Homebridge :}} <a class="btn" data-clipboard-target=".persist"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="persist copyAll" style='overflow: auto; with:90%;height:100px;'><?=shell_exec('ls -l '.dirname(__FILE__) . "/../../resources/homebridge/persist/")?></pre>

<?php
	$cachedAccessories = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/accessories/cachedAccessories");
?>
<h3>{{Cache Homebridge :}} (<?=validateJSON($cachedAccessories)?>)<a class="btn" data-clipboard-target=".cachedAccessories"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="cachedAccessories copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($cachedAccessories),JSON_PRETTY_PRINT)?></pre>

<script src="plugins/homebridge/desktop/js/clipboard.min.js"></script>
<script>var clipboard = new Clipboard('.btn');</script>
