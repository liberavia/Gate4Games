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
    'id'           => 'lvFeedback',
    'title'        => 'Feedback Button',
    'description'  => array(
        'de' => 'Modul stellt einen Feedback-Button zur Verfügung, der auf jeder Seite die Möglichkeit bietet ein Feedback an eine definierte E-Mail-Adresse zu senden',
        'en' => 'Module offers a Feedback button on each page of the shop, which can be used to send feedback on a defined E-Mail-Address',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // models
        'oxemail'                                   => 'lv/lvFeedback/extend/application/models/lvfeedback_oxemail',
        // core
        'oxviewconfig'                              => 'lv/lvFeedback/extend/core/lvfeedback_oxviewconfig',
    ),
    'files' => array(
        'lvsendfeedback'                            => 'lv/lvFeedback/application/controllers/lvsendfeedback.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'layout/base.tpl',         'block'=>'base_style',                                  'file'=>'extend/application/views/blocks/block_base_style.tpl' ),
        array( 'template' => 'layout/base.tpl',         'block'=>'base_js',                                     'file'=>'extend/application/views/blocks/block_base_js.tpl' ),
        array( 'template' => 'layout/page.tpl',         'block'=>'layout_header',                               'file'=>'extend/application/views/blocks/block_layout_header.tpl' ),
        array( 'template' => 'layout/page.tpl',         'block'=>'layout_breadcrumb',                           'file'=>'extend/application/views/blocks/block_layout_breadcrumb.tpl' ),
    ),
    'settings' => array(
        // group email
        array( 'group' => 'lvfeedback_email',           'name' => 'sLvFeedbackEmail',                   'type' => 'str',        'value' => "" ),
        // group captcha
        array( 'group' => 'lvfeedback_recaptcha',       'name' => 'blLvFeedbackUseCaptcha',             'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvfeedback_recaptcha',       'name' => 'sLvFeedbackRequestUrl',              'type' => 'str',        'value' => "https://www.google.com/recaptcha/api/siteverify" ),
        array( 'group' => 'lvfeedback_recaptcha',       'name' => 'sLvFeedbackWebsiteKey',              'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvfeedback_recaptcha',       'name' => 'sLvFeedbackSecretKey',               'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lvfeedback_debug',           'name' => 'blLvFeedbackLogActive',              'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvfeedback_debug',           'name' => 'sLvFeedbackLogLevel',                'type' => 'str',        'value' => '1' ),
        
    )
);
 
