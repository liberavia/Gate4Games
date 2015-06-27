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
 * Core class for all api operations with amazon partner net
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvamzpnapiconnector extends oxBase {
    
    /**
     * Data template for search requests
     * @var array
     */
    protected $_aLvRequestTemplateSearch = array(
        'AWSAccessKeyId'    =>'',
        'AssociateTag'      =>'',
        'BrowseNode'        =>'',
        'Condition'         =>'',
        'ItemPage'          =>'',
        'MaximumPrice'      =>'',
        'MinimumPrice'      =>'',
        'Operation'         =>'ItemSearch',
        'ResponseGroup'     =>'',
        'SearchIndex'       =>'',
        'Service'           =>'AWSECommerceService',
        'Timestamp'         =>'',
        'Version'           =>'2011-08-01',
    );
    
    /**
     * Data template for details requests
     * @var array
     */
    protected $_aLvRequestTemplateDetails = array(
        'AWSAccessKeyId'    =>'',
        'AssociateTag'      =>'',
        'IdType'            =>'ASIN',
        'ItemId'            =>'',
        'Operation'         =>'ItemSearch',
        'ResponseGroup'     =>'',
        'Service'           =>'AWSECommerceService',
        'Timestamp'         =>'',
        'Version'           =>'2011-08-01',
    );
    
    /**
     * Toggle yes by lang
     * @var type 
     */
    protected $_aLvToggleAttributeYesByLangAbbr = array(
        'de'    => 'Ja',
        'en'    => 'Yes',
    );
    
    /**
     * Maximum page result amazon is accepting to liver full results
     * @var int
     */
    protected $_iMaxPageResult = 10;
    
    /**
     * Template for header for creating request signature
     * @var array
     */
    protected $_aLvSignHeader = array(
        'GET',
        'webservices.amazon.de',
        '/onca/xml',
    );
    
    /**
     * Stores current processed asin
     * @var string
     */
    protected $_sCurrentAsin = null;
    
    /**
     * Flag that signals if log option is set to active
     * @var bool
     */
    protected $_blLvAmzPnLogActive = false;
    
    /**
     * Set log level default is 1 = Errors
     * @var int
     */
    protected $_iLvAmzPnLogLevel = 1;
    
    /**
     * Stores current used page number
     * @var int
     */
    protected $_iCurrentPageNumber = null;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvamzpn.log';
    
    /**
     * List of cateory mappings
     * @var array 
     */
    protected $_aCategoryMapping = array();
    
    /**
     * Default category which will be used there is no mapping match
     * @var string
     */
    protected $_sDefaultCategoryId = '';



    /**
     * Constructor adds configuration of logging
     */
    public function __construct() {
        $oConfig = $this->getConfig();
        $this->_blLvAmzPnLogActive = $oConfig->getConfigParam( 'blLvAmzPnLogActive' );
        $sLogLevel = $oConfig->getConfigParam( 'sLvAmzPnLogLevel' );
        if ( $sLogLevel ) {
            $this->_iLvAmzPnLogLevel = (int)$sLogLevel;
        }
        $this->_lvLoadCategoryMapping();
        $this->_sDefaultCategoryId = $oConfig->getConfigParam( 'sLvAmzPnDefaultCatId' );
        
        parent::__construct();
    }


    /**
     * Method returns the amount of pages of the browse node based search request
     * 
     * @param void
     * @return int
     */
    public function lvGetSearchPageAmount( $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex = null ) {
        $iPageAmount = 0;
        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'search', $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex );
        if ( $sSignedRequestUrl ) {
            $oResponse = $this->_lvGetRequestResult( $sSignedRequestUrl );
            if ( isset( $oResponse->Items->TotalPages ) ) {
                $iPageAmount = (int)$oResponse->Items->TotalPages;
            }
        }
        
        // we need to slow down requests due we don't want to run into throttled error
        sleep(1);
        
        return $iPageAmount;
    }
    
    
    /**
     * Public getter for returning maximum result pages amazon will deliver with full results
     * 
     * @param void
     * @return int
     */
    public function lvGetMaxPageResult() {
        return $this->_iMaxPageResult;
    }
    
    
    /**
     * Method returns an array of ASINS of defined browse node which are on the given page 
     * 
     * @param int $iPageNumber (optional)
     * @return array
     */
    public function lvGetItemSearchAsins( $iPageNumber = null ) {
        $this->_iCurrentPageNumber = $iPageNumber;
        
        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'search' );
        
        if ( $sSignedRequestUrl ) {
            $oResponse = $this->_lvGetRequestResult( $sSignedRequestUrl );
            print_r( $oResponse );
        }

        $this->_iCurrentPageNumber = null;
    }
    
    
    /**
     * Method returns an array of ASINS including detailed information of defined browse node which are on the given page 
     * 
     * @param int $iPageNumber (optional)
     * @return array
     */
    public function lvGetItemSearchAsinDetails( $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex = null, $iPageNumber = null ) {
        $aArticleData = array();
        $this->_iCurrentPageNumber = $iPageNumber;
        
        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'search', $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex );
        $this->lvLog( 'DEBUG: Requesting search page with LangAbbr '.$sLangAbbr.' and BrowseNodeIndex '.(string)$iBrowseNodeIndex.' and PriceRangeIndex '.(string)$iPriceRangeIndex."Signed URL is:\n".$sSignedRequestUrl );
        
        if ( $sSignedRequestUrl ) {
            $oResponse = $this->_lvGetRequestResult( $sSignedRequestUrl );
            if ( isset( $oResponse->Items->{Item} ) ) {
                foreach ( $oResponse->Items->{Item} as $oItem ) {
                    $sAsin = (string)$oItem->ASIN;
                    $aArticleData[$sAsin]['ARTNUM'] = $sAsin;

                    // fetching additional information from title
                    $sTitle = (string)$oItem->ItemAttributes->Title;
                    $sDRMInfo = $this->_lvFetchDRMInfoFromTitle( $sTitle );
                    if ( $sDRMInfo ) {
                        $aArticleData[$sAsin]['DRM'] = $sDRMInfo;
                        $sDownloadType = $this->_lvFetchDownloadTypeFromDRM( $sDRMInfo );
                        if ( $sDownloadType ) {
                            $aArticleData[$sAsin]['DOWNLOADTYPE'] = $sDownloadType;
                        }
                    }
                    $sTitle = $this->_lvCleanupAmazonTitle( $sTitle );
                    $aArticleData[$sAsin]['TITLE'] = $sTitle;

                    $aArticleData[$sAsin]['EXTURL'] = (string)$oItem->DetailPageURL;
                    $aArticleData[$sAsin]['COVERIMAGE'] = (string)$oItem->LargeImage->URL;

                    // go through image sets
                    if ( isset( $oItem->ImageSets->{ImageSet} ) ) {
                        $iIndex = 1;
                        foreach ( $oItem->ImageSets->{ImageSet} as $oImageSet ) {
                            $aArticleData[$sAsin]['PIC'.$iIndex] = (string)$oImageSet->LargeImage->URL;
                            $iIndex++;
                        }
                    }
                    // manufacturer
                    $aArticleData[$sAsin]['MANUFACTURER'] = (string)$oItem->ItemAttributes->Manufacturer;
                    // category handling
                    $sAmazonGenre = (string)$oItem->ItemAttributes->Genre;
                    $aArticleData[$sAsin]['GENRE'] = $sAmazonGenre;
                    if ( isset( $this->_aCategoryMapping[$sAmazonGenre] ) ) {
                        $aArticleData[$sAsin]['CATEGORYID']         = array( $this->_aCategoryMapping[$sAmazonGenre]['category'] );
                    }
                    else {
                        $aArticleData[$sAsin]['CATEGORYID']         = array( $this->_sDefaultCategoryId );
                    }


                    $aArticleData[$sAsin]['RELEASE'] = (string)$oItem->ItemAttributes->ReleaseDate;

                    // fetching language information
                    /**
                     * @todo Temporarily removed getting language information due its simply cram coming from API
                     * 
                    if ( isset( $oItem->ItemAttributes->Languages->{Language} ) ) {
                        foreach ( $oItem->ItemAttributes->Languages->{Language} as $oLanguage ) {
                            $sType = (string)$oLanguage->Type;
                            if ( $sType == 'Subtitled' ) {
                                $aArticleData[$sAsin]['LANGUAGEINFO']['SUBTITLE'] = (string)$oLanguage->Name;
                            }
                            if ( $sType == 'Dubbed' ) {
                                $aArticleData[$sAsin]['LANGUAGEINFO']['DUBBED'] = (string)$oLanguage->Name;
                            }
                            if ( $sType == 'Original' ) {
                                $aArticleData[$sAsin]['LANGUAGEINFO']['ORIGINAL'] = (string)$oLanguage->Name;
                            }
                        }
                    }
                    */
                    
                    // addon?
                    $blAddon = $this->_lvFetchAddonFromTitle( $sTitle );
                    if ( $blAddon ) {
                        $aArticleData[$sAsin]['ADDON'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                    }
                    
                    // DLC?
                    $blDLC = $this->_lvFetchDLCFromTitle( $sTitle );
                    if ( $blDLC ) {
                        $aArticleData[$sAsin]['DLC'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                    }
                    
                    // platform information (possible multiple tags)
                    $aPlatforms = array();
                    if ( isset( $oItem->ItemAttributes->Platform ) ) {
                        foreach ( $oItem->ItemAttributes->Platform as $sPlatform ) {
                            $aPlatforms[] = (string)$sPlatform;
                        }
                    }
                    
                    foreach ( $aPlatforms as $sRawPlatform ) {
                        $sCleanedPlatform = $this->_lvGetPlatform( $sRawPlatform );
                        
                        if ( $sCleanedPlatform ) {
                            $aArticleData[$sAsin]['COMPATIBILITY'][$sCleanedPlatform] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];
                        }
                    }
                    
                    //amazon sales rank
                    if ( isset( $oItem->SalesRank )  ) {
                        $aArticleData[$sAsin]['SALESRANK'] = (string)$oItem->SalesRank;
                    }
                    
                    if ( !isset( $oItem->OfferSummary->LowestNewPrice->Amount ) ) {
                        unset( $aArticleData[$sAsin] );
                        continue;
                    }
                    
                    // Price handling
                    $sTPriceCent    = (string)$oItem->ItemAttributes->ListPrice->Amount;
                    $sPriceCent     = (string)$oItem->OfferSummary->LowestNewPrice->Amount;
                    // double values
                    $dTPrice        = (double)$sTPriceCent/100;
                    $dPrice         = (double)$sPriceCent/100;
                    // assign prices
                    $aArticleData[$sAsin]['TPRICE'] = $dTPrice;
                    $aArticleData[$sAsin]['PRICE'] = $dPrice;
                    
                    // check if free to play or if article should be skipped
                    if ( $dTPrice = 0.01 && $dPrice == 0 ) {
                        // free to play article
                        $aArticleData[$sAsin]['FREETOPLAY'] = $this->_aLvToggleAttributeYesByLangAbbr[$sLangAbbr];;
                    }
                    else if ( $dPrice == 0 ) {
                        // remove article from import
                        unset( $aArticleData[$sAsin] );
                    }
                }
            }
        }
        else {
            // request failed => into log
            $this->lvLog( 'ERROR: Requested search page with LangAbbr '.$sLangAbbr.' and BrowseNodeIndex '.(string)$iBrowseNodeIndex.' and PriceRangeIndex '.(string)$iPriceRangeIndex." FAILED! Signed URL was:\n".$sSignedRequestUrl, 1 );
        }
        
        $this->_iCurrentPageNumber = null;
        
        return $aArticleData;
    }

    
    /**
     * Loggs a message depending on the defined loglevel. Default is debug-level
     * 
     * @param string $sMessage
     * @param int $iLogLevel
     * @return void
     */
    public function lvLog( $sMessage, $iLogLevel=4 ) {
        $oUtils = oxRegistry::getUtils();
        
        if ( $iLogLevel <= $this->_iLvAmzPnLogLevel ) {
            $sPrefix        = "[".date( 'Y-m-d H:i:s' )."] ";
            $sFullMessage   = $sPrefix.$sMessage."\n";
            
            $oUtils->writeToLog( $sFullMessage, $this->_sLogFile );
        }
    }
    
    
    /**
     * Method returns an array with all relevant product information for given asin
     * 
     * @param string $sAsin
     * @return array
     */
    public function lvGetProductDetails( $sAsin, $sLangAbbr ) {
        return array();
        
        /**
         * Could not determine more detailed result set, so I guess I will drop functionality completely
         */
//        $this->_sCurrentAsin = $sAsin;
//        $aArticleData = array();
//        
//        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'details', $sLangAbbr );
//
//        
//        return $aArticleData;
    }
    
    
    /**
     * Method checks amazon platform information to match compatibility data array
     * 
     * @param string $sRawPlatform
     * @return string
     */
    protected function _lvGetPlatform( $sRawPlatform ) {
        $sReturn = '';
        $sRawPlatform = strtolower( $sRawPlatform );
        
        if ( strpos( $sRawPlatform, 'windows' ) !== false ) {
            $sReturn = 'WIN';
        }
        else if ( strpos( $sRawPlatform, 'mac' ) !== false  ) {
            $sReturn = 'MAC';
        }
        else if ( strpos( $sRawPlatform, 'linux' ) !== false  ) {
            $sReturn = 'LIN';
        }
        
        return $sReturn;
    }
    
    
    /**
     * Loads category mapping from CSV and puts it into an array attribute
     * 
     */
    protected function _lvLoadCategoryMapping() {
        $sMappingFilePath = getShopBasePath()."/modules/lv/lvAmazonPartnerNetConnector/config/category_mapping.csv";
        if ( file_exists( $sMappingFilePath ) ) {
            $resMappingFile = fopen( $sMappingFilePath, "r" );
            while ( ( $aData = fgetcsv( $resMappingFile, 1000, ";" ) ) !== false ) {
                $sAmazonCategory = $aData[0];
                $this->_aCategoryMapping[$sAmazonCategory]['category'] = $aData[1];
            }                        
        }
    }
    
    /**
     * Returns DRM Information if available via title
     * 
     * @param string $sTitle
     * @return string
     */
    protected function _lvFetchDRMInfoFromTitle( $sTitle ) {
        $sDrm = "";
        // check if we have a steam code
        if ( strpos( $sTitle, '[PC Steam Code]' ) !== false ) {
            $sDrm = "Steam";
        }
        else if ( strpos( $sTitle, '[PC/Mac Steam Code]' ) !== false ) {
            $sDrm = "Steam";
        }
        else if ( strpos( $sTitle, '[PC/Mac Online Code]' ) !== false ) {
            $sDrm = "Online-Account";
        }
        else if ( strpos( $sTitle, '[PC Code - Origin]' ) !== false ) {
            $sDrm = "Origin";
        }
        else if ( strpos( $sTitle, '[PC/Mac Origin Code]' ) !== false ) {
            $sDrm = "Origin";
        }
        else if ( strpos( $sTitle, '[PC Online Code]' ) !== false ) {
            $sDrm = "Online-Account";
        }
        else if ( strpos( $sTitle, '[PC Download]' ) !== false ) {
            $sDrm = "Online-Activation";
        }
        
        return $sDrm;
    }
    
    
    /**
     * Determine download type from DRM
     * 
     * @param string $sDrm
     * @return string
     */
    protected function _lvFetchDownloadTypeFromDRM( $sDrm ) {
        $sDownloadType = '';
        
        switch( $sDrm ) {
            case 'Steam':
                $sDownloadType = 'Steam Download Key';
                break;
            case 'Origin':
                $sDownloadType = 'Origin Download Key';
                break;
        }
        
        return $sDownloadType;
    }
    
    
    /**
     * Guesses from title if the download is an addon
     * 
     * @param string $sTitle
     * @return boolean
     */
    protected function _lvFetchAddonFromTitle( $sTitle ) {
        $blAddon = false;
        
        if ( stripos( $sTitle, 'Add-on' ) ) {
            $blAddon = true;
        }
        
        return $blAddon;
    }
    
    
    /**
     * Guesses from title if download is DLC
     * 
     * @param string $sTitle
     * @return boolean
     */
    protected function _lvFetchDLCFromTitle( $sTitle ) {
        $blDLC = false;
        
        if ( strpos( $sTitle, 'DLC' ) ) {
            $blDLC = true;
        }
                
        return $blDLC;
    }
    
    
    /**
     * Cleanup title from things in brackets
     * 
     * @param string $sTitle
     * @return boolean
     */
    protected function _lvCleanupAmazonTitle( $sTitle ) {
        $sReturnTitle = $sTitle;
        // check if cleanup needed 
        if ( strpos( $sTitle, "[" ) !== false ) {
            $aTitleParts = explode( "[", $sTitle );
            $sReturnTitle = trim( $aTitleParts[0] );
        }
        
        return $sReturnTitle;
    }


    /**
     * Method returns a well formed signed API Request for api request
     * 
     * @param void
     * @return string
     */
    protected function _lvGetSignedRequest( $sType, $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex = null ) {
        $oConfig = $this->getConfig();
        
        // get all configured values
        $sLvAmzPnAssociateTag           = $oConfig->getConfigParam( 'sLvAmzPnAssociateTag' );
        $sLvAmzPnAWSAccessKeyId         = $oConfig->getConfigParam( 'sLvAmzPnAWSAccessKeyId' );
        $sLvAmzPnAWSSecretKey           = $oConfig->getConfigParam( 'sLvAmzPnAWSSecretKey' );
        $aLvAmzPnAWSService2Lang        = $oConfig->getConfigParam( 'aLvAmzPnAWSService2Lang' );
        $aLvAmzPnBrowseNodes            = $oConfig->getConfigParam( 'aLvAmzPnBrowseNodes' );
        $aLvAmzPnPriceRanges            = $oConfig->getConfigParam( 'aLvAmzPnPriceRanges' );
        $sLvAmzPnSearchIndex            = $oConfig->getConfigParam( 'sLvAmzPnSearchIndex' );
        $sLvAmzPnCondition              = $oConfig->getConfigParam( 'sLvAmzPnCondition' );
        $sLvAmzPnSearchResponseGroups   = $oConfig->getConfigParam( 'sLvAmzPnSearchResponseGroups' );
        $sLvAmzPnLookupResponseGroups   = $oConfig->getConfigParam( 'sLvAmzPnLookupResponseGroups' );
        
        // choose language depending service url, browse node and price range lists
        $sAmazonWebService              = $aLvAmzPnAWSService2Lang[$sLangAbbr];
        $sBrowseNodes                   = $aLvAmzPnBrowseNodes[$sLangAbbr];
        
        //get current browse node to request
        $aBrowseNodes                   = explode( '|', $sBrowseNodes );
        $sTargetBrowseNode              = $aBrowseNodes[$iBrowseNodeIndex];

        if ( $iPriceRangeIndex !== null ) {
            $sPriceRanges                   = $aLvAmzPnPriceRanges[$sLangAbbr];
            //get current price range to request
            $aPriceRanges                   = explode( '|', $sPriceRanges );
            $sPriceRange                    = $aPriceRanges[$iPriceRangeIndex];
            $aPriceRange                    = explode( ':', $sPriceRange );
            $iMaximumPrice                  = (int)$aPriceRange[1];
            $iMinimumPrice                  = (int)$aPriceRange[0];
        }
        
        // build configuration array to fetch from
        $aConfigSetup = array(
            'AWSAccessKeyId'    => $this->_lvSpecialUrlEncode( $sLvAmzPnAWSAccessKeyId ),
            'AssociateTag'      => $this->_lvSpecialUrlEncode( $sLvAmzPnAssociateTag ),
            'BrowseNode'        => $this->_lvSpecialUrlEncode( $sTargetBrowseNode ),
            'Condition'         => $this->_lvSpecialUrlEncode( $sLvAmzPnCondition ),
            'ResponseGroup'     => '',
            'SearchIndex'       => $this->_lvSpecialUrlEncode( $sLvAmzPnSearchIndex ),
            'ItemId'            => $this->_lvSpecialUrlEncode( $this->_sCurrentAsin ),
            'Timestamp'         => $this->_lvSpecialUrlEncode( $this->_lvGetCurrentRequestTimestamp() ),
            'ItemPage'          => ( $this->_iCurrentPageNumber ) ? (string)$this->_iCurrentPageNumber : '1',
        );
        
        if ( $iPriceRangeIndex !== null ) {
            $aConfigSetup['MaximumPrice'] = $iMaximumPrice;
            $aConfigSetup['MinimumPrice'] = $iMinimumPrice;
        }

        // select request template by type
        switch( $sType ) {
            case 'search':
                $aTemplate                      = $this->_aLvRequestTemplateSearch;
                $aConfigSetup['ResponseGroup']  = $this->_lvSpecialUrlEncode( $sLvAmzPnSearchResponseGroups );
                break;
            case 'details':
                $aTemplate                      = $this->_aLvRequestTemplateDetails;
                $aConfigSetup['ResponseGroup']  = $this->_lvSpecialUrlEncode( $sLvAmzPnLookupResponseGroups );
                break;
        }
        
        if ( !isset( $aTemplate ) ) {
            $this->lvLog( "ERROR: Wrong request type" , 1 );
            return;
        }
        
        // fill up values in template with configuration
        foreach ( $aTemplate as $sOption=>$sValue ) {
            if ( empty( $sValue ) ) {
                $aTemplate[$sOption] = $aConfigSetup[$sOption];
            }
        }
        
        // preparing values by imploding things back to strings
        $iIndex = 0 ;
        $sCanonicalUrl="";
        foreach ( $aTemplate as $sOption=>$sValue ) {
            if ( $iIndex > 0 ) {
                $sCanonicalUrl .= "&";
            }
            $sCanonicalUrl .= $sOption."=".$sValue;
            $iIndex++;
        }
        $sSignHeader    = implode( "\n", $this->_aLvSignHeader ); 
        
        $sToBeSigned = $sSignHeader."\n".$sCanonicalUrl;
        
        // build signature
        $sRawSignature      = hash_hmac( "sha256", $sToBeSigned, $sLvAmzPnAWSSecretKey, true );
        $sRawBaseSignature  = base64_encode( $sRawSignature );
        $sSignature         = $this->_lvHashUrlEncode( $sRawBaseSignature );
        
        // building complete request
        $sSignedRequestUrl = "http://".$sAmazonWebService."/onca/xml?".$sCanonicalUrl."&Signature=".$sSignature;

        return $sSignedRequestUrl;
    }
    
    /**
     * Returns Amazon request timestamp in utc time
     * 
     * @param void
     * @return strin
     */
    protected function _lvGetCurrentRequestTimestamp() {
        date_default_timezone_set( "UTC" ); 
        $sDate      = date( 'Y-m-d' );
        $sTime      = date( 'H:i:s' );
        $sSuffix    = '.000Z';
        
        $sTimeStamp = $sDate."T".$sTime.$sSuffix;
        
        return $sTimeStamp;
    }
    
    
    /**
     * Url encodes colon and comma
     * 
     * @param string $sInString
     * @return string
     */
    protected function _lvSpecialUrlEncode( $sInString ) {
        $aTrans = array (
            ','=>urlencode( ',' ),
            ':'=>urlencode( ':' ),
        );

        $sOutString = strtr( $sInString, $aTrans );

        return $sOutString;
    }
    
    
    /**
     * Url encodes sha256 hash
     * 
     * @param string $sInString
     * @return string
     */
    protected function _lvHashUrlEncode( $sInString ) {
        $aTrans = array (
            '+'=>urlencode( '+' ),
            '='=>urlencode( '=' ),
        );

        $sOutString = strtr( $sInString, $aTrans );
        
        return $sOutString;
    }
    
    
    /**
     * Performs the REST Request with given well formed request url and returns simplexml object
     * 
     * @param string $sSignedRequestUrl
     * @return object
     */
    protected function _lvGetRequestResult( $sSignedRequestUrl ) {
        $resCurl = curl_init();
        
        // configuration
        curl_setopt_array( 
            $resCurl, 
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $sSignedRequestUrl,
            )
        );

        $sXmlResponse = false;
        try {
            $sXmlResponse = curl_exec( $resCurl );
        } 
        catch ( Exception $e ) {
            $this->lvLog( 'ERROR: Requesting signed url '.$sSignedRequestUrl.'ended up with the following error:'.$e->getMessage(), 1 );
        }
        curl_close( $resCurl );
        
        // process xml with simplexml
        $oResponse = null;
        if ( $sXmlResponse ) {
            $oResonse = new SimpleXMLElement( $sXmlResponse );
        }
        
        return $oResonse;
    }
    
}
