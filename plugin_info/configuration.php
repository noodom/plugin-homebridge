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
include_file('core', 'authentification', 'php');
if(!isConnect()) {
	include_file('desktop', '404', 'php');
	die();
}

//sendVarToJs('hasIos', homebridge::check_ios());
?>
<style>
@font-face {
  font-family: Scancardium;
  src: url(/plugins/homebridge/resources/Scancardium.ttf);
}
input[type=checkbox] {
  transform: scale(1.5);
}
</style>
<form class="form-horizontal">
	<fieldset>
		<legend>
			<i class="fa fa-list-alt"></i> {{Homebridge}}
		</legend>
		<?php
			$interne = network::getNetworkAccess('internal');
			if($interne == null || $interne == 'http://:80' || $interne == 'https://:80'){
		?>
			<div class="form-group">
				<div class="col-lg-7">
					<span class="badge" style="background-color : #c9302c;">{{Attention votre adresse interne (configuration) n'est pas valide.}}</span>
				</div>
			</div>
		<?php
			}else{
		?>
			<div class="form-group">
				<label class="col-lg-4 control-label">{{Adresse Ip Homebridge}}</label>
				<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
					<span class="badge" style="background-color : #ec971f;margin-top:10px"><?php echo $interne; ?></span>
				</div>
			</div>
		<?php
			}
		?>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Nom Homebridge}}</label>
			<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
				<input class="configKey form-control" style="width:100%" data-l1key="name_homebridge" placeholder="<?php echo config::byKey('name') ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Activer les graphiques dans application Eve}}</label>
			<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
				<input type="checkbox" class="configKey form-control" style="width:100%" data-l1key="fakegato" />
			</div>
		</div>
		<div class="form-group hide">
			<label class="col-lg-4 control-label">{{MAC Homebridge}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="mac_homebridge" placeholder="CC:22:3D:E3:CE:30" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{PIN Homebridge (format : XXX-XX-XXX)}}</label>
			<div class="col-lg-3" style="background-color:#fff !important;padding-top:15px;padding-bottom:15px;">
				<input id="input_pin_homebridge" class="configKey form-control" maxlength="10" style="margin: auto; border:5px solid #000;height:70px;width:220px;text-align:center;font-size:25px;background-color:#fff !important;color:#000;border-radius:0px;font-family:Scancardium; letter-spacing: 1px;" data-l1key="pin_homebridge" placeholder="031-45-154" />
			</div><img id="qrCode" style="padding-left:100px" title="{{Fonctionne uniquement pour ajouter Jeedom, pour les plateformes supplémentaires, utilisez le PIN}}" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" border="0" />
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Réparation de Homebridge}}</label>
			<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
				<div><a class="btn btn-warning" style="width:48%;float:left;" id="bt_repairHome"><i class="fa fa-erase"></i> {{Réparer}}</a></div><div><a class="btn btn-danger" style="width:48%;float:right;" id="bt_repairHome_reinstall"><i class="fa fa-erase"></i> {{Réparer & Réinstaller}}</a></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Configuration avancée}}</label>
			<div class="col-lg-3" style="padding-left:0px;padding-right:0px;">
				<a class="btn btn-danger" style="width:100%" id="bt_platformFile"><i class="fa fa-file-o"></i> {{Plateforme Homebridge supplémentaire}}</a>
				<br /><br />
				<a class="btn btn-danger" style="width:100%" id="bt_accessoryFile"><i class="fa fa-file-o"></i> {{Accessoire Homebridge supplémentaire}}</a>
				<br />
				<input id="input_magicField" class="configKey form-control" data-l1key="magicField" style="background-color:transparent !important;border:0px !important;" />
			</div>
		</div>		
	</fieldset>
</form>
<script>
	$('input#input_pin_homebridge').on('keyup', function() {
		if(!this.value.match(/^\d\d\d-\d\d-\d\d\d$/)) {
			$('#div_alert').showAlert({
				message : this.value+" : {{Format incorrect (XXX-XX-XXX)}}",
				level : 'danger'
			});
			$('#qrCode').attr('src','');			
		}
		else {
			var forbiddenPIN = ["000-00-000","111-11-111","222-22-222","333-33-333","444-44-444","555-55-555","666-66-666","777-77-777","888-88-888","999-99-999","123-45-678","876-54-321"];
			if(forbiddenPIN.indexOf(this.value) != -1) {
				$('#div_alert').showAlert({
					message : this.value+" : {{Code PIN interdit par Apple}}",
					level : 'danger'
				});	
				$('#qrCode').attr('src','');
			}
			else {
				$('#div_alert').showAlert({
					message : this.value+" : {{Format correct}}",
					level : 'success'
				});	
				generateQRCode(this.value);
			}
		}
	});
	function generateQRCode(pin_homebridge = '') {
		$.ajax({
			type : 'POST',
			url : 'plugins/homebridge/core/ajax/homebridge.ajax.php',
			data : {
				action : 'generateQRCode',
				pin_homebridge : pin_homebridge
			},
			dataType : 'json',
			global : false,
			error : function(request, status, error) {
				$('#div_alert').showAlert({
					message : error.message,
					level : 'danger'
				});
			},
			success : function(data) {
				if(data.result != "")
					$('#qrCode').attr('src',data.result);
			}
		});
	}
	$('#bt_platformFile').on('click', function () {
		bootbox.confirm('{{Configuration avancée, à vos propres risques !!! Aucun support ne sera donné !!!}}', function(result) {
			if (result) {
				$('#md_modal2').dialog({title: "{{Configuration Plateforme Homebridge supplémentaire}}"});
				$('#md_modal2').load('index.php?v=d&plugin=homebridge&modal=platformHB.homebridge').dialog('open');
			}
		});
	});
	$('#bt_accessoryFile').on('click', function () {
		bootbox.confirm('{{Configuration avancée, à vos propres risques !!! Aucun support ne sera donné !!!}}', function(result) {
			if (result) {
				$('#md_modal2').dialog({title: "{{Configuration Accessoire Homebridge supplémentaire}}"});
				$('#md_modal2').load('index.php?v=d&plugin=homebridge&modal=accessoryHB.homebridge').dialog('open');
			}
		});
	});
	$('#bt_repairHome').on('click', function() {
		bootbox.confirm('{{Etes-vous sûr de vouloir réparer Homebridge ? Vous devrez réinstaller les équipements sur votre appareil iOS (Merci, de supprimer la passerelle Jeedom sur l\'app Home).}}', function(result) {
			if (result) {
				$.ajax({
					type : 'POST',
					url : 'plugins/homebridge/core/ajax/homebridge.ajax.php',
					data : {
						action : 'repairHomebridge',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						$('#div_plugin_configuration').setValues(data.result, '.configKey');
						$('#div_alert').showAlert({
							message : "{{Réparation Homebridge effectuée, merci de patienter jusqu'au démarrage du démon}}",
							level : 'success'
						});
					}
				});
			}
		});
	});
	$('#bt_repairHome_reinstall').on('click', function() {
		bootbox.confirm('{{Etes-vous sûr de vouloir supprimer et reinstaller Homebridge ? Vous devrez réinstaller les équipements sur votre appareil iOS (Merci, de supprimer la passerelle Jeedom sur l\'app Home).}}', function(result) {
			if (result) {
				$.ajax({
					type : 'POST',
					url : 'plugins/homebridge/core/ajax/homebridge.ajax.php',
					data : {
						action : 'repairHomebridge_reinstall',
					},
					dataType : 'json',
					global : false,
					error : function(request, status, error) {
						$('#div_alert').showAlert({
							message : error.message,
							level : 'danger'
						});
					},
					success : function(data) {
						$('#div_plugin_configuration').setValues(data.result, '.configKey');
						$('#div_alert').showAlert({
							message : "{{Réinstallation Homebridge effectuée, merci de patienter jusqu'à la fin de l'installation des dépendances}}",
							level : 'success'
						});
					}
				});
			}
		});
	});
	setTimeout(generateQRCode(),50);
</script>
