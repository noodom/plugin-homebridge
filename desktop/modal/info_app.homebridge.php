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

	$customValues=homebridge::getCustomData();
	$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend,$customValues['cmd']),homebridge::discovery_eqLogic($PluginToSend,$customValues['eqLogic']));
	$eqLogics = $sync_new[1];
	$cmds = $sync_new[0];
	
	$sync_array = array(
		'eqLogics' => $eqLogics['eqLogics'],
		'cmds' => $cmds['cmds'],
		'objects' => homebridge::delete_object_eqlogic_null(homebridge::discovery_object(),$eqLogics['eqLogics']),
		'scenarios' => homebridge::discovery_scenario($customValues['scenario'])
	);
	function validateJSON($toValidate) {
		if(is_json($toValidate)) {
			return 'JSON <i class="fa fa-check" style="color:#94ca02;" title="{{OK}}"> </i>';
		} else {
			$errorNum=json_last_error();
			switch ($errorNum) {
				case JSON_ERROR_DEPTH:
					$error = '{{La profondeur maximale de la pile a été atteinte.}}';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$error = '{{JSON invalide ou mal formé.}}';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$error = '{{Erreur lors du contrôle des caractères ; probablement un encodage incorrect.}}';
					break;
				case JSON_ERROR_SYNTAX:
					$error = '{{Erreur de syntaxe.}}';
					break;
				// PHP >= 5.3.3
				case JSON_ERROR_UTF8:
					$error = '{{Caractères UTF-8 malformés, possiblement mal encodés.}}';
					break;
				// PHP >= 5.5.0
				case JSON_ERROR_RECURSION:
					$error = '{{Une ou plusieurs références récursives sont présentes dans la valeur à encoder.}}';
					break;
				// PHP >= 5.5.0
				case JSON_ERROR_INF_OR_NAN:
					$error = '{{Une ou plusieurs valeurs NAN ou INF sont présentes dans la valeurs à encoder.}}';
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$error = '{{Une valeur d\'un type qui ne peut être encodée a été fournie.}}';
					break;
				default:
					$error = '{{Erreur inconnue}} ('.$errorNum.')';
					break;
			}
			return 'JSON <i class="fa fa-times" style="color:#FA5858;" title="{{'.$error.'}}"> </i>';
		}
	}
?>
<button id="copyAll"><i class="fa fa-copy" alt="{{Copier tout dans le presse-papier}}" title="{{Copier tout dans le presse-papier}}"> {{Copier tout}}</i></button>

<h3>{{Environnement NodeJS :}} <a class="btn" data-clipboard-target=".nodejs"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="nodejs copyAll" style='overflow: auto; with:90%;height:255px;'>
<?php
	$nodeVer=shell_exec("node -v");
	$nodejsVer=shell_exec("nodejs -v");
	if($nodeVer == $nodejsVer) :
?>
{{Version NodeJS}} : <?=$nodeVer?>
<?php
	else :
?>
<span style='color:red'>
{{Incohérence de versions}}, {{Cliquez sur "Réparer et Réinstaller"}}
node -v : <?=$nodeVer?>
ls -l node : <?=shell_exec("ls -l `which node`")?>
nodejs -v : <?=$nodejsVer?>
ls -l nodejs : <?=shell_exec("ls -l `which nodejs`")?>
</span>
<?php
	endif;
	$localVer =homebridge::getLocalVersion();
	$remoteVer=homebridge::getRemoteVersion();
	$diffVer  =version_compare($localVer,$remoteVer,'<');
?>
{{Version NPM}} : <?=shell_exec("npm -v")?>
{{Prefix Global}} : <?=shell_exec("npm prefix -g")?>
{{Root Global}} : <?=shell_exec("npm root -g")?>
{{Architecture}} : <?=shell_exec("arch")?>
Linux : <?=shell_exec("lsb_release -d -s")?>
Homebridge : <?=homebridge::getLocalVersion('homebridge')?>

HAP-NodeJS : <?=homebridge::getLocalVersion('homebridge/node_modules/hap-nodejs')?>

<?php if($diffVer) {echo "<span style='color:red'>{{Relancez les dépendances}}</br>";} ?>
{{Homebridge-Jeedom locale}} : <?=$localVer?>

{{Homebridge-Jeedom en ligne}} : <?=$remoteVer?>
<?php if($diffVer) {echo "</span>";} ?>

{{Utilisateur sélectionné}} : <?=user::byProfils("admin",true)[0]->getLogin()?>

{{Branche}} : <?=file_get_contents(dirname(__FILE__) . '/../../branch');?>

</pre>

<?php
	$codeURL = homebridge::generateQRCode('150x150');
?>
<h3>{{Code d'installation :}}&nbsp;<a class="btn" data-clipboard-target=".installCode"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="installCode copyAll" style='overflow: auto; with:90%;height:200px;'>
	<img src="<?php echo $codeURL ?>" border="0" /><br />
	<?php echo $codeURL.((extension_loaded('gmp'))?' (gmp OK)':' <span style="color:red">(gmp KO) {{Relancez les dépendances}}</span>') ?>
</pre>

<h3>{{Pièces :}} (<?=validateJSON(json_encode($sync_array['objects']))?>)&nbsp;<a class="btn" data-clipboard-target=".piece"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="piece copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['objects'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Périphériques :}} (<?=validateJSON(json_encode($sync_array['eqLogics']))?>)&nbsp;<a class="btn" data-clipboard-target=".eqLogics"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="eqLogics copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['eqLogics'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Commandes :}} (<?=validateJSON(json_encode($sync_array['cmds']))?>)&nbsp;<a class="btn" data-clipboard-target=".cmds"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="cmds copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['cmds'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Scénarios :}} (<?=validateJSON(json_encode($sync_array['scenarios']))?>)&nbsp;<a class="btn" data-clipboard-target=".scenarios"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="scenarios copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo json_encode($sync_array['scenarios'],JSON_PRETTY_PRINT); ?></pre>

<h3>{{Création Accessoires :}} &nbsp;<a class="btn" data-clipboard-target=".creation"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="creation copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo shell_exec("awk '/ WARNING /,/└────────────────────────/' ".log::getPathToLog('homebridge_daemon')) ?></pre>

<?php
	$otherPlatform = homebridge::getJSON('Platform');
?>
<h3>{{Plateforme Homebridge supplémentaire :}} (<?=validateJSON('['.str_replace('|',',',$otherPlatform).']')?>)&nbsp;<a class="btn" data-clipboard-target=".otherPlatform"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a>&nbsp;<font color='red'>!!! Attention: Peut contenir des mots de passe webcam !!!</font></h3>
<pre id='pre_eventlog' class="otherPlatform copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo $otherPlatform; ?></pre>

<?php
	$otherAccessory = homebridge::getJSON('Accessory');
?>
<h3>{{Accessoire Homebridge supplémentaire :}} (<?=validateJSON('['.str_replace('|',',',$otherAccessory).']')?>)&nbsp;<a class="btn" data-clipboard-target=".otherAccessory"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a>&nbsp;<font color='red'>!!! Attention: Peut contenir des mots de passe!!!</font></h3>
<pre id='pre_eventlog' class="otherAccessory copyAll" style='overflow: auto; with:90%;height:200px;'><?php echo $otherAccessory; ?></pre>

<?php
	$customData = file_get_contents(dirname(__FILE__) . '/../../data/customData.json');
?>
<h3>{{Custom Datas :}} (<?=validateJSON('['.str_replace('|',',',$customData).']')?>)&nbsp;<a class="btn" data-clipboard-target=".customData"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="customData copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($customData),JSON_PRETTY_PRINT)?></pre>

<h3>{{Environnement Avahi :}} <a class="btn" data-clipboard-target=".avahi"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="avahi copyAll" style='overflow: auto; with:90%;height:200px;'>
<?=shell_exec("avahi-browse _hap._tcp -t -v -r -p -f")?>

<?=shell_exec("avahi-browse _homekit._tcp -t -v -r -p -c -f")?>

<?=shell_exec("avahi-browse _airplay._tcp -t -v -r -p -c -f")?>

<?=shell_exec("ps aux | grep avahi | grep -v grep")?>

<?=shell_exec("ps aux | grep dbus | grep -v grep")?>

<?=shell_exec("grep \"homebridge\" /etc/avahi/avahi-daemon.conf")?>
</pre>

<h3>{{Environnement IP :}} <a class="btn" data-clipboard-target=".ip"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="ip copyAll" style='overflow: auto; with:90%;height:200px;'>
<?=shell_exec("ip addr")?>

<?=shell_exec("ip route")?>

<?=network::getNetworkAccess('internal')?>

JSONRPC : <?=config::byKey('api::core::jsonrpc::mode', 'core', 'enable')?>
</pre>

<?php
	$configJson = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/config.json");
	$configJson = json_decode($configJson,true);
	$configJson['platforms'][0]['apikey']="##########";
	$configJson = json_encode($configJson,JSON_PRETTY_PRINT);
?>
<h3>{{config.json :}} (<?=validateJSON($configJson)?>)&nbsp;<a class="btn" data-clipboard-target=".configJson"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="configJson copyAll" style='overflow: auto; with:90%;height:200px;'><?=$configJson?></pre>

<h3>{{DB Homebridge :}} <a class="btn" data-clipboard-target=".persist"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="persist copyAll" style='overflow: auto; with:90%;height:100px;'><?=shell_exec('ls -l '.dirname(__FILE__) . "/../../resources/homebridge/persist/")?></pre>

<?php
	$mac_homebridge = str_replace(':','',config::byKey('mac_homebridge','homebridge'));
	$AccessoryInfo = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/persist/AccessoryInfo.".$mac_homebridge.".json");
?>
<h3>{{Config du Bridge :}} (<?=validateJSON($AccessoryInfo)?>)&nbsp;<a class="btn" data-clipboard-target=".AccessoryInfo"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="AccessoryInfo copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($AccessoryInfo),JSON_PRETTY_PRINT)?></pre>

<?php
	$IdentifierCache = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/persist/IdentifierCache.".$mac_homebridge.".json");
?>
<h3>{{Cache Identifiants :}} (<?=validateJSON($IdentifierCache)?>)&nbsp;<a class="btn" data-clipboard-target=".IdentifierCache"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="IdentifierCache copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($IdentifierCache),JSON_PRETTY_PRINT)?></pre>

<?php
	$fakegato=( (config::byKey('fakegato','homebridge',false,true))?true:false);
	if(homebridge::isMagic('NBOD0V56Srf.k')) { // enable fakegato
		$fakegato=true;
	}
	if($fakegato) :
		$cmdPersist = 'i=0;echo \'[\';for file in '. dirname(__FILE__) . '/../../resources/homebridge/*_persist.json; do if [ "$i" -ne "0" ]; then echo \',\';fi;echo "{\"file\":\"$file\",\"content\":";cat "$file";echo \'}\';i=$((i + 1));done;echo \']\';';
		$PersistFakegato = shell_exec($cmdPersist);
?>
<h3>{{Persistence FakeGato :}} (<?=validateJSON($PersistFakegato)?>)&nbsp;<a class="btn" data-clipboard-target=".PersistFakegato"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="PersistFakegato copyAll" style='overflow: auto; with:90%;height:200px;'><?=$PersistFakegato?></pre>

<?php
	endif;
	$cachedAccessories = file_get_contents(dirname(__FILE__) . "/../../resources/homebridge/accessories/cachedAccessories");
?>
<h3>{{Cache Homebridge :}} (<?=validateJSON($cachedAccessories)?>)&nbsp;<a class="btn" data-clipboard-target=".cachedAccessories"><i class="fa fa-copy" alt="{{Copier dans le presse-papier}}" title="{{Copier dans le presse-papier}}"></i></a></h3>
<pre id='pre_eventlog' class="cachedAccessories copyAll" style='overflow: auto; with:90%;height:200px;'><?=json_encode(json_decode($cachedAccessories),JSON_PRETTY_PRINT)?></pre>

<script src="plugins/homebridge/desktop/js/clipboard.min.js"></script>
<script>
	var clipboard = new Clipboard('.btn');
	var allClipboard = new Clipboard('#copyAll', {
		text: function() {
			var text = "";
			$('.copyAll').each(function(){
				text+="§"+$(this).prev().text()+"\n";
				text+=$(this).text()+"\n\n";
			});
			return text;
		}
	});
</script>
