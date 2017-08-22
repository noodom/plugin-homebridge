
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
 $('#bt_healthhomebridge').on('click', function () {
    $('#md_modal').dialog({title: "{{Santé homebridge}}"});
    $('#md_modal').load('index.php?v=d&plugin=homebridge&modal=health').dialog('open');
})
 $('#info_app').on('click', function(){
	$('#md_modal').dialog({title: "{{Informations envoyées à Homebridge}}"});
	$('#md_modal').load('index.php?v=d&plugin=homebridge&modal=info_app.homebridge').dialog('open');
})
function clickplugin(id_plugin,name_plugin){
	$('#md_modal').dialog({title: "{{Configuration homebridge du Plugin "+name_plugin+"}}"});
    $('#md_modal').load('index.php?v=d&plugin=homebridge&modal=plugin.homebridge&plugin_id=' +id_plugin).dialog('open');
}

function clickobject(id_object){
	$('#md_modal').dialog({title: "{{Configuration homebridge de la Pièce}}"});
    $('#md_modal').load('index.php?v=d&plugin=homebridge&modal=object.homebridge&object_id=' +id_object).dialog('open');
}

function clickscenario(id_scenario,name_scenario){
	$('#md_modal').dialog({title: "{{Configuration homebridge du Scénario "+name_scenario+"}}"});
    $('#md_modal').load('index.php?v=d&plugin=homebridge&modal=scenario.homebridge&scenario_id=' +id_scenario).dialog('open');
}

$('li').click(function(){
	 setTimeout(function(){
		$('.eqLogicThumbnailContainer').packery();
		},50);
 });
var hash = document.location.hash;
if (hash) {
    $('.nav-tabs a[href="'+hash+'"]').tab('show');
} 
$('.nav-tabs a').on('shown.bs.tab', function (e) {
    window.location.hash = e.target.hash;
});

