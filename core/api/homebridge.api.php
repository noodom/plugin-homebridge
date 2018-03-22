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

if ($jsonrpc->getMethod() == 'sync_homebridge') {
	log::add('homebridge', 'info', 'Demande de Sync Homebridge');
			
	$customValues=homebridge::getCustomData();

	$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend,$customValues['cmd']),homebridge::discovery_eqLogic($PluginToSend,$customValues['eqLogic']));
	$eqLogics = $sync_new[1]['eqLogics'];
	$eqLogics = array_values($eqLogics);
	$cmds = $sync_new[0];
	
	$objects = homebridge::delete_object_eqlogic_null(homebridge::discovery_object(),$eqLogics);
	
	$sync_array = array(
		'eqLogics' => $eqLogics,
		'cmds' => $cmds['cmds'],
		'objects' => $objects,
		'scenarios' => homebridge::discovery_scenario($customValues['scenario']),
		'config' => array('datetime' => getmicrotime()),
	);

	$jsonrpc->makeSuccess($sync_array);
}

// HOMEBRIDGE API
// Eqlogic byId
if ($jsonrpc->getMethod() == 'getEql') {
	//log::add('homebridge', 'debug', 'Interrogation du module id:'.$params['id']);
	$customValues=homebridge::getCustomData();
	$sync_new = homebridge::change_cmdAndeqLogic([],homebridge::discovery_eqLogic($PluginToSend,$customValues['eqLogic']));
	$eqLogics = $sync_new[1]['eqLogics'];
	$eqLogics = array_values($eqLogics);

	$i = 0;
	$eqLogicAPI = array();
	$found=false;
	foreach($eqLogics as $eqLogic){
		if(isset($eqLogic["id"])){
			if($eqLogic["id"] == $params['id']){
				$found=$eqLogics[$i];
				break;
			}
		}
		$i++;   
    }
	if($found) {
		$jsonrpc->makeSuccess($eqLogics[$i]);
	} else {
		$jsonrpc->makeSuccess();
	}
}
if ($jsonrpc->getMethod() == 'getCmd') {
	//log::add('homebridge', 'info', 'Interrogation du module id:'.$params['id'].' Pour les cmds');
	$customValues=homebridge::getCustomData();
	$sync_new = homebridge::change_cmdAndeqLogic(homebridge::discovery_cmd($PluginToSend,$customValues['cmd']),homebridge::discovery_eqLogic($PluginToSend,$customValues['eqLogic']));
	$commandes = $sync_new[0]['cmds'];

	$i = 0;
	$cmdAPI = array();
	foreach($commandes as $cmd){
		if(isset($cmd["eqLogic_id"])){
			if($cmd["eqLogic_id"] == $params['id']){
				array_push($cmdAPI, $commandes[$i]);
			}
		}
		$i++;   
    }

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
