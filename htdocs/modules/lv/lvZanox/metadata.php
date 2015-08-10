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
    'id'           => 'lvZanox',
    'title'        => 'Zanox Affiliate Module',
    'description'  => array(
        'de' => 'Modul zur Zanox Affiliate-Anbindung',
        'en' => 'Module for Zanox affiliate interface',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // core
        'oxviewconfig'                              => 'lv/lvZanox/extend/core/lvzanox_oxviewconfig',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'layout/base.tpl',         'block'=>'head_meta_robots',            'file'=>'extend/application/views/blocks/block_head_meta_robots.tpl' ),
    ),
    'settings' => array(
        // group main
        array( 'group' => 'lvzanox_main',               'name' => 'sLvZanoxVerification',                   'type' => 'str',        'value' => "" ),
    )
);
 
