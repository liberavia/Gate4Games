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
    'id'           => 'lvGog',
    'title'        => 'Partner: Good Old Games (GOG.com)',
    'description'  => array(
        'de' => 'Partnermodul für gog.com',
        'en' => 'Partnermodule for gog.com',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvgog'             => 'lv/lvGog/application/models/lvgog.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvgogmain',                  'name' => 'sLvGogPartnerId',                'type' => 'str',        'value' => '' ),
        array( 
            'group' => 'lvgogmain',
            'name' => 'aLvGogVendorId',
            'type' => 'aarr',
            'value' => array(),
        ),
        array( 
            'group' => 'lvgogmain',
            'name' => 'aLvGogXmlStdFeeds',
            'type' => 'aarr',
            'value' => array(
                'de' => 'http://www.gog.com/games/feed?format=xml&country=DE&currency=EUR',
            ),
        ),
        array( 'group' => 'lvgogmain',                  'name' => 'sLvGogDefaultCategoryId',        'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvgogmain',                  'name' => 'sLvGogMaxPages',                 'type' => 'str',        'value' => '50' ),
        // group debug
        array( 'group' => 'lvgog_debug',                'name' => 'blLvGogLogActive',               'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvgog_debug',                'name' => 'sLvGogLogLevel',                 'type' => 'str',        'value' => '1' ),
    )
);