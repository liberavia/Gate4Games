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
    'id'           => 'lvExternalMedia',
    'title'        => 'Externe Bilder und Medien',
    'description'  => array(
        'de' => 'Modul zur Verwaltung externer Medien',
        'en' => 'Module for managing external media',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                 => 'lv/lvExternalMedia/extend/application/components/widgets/lvextmedia_oxwarticledetails',
        // models
        'oxmediaurl'                        => 'lv/lvExternalMedia/extend/application/models/lvextmedia_oxmediaurl',
        'oxpicturehandler'                  => 'lv/lvExternalMedia/extend/application/models/lvextmedia_oxpicturehandler',
        'oxarticle'                         => 'lv/lvExternalMedia/extend/application/models/lvextmedia_oxarticle',
        // controllers admin
        'article_pictures'                    => 'lv/lvExternalMedia/extend/application/controllers/admin/lvextmedia_article_pictures',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'page/details/inc/productmain.tpl',    'block'=>'details_productmain_zoom',        'file'=>'extend/application/views/blocks/block_details_productmain_zoom.tpl' ),
        array( 'template' => 'article_pictures.tpl',                'block'=>'admin_article_pictures_main',     'file'=>'extend/application/views/blocks/block_admin_article_pictures_main.tpl' ),
        array( 'template' => 'page/details/inc/productmain.tpl',    'block'=>'details_productmain_morepics',    'file'=>'extend/application/views/blocks/block_details_productmain_morepics.tpl' ),
    ),
    'settings' => array(
    )
);
 
