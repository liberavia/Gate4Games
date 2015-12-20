<?php
/**
 * IGDB.com extension module
 *
 * This module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.gate4games.com
 * @copyright (C) André Gregor-Herrmann
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'lvGateOsApi',
    'title'        => 'Gate OS API',
    'description'  => array(
        'de' => 'Modul zur Verbindung zu GateOS',
        'en' => 'Module for connecting to GateOS',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        // models
        'lvgateosapi'                       => 'lv/lvGateOsApi/application/models/lvgateosapi.php',
        // controllers
        'gateosapi'                         => 'lv/lvGateOsApi/controllers/gateosapi.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
    )
);
 
