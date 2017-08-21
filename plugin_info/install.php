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
	log::add('homebridge_api', 'info', 'Installation du Plugin Homebridge');
	$MobileExists=true;
	try {
		$pluginMobile = plugin::byId('mobile');
	} catch(Exception $e) {
		$mobileExists=false;
	}
	if(mobileExists) {
		log::add('homebridge_api', 'info', 'Présence du plugin Mobile [High Five old friend]');
		$pluginMobile->deamon_stop();
		
		$user_homebridge = config::byKey('user_homebridge','homebridge');
		$user_mobile = config::byKey('user_homebridge','mobile');
		if(!$user_homebridge && $user_mobile) {
			config::save('user_homebridge',$user_mobile,'homebridge');
			log::add('homebridge_api', 'info', 'Reprise du User de mobile:'.$user_mobile);
		}

		$pin_homebridge = config::byKey('pin_homebridge','homebridge');
		$pin_mobile = config::byKey('pin_homebridge','mobile');
		if(!$pin_homebridge && $pin_mobile) {
			config::save('pin_homebridge',$pin_mobile,'homebridge');
			log::add('homebridge_api', 'info', 'Reprise du PIN de mobile:'.$pin_mobile);
		}

		$name_homebridge = config::byKey('name_homebridge','homebridge');
		$name_mobile = config::byKey('name_homebridge','mobile');
		if(!$name_homebridge && $name_mobile) {
			config::save('name_homebridge',$name_mobile,'homebridge');
			log::add('homebridge_api', 'info', 'Reprise du Nom de mobile:'.$name_mobile);
		}

		$mac_homebridge = config::byKey('mac_homebridge','homebridge');
		$mac_mobile = config::byKey('mac_homebridge','mobile');
		if(!$mac_homebridge && $mac_mobile) {
			config::save('mac_homebridge',$mac_mobile,'homebridge');
			log::add('homebridge_api', 'info', 'Reprise de la MAC de mobile:'.$mac_mobile);
		}
		
		$platform_homebridge = dirname(__FILE__).'/../data/otherPlatform.json';
		$platform_mobile = dirname(__FILE__).'/../../mobile/data/otherPlatform.json';

		if(file_exists($platform_mobile) && file_exists($platform_homebridge)) {
			if(filemtime($platform_mobile) > filemtime($platform_homebridge)) {
				log::add('homebridge_api','info','Fichier de plateforme Mobile plus récent, on le reprend');
				exec('sudo cp '.$platform_mobile.' '.$platform_homebridge.' >> ' . log::getPathToLog(__CLASS__) . ' 2>&1 ');	
			}
		} else if(file_exists($platform_mobile) && !file_exists($platform_homebridge)) {
			log::add('homebridge_api','info','Fichier de plateforme Mobile préexistant, on le reprend');
			exec('sudo cp '.$platform_mobile.' '.$platform_homebridge.' >> ' . log::getPathToLog(__CLASS__) . ' 2>&1 ');	
		}
	}
	log::add('homebridge_api', 'info', 'Suppression homebridge-jeedom');
	$cmd = 'npm uninstall homebridge-jeedom --save';
	exec($cmd);
	log::add('homebridge_api', 'info', 'Suppression homebridge 1/3');
	$cmd = 'npm uninstall homebridge --save';
	exec($cmd);
	log::add('homebridge_api', 'info', 'Suppression homebridge 2/3');
	$cmd = 'sudo rm -fR /usr/local/lib/node_modules/homebridge >/dev/null 2>&1';
	exec($cmd);
	$cmd = 'sudo rm -fR /usr/lib/node_modules/homebridge >/dev/null 2>&1';
	exec($cmd);
	log::add('homebridge_api', 'info', 'Suppression homebridge 3/3');
	$cmd = 'sudo rm -f /usr/bin/homebridge >/dev/null 2>&1';
	exec($cmd);
	$cmd = 'sudo rm -f /usr/local/bin/homebridge >/dev/null 2>&1';
	exec($cmd);	
	log::add('homebridge_api', 'info', 'Installation des dépendances');
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install();
}

function homebridge_update(){
	log::add('homebridge', 'info', 'Mise à jour du Plugin Homebridge');
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install();
}
?>
