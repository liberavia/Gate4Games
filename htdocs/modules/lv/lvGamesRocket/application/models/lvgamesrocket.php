<?php

/*
 * Copyright (C) 2015 André Gregor-Herrmann
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
 * Description of lvgamesrocket
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvgamesrocket extends oxBase {
    /**
     * Flag for active log
     * @var int
     */
    protected $_blLogActive = false;

    /**
     * Set log level default is 1 = Errors
     * @var int
     */
    protected $_iLogLevel = 1;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvgr.log';
    
    /**
     * Instance of affiliate tools
     * @var object
     */
    protected $_oAffiliateTools = null;
    
    /**
     * Configuration instance
     * @var object
     */
    protected $_oLvConf = null;
    
    /**
     * Mapping of categories
     * @var array
     */
    protected $_aCategoryMapping = array();
    
    /**
     * Toggle yes by lang
     * @var type 
     */
    protected $_aLvToggleAttributeYesByLangAbbr = array(
        'de'    => 'Ja',
        'en'    => 'Yes',
    );
    
    /**
     * VendorId
     * @var string
     */
    protected $_sVendorId = '';
    
    /**
     * List of languages and belonging feed addresses
     * @var array
     */
    protected $_aFeeds = array();
    
    /**
     * Id of partner program
     * @var string
     */
    protected $_sPartnerId = null;
    
    /**
     * Default category id
     * @var string
     */
    protected $_sDefaultCategoryId = null;



    /**
     * Initiates needed things
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oAffiliateTools     = oxNew( 'lvaffiliate_tools' );
        $this->_oLvConf             = $this->getConfig();
        $this->_iLogLevel           = (int)$this->_oLvConf->getConfigParam( 'sLvGrLogLevel' );
        $this->_blLogActive         = (bool)$this->_oLvConf->getConfigParam( 'blLvGrLogActive' );
        $this->_aVendorId           = $this->_oLvConf->getConfigParam( 'aLvGrVendorId' );
        $this->_iMaxPages           = (int)$this->_oLvConf->getConfigParam( 'sLvGrMaxPages' );
        $this->_aFeeds              = $this->_oLvConf->getConfigParam( 'aLvGrCsvFeeds' );
        $this->_sPartnerId          = $this->_oLvConf->getConfigParam( 'sLvGrPartnerId' );
        $this->_sDefaultCategoryId  = $this->_oLvConf->getConfigParam( 'sLvGrDefaultCategoryId' );
        
        $this->_oAffiliateTools->lvSetLogInformation( $this->_blLogActive, $this->_sLogFile, $this->_iLogLevel );

    }
    
    /**
     * Returns configured vendor id
     * 
     * @param void
     * @return string
     */
    public function lvGetVendorId( $sLangAbbr ) {
        return $this->_aVendorId[$sLangAbbr];
    }
    

    /**
     * Returns ready formated data beeing ready for import via affiliate importer
     * 
     * @param string $sLangAbbr
     * @return mixed bool/array
     */
    public function lvGetImportData( $sLangAbbr ) {
        
        $sRequestUrl  = $this->_aFeeds[$sLangAbbr]."?k=".$this->_sPartnerId;

        $aResponse = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'CSV', $sCsvLineEnd = "\n", $sCsvDelimiter = '|', $sCsvEnclosure = '"' );

        $mResult = false;
        if ( $aResponse ) {
            $mResult = $this->_lvParseRequest( $aResponse, $sLangAbbr );
        }
        
        return $mResult;
    }
    
    /**
     * Parses a prepared CSV-Response to import ready data array
     * 
     * @param array $aResponse
     * @param string $sLangAbbr
     * @return array
     */
    protected function _lvParseRequest( $aResponse, $sLangAbbr ) {
        $aArticleData = array();
        
        foreach ( $aResponse as $aArticle ) {
            $sDownloadCategory                          = trim( $aArticle[8] );
            if ( $sDownloadCategory != "PC / Mac" ) continue;
            
            $sId                                        = (string)$aArticle[1];
            $dPrice                                     = (double)str_replace( ",", ".", $aArticle[4] );
            $dTPrice                                    = (double)str_replace( ",", ".", $aArticle[19] );
            $sTitle                                     = trim( $aArticle[1] );
            $sTitle                                     = $this->_oAffiliateTools->lvGetNormalizedName( $sTitle );
            $sPlattform                                 = trim( $aArticle[20] );
            $sDRM                                       = trim( $aArticle[21] );
            $sShortDesc                                 = trim( $aArticle[3] );
            
            $aArticleData[$sId]['ARTNUM']               = "GAMESROCKET-".$sId;
            $aArticleData[$sId]['TITLE']                = $sTitle;
            $aArticleData[$sId]['SHORTDESC']            = $sShortDesc;
            $aArticleData[$sId]['PRICE']                = $dPrice;
            $aArticleData[$sId]['TPRICE']               = $dTPrice;
            $aArticleData[$sId]['EXTURL']               = trim( $aArticle[6] );
            $aArticleData[$sId]['COVERIMAGE']           = trim( $aArticle[9] );
            $aArticleData[$sId]['SALESRANK']            = '999999';
            
            
            // DRM?
            $sTargetDRM = '';
            switch ( $sDRM ) {
                case 'no_drm':
                    $sTargetDRM = 'DRM Free';
                    break;
                case 'uplay':
                    $sTargetDRM = 'uplay';
                    break;
                case 'steam':
                    $sTargetDRM = 'Steam';
                    break;
                case 'battlenet':
                    $sTargetDRM = 'battlenet';
                    break;
                case 'origin':
                    $sTargetDRM = 'Origin';
                    break;
            }
            
            if ( $sTargetDRM ) {
                $aArticleData[$sId]['DRM']          = $sTargetDRM;
            }
            
            // free2play?
            if ( $sDRM == 'freetoplay' ) {
                $aArticleData[$sId]['FREETOPLAY']   = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
            }
            
            // addon?
            $blAddon = $this->_oAffiliateTools->lvFetchAddonFromTitle( $sTitle );
            if ( $blAddon ) {
                $aArticleData[$sId]['ADDON']        = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
            }

            // DLC?
            $blDLC = $this->_oAffiliateTools->lvFetchDLCFromTitle( $sTitle );
            if ( $blDLC ) {
                $aArticleData[$sId]['DLC']          = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
            }
            
            $aPlatforms = explode( "-", $sPlattform );
            foreach ( $aPlatforms as $sCurrentPlatform ) {
                switch ( $sCurrentPlatform ) {
                    case 'Windows':
                        $sTargetIndex = 'WIN';
                        break;
                    case 'Mac':
                        $sTargetIndex = 'MAC';
                        break;
                    case 'Linux':
                        $sTargetIndex = 'LIN';
                        break;
                    default:
                        $sTargetIndex = 'WIN';
                }
                
                $aArticleData[$sId]['COMPATIBILITY'][$sTargetIndex] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
            }

            $blValidData = $this->_oAffiliateTools->lvValidateData( $aArticleData[$sId] );

            if ( !$blValidData ) {
                unset( $aArticleData[$sId] );
            }
        }
        
        if ( count( $aArticleData ) <= 0 ) {
            $aArticleData = false;
        }
        
        return $aArticleData;
    }
    
}
