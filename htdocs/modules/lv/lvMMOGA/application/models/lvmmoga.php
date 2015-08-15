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
 * Description of lvmmoga
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvmmoga extends oxBase {
    
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
    protected $_sLogFile = 'lvmmoga.log';
    
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
     * List of allowed platformtypes
     * @var array
     */
    protected $_aAllowedPlatformTypes = array(
        'World of Warcraft'=>'MMORPG',
        'Guild Wars'=>'MMORPG',
        'Diablo 2'=>'',
        'EVE Online'=>'MMORPG',
        'Diablo 3'=>'Online-Account',
        'BattleNet Games'=>'Online-Account',
        'EA Games'=>'Origin',
        'Steam Games'=>'Steam',
        'Rift'=>'MMORPG',
        'Runescape'=>'MMORPG',
        'Game Keys'=>'',
        'Star Wars The Old Republic'=>'MMORPG',
        'Lord Of The Rings Online'=>'MMORPG',
        'Uplay Games'=>'uplay',
        'TERA Fate of Arun'=>'MMORPG',
        'Maple Story'=>'MMORPG',
        'Wildstar'=>'MMORPG',
        'GOG Keys'=>'DRM-Free',
        'Elder Scrolls Online'=>'MMORPG',
        'Final Fantasy XIV Reborn'=>'MMORPG',
        'ArcheAge'=>'MMORPG',
    );



    /**
     * Initiates needed things
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oAffiliateTools     = oxNew( 'lvaffiliate_tools' );
        $this->_oLvConf             = $this->getConfig();
        $this->_iLogLevel           = (int)$this->_oLvConf->getConfigParam( 'sLvMMOGALogLevel' );
        $this->_blLogActive         = (bool)$this->_oLvConf->getConfigParam( 'blLvMMOGALogActive' );
        $this->_aVendorId           = $this->_oLvConf->getConfigParam( 'aLvMMOGAVendorId' );
        $this->_iMaxPages           = (int)$this->_oLvConf->getConfigParam( 'sLvMMOGAMaxPages' );
        $this->_aFeeds              = $this->_oLvConf->getConfigParam( 'aLvMMOGACsvFeeds' );
        $this->_sPartnerId          = $this->_oLvConf->getConfigParam( 'sLvMMOGAPartnerId' );
        $this->_sDefaultCategoryId  = $this->_oLvConf->getConfigParam( 'sLvMMOGADefaultCategoryId' );
        
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
        
        $sRequestUrl  = $this->_aFeeds[$sLangAbbr];
        
        $aResponse = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'CSV' );
        
        $mResult = false;
        if ( $aResponse ) {
            $mResult = $this->_lvParseRequest( $aResponse, $sLangAbbr );
        }
        
        return $mResult;
    }
    
   /**
     * Due MMOGA does not deliver pics in their feed we will need them to fetch from their detailspage
     * 
     * @param array $aResponse
     * @return void
     */
    public function lvCheckAndUpdatePicturesByScraping( $sLangAbbr ) {
        $sVendorId = $this->lvGetVendorId( $sLangAbbr );
        
        if ( $sVendorId ) {
            $sArticlesView = getViewName( 'oxarticles' );
            $sQuery = "SELECT OXID,OXEXTURL FROM ".$sArticlesView." WHERE OXVENDORID='".$sVendorId."' AND OXPIC1 = ''";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    $sOxid  = $oRs->fields['OXID'];
                    $sUrl  = $oRs->fields['OXEXTURL'];
                    
                    $sExtPicUrl = $this->_lvFetchPicUrlByTargetLink( $sUrl );
                    
                    if ( $sExtPicUrl ) {
                        $sUpdateQuery = "UPDATE oxarticles SET OXPIC1=".$this->_oLvDb->quote( $sExtPicUrl )." WHERE OXID='".$sOxid."' LIMIT 1";
                        $this->_oLvDb->Execute( $sUpdateQuery );
                    }
                    
                    $oRs->moveNext();
                }
            }
        }
    }
    
    
    /**
     * Returns pic url fetched from details-page
     * 
     * @param string $sUrl
     * @return string
     */
    protected function _lvFetchPicUrlByTargetLink( $sUrl ) {
        $sPicUrl = '';
        $sRequestUrl = $this->_lvRemovePartnerIdFromLink( $sUrl );
        $sResult = $this->_oAffiliateTools->lvGetRestRequestResult( $this->_blLogActive, $sRequestUrl, 'RAW' );
        
        if ( $sResult ) {
            $sPicUrl = $this->_lvParseRequestForImage( $sResult );
        }
        
        return $sPicUrl;
    }
    
    
    /**
     * Scrapes the packshot from details page html
     * 
     * @param string $sHtml
     * @return string
     */
    protected function _lvParseRequestForImage( $sHtml ) {
        $sPicResult = '';
        preg_match_all( "/<div style=\"float:left;width:95px;text-align:center;\"><img src=\"(.*)\" alt=.?/", $sHtml, $aPicResult );
        if ( isset( $aPicResult[1][0] ) && $aPicResult[1][0] != '' ) {
            $sPicResult = $aPicResult[1][0];
        }

        return $sPicResult;
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
            
            $blCategoryTypeAllowed = $this->_lvCategoryTypeAllowed( trim( $aArticle[6] ) );
            if ( !$blCategoryTypeAllowed ) continue;
            
            $sId                                        = (string)$aArticle[0];
            $dPrice                                     = (double)str_replace( ",", ".", $aArticle[2] );
            $sTitle                                     = trim( $aArticle[1] );
            $sTitle                                     = $this->_oAffiliateTools->lvGetNormalizedName( $sTitle );
            
            $aArticleData[$sId]['ARTNUM']               = $sId;
            $aArticleData[$sId]['TITLE']                = $sTitle;
            $aArticleData[$sId]['PRICE']                = $dPrice;
            $aArticleData[$sId]['EXTURL']               = trim( $aArticle[5] );
            $aArticleData[$sId]['SALESRANK']            = '999999';
            $aArticleData[$sId]['DOWNLOADTYPE']         = 'Game-Key';
            
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
            
            // check drm from category row
            $sDrm = $this->_lvgetDrmFromCategoryType( trim( $aArticle[6] ) );
            if ( $sDrm != '' ) {
                $sDrm = str_replace( 'MMORPG', 'Online-Account', $sDrm );
                $aArticleData[$sId]['DRM'] = $sDrm;
            }
            
            // no information we'll guess its all about windows
            $aArticleData[$sId]['COMPATIBILITY']['WIN'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];

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
    
    
    /**
     * Method checks if given category type is allowed
     * 
     * @param type $sCategoryType
     * @return bool
     */
    protected function _lvCategoryTypeAllowed( $sCategoryType ) {
        $blAllowed      = false;
        $aTypes         = explode( "/", $sCategoryType );

        if ( count( $aTypes ) == 2 ) {
            $sKeyType       = trim( $aTypes[1] );
            $sPlatformType  = trim( $aTypes[0] );
            
            if ( $sKeyType == 'Cdkey' ) {
                if ( in_array( $sPlatformType, $this->_aAllowedPlatformTypes ) ) {
                    $blAllowed = true;
                }
            }
        }    
        
        return $blAllowed;
    }
    
    
    /**
     * Creates partnerlink including partner id and returns it
     * 
     * @param type $sLink
     * @return string
     */
    protected function _lvGetPartnerLink( $sLink ) {
        $sLink = trim( (string)$sLink );
        
        $sPartnerLink = $sLink.$this->_sPartnerId;
        
        return $sPartnerLink;
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
    
}
