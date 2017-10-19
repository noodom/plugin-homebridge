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
	log::add('homebridge', 'warn', 'Installation du Plugin Homebridge');
	$mobileExists=true;
	try {
		$pluginMobile = plugin::byId('mobile');
	} catch(Exception $e) {
		$mobileExists=false;
	}
	if($mobileExists && $pluginMobile && $pluginMobile->isActive()) {
		exec('echo "`date +"[%Y-%m-%d %T]"` Présence du plugin Mobile [High Five old friend]" >> '.log::getPathToLog('homebridge'));
		$pluginMobile->deamon_stop();
		
		$user_homebridge = config::byKey('user_homebridge','homebridge');
		$user_mobile = config::byKey('user_homebridge','mobile');
		if($user_mobile && !$user_homebridge) {
			config::save('user_homebridge',$user_mobile,'homebridge');
			config::remove('user_homebridge','mobile'); // delete it from mobile
			$user_homebridge = $user_mobile;
			exec('echo "`date +"[%Y-%m-%d %T]"` Reprise du User de mobile:'.$user_mobile.'" >> '.log::getPathToLog('homebridge'));
		}

		$pin_homebridge = config::byKey('pin_homebridge','homebridge');
		$pin_mobile = config::byKey('pin_homebridge','mobile');
		if($pin_mobile && !$pin_homebridge) {
			config::save('pin_homebridge',$pin_mobile,'homebridge');
			config::remove('pin_homebridge','mobile'); // delete it from mobile
			$pin_homebridge = $pin_mobile;
			exec('echo "`date +"[%Y-%m-%d %T]"` Reprise du PIN de mobile:'.$pin_mobile.'" >> '.log::getPathToLog('homebridge'));
		}

		$name_homebridge = config::byKey('name_homebridge','homebridge');
		$name_mobile = config::byKey('name_homebridge','mobile');
		if($name_mobile && !$name_homebridge) {
			config::save('name_homebridge',$name_mobile,'homebridge');
			config::remove('name_homebridge','mobile'); // delete it from mobile
			$name_homebridge = $name_mobile;
			exec('echo "`date +"[%Y-%m-%d %T]"` Reprise du Nom de mobile:'.$name_mobile.'" >> '.log::getPathToLog('homebridge'));
		}

		$mac_homebridge = config::byKey('mac_homebridge','homebridge');
		$mac_mobile = config::byKey('mac_homebridge','mobile');
		if($mac_mobile && !$mac_homebridge) {
			config::save('mac_homebridge',$mac_mobile,'homebridge');
			config::remove('mac_homebridge','mobile'); // delete it from mobile
			$mac_homebridge = $mac_mobile;
			exec('echo "`date +"[%Y-%m-%d %T]"` Reprise de la MAC de mobile:'.$mac_mobile.'" >> '.log::getPathToLog('homebridge'));
		}
		
		$ID_mobile = str_replace(':','',$mac_mobile);
		$ID_homebridge = str_replace(':','',$mac_homebridge);
		
		$platform_homebridge = dirname(__FILE__).'/../data/otherPlatform.json';
		$platform_mobile = dirname(__FILE__).'/../../mobile/data/otherPlatform.json';
		if(file_exists($platform_mobile) && file_exists($platform_homebridge)) {
			if(filemtime($platform_mobile) > filemtime($platform_homebridge)) {
				exec('echo "`date +"[%Y-%m-%d %T]"` Fichier de plateforme Mobile plus récent, on le reprend" >> '.log::getPathToLog('homebridge'));
				exec(system::getCmdSudo() . ' mv -f '.$platform_mobile.' '.$platform_homebridge.' >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');	// delete it from mobile
			}
		} else if(file_exists($platform_mobile) && !file_exists($platform_homebridge)) {
			exec('echo "`date +"[%Y-%m-%d %T]"` Fichier de plateforme Mobile préexistant, on le reprend" >> '.log::getPathToLog('homebridge'));
			exec(system::getCmdSudo() . ' mv -f '.$platform_mobile.' '.$platform_homebridge.' >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');	// delete it from mobile
		}
		
		$AccessoryInfoMoved = false;
		$AccessoryInfosMobile = dirname(__FILE__).'/../../mobile/resources/homebridge/persist/AccessoryInfo.*.json';
		$AccessoryInfoHomebridge = dirname(__FILE__).'/../resources/homebridge/persist/';
		$AccessoryInfosMobile = glob($AccessoryInfosMobile);
		if (!empty($AccessoryInfosMobile)) {
			foreach($AccessoryInfosMobile as $AccessoryInfoMobile) {
				if(file_exists($AccessoryInfoMobile) && !file_exists($AccessoryInfoHomebridge.basename($AccessoryInfoMobile))) {
					exec('echo "`date +"[%Y-%m-%d %T]"` Fichier '.basename($AccessoryInfoMobile).' de Mobile préexistant, on le reprend" >> '.log::getPathToLog('homebridge'));
					exec(system::getCmdSudo() . ' mv -f '.$AccessoryInfoMobile.' '.$AccessoryInfoHomebridge.basename($AccessoryInfoMobile).' >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
					$AccessoryInfoMoved = true;
				}
			}
		}
		
		$IdentifierCacheMoved = false;
		$IdentifierCachesMobile = dirname(__FILE__).'/../../mobile/resources/homebridge/persist/IdentifierCache.*.json';
		$IdentifierCacheHomebridge = dirname(__FILE__).'/../resources/homebridge/persist/';
		$IdentifierCachesMobile = glob($IdentifierCachesMobile);
		if (!empty($IdentifierCachesMobile)) {
			foreach($IdentifierCachesMobile as $IdentifierCacheMobile) {
				if(file_exists($IdentifierCacheMobile) && !file_exists($IdentifierCacheHomebridge.basename($IdentifierCacheMobile))) {
					exec('echo "`date +"[%Y-%m-%d %T]"` Fichier '.basename($IdentifierCacheMobile).' de Mobile préexistant, on le reprend" >> '.log::getPathToLog('homebridge'));
					exec(system::getCmdSudo() . ' mv -f '.$IdentifierCacheMobile.' '.$IdentifierCacheHomebridge.basename($IdentifierCacheMobile).' >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
					$IdentifierCacheMoved = true;
				}
			}
		}
		
		$cachedAccessoriesMoved = false;
		$cachedAccessoriesMobile = dirname(__FILE__).'/../../mobile/resources/homebridge/accessories/cachedAccessories';
		$cachedAccessoriesHomebridge = dirname(__FILE__).'/../resources/homebridge/accessories/cachedAccessories';
		if(file_exists($cachedAccessoriesMobile) && !file_exists($cachedAccessoriesHomebridge)) {
			exec('echo "`date +"[%Y-%m-%d %T]"` Fichier cachedAccessories de Mobile préexistant, on le reprend" >> '.log::getPathToLog('homebridge'));
			exec(system::getCmdSudo() . ' mv -f '.$cachedAccessoriesMobile.' '.$cachedAccessoriesHomebridge.' >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
			$cachedAccessoriesMoved = true;
		}
		
		if($cachedAccessoriesMoved) {
			exec(system::getCmdSudo() . ' rm -fR '.dirname(__FILE__).'/../../mobile/resources/homebridge/accessories/ >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
		}
		if($AccessoryInfoMoved && $IdentifierCacheMoved) {
			exec(system::getCmdSudo() . ' rm -fR '.dirname(__FILE__).'/../../mobile/resources/homebridge/persist/ >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
			if($cachedAccessoriesMoved) {
				exec(system::getCmdSudo() . ' rm -f '.dirname(__FILE__).'/../../mobile/resources/homebridge/config.json >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
				exec(system::getCmdSudo() . ' rm -fR '.dirname(__FILE__).'/../../mobile/resources/homebridge/ >> ' . log::getPathToLog('homebridge') . ' 2>&1 ');// delete it from mobile
			}
		}
		log::remove('mobile_homebridge');
		log::remove('mobile_homebridge_update');
	}
	
	//homebridge::uninstallHomebridge(); // will be uninstalled if new nodejs version
/*	log::add('homebridge', 'error', 'Lancement des dépendances de Homebridge, profitez-en pour lire la documentation ;)');
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install();*/
	log::add('homebridge', 'error', 'Merci pour l\'installation de ce plugin, consultez les notes de release (dans le Forum > Plugins Jeedom > [Catégorie] Communication > [Plugin Tiers] Homebridge) avant utilisation svp');
}

function homebridge_update(){
/*	log::add('homebridge', 'warn', 'Mise à jour du Plugin Homebridge');
	$pluginHomebridge = plugin::byId('homebridge');
	$pluginHomebridge->dependancy_install();*/
	//log::add('homebridge', 'error', '!!! Voir le changelog et doc pour les changements !!!');
	log::add('homebridge', 'error', 'Merci pour l\'installation de ce plugin, consultez les notes de release (dans le Forum > Plugins Jeedom > [Catégorie] Communication > [Plugin Tiers] Homebridge) avant utilisation svp');
}

function homebridge_remove(){
	log::add('homebridge', 'warn', 'Suppression du Plugin Homebridge');
	homebridge::uninstallHomebridge();
	log::add('homebridge', 'warn', 'Réactivation de avahi-daemon au démarrage...(il démarrera avec le système (risque d\'un bug sur la Smart du 1 jan 1970))');
	exec(system::getCmdSudo() . "systemctl enable avahi-daemon >/dev/null 2>&1");
}
?>
