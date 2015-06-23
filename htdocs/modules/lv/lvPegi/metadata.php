<?php
/**
 * External media module
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
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
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
    'id'           => 'lvPegi',
    'title'        => 'PEGI Altersinformationen',
    'description'  => array(
        'de' => 'Modul zum automatischen Bezug von PEGI Altersinformationen',
        'en' => 'Module for automatic fetching of PEGI Age recommendations',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvpegi'                        => 'lv/lvPegi/application/models/lvpegi.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvpegimain',        'name' => 'sLvPegiRequestBase',              'type' => 'str',        'value' => 'http://www.pegi.info/export/' ),
        array( 'group' => 'lvpegimain',        'name' => 'sLvPegiRequestPastMonths',        'type' => 'str',        'value' => '2' ),
        array( 'group' => 'lvpegimain',        'name' => 'sLvPegiInitImportFolder',         'type' => 'str',        'value' => 'import/' ),
        array( 'group' => 'lvpegimain',        'name' => 'sLvPegiInitImportFile',           'type' => 'str',        'value' => 'pegi.csv' ),
        array( 'group' => 'lvpegimain',        'name' => 'sLvPegiAttributeId',              'type' => 'str',        'value' => 'RecommendedAgePegi' ),
    )
);
 
