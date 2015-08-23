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
    'id'           => 'lvCKEditor',
    'title'        => 'CKEditor',
    'description'  => array(
        'de' => 'CKEditor mit Medienbrowser KCFinder für OXID',
        'en' => 'CKEditor with mediabrowser KCFinder for OXID',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // controllers -> admin
        'oxadmindetails'                    => 'lv/lvCKEditor/extend/application/admin/lvckeditor_oxadmindetails',
    ),
    'files' => array(
        'lvckeditor'                        => 'lv/lvCKEditor/application/models/lvckeditor.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvckeditor_media',      'name' => 'sLvUploadPath',         'type' => 'str',        'value' => 'out/media/' ),
    )
);
 
