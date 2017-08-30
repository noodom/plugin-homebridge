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

global $CUSTOM_GENERIC_TYPE;
$CUSTOM_GENERIC_TYPE = array(
	'BATTERY_CHARGING' => array('name' => 'Batterie en charge', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true),
	'ACTIVE' => array('name' => 'Statut Actif', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true),
	'DEFECT' => array('name' => 'Statut Defectueux', 'family' => 'Generic', 'type' => 'Info', 'ignore' => true)
);
