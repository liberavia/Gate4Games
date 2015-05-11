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
 */

/**
 * Provide module settings in template files.
 *
 * @author      HEINER DIRECT GmbH & Co. KG <oxid@heiner-direct.com>
 * @author      Klaus Weidenbach (HEINER DIRECT GmbH & Co. KG)
 * @author      Marat Bedoev (HEINER DIRECT GmbH & Co. KG)
 * @package     HDI
 * @subpackage  hdi-piwik
 */
class oxviewconfig_piwik extends oxviewconfig_piwik_parent {
	protected $_piwikUrl = null;
	protected $_piwikPageId = null;
	protected $_piwikMaxCustVar = null;
	protected $_piwikNlgoalId = null;
	protected $_piwikCustomIndexNewsletter = null;
	protected $_piwikCustomIndexPayment = null;
	protected $_piwikFirstReferrerConv = null;
	protected $_piwikEnableJSError = null;
	
	/**
	 * Returns the Piwik server URL.
	 *
	 * @return str
	 */
	public function getPiwikUrl() {
		if ( $this->_piwikUrl === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikUrl = $cfg->getConfigParam( 'hdi-piwik_sUrl' );
		}
		return $this->_piwikUrl;
	}

	/**
	 * Returns the Piwik page ID which to use.
	 *
	 * @return int
	 */
	public function getPiwikPageid() {
		if ( $this->_piwikPageId === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikPageId = intval( $cfg->getConfigParam( 'hdi-piwik_iPageid' ) );
		}
		return $this->_piwikPageId;
	}

	/**
	 * Returns Piwik's max number of allowed custom variables.
	 *
	 * @return int
	 */
	public function getPiwikMaxCustVar() {
		if ( $this->_piwikMaxCustVar === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikMaxCustVar = intval( $cfg->getConfigParam( 'hdi-piwik_iMaxCustVar' ) );
		}
		return $this->_piwikMaxCustVar;
	}

	/**
	 * Returns the goal ID for newsletter registration.
	 * The goal needs to be created manually in Piwik.
	 *
	 * @return int
	 */
	public function getPiwikNlgoalid() {
		if ( $this->_piwikNlgoalId === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikNlgoalId = intval( $cfg->getConfigParam( 'hdi-piwik_iNewsgoalid' ) );
		}
		return $this->_piwikNlgoalId;
	}

	/**
	 * Returns the index of the custom variable where to store newsletter tracking.
	 * The index can be a number between 1 and 5 and can only be used for one value
	 * for a site in Piwik. This custom variable has a page-scope.
	 *
	 * @return int
	 */
	public function getPiwikCustomIndexNewsletter() {
		if ( $this->_piwikCustomIndexNewsletter === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikCustomIndexNewsletter = intval( $cfg->getConfigParam( 'hdi-piwik_iCustindexNewsletter' ) );
		}
		return $this->_piwikCustomIndexNewsletter;
	}

	/**
	 * Returns the index of the custom variable where to store the selected payment method.
	 * The index can be a number between 1 and 5 and can only be used for one value
	 * for a site in Piwik. This custom variable has a visit-scope.
	 *
	 * @return int
	 */
	public function getPiwikCustomIndexPayment() {
		if ( $this->_piwikCustomIndexPayment === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			$this->_piwikCustomIndexPayment = intval( $cfg->getConfigParam( 'hdi-piwik_iCustindexPayment' ) );
		}
		return $this->_piwikCustomIndexPayment;
	}

	/**
	 * If set to true attribute a conversion to the first referrer. By default, conversion
	 * is attributed to the most recent referrer.
	 * This feature requires Piwik >= 1.2.0.
	 *
	 * @see setConversionAttributionFirstReferrer( bool )
	 * @since hdi-piwik 2.1.0
	 * @return boolean
	 */
	public function getFirstReferrerConv() {
		if ( $this->_piwikFirstReferrerConv === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			// use boolval() in PHP >= 5.5.0
			$this->_piwikFirstReferrerConv = (bool) $cfg->getConfigParam( 'hdi-piwik_blFirstReferrerConv' );
		}
		return $this->_piwikFirstReferrerConv;
	}

	/**
	 * Enable JavaScript error tracking by Piwik. Piwik can track uncaught JavaScript
	 * errors from the Shop frontend in it's Event Tracking.
	 * This feature requires Piwik >= 2.2.0.
	 *
	 * @see enableJSErrorTracking( enable )
	 * @since hdi-piwik 2.1.0
	 * @return boolean
	 */
	public function getPiwikEnableJSError() {
		if ( $this->_piwikEnableJSError === null ) {
			$cfg = oxRegistry::get( 'oxConfig' );
			// use boolval() in PHP >= 5.5.0
			$this->_piwikEnableJSError = (bool) $cfg->getConfigParam( 'hdi-piwik_blEnableJSErrorTrackin' );
		}
		return $this->_piwikEnableJSError;
	}

	/**
	 * Returns text that will get displayed in Piwik for custom variables.
	 *
	 * @since hdi-piwik 2.0.0
	 * @return string
	 */
	public function getPiwikText($textIdentifier) {
		$cfg = oxRegistry::get( 'oxConfig' );
		switch ( $textIdentifier ) {
			case 'Newsletter':
				return $cfg->getConfigParam( 'hdi-piwik_sCustomvarNewsletter' );
				break;
			case 'Payment':
				return $cfg->getConfigParam( 'hdi-piwik_sCustomvarPayment' );
				break;
			case 'NewsletterAcc':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterAccount' );
				break;
			case 'NewsletterAccOn':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterAccountOn' );
				break;
			case 'NewsletterAccOff':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterAccountOff' );
				break;
			case 'Newsletter1':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterOrdered' );
				break;
			case 'Newsletter2':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterActivated' );
				break;
			case 'Newsletter3':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterCanceled' );
				break;
			case 'Newsletter4':
				return $cfg->getConfigParam( 'hdi-piwik_sNewsletterViewed' );
				break;
			default:
				return 'hdi-piwik_undefined';
				break;
		}
	}

	/**
	 * Generate custom variable tracking from additional parameter mapping.
	 * Get's the addition parameter mapping configuration from the module's settings
	 * and builds the Piwik calls to track the custom variable.
	 *
	 * @see addPiwikParamMapVisit()
	 * @see addPiwikParamMapPage()
	 * @since hdi-piwik 2.0.0
	 * @param string $sScope the scope
	 * @return string
	 */
	private function _getPiwikParamMap($sScope) {
		$ret = '';
		$aParamMap = null;
		$cfg = oxRegistry::get( 'oxConfig' );
		switch ( $sScope ) {
			case 'visit':
				$aParamMap = $cfg->getConfigParam( 'hdi-piwik_aParamMapVisit' );
				break;
			case 'page':
				$aParamMap = $cfg->getConfigParam( 'hdi-piwik_aParamMapPage' );
				break;
		}
		if ( is_array( $aParamMap ) ) {
			foreach ( $aParamMap as $sParam => $sParamConf ) {
				$aParamConf = explode( '|', $sParamConf );
				$iCustVarIndex = intval( $aParamConf[0] );
				$sCustVarName = $aParamConf[1];
				// Piwik allows a configured fixed number of custom variables per scope
				// @TODO actually this check should be done in the module's settings already
				if ( $iCustVarIndex < 1 || $iCustVarIndex > $this->getPiwikMaxCustVar() ) {
					echo "<!-- // hdi-piwik: invalid custom variable index '$iCustVarIndex' for scope '$sScope' -->";
					continue;
				}
				// Piwik's maximum custom variable name length is 200, it is advised to use short names because of other browser or webserver limits
				// @TODO actually this should be done in the module's settings already
				if ( strlen( $sCustVarName ) > 200 ) {
					echo "<!-- // hdi-piwik: custom variable name too long for index '$iCustVarIndex' and scope '$sScope' -->";
					$sCustVarName = substr( $sCustVarName, 0, 200 );
				}
				// Get the special chars replaced parameter from URL
				$sParamValue = $cfg->getRequestParameter( $sParam );
				if ( ! empty( $sParamValue ) ) {
					// Piwik's maximum custom variable value length is 200, it is advised to use short names because of other browser or webserver limits
					$sParamValue = substr( $sParamValue, 0, 200 );
					$ret .= "_paq.push(['setCustomVariable',$iCustVarIndex,'$sCustVarName','$sParamValue','$sScope']);\n";
				}
			}
		}
		return $ret;
	}

	/**
	 * Returns additional configured custom variables for visit-scope.
	 *
	 * @since hdi-piwik 2.0.0
	 * @return string
	 */
	public function addPiwikParamMapVisit() {
		return $this->_getPiwikParamMap( 'visit' );
	}

	/**
	 * Returns additional configured custom variables for page-scope.
	 *
	 * @since hdi-piwik 2.0.0
	 * @return string
	 */
	public function addPiwikParamMapPage() {
		return $this->_getPiwikParamMap( 'page' );
	}
}