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

header('Content-Type: application/json');

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
global $jsonrpc;
if (!is_object($jsonrpc)) {
	throw new Exception(__('JSONRPC object not defined', __FILE__), -32699);
}

$params = $jsonrpc->getParams();
$PluginToSend = homebridge::PluginToSend();
//$filename = dirname(__FILE__) . '/../../../../tmp/syncHomebridge.txt';

if ($jsonrpc->getMethod() == 'sync') {
	log::add('homebridge', 'info', 'Demande de Sync');
}

if ($jsonrpc->getMethod() == 'sync_homebridge') {
	log::add('homebridge', 'info', 'Demande de Sync Homebridge');
	$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend),homebridge::discovery_eqLogic($PluginToSend));
	$eqLogics = $sync_new[1]['eqLogics'];
	$cmds = $sync_new[0];
    $eqLogics = array_values($eqLogics);
	
	$objects = homebridge::delete_object_eqlogic_null(homebridge::discovery_object(),$eqLogics);
	
	$sync_array = array(
		'eqLogics' => $eqLogics,
		'cmds' => $cmds['cmds'],
		'objects' => $objects,
		//'scenarios' => homebridge::discovery_scenario(),
		'config' => array('datetime' => getmicrotime()),
	);

	$jsonrpc->makeSuccess($sync_array);
}

// HOMEBRIDGE API
// Eqlogic byId
if ($jsonrpc->getMethod() == 'cmdsbyEqlogicID') {
	log::add('homebridge', 'info', 'Interrogation du module id:'.$params['id'].' Pour les cmds');
	$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend),homebridge::discovery_eqLogic($PluginToSend));
	$cmds = $sync_new[0];
	$i = 0;
	$commandes = $cmds['cmds'];
	$cmdAPI = array();
	foreach($commandes as $cmd){
		if(isset($cmd["eqLogic_id"])){
			if($cmd["eqLogic_id"] != $params['id']){
				unset($commandes[$i]);
			}else{
				array_push($cmdAPI, $commandes[$i]);
			}
		}
		$i++;   
    	}
   	log::add('homebridge', 'debug', 'Commande > '.json_encode($cmdAPI));
	$jsonrpc->makeSuccess($cmdAPI);
}
if ($jsonrpc->getMethod() == 'version') {
	$homebridge_update = update::byLogicalId('homebridge');
	$jsonrpc->makeSuccess($homebridge_update->getLocalVersion());	
}
if ($jsonrpc->getMethod() == 'event') {
	$eqLogic = eqLogic::byId($params['eqLogic_id']);
	if (!is_object($eqLogic)) {
		throw new Exception(__('EqLogic inconnu : ', __FILE__) . $params['eqLogic_id']);
	}
	$cmd = $eqLogic->getCmd(null, $params['cmd_logicalId']);
	if (!is_object($cmd)) {
		throw new Exception(__('Cmd inconnu : ', __FILE__) . $params['cmd_logicalId']);
	}
	$cmd->event($params['value']);
	$jsonrpc->makeSuccess();
}

throw new Exception(__('Aucune demande', __FILE__));
