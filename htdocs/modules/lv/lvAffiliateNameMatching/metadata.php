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
    'id'           => 'lvAffiliateNameMatching',
    'title'        => 'Affiliate Module - Extension Name-Matching',
    'description'  => array(
        'de' => 'Modul erweitert die Affiliate Tools um Name-Matching funktionalitäten',
        'en' => 'Module extends Affiliate Module - Tools for name matching apabilities',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // models
        'lvaffiliate_tools'                         => 'lv/lvAffiliateNameMatching/extend/application/models/lvaffiliate_tools_ext',
    ),
    'files' => array(
        // controllers admin
        'lvaffiliatenm_admin'                       => 'lv/lvAffiliateNameMatching/application/controllers/admin/lvaffiliatenm_admin.php',
        'lvaffiliatenm_admin_list'                  => 'lv/lvAffiliateNameMatching/application/controllers/admin/lvaffiliatenm_admin_list.php',
        'lvaffiliatenm_admin_main'                  => 'lv/lvAffiliateNameMatching/application/controllers/admin/lvaffiliatenm_admin_main.php',
        // models
        'lvaffiliatenm'                             => 'lv/lvAffiliateNameMatching/application/models/lvaffiliatenm.php',
        // core
        'lvaffiliatenmevents'                       => 'lv/lvAffiliateNameMatching/core/lvaffiliatenmevents.php',
    ),
    'templates' => array(
        'lvaffiliatenm_admin.tpl'                   => 'lv/lvAffiliateNameMatching/application/views/admin/tpl/lvaffiliatenm_admin.tpl',
        'lvaffiliatenm_admin_list.tpl'              => 'lv/lvAffiliateNameMatching/application/views/admin/tpl/lvaffiliatenm_admin_list.tpl',
        'lvaffiliatenm_admin_main.tpl'              => 'lv/lvAffiliateNameMatching/application/views/admin/tpl/lvaffiliatenm_admin_main.tpl',
        'lvaffiliatenmbottomnaviitem.tpl'           => 'lv/lvAffiliateNameMatching/application/views/admin/tpl/lvaffiliatenmbottomnaviitem.tpl',
    ),
    'events'       => array(
        
    ),
    'blocks' => array(
    ),
    'settings' => array(
    )
);
 
