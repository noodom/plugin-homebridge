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
 ini_set('display_errors', 0);
if (!isConnect('admin')) {
	throw new Exception('{{401 - AccèÈs non autorisÈ}}');
}

$object = object::byId($_GET['object_id']);
sendVarToJS('object', $_GET['object_id']);
function listAlarmSetModes($cmds,$selected) {
	$opt = "<option value='NOT'>Aucun</option>";
	foreach ($cmds as $cmd) {
		if($cmd->getDisplay('generic_type') == "ALARM_SET_MODE") {
			$val = $cmd->getid().'|'.$cmd->getName();
			$opt.= '<option value="'.$val.'"'.(($selected==$val)?" selected":'').'>'.$cmd->getName().'</option>';
		}
	}
	return $opt;
}
function listThermoSetModes($cmds,$selected) {
	$opt = "<option value='NOT'>Aucun</option>";
	foreach ($cmds as $cmd) {
		if($cmd->getDisplay('generic_type') == "THERMOSTAT_SET_MODE" && $cmd->getName() != "Off") {
			$val = $cmd->getid().'|'.$cmd->getName();
			$opt.= '<option value="'.$val.'"'.(($selected==$val)?" selected":'').'>'.$cmd->getName().'</option>';
		}
		if($cmd->getName() == 'Off' && $selected == 'Off') {
			return '<input class="eqLogicAttrThermo configuration hidden" data-l1key="configuration" data-l2key="Off" value="'.$cmd->getid().'|'.$cmd->getName().'" />';
		}
	}
	return $opt;
}
?>
<style>
	.orange {
		color:black !important;
	}
</style>
<div class="row">
	<div>
		<center>
		<?php
		echo str_replace('></i>', ' style="font-size : 6em;color:#767676;"></i>', $object->getDisplay('icon', '<i class="fa fa-lemon-o"></i>'));
		?>
		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center><?=$object->getName()?></center></span>
		</center>
	</div><br/>
	<div class="alert alert-info div_object_configuration" role="alert">
		{{Vous pouvez activer ou désactiver l'envoi de cette pièce vers l'application}}
		<?php
		$check = 'checked';
		if ($object->getDisplay('sendToApp', 1) == 0) {
			$check = 'unchecked';
		}
		?>
		<label class="checkbox-inline pull-right"><input type="checkbox" class="objectAttr" data-l1key="display" data-l2key="sendToApp" <?=$check?>/>{{Activer}}</label>
		<span class="form-control objectAttr" type="text" data-l1key="id" style="display : none;"><?=$_GET['object_id']?></span>
		<span class="form-control objectAttr" type="text" data-l1key="name" style="display : none;"><?=$object->getName()?></span>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 eqLogicPluginDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend><i class="fa fa-building"></i>  {{Type Générique de l'objet}}
			<div class="form-actions pull-right">
				<a class="btn btn-success eqLogicAction"  style="padding:0px 3px 0px 3px;" onclick="SaveObject()"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
			</div>
		</legend>

		<div class="EnregistrementDisplay"></div>
    
		<?php
		$tableau_cmd = [];
		$eqLogics = $object->getEqLogic();
		$customValuesArr = homebridge::getCustomData();
		?>
		<div class="panel-group" id="accordionConfiguration">
			<?php
			foreach ($eqLogics as $eqLogic) :
				$customEQValuesArr = [];
				if($eqLogic->getEqType_name() == "mobile") continue;
				$check = 'checked';
				if ($eqLogic->getConfiguration('sendToHomebridge', 1) == 0) {
					$check = 'unchecked';
				}
				$eql_id = $eqLogic->getId();
				foreach($customValuesArr['eqLogic'] as $eqLogicCustom) {
					if($eqLogicCustom['id'] == $eql_id) {
						$customEQValuesArr = $eqLogicCustom;	
						break;
					}
				}
				$eql_cmds = cmd::byEqLogicId($eql_id);
			?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionConfiguration" href="#config_<?=$eql_id?>" style="text-decoration:none">
								<span class="eqLogicAttr hidden" data-l1key="id"><?=$eql_id?></span>
								<?=$eqLogic->getHumanName(true)?>
								<a class="btn btn-mini btn-success eqLogicAction pull-right" style="padding:0px 3px 0px 3px;cursor:pointer;" onclick="SaveObject()"><i class="fa fa-floppy-o" style="color:white;"></i></a>
								<small>
									<label style="cursor:default;margin-left:5px">{{  Envoyer à Homebridge  }}<input style="display:inline-block" type="checkbox" class="eqLogicAttr configuration" data-l1key="configuration" data-l2key="sendToHomebridge" <?=$check?>/></label>
								</small>
							</a>
						</h3>
					</div>
					<div id="config_<?=$eql_id?>" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							switch($eqLogic->getEqType_name()) :
								case "alarm" :
									configAlarmModes($customEQValuesArr,$eql_cmds,$eql_id);
								break;
								case "netatmoThermostat":
								case "thermostat" :
									configThermoModes($customEQValuesArr,$eql_cmds,$eql_id);
								break;
								case "weather" :
								?>
									<span class="cmdAttr" data-l1key="id">{{Plugin Météo non supporté pour l'instant}}</span>
								<?php
								break;
								case "mode" :
								?>
									<span class="cmdAttr" data-l1key="id">{{Plugin Mode non supporté pour l'instant}}</span>
								<?php
								break;
								case "camera" :
								?>
									<span class="cmdAttr" data-l1key="id">{{Les caméras peuvent être gérées via les plateformes supplémentaires Homebridge}}</span>
								<?php
								break;
								default :
									$cmds = null;
									$cmds = cmd::byEqLogicId($eql_id);
									$isCustomisable = false;
								?>
									<table id='<?=$eql_id?>' class="table TableCMD">
										<tr>
											<th>{{Id Cmd}}</th>
											<th>{{Nom de la Commande}}</th>
											<th>{{Type Générique}}</th>
										</tr>
										<?php
										foreach ($cmds as $cmd) :
											$cmd_id = $cmd->getId();
											$customCMDValuesArr=['display'=>null];
											array_push($tableau_cmd, $cmd_id);
											foreach($customValuesArr['cmd'] as $cmdCustom) {
												if($cmdCustom['id'] == $cmd_id) {
													$customCMDValuesArr = $cmdCustom['display'];	
													break;
												}
											}
										?>
											<tr class="cmdLine">
												<td>
													<span class="cmdAttr" data-l1key="id"><?=$cmd_id?></span>
												</td>
												<td>
													<?php
													echo $cmd->getName();
													$display_icon = 'none';
													$icon ='';
													if (in_array($cmd->getDisplay('generic_type'), ['GENERIC_INFO','GENERIC_ACTION'])) {
														$display_icon = 'block';
														$icon = $cmd->getDisplay('icon');
													}
													?>
													<div class="iconeGeneric pull-right" style="display:<?=$display_icon?>;">
														<div>
															<span class="cmdAttr label label-info cursor" style="font-size : 1.2em;" ><?=$icon?></span>
															<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>
														</div>
													</div>
												</td>
												<td>
													<select class="cmdAttr form-control" data-l1key="display" data-l2key="generic_type" data-cmd_id="<?php echo $cmd_id; ?>">
														<option value="">{{Aucun}}</option>
														<?php
														$groups = array();
														//$generic_array = array_merge(jeedom::getConfiguration('cmd::generic_type'),homebridge::getCustomGenerics());
														$generic_array = jeedom::getConfiguration('cmd::generic_type') + homebridge::getCustomGenerics(); // merge without replace
														foreach ($generic_array as $key => $info) {
															if ($cmd->getType() == 'info' && $info['type'] == 'Action') {
																continue;
															} elseif ($cmd->getType() == 'action' && $info['type'] == 'Info') {
																continue;
															} elseif (isset($info['family']) && $info['family'] == 'Caméra') { // display ignored types
																continue;
															} elseif (isset($info['family']) && $info['family'] == 'Qualité D\'air' && !homebridge::isMagic('NBXZTP255Nq22')) { // display ignored types
																continue;
															} elseif (isset($info['family']) && $info['family'] == 'Alarme' && !homebridge::isMagic('NBpPxpeFf5QRA')) { // display ignored types
																continue;
															} elseif (isset($info['family']) && $info['family'] == 'Météo') { // display ignored types
																continue;
															} elseif (isset($info['family']) && $info['family'] == 'Mode') { // display ignored types
																continue;
															}
															$info['key'] = $key;
															if (!isset($groups[$info['family']])) {
																$groups[$info['family']][0] = $info;
															} else {
																array_push($groups[$info['family']], $info);
															}
														}
														ksort($groups);
														foreach ($groups as $group) {
															usort($group, function ($a, $b) {
															return strcmp($a['name'], $b['name']);
															});
															foreach ($group as $key => $info) {
																if ($key == 0) {
																	echo '<optgroup label="{{' . $info['family'] . '}}">';
																}
																$selected = '';
																if($info['key'] == $cmd->getDisplay('generic_type') || (isset($customCMDValuesArr['generic_type']) && $info['key'] == $customCMDValuesArr['generic_type'])){
																	if(in_array($info['key'],homebridge::PluginCustomisable())) {
																		$isCustomisable = $info['key'];
																	}
																	$selected=' selected';
																}
																echo '<option value="' . ((isset($info['homebridge_type']) && $info['homebridge_type'])?'HB|':'') . $info['key'] . '" '.((isset($info['homebridge_type']) && $info['homebridge_type'])?'class="orange"':'').$selected.'>' . $info['type'] . ' / ' . $info['name'] . '</option>';
															}
															echo '</optgroup>';
														}
														?>
													</select>
												</td>
											</tr>
										<?php
										endforeach;
										switch($isCustomisable) {
											case "GARAGE_STATE" :
											case "BARRIER_STATE":
												configBarrierGarage($customEQValuesArr,$eql_id);
											break;
											case "ALARM_SET_MODE" :
												if($eqLogic->getEqType_name() != 'alarm' && homebridge::isMagic('NBpPxpeFf5QRA')) :
													configAlarmModes($customEQValuesArr,$eql_cmds,$eql_id);
												endif;
											break;
											case "THERMOSTAT_SET_MODE" :
												if($eqLogic->getEqType_name() != 'thermostat') :
													configThermoModes($customEQValuesArr,$eql_cmds,$eql_id);
												endif;
											break;
											default:
											break;
										}
										?>
									</table>
							<?php
							endswitch;
							?>
						</div>
					</div>
				</div>
			<?php
			endforeach;
			?>
		</div>
		<div class="form-actions pull-right">
			<a class="btn btn-success eqLogicAction" onclick="SaveObject()" ><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
		</div>
	</div>
</div>

<script>
var changed=0;
var eqLogicsHomebridge = [];
var eqLogicsCustoms = [];
var oldValues = [];
// CHANGE CLICK
$('.cmdAttr').on('change',function(){
	$(this).closest('tr').attr('data-change','1');
});
$('.cmdAttr').on('click',function(){
	$(this).closest('tr').attr('data-change','1');
	var found = false;
	for(var i=0; i<oldValues.length ; i++) {
		if($(this).attr('data-cmd_id') == oldValues[i].id) {
			found=true;
		}
	}
	if(!found) {
		if($(this).value().substr(0,3) == 'HB|') {
			oldValues.push({
				'id' : $(this).attr('data-cmd_id'),
				'oldValue' : $(this).value()
			});
		}
	}
});
$('.objectAttr').on('change click',function(){
	changed=1;
});

$('.eqLogicAttr').on('change click',function(){
	var eqLogic = $(this).closest('.panel-title').getValues('.eqLogicAttr')[0];
	console.log(eqLogic.id,eqLogic.configuration);
	eqLogicsHomebridge.push(eqLogic);
});
$('.eqLogicAttrAlarm').on('change',function(){
	var eqLogic = $(this).closest('.panel-body').getValues('.eqLogicAttrAlarm')[0];
	console.log(eqLogic.id,eqLogic.configuration);
	eqLogicsCustoms.push(eqLogic);
});
$('.eqLogicAttrThermo').on('change',function(){
	var eqLogic = $(this).closest('.panel-body').getValues('.eqLogicAttrThermo')[0];
	console.log(eqLogic.id,eqLogic.configuration);
	eqLogicsCustoms.push(eqLogic);
});
$('.eqLogicAttrGarage').on('change',function(){
	var eqLogic = $(this).closest('.panel-body').getValues('.eqLogicAttrGarage')[0];
	console.log(eqLogic.id,eqLogic.configuration);
	eqLogicsCustoms.push(eqLogic);
});


// SAUVEGARDE
function SaveObject(){
	var cmds = []
	var customCmds = [];
	var cmdValues;
	$('.TableCMD tr').each(function(){
		if($(this).attr('data-change') == '1'){
			cmdValues = $(this).getValues('.cmdAttr')[0];
			if(cmdValues.display.generic_type.substr(0,3) == 'HB|') {
				cmdValues.display.generic_type = cmdValues.display.generic_type.replace('HB|','');
				customCmds.push(cmdValues);
			}
			else {
				cmds.push(cmdValues);
			}
		}
	});

	var eqLogicsHomebridgeFiltered = [];
	eqLogicsHomebridge.reverse();
	$.each(eqLogicsHomebridge, function(index, eqLogic) {
		var eqLogics = $.grep(eqLogicsHomebridgeFiltered, function (e) {
			return eqLogic.id === e.id;
		});
		if (eqLogics.length === 0) {
			eqLogicsHomebridgeFiltered.push(eqLogic);
		}
	});
	$.each(eqLogicsHomebridgeFiltered ,function(index, eqLogic){
		jeedom.eqLogic.simpleSave({
			eqLogic : eqLogic,
			error: function (error) {
				$('.EnregistrementDisplay').showAlert({message: error.message, level: 'danger'});
			},
			success: function (data) {
				$('.EnregistrementDisplay').showAlert({message: '{{Modifications sauvegardées avec succès}}', level: 'success'});
				eqLogicsHomebridge = [];
			}
		});
	});

	jeedom.cmd.multiSave({
		cmds : cmds,
		error: function (error) {
			$('.EnregistrementDisplay').showAlert({message: error.message, level: 'danger'});
		},
		success: function (data) {
			$('.EnregistrementDisplay').showAlert({message: '{{Modifications sauvegardées avec succès}}', level: 'success'});
		}
	});

	jeedom.object.save({
		object: $('.div_object_configuration').getValues('.objectAttr')[0],
		error: function (error) {
			$('.EnregistrementDisplay').showAlert({message: error.message, level: 'danger'});
		},
		success: function (data) {
			modifyWithoutSave = false;
			$('.EnregistrementDisplay').showAlert({message: '{{Sauvegarde effectuée avec succès}}', level: 'success'});
		}
	});
	
	var eqLogicsCustomsFiltered = [];
	eqLogicsCustoms.reverse();
	$.each(eqLogicsCustoms, function(index, eqLogic) {
		var eqLogics = $.grep(eqLogicsCustomsFiltered, function (e) {
			return eqLogic.id === e.id;
		});
		if (eqLogics.length === 0) {
			eqLogicsCustomsFiltered.push(eqLogic);
		}
	});
	// custom Save
	$.ajax({
		type: 'POST',
		url: 'plugins/homebridge/core/ajax/homebridge.ajax.php',
		data: {
			action: 'saveCustomData',
			eqLogic: eqLogicsCustomsFiltered,
			cmd: customCmds,
			oldValues: oldValues
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error, $('.EnregistrementDisplay'));
		},
		success: function (data) {
			if (data['result']!=false) {
				$('.EnregistrementDisplay').showAlert({
					message: '{{Sauvegarde de la configuration réussie.}}',
					level: 'success'
				});
				eqLogicsCustoms = [];
				oldValues = [];
			} else {
				$('.EnregistrementDisplay').showAlert({
					message: '{{Echec de la sauvegarde de la configuration : }}' + data['data'],
					level: 'danger'
				});
			}
		}
	});
}

// ICONE
$('body').undelegate('.cmdAction[data-l1key=chooseIcon]', 'click').delegate('.cmdAction[data-l1key=chooseIcon]', 'click', function () {
	var iconeGeneric = $(this).closest('.iconeGeneric');
	chooseIcon(function (_icon) {
		iconeGeneric.find('.cmdAttr[data-l1key=display][data-l2key=icon]').empty().append(_icon);
	});
	$(this).closest('tr').attr('data-change','1');
});

$('body').undelegate('.cmdAttr[data-l1key=display][data-l2key=icon]', 'click').delegate('.cmdAttr[data-l1key=display][data-l2key=icon]', 'click', function () {
	$(this).empty();
});

$('.cmdAttr[data-l1key=display][data-l2key=generic_type]').on('change', function () {
	var cmdLine = $(this).closest('.cmdLine');
	if ($(this).value() == 'GENERIC_INFO' || $(this).value() == 'GENERIC_ACTION') {
		cmdLine.find('.iconeGeneric').show();
	} else {
		cmdLine.find('.iconeGeneric').hide();
		cmdLine.find('.cmdAttr[data-l1key=display][data-l2key=icon]').empty();
	}
});
$('#md_modal').on('dialogclose', function () {
	if(changed==1) {
		location.reload();
	}
})
</script>
<?php
function configAlarmModes($customEQValuesArr,$eql_cmds,$eql_id) {
		if(isset($customEQValuesArr['configuration'])) {
			$SetModePresent = (($customEQValuesArr['configuration']['SetModePresent'])?$customEQValuesArr['configuration']['SetModePresent']:'NOT');
			$SetModeAbsent  = (($customEQValuesArr['configuration']['SetModeAbsent'])?$customEQValuesArr['configuration']['SetModeAbsent']:'NOT');
			$SetModeNuit    = (($customEQValuesArr['configuration']['SetModeNuit'])?$customEQValuesArr['configuration']['SetModeNuit']:'NOT');
		}
		else {
			$SetModePresent = 'NOT';
			$SetModeAbsent  = 'NOT';
			$SetModeNuit    = 'NOT';
		}
?>
		<span class="form-control eqLogicAttrAlarm" type="text" data-l1key="id" style="display : none;"><?=$eql_id?></span>
		<table class="table">
			<tr class="cmdLine">
				<th>{{Mode app Maison}}</th>
				<th>{{Mode app Eve}}</th>
				<th>{{Mode Jeedom}}</th>
			</tr>
			<tr class="cmdLine">
				<td>{{Domicile}}</td><td>{{Présence}}</td>
				<td>
					<select class="eqLogicAttrAlarm configuration" data-l1key="configuration" data-l2key="SetModePresent">
						<?=listAlarmSetModes($eql_cmds,$SetModePresent)?>
					</select>
				</td>
			</tr>
			<tr class="cmdLine">
				<td>{{À distance}}</td><td>{{Absence}}</td>
				<td>
					<select class="eqLogicAttrAlarm configuration" data-l1key="configuration" data-l2key="SetModeAbsent">
						<?=listAlarmSetModes($eql_cmds,$SetModeAbsent)?>
					</select>
				</td>
			</tr>
			<tr class="cmdLine">
				<td>{{Nuit}}</td><td>{{Nuit}}</td>
				<td>
					<select class="eqLogicAttrAlarm configuration" data-l1key="configuration" data-l2key="SetModeNuit">
						<?=listAlarmSetModes($eql_cmds,$SetModeNuit)?>
					</select>	
				</td>
			</tr>
			<tr class="cmdLine">
				<td></td><td></td>
				<td>
					<span class="cmdAttr" data-l1key="id">{{Merci de ne pas choisir plusieurs fois le même mode}}</span>
				</td>
			</tr>
		</table>	
<?php
}
function configThermoModes($customEQValuesArr,$eql_cmds,$eql_id) {
		if(isset($customEQValuesArr['configuration'])) {
			$Chauf = (($customEQValuesArr['configuration']['Chauf'])?$customEQValuesArr['configuration']['Chauf']:'NOT');
			$Clim  = (($customEQValuesArr['configuration']['Clim'])?$customEQValuesArr['configuration']['Clim']:'NOT');
		}
		else {
			$Chauf = 'NOT';
			$Clim  = 'NOT';
		}
?>
		<span class="form-control eqLogicAttrThermo" type="text" data-l1key="id" style="display : none;"><?=$eql_id?></span>
		<?=listThermoSetModes($eql_cmds,'Off')?>
		<table class="table">
			<tr class="cmdLine">
				<th>{{Mode app Maison}}</th>
				<th>{{Mode app Eve}}</th>
				<th>{{Mode Jeedom}}</th>
			</tr>
			<tr class="cmdLine">
				<td>{{Chauffer}}</td><td>{{CHAUF.}}</td>
				<td>
					<select class="eqLogicAttrThermo configuration" data-l1key="configuration" data-l2key="Chauf">
						<?=listThermoSetModes($eql_cmds,$Chauf)?>
					</select>
				</td>
			</tr>
			<tr class="cmdLine">
				<td>{{Refroidir}}</td><td>{{CLIM.}}</td>
				<td>
					<select class="eqLogicAttrThermo configuration" data-l1key="configuration" data-l2key="Clim">
						<?=listThermoSetModes($eql_cmds,$Clim)?>
					</select>
				</td>
			</tr>
			<tr class="cmdLine">
				<td></td><td></td>
				<td>
					<span class="cmdAttr" data-l1key="id">{{Merci de ne pas choisir plusieurs fois le même mode}}</span>
				</td>
			</tr>
		</table>	
<?php
}
function configBarrierGarage($customEQValuesArr,$eql_id) {
		if(isset($customEQValuesArr['configuration'])) {
			$customValues = (($customEQValuesArr['configuration']['customValues'])?$customEQValuesArr['configuration']['customValues']:false);
			$OPEN		  = ((isset($customEQValuesArr['configuration']['OPEN']))?$customEQValuesArr['configuration']['OPEN']:255);
			$OPENING	  = ((isset($customEQValuesArr['configuration']['OPENING']))?$customEQValuesArr['configuration']['OPENING']:254);
			$STOPPED	  = ((isset($customEQValuesArr['configuration']['STOPPED']))?$customEQValuesArr['configuration']['STOPPED']:253);
			$CLOSING	  = ((isset($customEQValuesArr['configuration']['CLOSING']))?$customEQValuesArr['configuration']['CLOSING']:252);
			$CLOSED		  = ((isset($customEQValuesArr['configuration']['CLOSED']))?$customEQValuesArr['configuration']['CLOSED']:0);
		}
		else {
			$customValues = false;
			$OPEN		  = 255;
			$OPENING	  = 254;
			$STOPPED	  = 253;
			$CLOSING	  = 252;
			$CLOSED		  = 0;
		}
	?>
		<span class="form-control eqLogicAttrGarage" type="text" data-l1key="id" style="display : none;"><?=$eql_id?></span>
		<span class="eqLogicAttrGarage configuration" type="text" data-l1key="configuration" data-l2key="customValues" style="display : none;">1</span>
		<tr><th colspan='3'>{{Personnalisation des états}}</th></tr>
		<tr>
			<td>&nbsp;</td>
			<td>{{Ouvert}}</td>
			<td><input type='text' class="eqLogicAttrGarage configuration" data-l1key="configuration" data-l2key="OPEN" value='<?=$OPEN?>' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{{Ouverture en cours}}</td>
			<td><input type='text' class="eqLogicAttrGarage configuration" data-l1key="configuration" data-l2key="OPENING" value='<?=$OPENING?>' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{{Stoppé}}</td>
			<td><input type='text' class="eqLogicAttrGarage configuration" data-l1key="configuration" data-l2key="STOPPED" value='<?=$STOPPED?>' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{{Fermeture en cours}}</td>
			<td><input type='text' class="eqLogicAttrGarage configuration" data-l1key="configuration" data-l2key="CLOSING" value='<?=$CLOSING?>' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{{Fermé}}</td>
			<td><input type='text' class="eqLogicAttrGarage configuration" data-l1key="configuration" data-l2key="CLOSED" value='<?=$CLOSED?>' /></td>
		</tr>
		<tr><td></td><td></td><td>{{Merci de vider les valeurs que vous n'utilisez pas (pas zéro, vide !)}}</td></tr>	
<?php
}
?>
