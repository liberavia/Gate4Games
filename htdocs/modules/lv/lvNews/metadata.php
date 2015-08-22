<?php
/**
 * News extension module
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
    'id'           => 'lvNews',
    'title'        => 'News Module',
    'description'  => array(
        'de' => 'Newserweiterung',
        'en' => 'News extenstion',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // controllers->admin
        'news_main'                         => 'lv/lvNews/extend/application/controllers/admin/lvnews_main',
        // models
        'oxnews'                            => 'lv/lvNews/extend/application/models/lvoxnews',
        // core
        'oxseoencoder'                      => 'lv/lvNews/extend/core/lvnews_oxseoencoder',
    ),
    'files' => array(
        // controllers
        'lvnews_overview'                   => 'lv/lvNews/application/controllers/lvnews_overview.php',
        'lvnews_details'                    => 'lv/lvNews/application/controllers/lvnews_details.php',
        // core
        'lvnewsevents'                      => 'lv/lvNews/core/lvnewsevents.php',
    ),
    'events'       => array(
        'onActivate'                        => 'lvnewsevents::onActivate',
        'onDeactivate'                      => 'lvnewsevents::onDeactivate',        
    ),
    'templates' => array(
        'lvnews_overview.tpl'               => 'lv/lvNews/application/views/frontend/page/info/lvnews_overview.tpl',
        'lvnews_details.tpl'                => 'lv/lvNews/application/views/frontend/page/info/lvnews_details.tpl',
    ),
    'blocks' => array(
        array( 'template' => 'news_main.tpl',           'block'=>'admin_news_main_form',            'file'=>'extend/application/views/blocks/block_admin_news_main_form.tpl' ),
    ),
    'settings' => array(
    )
);
 
