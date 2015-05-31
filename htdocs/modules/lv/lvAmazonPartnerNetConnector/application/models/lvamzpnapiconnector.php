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
     * Constructor adds configuration of logging
     */
    public function __construct() {
        $oConfig = $this->getConfig();
        $this->_blLvAmzPnLogActive = $oConfig->getConfigParam( 'blLvAmzPnLogActive' );
        $sLogLevel = $oConfig->getConfigParam( 'sLvAmzPnLogLevel' );
        if ( $sLogLevel ) {
            $this->_iLvAmzPnLogLevel = (int)$sLogLevel;
        }
        parent::__construct();
    }


    /**
     * Method returns the amount of pages of the browse node based search request
     * 
     * @param void
     * @return int
     */
    public function lvGetSearchPageAmount() {
        $iPageAmount = 0;
        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'search' );
        
        if ( $sSignedRequestUrl ) {
            $oResponse = $this->_lvGetRequestResult( $sSignedRequestUrl );
            if ( isset( $oResponse->Items->TotalPages ) ) {
                $iPageAmount = (int)$oResponse->Items->TotalPages;
            }
        }
        
        return $iPageAmount;
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
    public function lvGetItemSearchAsinDetails( $iPageNumber = null ) {
        $aArticleData = array();
        $this->_iCurrentPageNumber = $iPageNumber;
        
        $sSignedRequestUrl = $this->_lvGetSignedRequest( 'search' );
        
        if ( $sSignedRequestUrl ) {
            $oResponse = $this->_lvGetRequestResult( $sSignedRequestUrl );
            foreach ( $oResponse->Items->{Item} as $oItem ) {
                $sAsin = (string)$oItem->ASIN;
                $aArticleData[$sAsin]['ARTNUM'] = $sAsin;
                
                // fetching additional information from title
                $sTitle = (string)$oItem->ItemAttributes->Title;
                $sDRMInfo = $this->_lvFetchDRMInfoFromTitle( $sTitle );
                if ( $sDRMInfo ) {
                    $aArticleData[$sAsin]['DRM'] = $sDRMInfo;
                }
                $sTitle = $this->_lvCleanupAmazonTitle( $sTitle );
                $aArticleData[$sAsin]['TITLE'] = $sTitle;
                
                $aArticleData[$sAsin]['EXTURL'] = (string)$oItem->DetailPageURL;
                $aArticleData[$sAsin]['COVERIMAGE'] = (string)$oItem->LargeImage->URL;
                // go through image sets
                $iIndex = 1;
                foreach ( $oItem->ImageSets->{ImageSet} as $oImageSet ) {
                    $aArticleData[$sAsin]['PIC'.$iIndex] = (string)$oImageSet->LargeImage->URL;
                    $iIndex++;
                }
                $aArticleData[$sAsin]['MANUFACTURER'] = (string)$oItem->ItemAttributes->Manufacturer;
                $aArticleData[$sAsin]['GENRE'] = (string)$oItem->ItemAttributes->Genre;
                $aArticleData[$sAsin]['RELEASE'] = (string)$oItem->ItemAttributes->ReleaseDate;
                // fetching language information
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
                    /**
                     * @todo: There might be more to come it seems that language data is made for german market
                     */
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
                
                
                
            }
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
            $sFullMessage   = $sPrefix.$sMessage;
            
            $oUtils->writeToLog($sFullMessage, $this->_sLogFile );
        }
    }
    
    
    /**
     * Method returns an array with all relevant product information for given asin
     * 
     * @param string $sAsin
     * @return array
     */
    public function lvGetProductDetails( $sAsin ) {
        
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
        else if ( strpos( $sTitle, '[PC Online Code]' ) !== false ) {
            $sDrm = "Online-Account";
        }
        
        return $sDrm;
    }
    
    
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
    protected function _lvGetSignedRequest( $sType ) {
        $oConfig = $this->getConfig();
        
        // get all configured values
        $sLvAmzPnAssociateTag           = $oConfig->getConfigParam( 'sLvAmzPnAssociateTag' );
        $sLvAmzPnAWSAccessKeyId         = $oConfig->getConfigParam( 'sLvAmzPnAWSAccessKeyId' );
        $sLvAmzPnAWSSecretKey           = $oConfig->getConfigParam( 'sLvAmzPnAWSSecretKey' );
        $sLvAmzPnBrowseNode             = $oConfig->getConfigParam( 'sLvAmzPnBrowseNode' );
        $sLvAmzPnSearchIndex            = $oConfig->getConfigParam( 'sLvAmzPnSearchIndex' );
        $sLvAmzPnCondition              = $oConfig->getConfigParam( 'sLvAmzPnCondition' );
        $sLvAmzPnSearchResponseGroups   = $oConfig->getConfigParam( 'sLvAmzPnSearchResponseGroups' );
        $sLvAmzPnLookupResponseGroups   = $oConfig->getConfigParam( 'sLvAmzPnLookupResponseGroups' );
        
        // build configuration array to fetch from
        $aConfigSetup = array(
            'AWSAccessKeyId'    => $this->_lvSpecialUrlEncode( $sLvAmzPnAWSAccessKeyId ),
            'AssociateTag'      => $this->_lvSpecialUrlEncode( $sLvAmzPnAssociateTag ),
            'BrowseNode'        => $this->_lvSpecialUrlEncode( $sLvAmzPnBrowseNode ),
            'Condition'         => $this->_lvSpecialUrlEncode( $sLvAmzPnCondition ),
            'ResponseGroup'     => '',
            'SearchIndex'       => $this->_lvSpecialUrlEncode( $sLvAmzPnSearchIndex ),
            'ItemId'            => $this->_lvSpecialUrlEncode( $this->_sCurrentAsin ),
            'Timestamp'         => $this->_lvSpecialUrlEncode( $this->_lvGetCurrentRequestTimestamp() ),
            'ItemPage'          => ( $this->_iCurrentPageNumber ) ? (string)$this->_iCurrentPageNumber : '1',
        );

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
        /**
         * @todo There should be flexible ways to create multilang functionality. Currently this only Requests Amazon.de
         */
        $sSignedRequestUrl = "http://webservices.amazon.de/onca/xml?".$sCanonicalUrl."&Signature=".$sSignature;

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
        
        $sXmlResponse = curl_exec( $resCurl );
        curl_close( $resCurl );
        
        // process xml with simplexml
        $oResponse = null;
        if ( $sXmlResponse ) {
            $oResonse = new SimpleXMLElement( $sXmlResponse );
        }
        else {
            /**
             * @todo some error handling
             */
        }
        
        return $oResonse;
    }
    
}
