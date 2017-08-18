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
	log::add('homebridge', 'info', 'Installation du Plugin Homebridge');
	$MobileExists=true;
	try {
		$pluginMobile = plugin::byId('mobile');
	} catch(Exception $e) {
		$mobileExists=false;
	}
	if(mobileExists) {
		log::add('homebridge', 'info', 'Présence du plugin Mobile [High Five old friend]');
		$pluginMobile->deamon_stop();
		
		$user_homebridge = config::byKey('user_homebridge','homebridge');
		log::add('homebridge', 'info', 'my User:'.$user_homebridge);
		$user_mobile = config::byKey('user_homebridge','mobile');
		log::add('homebridge', 'info', 'mobile User:'.$user_mobile);
		if(!$user_homebridge && $user_mobile) {
			config::save('user_homebridge',$user_mobile,'homebridge');
			log::add('homebridge', 'info', 'Reprise du User de mobile:'.$user_mobile);
		}

		$pin_homebridge = config::byKey('pin_homebridge','homebridge');
		log::add('homebridge', 'info', 'my pin:'.$pin_homebridge);
		$pin_mobile = config::byKey('pin_homebridge','mobile');
		log::add('homebridge', 'info', 'mobile pin:'.$pin_mobile);
		if(!$pin_homebridge && $pin_mobile) {
			config::save('pin_homebridge',$pin_mobile,'homebridge');
			log::add('homebridge', 'info', 'Reprise du PIN de mobile:'.$pin_mobile);
		}

		$name_homebridge = config::byKey('name_homebridge','homebridge');
		log::add('homebridge', 'info', 'my name:'.$name_homebridge);
		$name_mobile = config::byKey('name_homebridge','mobile');
		log::add('homebridge', 'info', 'mobile name:'.$name_mobile);
		if(!$name_homebridge && $name_mobile) {
			config::save('name_homebridge',$name_mobile,'homebridge');
			log::add('homebridge', 'info', 'Reprise du Nom de mobile:'.$name_mobile);
		}

		$mac_homebridge = config::byKey('mac_homebridge','homebridge');
		log::add('homebridge', 'info', 'my mac:'.$mac_homebridge);
		$mac_mobile = config::byKey('mac_homebridge','mobile');
		log::add('homebridge', 'info', 'mobile mac:'.$mac_mobile);
		if(!$mac_homebridge && $mac_mobile) {
			config::save('mac_homebridge',$mac_mobile,'homebridge');
			log::add('homebridge', 'info', 'Reprise de la MAC de mobile:'.$mac_mobile);
		}
		
		$log_mobile = log::getLogLevel('mobile');
		$log_homebridge = log::getLogLevel('homebridge');
		log::add('homebridge', 'info', 'B Log mobile:'.$log_mobile);
		log::add('homebridge', 'info', 'B Log homebridge:'.$log_homebridge);
		config::save('log::level::homebridge',$log_mobile);
		$log_mobile = log::getLogLevel('mobile');
		$log_homebridge = log::getLogLevel('homebridge');
		log::add('homebridge', 'info', 'A Log mobile:'.$log_mobile);
		log::add('homebridge', 'info', 'A Log homebridge:'.$log_homebridge);
		
		// + copy data directory*/	
		$platform_mobile = dirname(__FILE__).'/../../../mobile/data/otherPlatform.json';
		$platform_homebridge = dirname(__FILE__).'/../../data/otherPlatform.json';
		log::add('homebridge','info','my exists '.$platform_homebridge.' ? '.file_exists($platform_homebridge));
		if(file_exists($platform_homebridge)) log::add('homebridge','info','my dateM ? '.filemtime($platform_homebridge));
		log::add('homebridge','info','mobile exists '.$platform_mobile.' ? '.file_exists($platform_mobile));
		if(file_exists($platform_mobile)) log::add('homebridge','info','mobile dateM ? '.filemtime($platform_mobile));
	}
	$pluginHomebridge = plugin::byId('homebridge');
	//$pluginHomebridge->generate_file();
	$pluginHomebridge->dependancy_install(true);
	$pluginHomebridge->generate_file();
}

function homebridge_update(){
	log::add('homebridge', 'info', 'Mise à jour du Plugin Homebridge');
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install(true);
}
?>
