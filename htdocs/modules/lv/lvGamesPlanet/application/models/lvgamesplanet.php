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
 * Description of lvgamesplanet
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvgamesplanet extends oxBase {
    
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
    protected $_sLogFile = 'lvgp.log';
    
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
     * Database object
     * @var object
     */
    protected $_oLvDb = null;
        
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
     * Initiates needed things
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oAffiliateTools = oxNew( 'lvaffiliate_tools' );
        $this->_oLvConf         = $this->getConfig();
        $this->_oLvDb           = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $this->_iLogLevel       = (int)$this->_oLvConf->getConfigParam( 'sLvGpLogLevel' );
        $this->_blLogActive     = (bool)$this->_oLvConf->getConfigParam( 'blLvGpLogActive' );
        $this->_aVendorId       = $this->_oLvConf->getConfigParam( 'aLvGpVendorId' );
        
        $this->_oAffiliateTools->lvSetLogInformation( $this->_blLogActive, $this->_sLogFile, $this->_iLogLevel );
        $this->_lvLoadCategoryMapping();
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
     * @param string $sType (optional)
     * @return array
     */
    public function lvGetImportData( $sLangAbbr, $sType='std' ) {
        switch( $sType ) {
            case 'std':
                $aFeeds = $this->_oLvConf->getConfigParam( 'aLvGamesplanetXmlStdFeeds' );
                break;
            case 'flash':
                $aFeeds = $this->_oLvConf->getConfigParam( 'aLvGamesplanetXmlFlashDeals' );
                break;
            case 'charts':
                $aFeeds = $this->_oLvConf->getConfigParam( 'aLvGamesplanetXmlCharts' );
                break;
            default:
                $aFeeds = $this->_oLvConf->getConfigParam( 'aLvGamesplanetXmlStdFeeds' );
        }
        
        $sRequestUrl = $aFeeds[$sLangAbbr];
        $oResponse = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'XML' );

        $aResult = array();
        if ( $oResponse ) {
            $aResult = $this->_lvParseRequest( $oResponse, $sLangAbbr );
        }
        
        return $aResult;
    }
    
    
    /**
     * Method takes care of fetching system requirements from details page
     * 
     * @param string $sLangAbbr
     * @return void
     */
    public function lvFillSysRequirements( $sLangAbbr ) {
        $sQuery = "
            SELECT 
                o2a.OXID, 
                o2a.OXATTRID, 
                oa.OXEXTURL 
            FROM oxobject2attribute o2a 
            LEFT JOIN oxarticles oa ON (o2a.OXOBJECTID=oa.OXID) 
            WHERE o2a.OXATTRID IN ( 'CompatibilityTypeWin', 'CompatibilityTypeMac', 'CompatibilityTypeLin' ) 
            AND oa.OXVENDORID='".$this->lvGetVendorId( $sLangAbbr )."' 
        ";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid          = $oRs->fields['OXID'];
                $sAttributeId   = $oRs->fields['OXATTRID'];
                $sDetailsUrl    = $oRs->fields['OXEXTURL'];
                
                $sSysRequirements = $this->_lvFetchSysRequirementsFromDetails( $sDetailsUrl, $sAttributeId );
                
                if ( $sSysRequirements ) {
                    $this->_lvUpdateSysRequirements( $sOxid, $sSysRequirements );
                }
                
                $oRs->moveNext();
            }
        }
    }
    
    
    /**
     * Updating Systemrequirements
     * 
     * @param string $sOxid
     * @param string $sSysRequirements
     * @return void
     */
    protected function _lvUpdateSysRequirements( $sOxid, $sSysRequirements ) {
        $sSysRequirements = $this->_oLvDb->quote( $sSysRequirements );
        $sQuery = "
            UPDATE oxobject2attribute SET 
                LVATTRDESC      = ".$sSysRequirements.",
                LVATTRDESC_1    = ".$sSysRequirements.",
                LVATTRDESC_2    = ".$sSysRequirements.",
                LVATTRDESC_3    = ".$sSysRequirements."
            WHERE
                OXID='".$sOxid."'
        ";
        
        $this->_oLvDb->Execute( $sQuery );
    }
    
    
    /**
     * 
     * @param string $sDetailsUrl
     * @param string $sAttributeId
     * @return string
     */
    protected function _lvFetchSysRequirementsFromDetails( $sUrl, $sAttributeId ) {
        $sSysRequirements = '';
        $sRequestUrl = $this->_lvRemovePartnerIdFromLink( $sUrl );
        $sResult = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'RAW' );
        
        if ( $sResult ) {
            $sSysRequirements = $this->_lvParseRequestForSysRequirements( $sResult, $sAttributeId );
        }
        
        return $sSysRequirements;
    }
    
    
    /**
     * Scrapes sysrequirements from details page html
     * 
     * @param string $sHtml
     * @param string $sAttributeId
     * @return string
     */
    protected function _lvParseRequestForSysRequirements( $sHtml, $sAttributeId ) {
        $sSysRequirements = '';
        
        $oDom = new DOMDocument();
        $oDom->loadHTML( $sHtml );
        
        // fetch  sysrequirements part
        $oSysReq = $dom->getElementById( 'sysreqs' );
        
        // get all data
        $aSysReqDataNodes = $oSysReq->getElementsByTagName( 'table' );
        $aSysReqHeadNodes = $oSysReq->getElementsByTagName( 'h2' );
        
        foreach ( $aSysReqDataNodes as $oNode ) {
            $aDataTables[] = (string)$oNode->ownerDocument->saveXML($oNode);
        }

        foreach ( $aSysReqHeadNodes as $oNode ) {
            $aHeadlines[] = (string)$oNode->ownerDocument->saveXML($oNode);
        }
        
        
        switch( $sAttributeId ) {
            case 'CompatibilityTypeWin':
                $sNeedle = 'Windows';
                break;
            case 'CompatibilityTypeMac':
                $sNeedle = 'Mac OS';
                break;
            case 'CompatibilityTypeLin':
                $sNeedle = 'Linux';
                break;
        }
        
        $iSearchIndex = null;
        foreach ( $aHeadlines as $iIndex=>$sContent ) {
            if ( strpos( $sContent, $sNeedle) !== false ) {
                $iSearchIndex = $iIndex;
            }
        }
        
        if ( $iSearchIndex !== null ) {
            $sSysRequirements = $aDataTables[$iSearchIndex];
        }

        return $sSysRequirements;
    }
    
    
    /**
     * Removes partner id from link
     * 
     * @param string $sUrl
     * @return string
     */
    protected function _lvRemovePartnerIdFromLink( $sUrl ) {
        $aRemovals = array();
        $aRemovals[] = "?ref=".$this->_sPartnerId;
        $aRemovals[] = "&ref=".$this->_sPartnerId;
        
        foreach ( $aRemovals as $sRemoval ) {
            $sUrl = str_replace( $sRemoval, "", $sUrl );
        }
        
        return $sUrl;
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
        if ( isset( $oResponse->product ) ) {
            foreach ( $oResponse->product as $oProduct ) {
                $sId = (string)$oProduct->uid;
                $aArticleData[$sId]['ARTNUM']               = $sId;
                $sTitle                                     = trim( (string)$oProduct->name );
                $aArticleData[$sId]['TITLE']                = $sTitle;
                $aArticleData[$sId]['PRICE']                = (double)$oProduct->price;
                $aArticleData[$sId]['TPRICE']               = (double)$oProduct->price_base;
                $aArticleData[$sId]['EXTURL']               = trim( (string)$oProduct->link );
                $aArticleData[$sId]['MANUFACTURER']         = trim( (string)$oProduct->publisher );
                $sCategory                                  = trim( (string)$oProduct->category );
                $aArticleData[$sId]['GENRE']                = $sCategory;
                $aArticleData[$sId]['CATEGORYID']           = array( $this->_aCategoryMapping[$sCategory]['category'] );
                $aArticleData[$sId]['SHORTDESC']            = trim( (string)$oProduct->desc );
                $aArticleData[$sId]['COVERIMAGE']           = trim( (string)$oProduct->packshot );
                $aArticleData[$sId]['SALESRANK']            = '999999';
                
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
                $sWin       = (string)$oProduct->platforms->pc;
                $sMac       = (string)$oProduct->platforms->mac;
                $sLin       = (string)$oProduct->platforms->linux;
                
                if ( $sWin == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['WIN'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }
                if ( $sMac == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['MAC'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }
                if ( $sLin == 'true' ) {
                    $aArticleData[$sId]['COMPATIBILITY']['LIN'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                }

                $blValidData = $this->_lvValidateData( $aArticleData[$sId] );
                
                if ( !$blValidData ) {
                    unset( $aArticleData[$sId] );
                }
            }
        }
        
        return $aArticleData;
    }
    
    
    /**
     * Method checks if data fulfills minimum
     * 
     * @param type $aCurrentArticle
     * @return bool
     */
    protected function _lvValidateData( $aCurrentArticle ) {
        $blDataIsValid = true;
        
        if ( !isset( $aCurrentArticle['TITLE'] ) || trim( $aCurrentArticle['TITLE'] )  == '' ) {
            $blDataIsValid = false;
        }
        if ( !isset( $aCurrentArticle['ARTNUM'] ) || trim( $aCurrentArticle['ARTNUM'] )  == '' ) {
            $blDataIsValid = false;
        }
        
        return $blDataIsValid;
    }
    
    
    /**
     * Loads category mapping from CSV and puts it into an array attribute
     * 
     * @param void
     * @return void
     */
    protected function _lvLoadCategoryMapping() {
        $sMappingFilePath = getShopBasePath()."/modules/lv/lvGamesPlanet/config/category_mapping.csv";
        if ( file_exists( $sMappingFilePath ) ) {
            $resMappingFile = fopen( $sMappingFilePath, "r" );
            while ( ( $aData = fgetcsv( $resMappingFile, 1000, ";" ) ) !== false ) {
                $sAmazonCategory = $aData[0];
                $this->_aCategoryMapping[$sAmazonCategory]['category'] = $aData[1];
            }                        
        }
    }
    
}
