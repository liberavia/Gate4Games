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
    public function lvGetProductsWithoutVideo() {
        $aReturn = array();
        
        $sQuery = "
            SELECT oa.OXID
            FROM 
                oxarticles oa 
            LEFT JOIN 
                oxmediaurls om ON ( oa.OXID=om.OXOBJECTID ) 
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
        $sRequestUrl    = $this->_lvGetRequestUrl( $sOxid );
        $aResult        = $this->_lvGetRequestResult( $sRequestUrl );
        
        if ( count( $aResult ) > 0 ) {
            foreach ( $aResult['items'] as $aVideoInfo ) {
                $sVideoId       = (string)$aVideoInfo['id']['videoId'];
                $sVideoTitle    = (string)$aVideoInfo['snippet']['title'];
                
                if ( $sVideoId != '' ) {
                    $this->_lvAddVideoUrlToProduct( $sOxid, $sVideoId, $sVideoTitle );
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
    protected function _lvAddVideoUrlToProduct( $sOxObjectId, $sVideoId, $sVideoTitle ) {
        $oUtilsObject               = oxRegistry::get( 'oxUtilsObject' );
        $sNewId                     = $oUtilsObject->generateUId();
        $sLvApiBaseTargetAddress    = $this->_oLvConfig->getConfigParam( 'sLvApiBaseTargetAddress' );
        
        if ( $sLvApiBaseTargetAddress ) {
            $sYouTubeVideoUrl = $sLvApiBaseTargetAddress.$sVideoId;
            $sVideoTitle = $this->_oLvDb->quote( $sYouTubeVideoUrl );
            
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
                    OXISUPLOADED
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
                    '0'
                )
            ";
            
            $this->_oLvDb->Execute( $sQuery );
        }
    }
    
    
    /**
     * Returns request url based on article id and config params
     * 
     * @param string $sOxid
     * @return string
     */
    protected function _lvGetRequestUrl( $sOxid, $sExtendId=null ) {
        $sRequestUrl = "";
        
        $sQuery = "
            SELECT OXTITLE
            FROM 
                oxarticles
            WHERE 
                OXID = '".$sOxid."'
        ";
        
        $sTitle = $this->_oLvDb->GetOne( $sQuery );
        
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
            $sLvApiChannelId                    = $this->_oLvConfig->getConfigParam( 'sLvApiChannelId' );
            
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
            $sTitle = '"'.$sTitle.'"';
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
