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
    'id'           => 'lvAgeCheck',
    'title'        => 'Age Check',
    'description'  => array(
        'de' => 'Modul zur Altersüberprüfung',
        'en' => 'Module for checking the age of a visitor',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                 => 'lv/lvAgeCheck/extend/application/components/widgets/lvagecheck_oxwarticledetails',
    ),
    'files' => array(
        'lvagecheck'                        => 'lv/lvAgeCheck/application/controllers/lvagecheck.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
        'lvagecheck.tpl'                    => 'lv/lvAgeCheck/application/views/frontend/lvagecheck.tpl',
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvagemain',      'name' => 'sLvCheckFromAge',         'type' => 'str',        'value' => '16' ),
    )
);
 
