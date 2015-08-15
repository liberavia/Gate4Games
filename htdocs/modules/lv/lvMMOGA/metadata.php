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
    'id'           => 'lvMMOGA',
    'title'        => 'Partner: MMOGA.de',
    'description'  => array(
        'de' => 'Partnermodul für mmoga.de',
        'en' => 'Partnermodule for mmoga.de',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvmmoga'                           => 'lv/lvMMOGA/application/models/lvmmoga.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvmmoga_main',                  'name' => 'sLvMMOGAPartnerId',                'type' => 'str',        'value' => '' ),
        array( 
            'group' => 'lvmmoga_main',
            'name' => 'aLvMMOGAVendorId',
            'type' => 'aarr',
            'value' => array(),
        ),
        array( 
            'group' => 'lvmmoga_main',
            'name' => 'aLvMMOGACsvFeeds',
            'type' => 'aarr',
            'value' => array(
                'de' => 'www.mmoga.de/sitemap.html?csv=1&n=mm_affiliate&k=NkoiAbcZCaKgu0pjFDeS&key_only=1',
            ),
        ),
        array( 'group' => 'lvmmoga_main',               'name' => 'sLvMMOGADefaultCategoryId',          'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lmmoga_debug',               'name' => 'blLvMMOGALogActive',                 'type' => 'bool',       'value' => false ),
        array( 'group' => 'lmmoga_debug',               'name' => 'sLvMMOGALogLevel',                   'type' => 'str',        'value' => '1' ),
    )
);