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
 * Description of lvgog
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvgog extends oxBase {
    
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
    protected $_sLogFile = 'lvgog.log';
    
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
     * Indicates the current page number
     * @var int
     */
    protected $_iCurrentPage = null;

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
     * Amount of maximum requested pages
     * @var int
     */
    protected $_iMaxPages = null;
    
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
        
        $this->_oAffiliateTools = oxNew( 'lvaffiliate_tools' );
        $this->_oLvConf             = $this->getConfig();
        $this->_iLogLevel           = (int)$this->_oLvConf->getConfigParam( 'sLvGogLogLevel' );
        $this->_blLogActive         = (bool)$this->_oLvConf->getConfigParam( 'blLvGogLogActive' );
        $this->_aVendorId           = $this->_oLvConf->getConfigParam( 'aLvGogVendorId' );
        $this->_iMaxPages           = (int)$this->_oLvConf->getConfigParam( 'sLvGogMaxPages' );
        $this->_aFeeds              = $this->_oLvConf->getConfigParam( 'aLvGogXmlStdFeeds' );
        $this->_sPartnerId          = $this->_oLvConf->getConfigParam( 'sLvGogPartnerId' );
        $this->_sDefaultCategoryId  = $this->_oLvConf->getConfigParam( 'sLvGogDefaultCategoryId' );
        
        $sMappingFilePath           = getShopBasePath()."/modules/lv/lvGog/config/category_mapping.csv";
        $this->_aCategoryMapping    = $this->_oAffiliateTools->lvGetCategoryMapping( $sMappingFilePath );
        $this->_iCurrentPage        = 0;
        
        $this->_oAffiliateTools->lvSetLogInformation( $this->_blLogActive, $this->_sLogFile, $this->_iLogLevel );

    }
    
    /**
     * Returns ready formated data beeing ready for import via affiliate importer
     * 
     * @param string $sLangAbbr
     * @param string $sType (optional)
     * @return mixed bool/array
     */
    public function lvGetNextImportData( $sLangAbbr ) {
        $this->_iCurrentPage++;
        
        // check if maximum pages are exceeded
        if ( $this->_iCurrentPage > $this->_iMaxPages ) return false;
        
        $sRequestUrl  = $this->_aFeeds[$sLangAbbr];
        $sRequestUrl .= "&page=".(string)$this->_iCurrentPage;
        
        $oResponse = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'XML' );

        $mResult = false;
        if ( $oResponse ) {
            $mResult = $this->_lvParseRequest( $oResponse, $sLangAbbr );
        }
        
        return $mResult;
    }

    /**
     * Parses a simple-XML Result for building a import ready data array
     * 
     * @param object $oResponse
     * @param string $sLangAbbr
     * @return array
     */
    protected function _lvParseRequest( $oResponse, $sLangAbbr ) {
        $aArticleData = array();
        if ( isset( $oResponse->products->product ) ) {
            foreach ( $oResponse->products->product as $oProduct ) {
                $sId                                        = (string)$oProduct->id;
                $aArticleData[$sId]['ARTNUM']               = $sId;
                $sTitle                                     = trim( (string)$oProduct->title );
                $aArticleData[$sId]['TITLE']                = $sTitle;
                $dPriceRaw                                  = (double)$oProduct->price_raw;
                $dPrice                                     = $dPriceRaw/100;
                $dDiscountRaw                               = (double)$oProduct->discount_raw;
                $dDiscount                                  = $dDiscountRaw/100;
                $aArticleData[$sId]['PRICE']                = $dPrice;
                $aArticleData[$sId]['TPRICE']               = $dPrice+$dDiscount;
                $aArticleData[$sId]['EXTURL']               = $this->_lvGetPartnerLink( $oProduct->link );
                $aArticleData[$sId]['MANUFACTURER']         = trim( (string)$oProduct->publisher );
                $sCategory                                  = trim( (string)$oProduct->gener_1 );
                $aArticleData[$sId]['GENRE']                = $sCategory;
                $aArticleData[$sId]['CATEGORYID']           = array( ( isset( $this->_aCategoryMapping[$sCategory]['category'] ) ) ? $this->_aCategoryMapping[$sCategory]['category'] : $this->_sDefaultCategoryId );
                $aArticleData[$sId]['RELEASE']              = (string)$oProduct->original_release_date;
                $aArticleData[$sId]['COVERIMAGE']           = trim( (string)$oProduct->img_cover );
                $aArticleData[$sId]['SALESRANK']            = '999999';
                $aArticleData[$sId]['DRM']                  = 'DRM Free';
                
                // addon?
                $blAddon = $this->_oAffiliateTools->lvFetchAddonFromTitle( $sTitle );
                if ( $blAddon ) {
                    $aArticleData[$sId]['ADDON']    = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }

                // DLC?
                $blDLC = $this->_oAffiliateTools->lvFetchDLCFromTitle( $sTitle );
                if ( $blDLC ) {
                    $aArticleData[$sId]['DLC']      = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }
                
                // platform
                $sWin       = (string)$oProduct->windows_compatible;
                $sMac       = (string)$oProduct->mac_compatible;
                $sLin       = (string)$oProduct->linux_compatible;
                
                if ( $sWin == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['WIN'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }
                if ( $sMac == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['MAC'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }
                if ( $sLin == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['LIN'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }

                $blValidData = $this->_oAffiliateTools->lvValidateData( $aArticleData[$sId] );
                
                if ( !$blValidData ) {
                    unset( $aArticleData[$sId] );
                }
            }
        }
        
        if ( count( $aArticleData ) <= 0 ) {
            $aArticleData = false;
        }
        
        return $aArticleData;
    }
    
    
    
    /**
     * Creates partnerlink including partner id and returns it
     * 
     * @param type $sLink
     * @return string
     */
    protected function _lvGetPartnerLink( $sLink ) {
        $sLink = trim( (string)$sLink );
        
        if ( strpos( $sLink, '?' ) !== false ) {
            $sPartnerLink = $sLink."&pp=".$this->_sPartnerId;
        }
        else {
            $sPartnerLink = $sLink."?pp=".$this->_sPartnerId;
        }
        
        return $sPartnerLink;
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
    
    
}
