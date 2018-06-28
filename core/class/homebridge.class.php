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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class homebridge extends eqLogic {
	/*     * *************************Attributs****************************** */

	//private static $_PLUGIN_COMPATIBILITY = array('openzwave', 'rfxcom', 'edisio', 'ipx800', 'mySensors', 'Zibasedom', 'virtual', 'camera','apcupsd', 'btsniffer', 'dsc', 'h801', 'rflink', 'mysensors', 'relaynet', 'remora', 'unipi', 'playbulb', 'doorbird','netatmoThermostat');

	/*     * ***********************Methode static*************************** */

	/*public static function Pluginsuported() {
		$Pluginsuported = ['openzwave','rfxcom','edisio','mpower', 'mySensors', 'Zibasedom', 'virtual', 'camera','weather','philipsHue','enocean','wifipower','alarm','mode','apcupsd', 'btsniffer','dsc','rflink','mysensors','relaynet','remora','unipi','eibd','thermostat','netatmoThermostat','espeasy','jeelink','teleinfo','tahoma','protexiom','lifx','wattlet','rfplayer','openenocean'];
		return $Pluginsuported;
	}*/
	
	/*public static function PluginWidget() {
		$PluginWidget = ['alarm','camera','thermostat','netatmoThermostat','weather','mode'];	
		return $PluginWidget; 
	}*/
	public static $_listenEvents = array('cmd::update', 'scenario::update');

	public static function getCustomGenerics(){
		$CUSTOM_GENERIC_TYPE = array(
			'ENERGY_INUSE' => array('name' => 'Prise En Utilisation (Homebridge)', 'family' => 'Prise', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'PUSH_BUTTON' => array('name' => 'Bouton poussoir (Homebridge)', 'family' => 'Interrupteur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_STATE' => array('name' => 'Interrupteur Etat (Homebridge)', 'family' => 'Interrupteur', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_ON' => array('name' => 'Interrupteur Bouton On (Homebridge)', 'family' => 'Interrupteur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_OFF' => array('name' => 'Interrupteur Bouton Off (Homebridge)', 'family' => 'Interrupteur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_STATELESS_ALLINONE' => array('name' => 'Interrupteur Programmable (Multi-Valeur) (Homebridge)', 'family' => 'Interrupteur Programmable', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_STATELESS_SINGLE' => array('name' => 'Interrupteur Programmable Binaire (Simple Click) (Homebridge)', 'family' => 'Interrupteur Programmable', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_STATELESS_DOUBLE' => array('name' => 'Interrupteur Programmable Binaire (Double Click) (Homebridge)', 'family' => 'Interrupteur Programmable', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SWITCH_STATELESS_LONG' => array('name' => 'Interrupteur Programmable Binaire (Long Click) (Homebridge)', 'family' => 'Interrupteur Programmable', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'ACTIVE' => array('name' => 'Statut Actif (Homebridge)', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'ONLINE' => array('name' => 'Statut Online (Homebridge)', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'OCCUPANCY' => array('name' => 'Présence Occupation (Homebridge)', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'DEFECT' => array('name' => 'Statut Défectueux (Homebridge)', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_VOLUME' => array('name' => 'Haut-Parleur Volume (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_SET_VOLUME' => array('name' => 'Haut-Parleur Volume (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_MUTE' => array('name' => 'Haut-Parleur Mute (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_MUTE_TOGGLE' => array('name' => 'Haut-Parleur Toggle Mute (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_MUTE_ON' => array('name' => 'Haut-Parleur Mute (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'SPEAKER_MUTE_OFF' => array('name' => 'Haut-Parleur UnMute (Homebridge)', 'family' => 'Haut-Parleur', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'LIGHT_STATE_BOOL' => array('name' => 'Lumière Etat (Binaire) (Homebridge)', 'family' => 'Lumière', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'LIGHT_COLOR_TEMP' => array('name' => 'Lumière Température Couleur (Homebridge)', 'family' => 'Lumière', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'LIGHT_SET_COLOR_TEMP' => array('name' => 'Lumière Température Couleur (Homebridge)', 'family' => 'Lumière', 'type' => 'Action', 'ignore' => true, 'homebridge_type' => true),
			'AIRQUALITY_INDEX' => array('name' => 'Qualité d\'air (Indice AQI) (Homebridge)', 'family' => 'Qualité D\'air', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'WEATHER_UVINDEX' => array('name' => 'Météo Index UV (Homebridge)', 'family' => 'Météo', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true),
			'WEATHER_VISIBILITY' => array('name' => 'Météo Visibilité (Homebridge)', 'family' => 'Météo', 'type' => 'Info', 'ignore' => true, 'homebridge_type' => true)
		);
		return $CUSTOM_GENERIC_TYPE;
	}
	
	public static function PluginMultiInEqLogic(){
		$PluginMulti = ['LIGHT_STATE','ENERGY_STATE','FLAP_STATE','HEATING_STATE','SIREN_STATE','LOCK_STATE'];
		return $PluginMulti;
	}

	public static function PluginCustomisable(){
		$PluginCustomisable = ['GARAGE_STATE','BARRIER_STATE','ALARM_SET_MODE','THERMOSTAT_SET_MODE','SWITCH_STATELESS_ALLINONE','SWITCH_STATELESS_SINGLE','SWITCH_STATELESS_DOUBLE','SWITCH_STATELESS_LONG'];
		return $PluginCustomisable;
	}
	
	public static function PluginAutoConfig(){
		return json_decode(self::getJSON('autoconfig','core/config'),true);
	}
	
	public static function DisallowedPIN() {
		$DisallowedPIN = ['000-00-000','111-11-111','222-22-222','333-33-333','444-44-444','555-55-555','666-66-666','777-77-777','888-88-888','999-99-999','123-45-678','876-54-321'];
		return $DisallowedPIN;
	}
	
	public static function PluginToSend() {
		$PluginToSend=[];
		$plugins = plugin::listPlugin(true);
		foreach ($plugins as $plugin){
			$plugId = $plugin->getId();
			if ($plugId != 'homebridge' && $plugId != 'mobile') {
				array_push($PluginToSend, $plugId);
			}
		}
		return $PluginToSend;
	}

	/**************************************************************************************/
	/*                                                                                    */
	/*                        Permet d'installer les dépendances                          */
	/*                                                                                    */
	/**************************************************************************************/
	/*public static function check_ios() {
		$ios = 0;
		foreach (eqLogic::byType('homebridge') as $homebridge){
			if($homebridge->getConfiguration('type_homebridge') == "ios"){
				$ios = 1;
			}
		}
		return $ios;
	}*/
	public static function cronDaily() {
		self::cleanCustomData();
	}
	
	public static function dependancy_info() {
		$return = [];
		$return['log'] = 'homebridge_dep';
		$return['progress_file'] = jeedom::getTmpFolder('homebridge') . '/dependance';
		
		/*log::add('homebridge','debug',"version locale:".self::getLocalVersion()."\t"."version en ligne(".self::getBranch()."):".self::getRemoteVersion());
		log::add('homebridge','debug',"locale >= en ligne:".((version_compare(self::getLocalVersion(),self::getRemoteVersion(),'>='))?'ok':'ko'));
		log::add('homebridge','debug',"/usr/bin/homebridge existe:".((file_exists('/usr/bin/homebridge'))?'oui':'non'));
		log::add('homebridge','debug',"/usr/local/bin/homebridge existe:".((file_exists('/usr/local/bin/homebridge'))?'oui':'non'));*/
		
		if (file_exists(dirname(__FILE__) . '/../../resources/node_modules/homebridge/bin/homebridge') && version_compare(self::getLocalVersion(),self::getRemoteVersion(),'>=')) {
			$return['state'] = 'ok';
		} else {
			$return['state'] = 'nok';
		}	
		return $return;
	}
	
	public static function dependancy_install() {
		if (file_exists(jeedom::getTmpFolder('homebridge') . '/dependance')) {
		    return;
		}
		log::remove(__CLASS__ . '_dep');
		self::generate_file();
		
        return array('script' => dirname(__FILE__) . '/../../resources/install_homebridge.sh '.network::getNetworkAccess('internal','ip').' '.self::getBranch(),
					 'log' => log::getPathToLog(__CLASS__ . '_dep'));
	}
	
	public static function getLocalVersion($plugin='homebridge-jeedom') {
		$npmRoot = dirname(__FILE__).'/../../resources/node_modules';
		if (!file_exists($npmRoot.'/homebridge-jeedom/package.json')) {
			$version = '0';
			$serial  = '';
		} else {
			$packageJson = file_get_contents($npmRoot.'/'.$plugin.'/package.json');
			$packageJson = json_decode($packageJson,true);
			$version = (($packageJson['version'][0] != 'v')?$packageJson['version']:substr($packageJson['version'],1));
			$serial = $packageJson['cust_serial'];
		}
		return $version.(($serial)?'.'.$serial:'');
	}
	
	public static function getRemoteVersion() {
		$remotePackage = "https://raw.githubusercontent.com/NebzHB/homebridge-jeedom/".self::getBranch()."/package.json";
		$packageJson = @file_get_contents($remotePackage);
		if ($packageJson === false) {
			$version = '0';
			$serial  = '';
		} else {
			$packageJson = json_decode($packageJson,true);
			$version = (($packageJson['version'][0] != 'v')?$packageJson['version']:substr($packageJson['version'],1));
			$serial = $packageJson['cust_serial'];
		}
		return $version.(($serial)?'.'.$serial:'');
	}
	
	public static function getBranch() {
		$branch = @strtolower(@trim(@file_get_contents(dirname(__FILE__) . '/../../branch')));
		if(!$branch) {
			$branch = 'master';
		}
		return $branch;
	}
	
	public static function getJSON($file,$folder = 'data'){
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../'.$folder);
		exec(system::getCmdSudo() . 'chmod -R 775 ' . dirname(__FILE__) . '/../../'.$folder);
		exec('touch ' . dirname(__FILE__) . '/../../'.$folder.'/'.$file.'.json');
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../'.$folder);
		exec(system::getCmdSudo() . 'chmod -R 775 ' . dirname(__FILE__) . '/../../'.$folder);
		return file_get_contents(dirname(__FILE__) . '/../../'.$folder.'/'.$file.'.json');
	}
	public static function saveJSON($fileContent,$file,$folder= 'data'){
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../'.$folder);
		exec(system::getCmdSudo() . 'chmod -R 775 ' . dirname(__FILE__) . '/../../'.$folder);
		$ret = file_put_contents(dirname(__FILE__) . '/../../'.$folder.'/'.$file.'.json',$fileContent);
		return (($ret===false)?false:true);
	}
	
	public static function saveCustomData($eqLogicToSave,$cmdToSave,$scenarioToSave,$cmdOldValues){
		$content = homebridge::getCustomData();
		
		foreach ($eqLogicToSave as $newVal) {
			$found = false;
			foreach ($content['eqLogic'] as $id => $eqLogic) {
				if($eqLogic['id'] == $newVal['id']) {
					if($newVal['configuration']) 
						$content['eqLogic'][$id]['configuration'] = $newVal['configuration'];
					if($newVal['display'])
						$content['eqLogic'][$id]['display'] = $newVal['display'];
					$found = true;
					break;
				}
			}
			if(!$found) {
				array_push($content['eqLogic'],$newVal);
			}
		}

		foreach ($scenarioToSave as $newScenario) {
			$found = false;
			foreach ($content['scenario'] as $id => $scenario) {
				if($scenario['id'] == $newScenario['id']) {
					if($newScenario['configuration']) 
						$content['scenario'][$id]['configuration'] = $newScenario['configuration'];
					$found = true;
					break;
				}
			}
			if(!$found) {
				array_push($content['scenario'],$newScenario);
			}
		}

		foreach ($cmdOldValues as $oldValCmd) {
			//echo "-oldValCmd:".$oldValCmd['id'];
			for($i=0;$i< count($content['cmd']);$i++) {
				//echo "--contentCmd:".$content['cmd'][$i]['id'];
				if($content['cmd'][$i]['id'] == $oldValCmd['id']) {
					//echo "---match ".$i;
					log::add('homebridge','debug','Suppression :'.$content['cmd'][$i]['id'].$i);
					array_splice($content['cmd'],$i,1);
					break;
				}
			}
		}			

		foreach ($cmdToSave as $newValCmd) {
			$found = false;
			foreach ($content['cmd'] as $id => $cmd) {
				if($cmd['id'] == $newValCmd['id']) {
					if($newValCmd['configuration']) 
						$content['cmd'][$id]['configuration'] = $newValCmd['configuration'];
					if (jeedom::version() >= '3.2.1') {
						if($newValCmd['generic_type'])
							$content['cmd'][$id]['generic_type'] = $newValCmd['generic_type'];
					} else {
						if($newValCmd['display'])
-							$content['cmd'][$id]['display'] = $newValCmd['display'];
					}
					$found = true;
					break;
				}
			}
			if(!$found) {
				array_push($content['cmd'],$newValCmd);
			}
		}	

		$content = json_encode($content);
		$ret = file_put_contents(dirname(__FILE__) . '/../../data/customData.json',$content);
		return (($ret===false)?false:true);
	}
	public static function getCustomData(){
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../data');
		exec(system::getCmdSudo() . 'chmod -R 775 ' . dirname(__FILE__) . '/../../data');
		exec('touch ' . dirname(__FILE__) . '/../../data/customData.json');
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../data');
		exec(system::getCmdSudo() . 'chmod -R 775 ' . dirname(__FILE__) . '/../../data');
		$content = file_get_contents(dirname(__FILE__) . '/../../data/customData.json');
		if(!$content) $content = '';
		$content = json_decode($content,true);
		if(!$content) {
			$content['eqLogic']=[];
			$content['cmd']    =[];
			$content['scenario']=[];
		} else if(!$content['scenario']) {
			$content['scenario']=[];
		} else if(!$content['eqLogic']) {
			$content['eqLogic']=[];
		} else if(!$content['cmd']) {
			$content['cmd']=[];
		}
		return $content;
	}
	public static function cleanCustomData(){
		log::add('homebridge','info','Nettoyage journalier des eqLogics & cmds n\'existant plus dans Jeedom mais toujours dans notre config');
		$content = homebridge::getCustomData();
		$found=false;
		foreach ($content['eqLogic'] as $keyEqLogicCustom => $eqLogicCustom) {
			$eqLogicExists = eqLogic::byId($eqLogicCustom['id']);
			if (!is_object($eqLogicExists)) {
				log::add('homebridge','info','Le perif avec l\'id '.$eqLogicCustom['id'].'('.$keyEqLogicCustom.') n\'existe plus dans Jeedom, on l\'efface de notre bdd custom');
				array_splice($content['eqLogic'],$keyEqLogicCustom,1);
				$found=true;
			}
		}
		foreach ($content['scenario'] as $keyScenarioCustom => $ScenarioCustom) {
			$ScenarioExists = scenario::byId($ScenarioCustom['id']);
			if (!is_object($ScenarioExists)) {
				log::add('homebridge','info','Le scenario avec l\'id '.$ScenarioCustom['id'].'('.$keyScenarioCustom.') n\'existe plus dans Jeedom, on l\'efface de notre bdd custom');
				array_splice($content['scenario'],$keyScenarioCustom,1);
				$found=true;
			}
		}
		foreach ($content['cmd'] as $keyCmdCustom => $cmdCustom) {
			$cmdExists = cmd::byId($cmdCustom['id']);
			if (!is_object($cmdExists)) {
				log::add('homebridge','info','La cmd avec l\'id '.$cmdCustom['id'].'('.$keyCmdCustom.') n\'existe plus dans Jeedom, on l\'efface de notre bdd custom');				
				array_splice($content['cmd'],$keyCmdCustom,1);
				$found=true;
			}
		}
		if($found) {
			$content = json_encode($content);
			$ret = file_put_contents(dirname(__FILE__) . '/../../data/customData.json',$content);
			return (($ret===false)?false:true);
		}
		return true;
	}
	
	public static function migrateCustomData(){
		$migrated321 = config::byKey('migrated321','homebridge',false,true);
		
		if(!$migrated321) {
			log::add('homebridge','info','Migration des données spécifique à Homebridge');
			$content = homebridge::getCustomData();
			$found=false;

			foreach ($content['cmd'] as $keyCmdCustom => $cmdCustom) {
				if ($cmdCustom['display']['generic_type']) {
					log::add('homebridge','debug','Modification de la commande '.$cmdCustom['id'].' generic_type : '.$cmdCustom['display']['generic_type']);
					$content['cmd'][$keyCmdCustom]['generic_type'] = $cmdCustom['display']['generic_type'];
					unset($content['cmd'][$keyCmdCustom]['display']);
					$found=true;
				}
			}
			
			if($found) {
				$content = json_encode($content);
				$ret = file_put_contents(dirname(__FILE__) . '/../../data/customData.json',$content);
				$ret = (($ret===false)?false:true);
				config::save('migrated321',$ret,'homebridge');
				return $ret;
			}
			config::save('migrated321',true,'homebridge');
			return true;
		}
	}
	public static function getCamInfo($eqLogic_array) {

		//Camera
		if(isset($eqLogic_array["eqType_name"]) && $eqLogic_array["eqType_name"] == "camera"){
			$returnArray = [];
			$returnArray["videoFramerate"] = intval($eqLogic_array["configuration"]["videoFramerate"]);
			//$returnArray["ip"] = $eqLogic_array["configuration"]["ip"];
			//$returnArray["port"]=$eqLogic_array["configuration"]["port"];
			//$returnArray["protocole"] = $eqLogic_array["configuration"]["protocole"];
			//$returnArray["username"] = $eqLogic_array["configuration"]["username"];
			//$returnArray["password"] = $eqLogic_array["configuration"]["password"];
			$replace = array(
					'#username#' => $eqLogic_array["configuration"]["username"],
					'#password#' => $eqLogic_array["configuration"]["password"],
					'#ip#' => $eqLogic_array["configuration"]["ip"],
					'#port#' => $eqLogic_array["configuration"]["port"]
				);
			$returnArray["fluxValid"]=false;
			if($eqLogic_array["configuration"]["cameraStreamAccessUrl"]) { // rtsp flux (or mjpeg ?)
				//$returnArray["cameraStreamAccessUrl"] = $eqLogic_array["configuration"]["cameraStreamAccessUrl"];							
				$returnArray["flux"]=str_replace(array_keys($replace), $replace, $eqLogic_array["configuration"]["cameraStreamAccessUrl"]);
				
				$isFullURL = strpos($returnArray["flux"],'://');
				if($isFullURL!==false) {
					$returnArray["fluxValid"]=true;
					$returnArray["fluxProtocole"]= substr($returnArray["flux"],0,$isFullURL);
				}
			}
			

			
			// still Image
			$returnArray["imageValid"] = false;
			//$returnArray["urlStream"] = $eqLogic_array["configuration"]["urlStream"];
		
			// my direct method
			$returnArray["image"]=  $eqLogic_array["configuration"]["protocole"].'://'.
												$eqLogic_array["configuration"]["ip"].':'.
												$eqLogic_array["configuration"]["port"].
												str_replace(array_keys($replace), $replace, $eqLogic_array["configuration"]["urlStream"]);
			$binary_data = file_get_contents($returnArray["image"]);
			if($binary_data){
				$im = imagecreatefromstring($binary_data);
				$returnArray["imageWidth"]=imagesx($im);
				$returnArray["imageHeight"]=imagesy($im);
				$returnArray["imageValid"] = true;
				$binary_data=null;
				$im=null;
			} else {// from jeedom getUrl method
				$returnArray["image"]=$eqLogic->getUrl($eqLogic_array["configuration"]["urlStream"]);
				$binary_data = file_get_contents($returnArray["image"]);
				if($binary_data){
					$im = imagecreatefromstring($binary_data);
					$returnArray["imageWidth"]=imagesx($im);
					$returnArray["imageHeight"]=imagesy($im);
					$returnArray["imageValid"] = true;
					$binary_data=null;
					$im=null;
				} else {// from jeedom flux method
					$returnArray["image"]=network::getNetworkAccess('internal') . '/' .$eqLogic->getUrl($eqLogic_array["configuration"]["urlStream"],true);
					$binary_data = file_get_contents($returnArray["image"]);
					if($binary_data){
						$im = imagecreatefromstring($binary_data);
						$returnArray["imageWidth"]=imagesx($im);
						$returnArray["imageHeight"]=imagesy($im);
						$returnArray["imageValid"] = true;
						$binary_data=null;
						$im=null;
					} else {
						unset($returnArray["image"]);
					}
				}
			}
			
		}
		return $returnArray;
	}
	public static function cryptedMagic() {
		$magicField = config::byKey('magicField','homebridge',"",true);
		$magicField = explode(" ",$magicField);
		foreach($magicField as &$magicWord) {
			$magicWord = crypt($magicWord,"NBZ");
		}
		return $magicField;	
	}
	
	public static function isMagic($magicValue) {
		$magicField = homebridge::cryptedMagic();
		return ((array_search($magicValue,$magicField) !== false) ? true : false);
	}	
	
	public static function generate_file(){
		log::add('homebridge','info','Génération du fichier config.json de Homebridge');
		if(self::deamon_info()=="ok") self::deamon_stop();

		if(jeedom::version() >= '3.2.1' && config::byKey('migrated321','homebridge',false,true) === false) homebridge::migrateCustomData();
		
		$AdminUsers= user::byProfils("admin",true);
		$user = $AdminUsers[0]; // take the first one
		if(is_object($user)){
			$apikey = $user->getHash();
		}else{
			$apikey = config::byKey('api');
		}
		//$apikey = jeedom::getApiKey('homebridge'); need to manage jeeHomebridge.php first

		if(homebridge::isMagic('NBJCKZ/fOJDnM')) { // enable beta
			file_put_contents(dirname(__FILE__) . '/../../branch','beta');
		}
		if(homebridge::isMagic('NBz//9iJgk0sA')) { // enable alpha
			file_put_contents(dirname(__FILE__) . '/../../branch','alpha');
		}
		if(homebridge::isMagic('NBe/9kOLwyupc')) { // enable master
			file_put_contents(dirname(__FILE__) . '/../../branch','master');
		}
		$fakegato=( (config::byKey('fakegato','homebridge',false,true))?true:false);
		if(homebridge::isMagic('NBOD0V56Srf.k')) { // enable fakegato
			$fakegato=true;
		}
		
		$pin_homebridge = config::byKey('pin_homebridge','homebridge','031-45-154',true);
		config::save('pin_homebridge',$pin_homebridge,'homebridge');
		$name_homebridge = config::byKey('name_homebridge','homebridge',config::byKey('name'),true);
		config::save('name_homebridge',$name_homebridge,'homebridge');
		$mac_homebridge = config::byKey('mac_homebridge','homebridge',self::generateRandomMac(),true);
		config::save('mac_homebridge',$mac_homebridge,'homebridge');
		$setupID_homebridge = str_replace(':','',$mac_homebridge);
		$setupID_homebridge = substr($setupID_homebridge,-4);
		
		if(in_array($pin_homebridge,self::DisallowedPIN())) {
			log::add('homebridge', 'error', 'Le PIN Homebridge n\'est pas autorisée par Apple : '.$pin_homebridge);	
		}
		
		$response = [];
		$response['bridge'] = [];
		$response['bridge']['name'] = $name_homebridge;
		$response['bridge']['username'] = $mac_homebridge;
		$response['bridge']['port'] = 51826;
		$response['bridge']['pin'] = $pin_homebridge;
		
		$response['bridge']['manufacturer'] = "Jeedom";
		$response['bridge']['model'] = "Homebridge";
		$response['bridge']['serialNumber'] = $mac_homebridge;
		$response['bridge']['setupID'] = $setupID_homebridge;

		
		$response['description'] = "Autogenerated config file by Jeedom";
		
		$plateform['platform'] = "Jeedom";
		$plateform['name'] = $name_homebridge;
		$plateform['url'] = network::getNetworkAccess('internal');
		$plateform['apikey'] = $apikey;
		$plateform['pollerperiod'] = 0.05;
		$plateform['fakegato'] = $fakegato;
		$plateform['debugLevel'] = log::getLogLevel('homebridge');
		$plateform['myPlugin'] = 'homebridge';
		$plateform['magicField'] = join(' ',homebridge::cryptedMagic());
		if(homebridge::isMagic('NBwoMwwLkQk0k')) { // High Level Debug
			$plateform['debugLevel'] = 0;
		}		
		$response['platforms'] = [];
		$response['platforms'][] = $plateform;

		// get file and add it if it's valid
		$jsonFile = homebridge::getJSON('otherPlatform');
		$jsonPlatforms = explode('|',$jsonFile);
		if(!$jsonPlatforms)
			$jsonPlatforms = array($jsonFile);
		
		config::save('hasAlexa',false,'homebridge');
		foreach ($jsonPlatforms as $jsonPlatform) {
			$jsonArr = json_decode($jsonPlatform,true);
			if($jsonArr !== null) {
				$pluginCameraExists=true;
				try {
					$pluginCamera = plugin::byId('camera');
				} catch(Exception $e) {
					$pluginCameraExists=false;
				}
				if($jsonArr['platform']=='Camera-ffmpeg') {
					if($pluginCameraExists) {
						$AVCONVexists = shell_exec('file -bi `which avconv`');
						$FFMPEGexists = shell_exec('file -bi `which ffmpeg`');
						
						if (strpos($AVCONVexists, 'application') !== false) {
							log::add('homebridge','info','Avconv existe et c\'est un exécutable, on l\'utilise');
							$jsonArr['videoProcessor'] = dirname(__FILE__) . '/../../resources/ffmpeg-wrapper';
						} elseif (strpos($FFMPEGexists, 'application') !== false) {
							log::add('homebridge','info','FFMPEG existe et c\'est un exécutable, on l\'utilise');
							$jsonArr['videoProcessor'] = 'ffmpeg';
						} else {
							log::add('homebridge','error','Ni FFMPEG, ni avconv n\'existent... impossible de faire fonctionner les caméras');
							log::add('homebridge','error','Réinstallez les dépendances du plugin Camera');
						}
					}
					else {
						log::add('homebridge','error','Le plugin Camera n\'existe pas, installez-le');
					}
				} elseif ($jsonArr['platform']=='Alexa') {
					config::save('hasAlexa',true,'homebridge'); // we have Alexa config so we'll start the daemon as in Insecure
				}
				$response['platforms'][] = $jsonArr;
			}
		}
		
		$jsonFileAccessory = homebridge::getJSON('otherAccessory');
		$jsonAccessories = explode('|',$jsonFileAccessory);
		if(!$jsonAccessories)
			$jsonAccessories = array($jsonFileAccessory);
		foreach ($jsonAccessories as $jsonAccessory) {
			$jsonArrAcc = json_decode($jsonAccessory,true);
			if($jsonArrAcc !== null) {
				$response['accessories'][] = $jsonArrAcc;
			}
		}
		
		exec(system::getCmdSudo() . 'mkdir ' . dirname(__FILE__) . '/../../resources/homebridge >/dev/null 2>&1 &');
		exec(system::getCmdSudo() . 'chown -R www-data:www-data ' . dirname(__FILE__) . '/../../resources');
		$fp = fopen(dirname(__FILE__) . '/../../resources/homebridge/config.json', 'w');
		fwrite($fp, json_encode($response));
		fclose($fp);
		if(!file_exists(dirname(__FILE__) . '/../../resources/homebridge/config.json')) {
			log::add('homebridge','error','Le fichier config.json de Homebridge n\'existe pas : '.dirname(__FILE__) . '/../../resources/homebridge/config.json');
		}
	}
	
	public static function deamon_info() {
		$return = [];
		$return['log'] = 'homebridge';
		$return['state'] = 'nok';
		
		$result = exec("ps -eo pid,command | grep ' homebridge' | grep -v grep | awk '{print $1}'");
		if ($result <> 0) {
            $return['state'] = 'ok';
        }
		$return['launchable'] = 'ok';
		return $return;
	}
	public static function deamon_start($_debug = false) {
		if(log::getLogLevel('homebridge')==100) $_debug=true;
		log::add('homebridge', 'info', 'Mode debug : ' . $_debug);
		self::deamon_stop();
		self::generate_file();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}

		if(strtolower(jeedom::getHardwareName()) == "docker") {
			// check dbus-daemon started, if not, start
			$cmd = 'if [ $(ps -ef | grep -v grep | grep "dbus-daemon" | wc -l) -eq 0 ]; then ' . system::getCmdSudo() . 'service dbus start;echo "Démarrage dbus-daemon";sleep 1; fi';
			exec($cmd . ' >> ' . log::getPathToLog('homebridge') . ' 2>&1');
			// start 2 times if Docker
			$cmd = 'if [ $(ps -ef | grep -v grep | grep "avahi-daemon" | wc -l) -eq 0 ]; then ' . system::getCmdSudo() . 'service avahi-daemon start;echo "Démarrage avahi-daemon 2";sleep 1; fi';
			exec($cmd . ' >> ' . log::getPathToLog('homebridge') . ' 2>&1');
			// start 2 times if Docker
			$cmd = 'if [ $(ps -ef | grep -v grep | grep "avahi-daemon" | wc -l) -eq 0 ]; then ' . system::getCmdSudo() . 'service avahi-daemon start;echo "Démarrage avahi-daemon 2";sleep 1; fi';
			exec($cmd . ' >> ' . log::getPathToLog('homebridge') . ' 2>&1');
		} else {
			// check avahi-daemon started, if not, start
			$cmd = 'if [ $(ps -ef | grep -v grep | grep "avahi-daemon" | wc -l) -eq 0 ]; then ' . system::getCmdSudo() . 'systemctl start avahi-daemon.service;echo "Démarrage avahi-daemon";sleep 1; fi';
			exec($cmd . ' >> ' . log::getPathToLog('homebridge') . ' 2>&1 &');	
		}
		
		$insecure='';
		if(homebridge::isMagic('NBakLcxU29STU') || config::byKey('hasAlexa','homebridge',false,true)) { // pass homebridge insecure (for alexa)
			$insecure='-I ';
			log::add('homebridge', 'info', 'Configuration Alexa détectée, le Démon sera démarré en "Insecure" (Permet à un plugin d\'accéder aux status des accessoires)');
		}			
		
		$cmd = 'export AVAHI_COMPAT_NOWARN=1;'. (($_debug) ? 'DEBUG=* ':'') .dirname(__FILE__) . '/../../resources/node_modules/homebridge/bin/homebridge '. (($_debug) ? '-D ':'') . $insecure . '--no-qrcode ' .'-U '.dirname(__FILE__) . '/../../resources/homebridge';
		exec($cmd . ' >> ' . log::getPathToLog('homebridge_daemon') . ' 2>&1 &');
		$i = 0;
		while ($i < 30) {
			$deamon_info = self::deamon_info();
			if ($deamon_info['state'] == 'ok') {
				break;
			}
			sleep(1);
			$i++;
		}
		if ($i >= 30) {
			log::add('homebridge', 'error', 'Impossible de lancer le démon homebridge, relancer le démon en debug et vérifiez la log', 'unableStartDeamon');
			return false;
		}
		message::removeAll('homebridge', 'unableStartDeamon');
		log::add('homebridge', 'info', 'Démon homebridge lancé');
		
		// Check if multiple IP's -> warning because could cause problems with mdns https://github.com/nfarina/homebridge/issues/1351
		$cmd = 'if [ $(' . system::getCmdSudo() . 'ip addr | grep "inet " | grep -v " tun" | grep -v " lo" | wc -l) -gt 1 ]; then echo "WARNING : Vous avez plusieurs IP de configurées, cela peut poser problème avec Homebridge et mDNS"; fi';
		exec($cmd . ' >> ' . log::getPathToLog('homebridge') . ' 2>&1 &');
		return true;
	}
	public static function deamon_stop() {
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] <> 'ok') {
            return true;
        }
        
		$pid = exec("ps -eo pid,command | grep ' homebridge' | grep -v grep | awk '{print $1}'");
        if ($pid) {
            system::kill($pid);
        }
        system::kill('homebridge');
		system::fuserk(51826);
		
        $check = self::deamon_info();
        $retry = 0;
        while ($deamon_info['state'] == 'ok') {
           $retry++;
            if ($retry > 10) {
                return;
            } else {
                sleep(1);
            }
        }
        return self::deamon_info();
	}
	

	/**************************************************************************************/
	/*                                                                                    */
	/*            Permet de supprimer tout Homebridge                		      */
	/*                                                                                    */
	/**************************************************************************************/
	
	public static function uninstallHomebridge() {
		/*
		log::add('homebridge', 'info', 'Suppression homebridge-camera-ffmpeg...');
		$cmd = system::getCmdSudo() . 'npm rm -g homebridge-camera-ffmpeg --save';
		exec($cmd);
		log::add('homebridge', 'info', 'Suppression homebridge-jeedom...');
		$cmd = system::getCmdSudo() . 'npm rm -g homebridge-jeedom --save';
		exec($cmd);
		log::add('homebridge', 'info', 'Suppression homebridge...');
		$cmd = system::getCmdSudo() . 'npm rm -g homebridge --save';
		exec($cmd);
		log::add('homebridge', 'info', 'Suppression request...');
		$cmd = system::getCmdSudo() . 'npm rm -g request --save';
		exec($cmd);
		log::add('homebridge', 'info', 'Suppression node-gyp...');
		$cmd = system::getCmdSudo() . 'npm rm -g node-gyp --save';
		exec($cmd);
		log::add('homebridge', 'info', 'Rebuild...');
		$cmd = 'cd `npm root -g`;' . system::getCmdSudo() . 'npm rebuild;';
		exec($cmd);
		
		log::add('homebridge', 'info', 'Suppression bin homebridge');
		$cmd = system::getCmdSudo() . 'rm -f /usr/bin/homebridge >/dev/null 2>&1';
		exec($cmd);
		$cmd = system::getCmdSudo() . 'rm -f /usr/local/bin/homebridge >/dev/null 2>&1';
		exec($cmd);
		*/
		$cmd = system::getCmdSudo() . 'rm -rf '.dirname(__FILE__) . '/../../resources/node_modules &>/dev/null';
		log::add('homebridge', 'info', 'Homebridge supprimé');
	}
	
	
	public static function repairHomebridge($reinstall=true) {
		$pluginHomebridge = plugin::byId('homebridge');
		log::add('homebridge', 'info', 'Procedure de réparation');
		$pluginHomebridge->deamon_stop();
		log::add('homebridge', 'info', 'suppression des accessoires et du persist');
		$cmd = system::getCmdSudo() . 'rm -Rf '.dirname(__FILE__) . '/../../resources/homebridge/accessories';
		exec($cmd);
		$cmd = system::getCmdSudo() . 'rm -Rf '.dirname(__FILE__) . '/../../resources/homebridge/persist';
		exec($cmd);
		$cmd = system::getCmdSudo() . 'rm -f '.dirname(__FILE__) . '/../../resources/homebridge/*_persist.json';
		exec($cmd);
		if($reinstall) {
			homebridge::uninstallHomebridge();
			$cmd = system::getCmdSudo() . 'apt-get -y --purge autoremove nodejs npm';
			exec($cmd);
		}
		$mac_homebridge = self::generateRandomMac();
		log::add('homebridge', 'info', 'création d\'une nouvelle MAC adress : '.$mac_homebridge);
		config::save('mac_homebridge',$mac_homebridge,'homebridge');
		$name_homebridge = config::byKey('name').'_Repaired_'.base_convert(mt_rand(0,255),10,16);
		config::save('name_homebridge',$name_homebridge,'homebridge');
		if($reinstall) {
			log::add('homebridge', 'info', 'réinstallation des dependances');
			$pluginHomebridge->dependancy_install();
		}
		if(strtolower(jeedom::getHardwareName()) == "docker") {
			exec(system::getCmdSudo().'systemctl restart dbus.service || '.system::getCmdSudo().'service dbus restart;sleep 1');
		}
		exec(system::getCmdSudo().'systemctl restart avahi-daemon.service || '.system::getCmdSudo().'service avahi-daemon restart');
		$return['mac_homebridge']=$mac_homebridge;
		$return['name_homebridge']=$name_homebridge;
		return $return;
	}

	public static function generateRandomMac() {
		return strtoupper(implode(':', str_split(substr(md5(mt_rand()), 0, 12), 2)));
	}

	public static function generateQRCode($size,$pin_homebridge = '') {
		if($pin_homebridge == '')
			$pin_homebridge = config::byKey('pin_homebridge','homebridge','031-45-154',true);

		$mac_homebridge = config::byKey('mac_homebridge','homebridge','NEBZ',true);
		$setupID_homebridge = str_replace(':','',$mac_homebridge);
		$setupID_homebridge = substr($setupID_homebridge,-4);
		
		$size = $size ? $size : '100x100';
		
		$Link="";
		if(extension_loaded('gmp')) {
			$pin_homebridge=str_replace('-','',$pin_homebridge);
			$payload = gmp_mul(gmp_init(2,16),gmp_pow(2,31));
			$payload = gmp_or($payload,gmp_mul(gmp_init(1,16),gmp_pow(2,28)));
			$payload = gmp_or($payload,gmp_init($pin_homebridge,10));
			$Link=trim("X-HM://00".strtoupper(gmp_strval($payload,36)).$setupID_homebridge);
		} 
		
		if(strlen($Link) > 0 && strlen($Link) < 30)
			return '/plugins/homebridge/3rdparty/genQR.php?size='.$size.'&rnd='.rand().'&data='.$Link;
		//http://chart.apis.google.com/chart?cht=qr&chs='.$size.'&chl='.$Link.'&chld=H|0
		else
			return "";
	}
	
	/**************************************************************************************/
	/*                                                                                    */
	/*            Permet de decouvrir tout les modules de la Jeedom compatible            */
	/*                                                                                    */
	/**************************************************************************************/

	public static function discovery_eqLogic($plugin = [],$customEqLogics){
		$return = [];
		foreach ($plugin as $plugin_type) {
			$eqLogics = eqLogic::byType($plugin_type/*, true*/);
			if (!is_array($eqLogics)) {
				continue;
			}
			foreach ($eqLogics as $eqLogic) {
				if(		$eqLogic->getObject_id() !== null // has room
					&& 	object::byId($eqLogic->getObject_id())->getDisplay('sendToApp', 1) == 1 // if that room is active
					){
					
					$eqLogic_array = utils::o2a($eqLogic);
					
					foreach($customEqLogics as $custeqLogic) { // import customConfiguration
						if($eqLogic_array['id'] == $custeqLogic['id']) {
							$eqLogic_array['customConfiguration'] = $custeqLogic['configuration'];
							break;
						}
					}
					
					

					if(isset($eqLogic_array["configuration"]["sendToHomebridge"])){
						$eqLogic_array["sendToHomebridge"] = intval($eqLogic_array["configuration"]["sendToHomebridge"]);
					}
					
					//Alarm
					if(isset($eqLogic_array["customConfiguration"]['SetModeAbsent'])){
						if(!isset($eqLogic_array["alarmModes"])) $eqLogic_array["alarmModes"] = [];
						$eqLogic_array["alarmModes"]["SetModeAbsent"] = $eqLogic_array["customConfiguration"]['SetModeAbsent'];
					}
					if(isset($eqLogic_array["customConfiguration"]['SetModePresent'])){
						if(!isset($eqLogic_array["alarmModes"])) $eqLogic_array["alarmModes"] = [];
						$eqLogic_array["alarmModes"]["SetModePresent"] = $eqLogic_array["customConfiguration"]['SetModePresent'];
					}
					if(isset($eqLogic_array["customConfiguration"]['SetModeNuit'])){
						if(!isset($eqLogic_array["alarmModes"])) $eqLogic_array["alarmModes"] = [];
						$eqLogic_array["alarmModes"]["SetModeNuit"] = $eqLogic_array["customConfiguration"]['SetModeNuit'];
					}
					
					//Thermostat
					if(isset($eqLogic_array["customConfiguration"]['Chauf'])){
						if(!isset($eqLogic_array["thermoModes"])) $eqLogic_array["thermoModes"] = [];
						$eqLogic_array["thermoModes"]["Chauf"] = $eqLogic_array["customConfiguration"]['Chauf'];
					}
					if(isset($eqLogic_array["customConfiguration"]['Clim'])){
						if(!isset($eqLogic_array["thermoModes"])) $eqLogic_array["thermoModes"] = [];
						$eqLogic_array["thermoModes"]["Clim"] = $eqLogic_array["customConfiguration"]['Clim'];
					}
					if(isset($eqLogic_array["customConfiguration"]['Off'])){
						if(!isset($eqLogic_array["thermoModes"])) $eqLogic_array["thermoModes"] = [];
						$eqLogic_array["thermoModes"]["Off"] = $eqLogic_array["customConfiguration"]['Off'];
					}
					
					if(isset($eqLogic_array["customConfiguration"]['customValues'])){
						if(!isset($eqLogic_array["customValues"])) $eqLogic_array["customValues"] = [];
						
						$tempArray['OPEN'] = $eqLogic_array["customConfiguration"]['OPEN'];
						$tempArray['OPEN'] = (($tempArray['OPEN'] != "")?intval($tempArray['OPEN']):NULL);
						$tempArray['OPENING'] = $eqLogic_array["customConfiguration"]['OPENING'];
						$tempArray['OPENING'] = (($tempArray['OPENING'] != "")?intval($tempArray['OPENING']):NULL);
						$tempArray['STOPPED'] = $eqLogic_array["customConfiguration"]['STOPPED'];
						$tempArray['STOPPED'] = (($tempArray['STOPPED'] != "")?intval($tempArray['STOPPED']):NULL);
						$tempArray['CLOSING'] = $eqLogic_array["customConfiguration"]['CLOSING'];
						$tempArray['CLOSING'] = (($tempArray['CLOSING'] != "")?intval($tempArray['CLOSING']):NULL);
						$tempArray['CLOSED'] = $eqLogic_array["customConfiguration"]['CLOSED'];
						$tempArray['CLOSED'] = (($tempArray['CLOSED'] != "")?intval($tempArray['CLOSED']):NULL);

						$eqLogic_array["customValues"] = $tempArray;
						$tempArray=[];
					}
					if (isset($eqLogic_array['isVisible'])){
						$eqLogic_array['isVisible']=intval($eqLogic_array['isVisible']);
					}
					if (isset($eqLogic_array['isEnable'])){
						$eqLogic_array['isEnable']=intval($eqLogic_array['isEnable']);
					}
					unset($eqLogic_array['eqReal_id'],$eqLogic_array['configuration'],$eqLogic_array['customConfiguration'], $eqLogic_array['specificCapatibilities'],$eqLogic_array['timeout'],$eqLogic_array['category'],$eqLogic_array['display']);
					$return[] = $eqLogic_array;
				}
			}
		}
		return $return;
	}
	
	public static function discovery_cmd($plugin = [],$customCmds){
		$return = [];
		$PluginAutoConfig = self::PluginAutoConfig();
		foreach ($plugin as $plugin_type) {
			$eqLogics = eqLogic::byType($plugin_type/*, true*/);
			if (!is_array($eqLogics)) {
				continue;
			}
			foreach ($eqLogics as $eqLogic) {
				$i = 0;
				if(		$eqLogic->getObject_id() !== null // has room
					&& 	object::byId($eqLogic->getObject_id())->getDisplay('sendToApp', 1) == 1 // if that room is active
					){
						
					$cmds = $eqLogic->getCmd();
					
					$pluginId = $eqLogic->getEqType_name();
					$specificField=null;
					$specificValue=null;
					if(isset($PluginAutoConfig[$pluginId])) {
						$specificField = $PluginAutoConfig[$pluginId]['field'];
						if(isset($specificField)) {
							$specificValue = $eqLogic->getConfiguration($specificField);
						}
					}
					
					foreach ($cmds as $cmd) {
						$cmd_array = $cmd->exportApi();
						
						// not needed anymore, done by the core
						//if (jeedom::version() >= '3.2.1') {
						//	if(!$cmd_array['generic_type'] && $cmd_array['display'] && $cmd_array['display']['generic_type']) {
						//		$cmd->setGeneric_type($cmd_array['display']['generic_type']);
						//		$cmd->save();
						//		$cmd_array['generic_type']=$cmd_array['display']['generic_type'];
						//	}
						//}
							
						// replace generic_type if auto-config data exists
						$logicalId = $cmd_array['logicalId'];
						if(!isset($specificValue)) $specificValue = 'default';
						if(	isset($PluginAutoConfig[$pluginId]) && 
							isset($PluginAutoConfig[$pluginId][$specificValue]) && 
							isset($PluginAutoConfig[$pluginId][$specificValue][$logicalId])) {
						
							$cmd_array['generic_type'] = $PluginAutoConfig[$pluginId][$specificValue][$logicalId];
						}
						
						// replace generic_type if custom type exists							
						foreach($customCmds as $custCmd) { 
							if($cmd_array['id'] == $custCmd['id']) {
								if (jeedom::version() >= '3.2.1') {
									$cmd_array['generic_type'] = $custCmd['generic_type'];
								} else {
									$cmd_array['generic_type'] = $custCmd['display']['generic_type'];
								}
								$cmd_array['customConfiguration'] = $custCmd['configuration'];
								break;
							}
						}

						// we kept errors as it might be a custom but now we could ignore it
						if(in_array($cmd_array['generic_type'],['GENERIC_ERROR','DONT'])) continue;
						
						//Variables
						$maxValue = null;
						$minValue = null;
						$listValue = null;
						$actionCodeAccess = null;
						$actionConfirm = null;
						$icon = null;
						$invertBinary = null;
						$title_disable = null;
						$title_placeholder = null;
						$message_placeholder = null;
							
						if(isset($cmd_array['configuration'])){
							$configuration = $cmd_array['configuration'];
							if(isset($configuration['maxValue']) && $configuration['maxValue'] != ""){
								$maxValue = $configuration['maxValue'];
							}
							if(isset($configuration['minValue']) && $configuration['minValue'] != ""){
								$minValue = $configuration['minValue'];
							}
							if(isset($configuration['listValue']) && $configuration['listValue'] != ""){
								$listValue = $configuration['listValue'];
							}
							if(isset($configuration['actionCodeAccess'])){
								$actionCodeAccess = $configuration['actionCodeAccess'];
							}
							if(isset($configuration['actionConfirm'])){
								$actionConfirm = $configuration['actionConfirm'];
							}
						}
						if(isset($cmd_array["customConfiguration"]['customValuesStatelessAllinone']) && $cmd_array["customConfiguration"]['customValuesStatelessAllinone']=="1"){
							if(!isset($cmd_array["customValues"])) $cmd_array["customValues"] = [];
							
							$tempArray['SINGLE'] = (($cmd_array["customConfiguration"]['SINGLE'] != "")?$cmd_array["customConfiguration"]['SINGLE']:'TODEL');
							$tempArray['DOUBLE'] = (($cmd_array["customConfiguration"]['DOUBLE'] != "")?$cmd_array["customConfiguration"]['DOUBLE']:'TODEL');
							$tempArray['LONG'] =   (($cmd_array["customConfiguration"]['LONG'] != "")?$cmd_array["customConfiguration"]['LONG']:'TODEL');

							foreach($tempArray as $label => $val) {
								if($val === 'TODEL') unset($tempArray[$label]);
							}

							$cmd_array["customValues"] = $tempArray;
							$tempArray=[];
						}	
						if(isset($cmd_array["customConfiguration"]['customValuesStateless']) && $cmd_array["customConfiguration"]['customValuesStateless']=="1"){
							if(!isset($cmd_array["customValues"])) $cmd_array["customValues"] = [];
							
							$tempArray['BUTTON'] = (($cmd_array["customConfiguration"]['BUTTON'] != "")?intval($cmd_array["customConfiguration"]['BUTTON']):'TODEL');

							foreach($tempArray as $label => $val) {
								if($val === 'TODEL') unset($tempArray[$label]);
							}

							$cmd_array["customValues"] = $tempArray;
							$tempArray=[];
						}								
						if(isset($cmd_array['display'])){
							$display = $cmd_array['display'];
							if(isset($display['icon'])){
								$icon = $display['icon'];
							}
							if(isset($display['invertBinary'])){
								$invertBinary = $display['invertBinary'];
							}
							if(isset($display['title_disable'])){
								$title_disable = $display['title_disable'];
							}
							if(isset($display['title_placeholder'])){
								$title_placeholder = $display['title_placeholder'];
							}
							if(isset($display['message_placeholder'])){
								$message_placeholder = $display['message_placeholder'];
							}
						}
						
						unset($cmd_array['isHistorized'],$cmd_array['configuration'], $cmd_array["customConfiguration"], $cmd_array['template'], $cmd_array['display'], $cmd_array['html']);
						
						if ($maxValue != null) {
							$cmd_array['configuration']['maxValue'] = floatval($maxValue);
						}
						if ($minValue != null) {
							$cmd_array['configuration']['minValue'] = floatval($minValue);
						}
						if ($listValue != null){
							$cmd_array['configuration']['listValue'] = $listValue;	
						}
						if ($icon != null) {
							$cmd_array['display']['icon'] = $icon;
						}
						if(isset($invertBinary)){
							if ($invertBinary != null) {
								$cmd_array['display']['invertBinary'] = intval($invertBinary);
							}
						}
						if(isset($title_disable)){
							if ($title_disable != null) {
								$cmd_array['display']['title_disable'] = $title_disable;
							}
						}
						if(isset($title_placeholder)){
							if ($title_placeholder != null) {
								$cmd_array['display']['title_placeholder'] = $title_placeholder;
							}
						}
						if(isset($message_placeholder)){
							if ($message_placeholder != null) {
								$cmd_array['display']['message_placeholder'] = $message_placeholder;
							}
						}
						if(isset($actionCodeAccess)){
							if($actionCodeAccess !== null ){
								if($actionCodeAccess !== ''){
									$cmd_array['configuration']['actionCodeAccess'] = true;
								}
							}
						}
						if(isset($actionConfirm)){
							if($actionConfirm !== null){
								if($actionConfirm == 1){
									$cmd_array['configuration']['actionConfirm'] = true;
								}
							}
						}
						if ($cmd_array['type'] == 'action'){
							unset($cmd_array['currentValue']);
						}
						if ($cmd_array['type'] == 'info'){
							if ($cmd_array['value'] === null || $cmd_array['value'] == "") {
								unset($cmd_array['value']);
							}
							$cmd_array['configuration']['phpType'] = gettype($cmd_array['currentValue']);
							
							if ($cmd_array['subType'] == 'numeric' && $cmd_array['currentValue'] == '')
							{
								//if not yet initialized
								$cmd_array['currentValue']=0;
							}
						}
						
						if (isset($cmd_array['value']) && $cmd_array['value'] !== null && $cmd_array['value'] != ""){
							$cmd_array['value'] = str_replace("#","",$cmd_array['value']);	
						}
						if ($cmd_array['unite'] === null || $cmd_array['unite'] == ""){
							unset($cmd_array['unite']);
						}
						if (isset($cmd_array['isVisible'])){
							$cmd_array['isVisible']=intval($cmd_array['isVisible']);
						}
						$cmds_array[] = $cmd_array;
						$i++;
					}
					if($i > 0){
						$return = $cmds_array;
					}
				}
			}
		}
		return $return;
	}
	
	public static function discovery_multi($cmds) {
		$array_final = [];
		$tableData = self::PluginMultiInEqLogic();
		foreach ($cmds as &$cmd) {
			if(in_array($cmd['generic_type'], $tableData)){
				$keys = array_keys(array_column($cmds,'eqLogic_id'), $cmd['eqLogic_id']);
				$trueKeys = array_keys(array_column($cmds,'generic_type'), $cmd['generic_type']);
				//if(count($keys) > 1 && count($trueKeys) > 1){
					$result =  array_intersect($keys, $trueKeys);
					if(count($result) > 1){
						$array_final = array_merge_recursive($array_final, $result);
					}
				//}
				
			}
		}
		$dif = [];
		$array_cmd_multi = [];
		foreach ($array_final as &$array_fi){
			if(!in_array($array_fi, $dif)){
				array_push($dif, $array_fi);
				array_push($array_cmd_multi,$array_fi);
			}
		}
		
		return $array_cmd_multi;
	}
	
	public static function change_cmdAndeqLogic($cmds,$eqLogics){
		$plage_cmd = self::discovery_multi($cmds);
		$eqLogic_array = [];
		$nbr_cmd = count($plage_cmd);
		//log::add('homebridge', 'debug', 'plage cmd > '.json_encode($plage_cmd).' // nombre > '.$nbr_cmd);
		if($nbr_cmd != 0){
			$i = 0;
			while($i < $nbr_cmd){
				log::add('homebridge', 'debug', 'nbr cmd > '.$i.' // id > '.$plage_cmd[$i]);
				$eqLogic_id = $cmds[$plage_cmd[$i]]['eqLogic_id'];
				$name_cmd = $cmds[$plage_cmd[$i]]['name'];
				foreach ($eqLogics as &$eqLogic){
					if($eqLogic['id'] == $eqLogic_id){
						$eqLogic_name = $eqLogic['name'].' / '.$name_cmd;
					}
				}
				log::add('homebridge', 'debug', 'nouveau nom > '.$eqLogic_name);
				$id = $cmds[$plage_cmd[$i]]['id'];
				$new_eqLogic_id = '999'.$eqLogic_id.''.$id;
				$cmds[$plage_cmd[$i]]['eqLogic_id'] = $new_eqLogic_id;
				$keys = array_keys(array_column($cmds,'eqLogic_id'),$eqLogic_id);
				$nbr_keys = count($keys);
				$j = 0;
				while($j < $nbr_keys){
					if(isset($cmds[$keys[$j]]['value']) && $cmds[$keys[$j]]['value'] == $cmds[$plage_cmd[$i]]['id'] && $cmds[$keys[$j]]['type'] == 'action'){
						log::add('homebridge', 'info', 'Changement de l\'action > '.$cmds[$keys[$j]]['id']);
						$cmds[$keys[$j]]['eqLogic_id'] = $new_eqLogic_id;
					}
					$j++;
				}
				array_push($eqLogic_array,array($eqLogic_id, $new_eqLogic_id, $eqLogic_name));
				$i++;
			}
			
			$column_eqlogic = array_column($eqLogics,'id');
			foreach ($eqLogic_array as &$eqlogic_array_one) {
				$keys = array_keys($column_eqlogic, $eqlogic_array_one[0]);
				$new_eqLogic = $eqLogics[$keys[0]];
				$new_eqLogic['id'] = $eqlogic_array_one[1];
				$new_eqLogic['name'] = $eqlogic_array_one[2];
				array_push($eqLogics, $new_eqLogic);
			}		
		}
		
		$new_cmds = array('cmds' => $cmds);
		$new_eqLogic = array('eqLogics' => $eqLogics);
		$news = array($new_cmds,$new_eqLogic);
		return $news;
	}
	
	public static function discovery_object() {
		$all = utils::o2a(object::all());
		$return = [];
		foreach ($all as &$object){
			if (isset($object['display']['sendToApp']) && $object['display']['sendToApp'] == "0") {
				continue;
			} else {
				unset($object['configuration'],$object['display']['tagColor'], $object['display']['tagTextColor'],$object['display']['summaryTextColor'],$object['display']['icon']);
				if (isset($object['isVisible'])) {
					$object['isVisible']=intval($object['isVisible']);
				}
				if (isset($object['display']['sendToApp'])) {
					$object['display']['sendToApp']=intval($object['display']['sendToApp']);
				}
				$return[]=$object;
			}
		}
		return $return;
	}
	 
	public static function discovery_scenario($customScenarios) {
		$all = utils::o2a(scenario::all());
		$return = [];
		foreach ($all as &$scenario){
			if (isset($scenario['display']['sendToApp']) && $scenario['display']['sendToApp'] == "0") {
				continue;
			} else {
				if ($scenario['display']['name'] != ''){
					$scenario['name'] = $scenario['display']['name'];
				}
				foreach($customScenarios as $custScen) { 
					if($scenario['id'] == $custScen['id']) {
						$scenario['sendToHomebridge'] = intval($custScen['configuration']['sendToHomebridge']);
						break;
					}
				}
				unset($scenario['mode'],$scenario['schedule'], $scenario['scenarioElement'],$scenario['trigger'],$scenario['timeout'],$scenario['description'],$scenario['configuration'],$scenario['type'],$scenario['display']['name']);
				if (isset($scenario['isVisible'])) {
					$scenario['isVisible']=intval($scenario['isVisible']);
				}
				if (isset($scenario['isActive'])) {
					$scenario['isActive']=intval($scenario['isActive']);
				}
				if (isset($scenario['display']['sendToApp'])) {
					$scenario['display']['sendToApp']=intval($scenario['display']['sendToApp']);
				}
				if ($scenario['display'] == [] || $scenario['display']['icon'] == ''){
					unset($scenario['display']);
				}	
				unset($scenario['lastLaunch']);
				$return[]=$scenario;
			}	
		}
		return $return;
	}
	
	public static function delete_object_eqlogic_null($objects, $eqLogics) {
		$return = array();
		$object_id = array();
		foreach ($eqLogics as $eqLogic) {
			$object_id[$eqLogic['object_id']] = $eqLogic['object_id'];
		}
		foreach ($objects as $object) {
			if (!isset($object_id[$object['id']])) {
				continue;
			}
			$return[] = $object;
		}
		return $return;
	}

	
	/**************************************************************************************/
	/*                                                                                    */
	/*                         Permet de creer l'ID Unique du téléphone                   */
	/*                                                                                    */
	/**************************************************************************************/
	
	/*public function postInsert() {
		$key = config::genKey(32);
		$this->setLogicalId($key);
		$this->save();
	}
	
	public function postSave() {
		$this->crea_cmd();
	}
    
    function crea_cmd() {
    	$cmd = $this->getCmd(null, 'notif');
        if (!is_object($cmd)) {
			$cmd = new homebridgeCmd();
			$cmd->setLogicalId('notif');
			$cmd->setName(__('Notif', __FILE__));
			$cmd->setIsVisible(1);
			$cmd->setDisplay('generic_type', 'GENERIC_ACTION');
		}
		$cmd->setOrder(0);
		$cmd->setType('action');
		$cmd->setSubType('message');
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();

    }*/
	

	/*     * *********************Méthodes d'instance************************* */

	/*     * **********************Getteur Setteur*************************** */
}

class homebridgeCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	/*
											 * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
											public function dontRemoveCmd() {
											return true;
											}
											 */

	/*public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		$arn = $eqLogic->getConfiguration('notificationArn', null);
		$os = $eqLogic->getConfiguration('type_homebridge', null);
        if ($this->getType() != 'action') {
			return;
		}
		log::add('homebridge', 'debug', 'Notif > '.json_encode($_options).' / '.$eqLogic->getId().' / '.$this->getLogicalId(), 'config');
		if($this->getLogicalId() == 'notif') {
			log::add('homebridge', 'debug', 'Commande de notification ', 'config');
			if($arn != null && $os != null){
				self::notification($arn,$os,$_options['title'],$_options['message']);
				log::add('homebridge', 'debug', 'Action : Envoi d\'une configuration ', 'config');
			}else{
				log::add('homebridge', 'debug', 'ARN non configuré ', 'config');	
			}
		};
	}*/

	/*     * **********************Getteur Setteur*************************** */
}
