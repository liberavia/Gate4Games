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
 * Description of lvyoutube_reviews
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvyoutube_reviews extends lvyoutube_reviews_parent {
    
    
    
    /**
     * Method tries to fetch and add a youtube game review to a certain product
     * 
     * @param string $sOxid
     * @return void
     */
    public function lvAddVideoReviewForProduct( $sOxid ) {
        $sRequestUrl    = $this->_lvGetRequestUrl( $sOxid, 'productreview' );
        $aResult        = $this->_lvGetRequestResult( $sRequestUrl );
        
        if ( count( $aResult ) > 0 ) {
            foreach ( $aResult['items'] as $aVideoInfo ) {
                $sVideoId       = (string)$aVideoInfo['id']['videoId'];
                $sVideoTitle    = (string)$aVideoInfo['snippet']['title'];
                
                if ( $sVideoId != '' ) {
                    $this->_lvAddVideoUrlToProduct( $sOxid, $sVideoId, $sVideoTitle, 'productreview' );
                }
            }
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
        
        if ( $sExtendId == 'productreview' ) {
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
                $sLvApiRequestPart                  = $this->_oLvConfig->getConfigParam( 'sLvApiRequestPartRev' );
                $sLvApiRequestMaxResults            = $this->_oLvConfig->getConfigParam( 'sLvApiRequestMaxResultsRev' );
                $sLvApiRequestOrder                 = $this->_oLvConfig->getConfigParam( 'sLvApiRequestOrderRev' );
                $sLvApiRequestPrefix                = $this->_oLvConfig->getConfigParam( 'sLvApiRequestPrefixRev' );
                $sLvApiRequestSuffix                = $this->_oLvConfig->getConfigParam( 'sLvApiRequestSuffixRev' );
                $sLvApiChannelId                    = $this->_oLvConfig->getConfigParam( 'sLvApiChannelIdRev' );

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
            
        }
        else {
            $sRequestUrl = parent::_lvRequestUrl( $sOxid );
        }
        
        return $sRequestUrl;
    }
    
    
}
