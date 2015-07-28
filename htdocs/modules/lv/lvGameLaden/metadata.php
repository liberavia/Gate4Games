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
    'id'           => 'lvGameladen',
    'title'        => 'Partner: Gameladen',
    'description'  => array(
        'de' => 'Partnermodul für gameladen.com',
        'en' => 'Partnermodule for gameladen.com',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvgameladen'               => 'lv/lvGameLaden/application/models/lvgameladen.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvgalamain',                 'name' => 'sLvGaLaPartnerId',                'type' => 'str',        'value' => '' ),
        array( 
            'group' => 'lvgalamain',
            'name' => 'aLvGaLaVendorId',
            'type' => 'aarr',
            'value' => array(),
        ),
        array( 
            'group' => 'lvgalamain',
            'name' => 'aLvGaLaCsvFeeds',
            'type' => 'aarr',
            'value' => array(
                'de' => 'http://www.gameladen.com/media/feedgenerator/Gamekey.csv',
            ),
        ),
        array( 'group' => 'lvgalamain',                  'name' => 'sLvGaLaDefaultCategoryId',        'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lvgala_debug',                'name' => 'blLvGaLaLogActive',               'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvgala_debug',                'name' => 'sLvGaLaLogLevel',                 'type' => 'str',        'value' => '1' ),
    )
);