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
 * Description of lvyoutube
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvyoutube extends oxBase {
    
    /**
     * Database object
     * @var object
     */
    protected $_oLvDb = null;

    /**
     * Config object
     * @var object
     */
    protected $_oLvConfig = null;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvyoutube.log';
    
    /**
     * Initiate needed objects and values
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oLvConfig   = $this->getConfig();
        $this->_oLvDb       = oxDb::getDb( MODE_FETCH_ASSOC );
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
     * Returns an array with OXIDs of products that currently have no video assignment
     * 
     * @param void
     * @return array
     */
    public function lvGetProductsWithoutVideo( $sMediaType='productvideo' ) {
        $aReturn = array();
        
        $sQuery = "
            SELECT oa.OXID
            FROM 
                oxarticles oa 
            LEFT JOIN 
                ( SELECT * FROM oxmediaurls WHERE LVMEDIATYPE='".$sMediaType."' ) om ON ( oa.OXID=om.OXOBJECTID ) 
            WHERE 
                oa.OXPARENTID != '' AND 
                om.OXURL IS NULL
        ";
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid = $oRs->fields['OXID'];
                if ( $sOxid ) {
                    $aReturn[] = $sOxid;
                }
                $oRs->moveNext();
            }
        }
        
        return $aReturn;
    }
    
    
    /**
     * Method tries to fetch and add a youtube video to a certain product
     * 
     * @param string $sOxid
     * @return void
     */
    public function lvAddVideoForProduct( $sOxid ) {
        $aLvApiChannelIds                   = $this->_oLvConfig->getConfigParam( 'aLvApiChannelIds' );
        $blLvTitleCheck                     = $this->_oLvConfig->getConfigParam( 'blLvTitleCheck' );
        
        // channelid is optional. If empty fill with empty dummy value
        if ( !$aLvApiChannelIds || count( $aLvApiChannelIds ) ) {
            $aLvApiChannelIds = array('');
        }
        
        $blMatch = false;
        foreach ( $aLvApiChannelIds as $sChannelId ) {
            if ( $blMatch ) continue;
            
            $sRequestUrl    = $this->_lvGetRequestUrl( $sOxid, $sChannelId );
            $aResult        = $this->_lvGetRequestResult( $sRequestUrl );

            if ( count( $aResult ) > 0 ) {
                foreach ( $aResult['items'] as $aVideoInfo ) {
                    if ( $blMatch ) continue;
                    $sVideoId       = (string)$aVideoInfo['id']['videoId'];
                    $sVideoTitle    = $this->_lvGetNormalizedName( (string)$aVideoInfo['snippet']['title'] );
                    $sProductTitle  = $this->_lvGetProductTitle( $sOxid );
                    
                    if ( $blLvTitleCheck ) {
                        if ( stripos( $sVideoTitle, $sProductTitle ) !== false ) {
                            $blVideoTitleValid = true;
                        }
                        else {
                            $blVideoTitleValid = false;
                        }
                    }
                    else {
                        $blVideoTitleValid = true;
                    }
                    
                    if ( $sVideoId != '' && $blVideoTitleValid ) {
                        $this->_lvAddVideoUrlToProduct( $sOxid, $sVideoId, $sVideoTitle );
                        $blMatch = true;
                    }
                }
            }
        }
    }
    
    
    /**
     * Adding YouTube videoUrl to certain product
     * 
     * @param string $sOxid
     * @param string $sVideoId
     * @return void
     */
    protected function _lvAddVideoUrlToProduct( $sOxObjectId, $sVideoId, $sVideoTitle, $sMediaType='productvideo' ) {
        $oUtilsObject               = oxRegistry::get( 'oxUtilsObject' );
        $sNewId                     = $oUtilsObject->generateUId();
        $sLvApiBaseTargetAddress    = $this->_oLvConfig->getConfigParam( 'sLvApiBaseTargetAddress' );
        
        if ( $sLvApiBaseTargetAddress ) {
            $sYouTubeVideoUrl = $sLvApiBaseTargetAddress.$sVideoId;
            $sVideoTitle = $this->_oLvDb->quote( $sVideoTitle );
            
            $sQuery ="
                INSERT INTO oxmediaurls
                (
                    OXID,
                    OXOBJECTID,
                    OXURL,
                    OXDESC,
                    OXDESC_1,
                    OXDESC_2,
                    OXDESC_3,
                    OXISUPLOADED,
                    LVMEDIATYPE
                )
                VALUES
                (
                    '".$sNewId."',
                    '".$sOxObjectId."',
                    '".$sYouTubeVideoUrl."',
                    ".$sVideoTitle.",
                    ".$sVideoTitle.",
                    ".$sVideoTitle.",
                    ".$sVideoTitle.",
                    '0',
                    '".$sMediaType."'
                )
            ";
            
            $this->_oLvDb->Execute( $sQuery );
        }
    }
    
    
    /**
     * Returns youtube cleaned title of a product id
     * 
     * @param string $sOxid
     * @return string
     */
    protected function _lvGetProductTitle( $sOxid ) {
        $sQuery = "
            SELECT OXTITLE
            FROM 
                oxarticles
            WHERE 
                OXID = '".$sOxid."'
        ";
        
        $sTitle = $this->_oLvDb->GetOne( $sQuery );

        if ( !$sTitle ) {
            // try to fetch title from parent
            $sQuery = "
                SELECT OXPARENTID
                FROM 
                    oxarticles
                WHERE 
                    OXID = '".$sOxid."'
            ";
            $sParentOxid = $this->_oLvDb->GetOne( $sQuery );

            $sQuery = "
                SELECT OXTITLE
                FROM 
                    oxarticles
                WHERE 
                    OXID = '".$sParentOxid."'
            ";
            $sTitle = $this->_oLvDb->GetOne( $sQuery );
        }
        
        $aLvTitleRemove = $this->_oLvConfig->getConfigParam( 'aLvTitleRemove' );
        if ( $aLvTitleRemove && is_array($aLvTitleRemove) && count($aLvTitleRemove) > 0 ) {
            foreach ( $aLvTitleRemove as $sCurrentRemoval ) {
                $sTitle = str_replace( $sCurrentRemoval, "", $sTitle );
            }
        }
        
        $sTitle = $this->_lvGetNormalizedName( (string)$sTitle );
        
        return $sTitle;
    }
    
    
    /**
     * Method tries to normalize name so it can be better matched with existing articles
     * 
     * @param string $sTitleFromVendor
     * @return string
     */
    protected function _lvGetNormalizedName( $sTitleFromVendor ) {
        $sNormalizedTitle = str_replace( ":", "", $sTitleFromVendor );
        $sNormalizedTitle = str_replace( "-", "", $sNormalizedTitle );        
        $sNormalizedTitle = str_replace( "  ", " ", $sNormalizedTitle );        
        $sNormalizedTitle = $this->lvRoman2Arabic( $sNormalizedTitle );
        $sNormalizedTitle = str_replace( "®", "", $sNormalizedTitle );
        
        // general cleanup of hidden signs
        $sNormalizedTitle = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $sNormalizedTitle );
        
        return $sNormalizedTitle;
    }
    
    /**
     * Converts first 20 roman numbers to arabic numbers
     * 
     * @param string $sNormalizedTitle
     * @return string
     */
    public function lvRoman2Arabic( $sTitle ) {
        $aRomanNumbers2Arabic = array(
            'I'     =>'1',
            'II'    =>'2',
            'III'   =>'3',
            'IV'    =>'4',
            'VI'    =>'6',
            'VII'   =>'7',
            'VIII'  =>'8',
            'IX'    =>'9',
            'XI'    =>'11',
            'XII'   =>'12',
            'XIII'  =>'13',
            'XIV'   =>'14',
            'XV'    =>'15',
            'XVI'   =>'16',
            'XVII'  =>'17',
            'XVIII' =>'18',
            'XIX'   =>'19',
            'XX'    =>'20',
        );

        foreach ( $aRomanNumbers2Arabic as $sRomanNumber=>$sArabicNumber ) {
            if ( $this->lvContainsRoman( $sRomanNumber, $sTitle ) ) {
                $sTitle = $this->lvContainsRoman( $sRomanNumber, $sTitle, $sArabicNumber );
            }
        }
        
        return $sTitle;
    }
    
    /**
     * Has two functions. First just checks if certain roman number exists in title
     * Second one exchanges this number with given parameter
     * 
     * @param string $sRomanNumber
     * @param string $sTitle
     * @param string $sExchange
     * @return mixed bool/string
     */
    public function lvContainsRoman( $sRomanNumber, $sTitle, $sExchange=false ) {
        $mReturn = false;
        
        $blExchangeValid = ( $sExchange != false && !empty( $sExchange ) && is_numeric( $sExchange ) ); 
        
        $aTitleParts = explode( " ",  $sTitle );

        foreach ( $aTitleParts as $iIndex=>$sTitlePart ) {
            $sTitlePart = trim( $sTitlePart );
            if ( strlen( $sTitlePart ) == strlen( $sRomanNumber ) ) {
                if ( $sRomanNumber == $sTitlePart ) {
                    $mReturn = true;
                    if ( $blExchangeValid ) {
                        $aTitleParts[$iIndex] = $sExchange;
                    }
                }
            }
        }
        
        if ( $blExchangeValid && is_array( $aTitleParts ) && count( $aTitleParts ) > 0 ) {
            $mReturn = implode( " ", $aTitleParts );
        }
        
        return $mReturn;
    }

    
    /**
     * Returns request url based on article id and config params
     * 
     * @param string $sOxid
     * @return string
     */
    protected function _lvGetRequestUrl( $sOxid, $sLvApiChannelId, $sExtendId=null ) {
        $sRequestUrl = "";
        
        $sTitle = $this->_lvGetProductTitle( $sOxid );
        
        if ( $sTitle ) {
            // get configuration
            $sLvApiKey                          = $this->_oLvConfig->getConfigParam( 'sLvApiKey' );
            $sLvApiBaseRequestAddress           = $this->_oLvConfig->getConfigParam( 'sLvApiBaseRequestAddress' );
            $sLvApiRequestAction                = $this->_oLvConfig->getConfigParam( 'sLvApiRequestAction' );
            $sLvApiRequestPart                  = $this->_oLvConfig->getConfigParam( 'sLvApiRequestPart' );
            $sLvApiRequestMaxResults            = $this->_oLvConfig->getConfigParam( 'sLvApiRequestMaxResults' );
            $sLvApiRequestOrder                 = $this->_oLvConfig->getConfigParam( 'sLvApiRequestOrder' );
            $sLvApiRequestPrefix                = $this->_oLvConfig->getConfigParam( 'sLvApiRequestPrefix' );
            $sLvApiRequestSuffix                = $this->_oLvConfig->getConfigParam( 'sLvApiRequestSuffix' );
            
            $sRequestUrl     = $sLvApiBaseRequestAddress.$sLvApiRequestAction."?part=".$sLvApiRequestPart;
            if ( $sLvApiRequestMaxResults && $sLvApiRequestMaxResults != '' && is_numeric( $sLvApiRequestMaxResults ) ) {
                $sRequestUrl    .= "&maxResults=".trim( $sLvApiRequestMaxResults );
            }
            if ( $sLvApiRequestOrder && $sLvApiRequestOrder != '' ) {
                $sRequestUrl    .= "&order=".trim( $sLvApiRequestOrder );
            }
            if ( $sLvApiChannelId && $sLvApiChannelId != '' ) {
                $sRequestUrl    .= "&channelId=".$sLvApiChannelId;
            }
            
            // search title
            // first quote title so it will be surely found
            if ( $sLvApiRequestPrefix && $sLvApiRequestPrefix != '' ) {
                $sLvApiRequestPrefix = trim( $sLvApiRequestPrefix );
                $sTitle = $sLvApiRequestPrefix." ".$sTitle;
            }
            if ( $sLvApiRequestSuffix && $sLvApiRequestSuffix != '' ) {
                $sLvApiRequestSuffix = trim( $sLvApiRequestSuffix );
                $sTitle = $sTitle." ".$sLvApiRequestSuffix;
            }
            $sTitleUrlEncoded = urlencode( $sTitle );
            
            $sRequestUrl        .= "&q=".$sTitleUrlEncoded;
            
            $sRequestUrl        .= "&type=video&videoDefinition=high";
            
            $sRequestUrl        .= "&key=".$sLvApiKey;
        }
        
        return $sRequestUrl;
    }
    
    
    /**
     * Performs the REST Request with given well formed request url and returns an array
     * 
     * @param string $sRequestUrl
     * @return array
     */
    protected function _lvGetRequestResult( $sRequestUrl ) {
        $aResponse = array();
        $resCurl = curl_init();
        
        // configuration
        curl_setopt_array( 
            $resCurl, 
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $sRequestUrl,
            )
        );

        $sJsonResponse = false;
        try {
            $sJsonResponse = curl_exec( $resCurl );
        } 
        catch ( Exception $e ) {
            $this->lvLog( 'ERROR: Requesting url '.$sRequestUrl.'ended up with the following error:'.$e->getMessage(), 1 );
        }
        curl_close( $resCurl );
        // process json
        if ( $sJsonResponse ) {
            $aResponse = json_decode( $sJsonResponse, true );
        }
        
        return $aResponse;
    }
    
    
}
