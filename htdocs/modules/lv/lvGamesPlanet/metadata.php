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
    'id'           => 'lvGamesPlanet',
    'title'        => 'Partner: Gamesplanet',
    'description'  => array(
        'de' => 'Partnermodul für gamesplanet.com',
        'en' => 'Partnermodule for gamesplanet.com',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvgamesplanet'             => 'lv/lvGamesPlanet/application/models/lvgamesplanet.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 
            'group' => 'lvgpmain',
            'name' => 'aLvGpVendorId',
            'type' => 'aarr',
            'value' => array(),
        ),
        array( 
            'group' => 'lvgpmain',
            'name' => 'aLvGamesplanetXmlStdFeeds',
            'type' => 'aarr',
            'value' => array(
                'de' => 'https://de.gamesplanet.com/api/v1/products/feed.xml?ref=gate4games',
            ),
        ),
        array( 
            'group' => 'lvgpmain',
            'name' => 'aLvGamesplanetXmlFlashDeals',
            'type' => 'aarr',
            'value' => array(
                'de' => 'https://de.gamesplanet.com/api/v1/products/flash.xml?ref=gate4games',
            ),
        ),
        array( 
            'group' => 'lvgpmain',
            'name' => 'aLvGamesplanetXmlCharts',
            'type' => 'aarr',
            'value' => array(
                'de' => 'https://de.gamesplanet.com/api/v1/products/charts.xml?ref=gate4games',
            ),
        ),
        array( 'group' => 'lvgpmain',                   'name' => 'sLvGpDefaultCategoryId',         'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lvgp_debug',                 'name' => 'blLvGpLogActive',                'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvgp_debug',                 'name' => 'sLvGpLogLevel',                  'type' => 'str',        'value' => '1' ),
    )
);