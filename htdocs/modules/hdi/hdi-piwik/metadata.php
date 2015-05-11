<?php
/**
 * hdi-piwik: OXID module to include Piwik tracking code.
 * Copyright (C) 2012-2014 HEINER DIRECT GmbH & Co. KG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      HEINER DIRECT GmbH & Co. KG <oxid@heiner-direct.com>
 * @author      Klaus Weidenbach (HEINER DIRECT GmbH & Co. KG)
 * @author      Marat Bedoev (HEINER DIRECT GmbH & Co. KG)
 * @link        http://www.heiner-direct.com
 *
 * @copyright   HEINER DIRECT GmbH & Co. KG 2012-2014
 * @license     GPLv3
 *
 * @package     HDI
 * @subpackage  hdi-piwik
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

$oLang = oxRegistry::getLang();

/**
 * Module information
 */
$aModule = array(
	'id' => 'hdi-piwik',
	'title' => '<strong style="color:#006a8c;border:1px solid #e30061;padding:0 2px;background:white">HDI</strong> <strong>Piwik</strong>',
	'lang' => 'en',
	'description' => array(
		'en' => 'Add Piwik tracking code to track activities in your online shop. You need access to a <a href="http://www.piwik.org" target="_blank">Piwik</a> installation to use this module.<br><br><b>UPDATE INFORMATION</b><br>When you update from an earlier release (<2.0) make sure to update the module\'s settings. It is a breaking change and configuration needs to be updated!<br><b>UPDATE INFORMATION for v1.1</b><br>This release uses piwik\'s internal <i>site search</i> which is available since Piwik 1.9. This means that the former search tracking with custom variable will not be available anymore!',
		'de' => 'Fügt einen Piwik Tracking Code hinzu um eine Webanalyse ihres Shops zu erhalten. Sie benötigen Zugriff auf ein <a href="http://www.piwik.org" target="_blank">Piwik</a> um dies verwenden zu können.<br><br><b>UPDATE HINWEIS</b><br>Sollten sie von einer früheren Version dieses Moduls updaten (<2.0), passen sie bitte die Modulein&shy;stellungen an!<br><b>UPDATE HINWEIS für v1.1</b><br>Es wird die seit Piwik 1.9 enthaltene <i>Interne Suche</i> verwendet und daher ist das Tracking über die Benutzer&shy;definierten Variablen nicht mehr verfügbar!'
	),
	'thumbnail' => 'hdi.png',
	'version' => '2.1.0 for OXID eShop >= 4.7',
	'author' => 'Klaus Weidenbach, HEINER DIRECT GmbH & Co. KG',
	'email' => 'oxid@heiner-direct.com',
	'url' => 'http://www.heiner-direct.com',
	'extend' => array(
		'oxviewconfig' => 'hdi/hdi-piwik/extend/oxviewconfig_piwik'
	),
	'blocks' => array(
		array('template' => 'layout/base.tpl', 'block' => 'base_style', 'file' => '/views/blocks/piwik.tpl')
	),
	'settings' => array(
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_sUrl', 'type' => 'str', 'value' => ''),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_iPageid', 'type' => 'str', 'value' => ''),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_iNewsgoalid', 'type' => 'str', 'value' => '0'),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_iMaxCustVar', 'type' => 'str', 'value' => '5'),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_iCustindexNewsletter', 'type' => 'str', 'value' => '0'),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_iCustindexPayment', 'type' => 'str', 'value' => '0'),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_blFirstReferrerConv', 'type' => 'bool', 'value' => 'false'),
		array('group' => 'hdi-piwik_Main', 'name' => 'hdi-piwik_blEnableJSErrorTrackin', 'type' => 'bool', 'value' => 'false'),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sCustomvarNewsletter', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_CUSTOM_NEWSLETTER')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sCustomvarPayment', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_CUSTOM_PAYMENT')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterAccount', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterAccountOn', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_ON')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterAccountOff', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_OFF')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterViewed', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_VIEWED')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterOrdered', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_ORDERED')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterActivated', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_ACTIVATED')),
		array('group' => 'hdi-piwik_CustomVars', 'name' => 'hdi-piwik_sNewsletterCanceled', 'type' => 'str', 'value' => $oLang->translateString('SHOP_MODULE_hdi-piwik_NEWSLETTER_CANCELED')),
		array('group' => 'hdi-piwik_Params', 'name' => 'hdi-piwik_aParamMapVisit', 'type' => 'aarr', 'value' => null),
		array('group' => 'hdi-piwik_Params', 'name' => 'hdi-piwik_aParamMapPage', 'type' => 'aarr', 'value' => null)
	)
);