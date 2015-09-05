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
    'id'           => 'lvYouTubeAddonLetsPlay',
    'title'        => 'YouTube Product Videos (LetsPlay-Addon)',
    'description'  => array(
        'de' => 'Modul zum automatischen Bezug von Lets Play Videos von YouTube',
        'en' => 'Module for automatic fetching of lets play videos from YouTube',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                 => 'lv/lvYouTubeAddonLetsPlay/extend/application/components/widgets/lvyoutube_letsplay_oxwarticledetails',
        // models
        'lvyoutube'                         => 'lv/lvYouTubeAddonLetsPlay/extend/application/models/lvyoutube_letsplay',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
        'lvyoutube_letsplay.tpl'             => 'lv/lvYouTubeAddonLetsPlay/application/views/frontend/tpl/page/details/inc/lvyoutube_letsplay.tpl'
    ),
    'blocks' => array(
        array( 'template' => 'page/details/inc/tabs.tpl',               'block'=>'details_tabs_attributes',                         'file'=>'extend/application/views/blocks/block_widget_details_tabs_attributes.tpl' ),
    ),
    'settings' => array(
        // group search params
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestPartLep',               'type' => 'str',        'value' => 'snippet' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestMaxResultsLep',         'type' => 'str',        'value' => '1' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestOrderLep',              'type' => 'str',        'value' => 'relevance' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestPrefixLep',             'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'sLvApiRequestSuffixLep',             'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvyoutubeparams',        'name' => 'aLvApiChannelIdsLep',                'type' => 'arr',        'value' => array() ),
    )
);
 
