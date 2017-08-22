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
		'objects' => homebridge::delete_object_eqlogic_null(homebridge::discovery_object(),$eqLogics['eqLogics']),
		//'scenarios' => homebridge::discovery_scenario(),
		'config' => array('datetime' => getmicrotime()),
	);
?>
<!--<button class="btn" data-clipboard-target=".copyAll"><i class="fa fa-copy" alt="Copier tout dans le presse-papier" title="Copier tout dans le presse-papier"> Copier tout</i></button>-->
<h3>{{JSON valide :}}<h3>
<pre id='pre_eventlog' style='overflow: auto; with:90%;'><?php
	$ob = json_decode(json_encode($sync_array));
	if($ob === null) {
		echo '{{Json mal encodé}}';
	}else{
		echo '{{Json bien encodé}}';
	}
?></pre>
<h3>{{Pièces :}} <a class="btn" data-clipboard-target=".piece"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="piece copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['objects'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Périphériques :}} <a class="btn" data-clipboard-target=".eqLogics"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="eqLogics copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['eqLogics'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Commandes :}} <a class="btn" data-clipboard-target=".cmds"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="cmds copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['cmds'],JSON_PRETTY_PRINT); ?></pre>

<!--<h3>{{Scénarios :}}</h3>
<pre id='pre_eventlog' style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['scenarios'],JSON_PRETTY_PRINT); ?></pre>-->

<h3>{{Configurations :}}</h3>
<pre id='pre_eventlog' class="config copyAll" style='overflow: auto; with:90%;'><?php echo json_encode($sync_array['config']); ?></pre>

<h3>{{NodeJS :}} <a class="btn" data-clipboard-target=".nodejs"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="nodejs copyAll" style='overflow: auto; with:90%;height:200px;'>
node -v : <?=shell_exec("node -v")?>
nodejs -v : <?=shell_exec("nodejs -v")?>
npm -v : <?=shell_exec("npm -v")?>
npm prefix -g : <?=shell_exec("npm prefix -g")?>
Linux :
<?=shell_exec("cat /etc/*-release")?>
</pre>

<h3>{{Avahi :}} <a class="btn" data-clipboard-target=".avahi"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="avahi copyAll" style='overflow: auto; with:90%;height:100px;'><?=shell_exec("avahi-browse _hap._tcp -t -v -r -p")?></pre>

<h3>{{IP :}} <a class="btn" data-clipboard-target=".ip"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="ip copyAll" style='overflow: auto; with:90%;height:200px;'><?=shell_exec("ip addr")?></pre>

<h3>{{config.json :}} <a class="btn" data-clipboard-target=".configJson"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="configJson copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode(file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/config.json")),JSON_PRETTY_PRINT)?></pre>

<h3>{{persist :}} <a class="btn" data-clipboard-target=".persist"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="persist copyAll" style='overflow: auto; with:90%;height:100px;'><?=shell_exec('ls -alF '.dirname(__FILE__) . "/../../resources/homebridge/persist/")?></pre>

<h3>{{cachedAccessories :}} <a class="btn" data-clipboard-target=".cachedAccessories"><i class="fa fa-copy" alt="Copier dans le presse-papier" title="Copier dans le presse-papier"></i></a></h3>
<pre id='pre_eventlog' class="cachedAccessories copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode(file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/accessories/cachedAccessories")),JSON_PRETTY_PRINT)?></pre>

<script src="plugins/homebridge/desktop/js/clipboard.min.js"></script>
<script>var clipboard = new Clipboard('.btn');</script>
