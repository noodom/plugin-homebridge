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
		log::add('homebridge', 'debug', 'Présence du plugin Mobile [High Five old friend]');
		$pluginMobile->deamon_stop();
		
		$user_homebridge = config::byKey('user_homebridge','homebridge');
		log::add('homebridge', 'debug', 'my User:'.$user_homebridge);
		$user_mobile = config::byKey('user_homebridge','mobile');
		log::add('homebridge', 'debug', 'mobile User:'.$user_mobile);
		if(!$user_homebridge && $user_mobile) {
			config::save('user_homebridge',$user_mobile,'homebridge');
		}

		$pin_homebridge = config::byKey('pin_homebridge','homebridge');
		log::add('homebridge', 'debug', 'my pin:'.$pin_homebridge);
		$pin_mobile = config::byKey('pin_homebridge','mobile');
		log::add('homebridge', 'debug', 'mobile pin:'.$pin_mobile);
		if(!$pin_homebridge && $pin_mobile) {
			config::save('pin_homebridge',$pin_mobile,'homebridge');
		}

		$name_homebridge = config::byKey('name_homebridge','homebridge');
		log::add('homebridge', 'debug', 'my name:'.$name_homebridge);
		$name_mobile = config::byKey('name_homebridge','mobile');
		log::add('homebridge', 'debug', 'mobile name:'.$name_mobile);
		if(!$name_homebridge && $name_mobile) {
			config::save('name_homebridge',$name_mobile,'homebridge');
		}

		$mac_homebridge = config::byKey('mac_homebridge','homebridge');
		log::add('homebridge', 'debug', 'my mac:'.$mac_homebridge);
		$mac_mobile = config::byKey('mac_homebridge','mobile');
		log::add('homebridge', 'debug', 'mobile mac:'.$mac_mobile);
		if(!$mac_homebridge && $mac_mobile) {
			config::save('mac_homebridge',$mac_mobile,'homebridge');
		}
		
		config::save('log::level::homebridge',config::byKey('log::level::mobile'));
		
		// + copy data directory*/	
	}
	$pluginHomebridge = plugin::byId('homebridge');
	/*$pluginHomebridge->dependancy_install(true);*/
	$pluginHomebridge->generate_file();
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
		/*$pluginHomebridge->dependancy_install(true);*/
	//}
}
?>
