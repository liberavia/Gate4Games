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
    'id'           => 'lvYouTubeProductVideo',
    'title'        => 'YouTube Product Videos',
    'description'  => array(
        'de' => 'Modul zum automatischen Bezug von Produktvideos von YouTube',
        'en' => 'Module for automatic fetching of product videos from YouTube',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvyoutube'                        => 'lv/lvYouTubeProductVideo/application/models/lvyoutube.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        // group connect
        array( 'group' => 'lvyoutubeconnect',       'name' => 'sLvApiKey',                      'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvyoutubeconnect',       'name' => 'sLvApiBaseRequestAddress',       'type' => 'str',        'value' => 'https://www.googleapis.com/youtube/v3/' ),
        array( 'group' => 'lvyoutubeconnect',       'name' => 'sLvApiRequestAction',            'type' => 'str',        'value' => 'search' ),
        array( 'group' => 'lvyoutubeconnect',       'name' => 'sLvApiBaseTargetAddress',        'type' => 'str',        'value' => 'https://www.youtube.com/watch?v=' ),
        // group search params
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestPart',              'type' => 'str',        'value' => 'snippet' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestMaxResults',        'type' => 'str',        'value' => '1' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestOrder',             'type' => 'str',        'value' => 'viewCount' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestPrefix',            'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestSuffix',            'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiChannelId',                'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lvyoutubedebug',         'name' => 'blLvYouTubeLogActive',           'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvyoutubedebug',         'name' => 'sLvYouTubeLogLevel',             'type' => 'str',        'value' => '1' ),
    )
);
 
