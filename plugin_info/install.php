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
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function homebridge_install(){
	log::add('homebridge', 'debug', 'Installation du Plugin Homebridge');
	$MobileExists=true;
	try {
		$pluginMobile = plugin::byId('mobile');
	} catch(Exception $e) {
		$mobileExists=false;
	}
	if(mobileExists) {
		log::add('homebridge', 'debug', 'Présence du plugin Mobile');
		$pluginMobile->deamon_stop();
	}
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install(true);
}

function homebridge_update(){
	log::add('homebridge', 'debug', 'Mise à jour du Plugin Homebridge');
	/*$ios = 0;
    	foreach (eqLogic::byType('homebridge') as $homebridge){
		if($homebridge->getLogicalId() == null || $homebridge->getLogicalId() == ""){
			$homebridge->remove();
		}else{
			if($homebridge->getConfiguration('type_homebridge') == "ios"){
				$ios = 1;
			}
		}
	}
	if($ios == 1){*/
		$pluginHomebridge = plugin::byId('homebridge');
		$pluginHomebridge->dependancy_install(true);
	//}
}
?>
