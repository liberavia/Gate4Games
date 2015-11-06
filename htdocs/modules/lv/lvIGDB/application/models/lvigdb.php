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
 * Description of lvigdb
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvigdb extends oxBase {
    
    /**
     * Counts requests so we won't exceed the limit defined in _iLvIgdbRequestMaxCount
     * @var int
     */
    protected $_iLvIgdbRequestCounter = 0;
    
    /**
     * Maximum count of requests allowed. We can abort operations if we exceed this limit
     * @var int
     */
    protected $_iLvIgdbRequestMaxCount = 10000;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvigdb.log';

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
     * Table name for IGDB-Operations
     * @var string
     */
    protected $_sLvIgdbTable = 'lvigdb';
    
    /**
     * Affiliate Tools from affiliate module
     * @var object
     */
    protected $_oAffiliateTools = null;
    
    /**
     * Flag that indicates if affiliate tools are available in this context
     * @var boolean
     */
    protected $_blAffiliateToolsAvailable = true;
    
    /**
     * List of articles that shall be requested
     * @var array
     */
    protected $_aAffectedArticles = array();
    
    /**
     * Configured days after Data should be refreshed
     * @var int
     */
    protected $_iLvIGDBRefreshDayRatio = null;
    
    /**
     * Configured auth token for requests
     * @var string
     */
    protected $_sLvIgdbApiAuthToken = '';

    /**
     * Configured attribute id for release date
     * @var string
     */
    protected $_sLvIGDBReleaseDateAttribute = '';

    /**
     * Base URL for API-Calls
     * @var string
     */
    protected $_sLvIgdbApiBaseUrl = 'https://www.igdb.com/api/v1/games/';
    
    /**
     * Base URL
     * @var string
     */
    protected $_sLvIgdbBaseUrl = 'https://www.igdb.com';

    /**
     * Base rating for articles that are for future release titles
     * @var double
     */
    protected $_dBaseRatingForUpcomingGames = 5.5;
    
    /**
     * Terms that are lousy to search for
     * @var array
     */
    protected $_aCleanupSearchTerms = array();
    
    
    

    /**
     * Init neeeded objects that will be used in this class
     */
    public function __construct() {
        $this->_oLvDb                       = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $this->_oLvConfig                   = $this->getConfig();
        $this->_iLvIGDBRefreshDayRatio      = (int)$this->_oLvConfig->getConfigParam( 'sLvIGDBRefreshDayRatio' );
        $this->_sLvIgdbApiAuthToken         = trim( $this->_oLvConfig->getConfigParam( 'sLvIGDBAuthToken' ) );
        $this->_aCleanupSearchTerms         = $this->_oLvConfig->getConfigParam( 'aLvIGDBCleanupTerms' );
        $this->_sLvIGDBReleaseDateAttribute = trim( $this->_oLvConfig->getConfigParam( 'sLvIGDBReleaseDateAttribute' ) );
        $this->_iLvIgdbRequestCounter       = 0;
        
        // try to get affiliate tools if available
        try {
            $this->_oAffiliateTools = oxNew( 'lvaffiliate_tools' );
        } 
        catch ( Exception $ex ) {
            // there are no affiliate tools which has been catched so we flag affiliate tools not available
            $this->_blAffiliateToolsAvailable = false;
        }
        
    }


    /**
     * Triggers start of igdb import data job
     * 
     * @param void
     * @return void
     */
    public function lvIgdbImportData() {
        $this->_lvSetAffectedArticles();
        $this->_lvRequestAndUpdateData();
    }
    
    
    /**
     * Method sets the list of articles that should be requested 
     * 
     * @param void
     * @return void
     */
    protected function _lvSetAffectedArticles() {
        $sArticlesTable = getViewName( 'oxarticles' );
        
        $iMinLastTimeStamp  = strtotime( "- ".$this->_iLvIGDBRefreshDayRatio." days" );
        $sMinLastDate       = date( 'Y-m-d', $iMinLastTimeStamp );
        
        $sQuery = "
            SELECT 
                OXID, 
                OXTITLE 
            FROM ".$sArticlesTable."
            WHERE
                OXPARENTID='' AND
                LVIGDB_LAST_UPDATED <= '".$sMinLastDate."'
            LIMIT ".$this->_iLvIgdbRequestMaxCount."
        ";
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sTitle = $oRs->fields['OXTITLE'];
                
                if ( $this->_blAffiliateToolsAvailable ) {
                    $sTitle = $this->_oAffiliateTools->lvGetNormalizedName( $sTitle );
                }
                
                $aArticleElement = array(
                    'OXID'  => $oRs->fields['OXID'],
                    'TITLE' => $sTitle,
                );
                
                $this->_aAffectedArticles[] = $aArticleElement;
                
                $oRs->moveNext();
            }
        }
    }
    

    /**
     * Update all information available
     * 
     * @param string $sLvIgdbId
     * @param string $sOxid
     * @param array $aGameDetails
     */
    protected function _lvIgdbUpdateDetails( $sLvIgdbId, $sOxid, $aGameDetails ) {
        $sSlug                  = $this->_oLvDb->quote( trim( $aGameDetails['slug'] ) );
        $sReleaseDate           = ( isset( $aGameDetails['release_date'] ) ) ? trim( $aGameDetails['release_date'] ) : '';
        $iReleaseTime           = strtotime( $sReleaseDate );
        $iNowTime               = time();
        $sSummary               = ( isset( $aGameDetails['summary'] ) ) ? trim( $aGameDetails['summary'] ): "";
        $sSummary               = $this->_oLvDb->quote( $sSummary );
        $sCover                 = ( isset( $aGameDetails['cover']['url'] ) ) ? trim( $aGameDetails['cover']['url'] ): "";;
        $sCover                 = str_replace( "_thumb.png", "_big.png", $sCover );
        $sCompanies             = ( isset( $aGameDetails['companies'] ) ) ? serialize( $aGameDetails['companies'] ): "";
        $sCompanies             = $this->_oLvDb->quote( $sCompanies );
        $sScreenshots           = ( isset( $aGameDetails['screenshots'] ) ) ? serialize( $aGameDetails['screenshots'] ): "";
        $sScreenshots           = $this->_oLvDb->quote( $sScreenshots );
        $sVideos                = ( isset( $aGameDetails['videos'] ) ) ? serialize( $aGameDetails['videos'] ): "";
        $sVideos                = $this->_oLvDb->quote( $sVideos );
        
        $dRating                = ( isset( $aGameDetails['rating'] ) ) ? round( (double)$aGameDetails['rating'], 2 ): 0.0;
        if ( $dRating == 0.0 && $iReleaseTime > $iNowTime ) {
            $dFakeRating            = $this->_dBaseRatingForUpcomingGames;
            if ( isset( $aGameDetails['release_dates'] ) ) {
                $dFakeRating        += (double)count( $aGameDetails['release_dates'] );
            }
        }
        
        $sTitle                 = trim( $aGameDetails['name'] );
        if ( $this->_blAffiliateToolsAvailable ) {
            $sTitle             = $this->_oAffiliateTools->lvGetNormalizedName( $sTitle );
        }
        $sTitle                 = $this->_oLvDb->quote( $sTitle );
        
        $sGenres                = "";
        if ( isset( $aGameDetails['genres'] ) ) {
            foreach ( $aGameDetails['genres'] as $iIndex=>$aGenre ) {
                if ( $iIndex > 0 ) {
                    $sGenres    .= ",";
                }
                $sGenres        .= $aGenre['name'];
            }
        }
        $sGenres                = $this->_oLvDb->quote( $sGenres );
        
        $sThemes                = "";
        if ( isset( $aGameDetails['themes'] ) ) {
            foreach ( $aGameDetails['themes'] as $iIndex=>$aTheme ) {
                if ( $iIndex > 0 ) {
                    $sThemes    .= ",";
                }
                $sThemes        .= $aGenre['name'];
            }
        }
        $sThemes                = $this->_oLvDb->quote( $sThemes );
        
        $sQuery = "
            UPDATE ".$this->_sLvIgdbTable." SET
                LVIGDB_NAME                 = ".$sTitle.",
                LVIGDB_SLUG                 = ".$sSlug.",
                LVIGDB_RATING               = '".$dRating."',
                LVIGDB_RELEASE_DATE         = '".$sReleaseDate."',       
                LVIGDB_SUMMARY              = ".$sSummary.",
                LVIGDB_THEMES               = ".$sThemes.",
                LVIGDB_COMPANIES            = ".$sCompanies.",
                LVIGDB_COVER                = '".$sCover."',
                LVIGDB_SCREENSHOTS          = ".$sScreenshots.",
                LVIGDB_VIDEOS               = ".$sVideos.",
                LVIGDB_LAST_UPDATED         = NOW()
            WHERE 
                LVIGDBID                    = '".$sLvIgdbId."'
        ";

        $this->_oLvDb->Execute( $sQuery );
        
        $dRelevanceRating = ( isset( $dFakeRating ) && $dFakeRating > 0 ) ? $dFakeRating: $dRating;
        $iRelevance = $this->_lvCalcRelevance( $dRelevanceRating, $sReleaseDate, $sOxid );
        
        // update oxarticles
        $sQuery = "
            UPDATE oxarticles SET
                LVIGDB_RATING               = '".$dRating."',
                LVIGDB_RELEVANCE            = '".(string)$iRelevance."',
                LVIGDB_RELEASE_DATE         = '".$sReleaseDate."',       
                LVIGDB_LAST_UPDATED         = NOW()
            WHERE 
                OXID = '".$sOxid."'
        ";
        
        $this->_oLvDb->Execute( $sQuery );
        
        // set release date for all variants if set
        if ( $this->_sLvIGDBReleaseDateAttribute && $sReleaseDate ) {
            $this->_lvSetReleaseDateAttribute( $sOxid, $sReleaseDate );
        }
        
        // check cover available of all variants an set this cover if not
        if ( $sCover ) {
            $this->_lvCheckAndSetMissingCover( $sOxid, $sCover );
        }
        
        // check and set summary for those variants missing it
        if ( $sSummary ) {
            $this->_lvCheckAndSetMissingSummary( $sOxid, $sSummary );
        }
        
        /**
         * @todo screenshots, videos, genres (inlcuding category assignments)
         */
    }
    
    
    /**
     * Set release date attribute to given value
     * 
     * @param string $sOxid
     * @param string $sReleaseDate
     * @return void
     */
    protected function _lvSetReleaseDateAttribute( $sOxid, $sReleaseDate ) {
        $aOxids = $this->_lvGetVariantIds( $sOxid );
        
        foreach ( $aOxids as $sCurrentOxid ) {
            $sDeleteQuery = "DELETE FROM oxobject2attribute WHERE OXOBJECTID='".$sCurrentOxid."' AND OXATTRID='".$this->_sLvIGDBReleaseDateAttribute."'";
            $this->_oLvDb->Execute( $sDeleteQuery );
            $sNewOxid = oxRegistry::get( 'oxUtilsObject' )->generateUId();
            
            $sInsertQuery = "
                INSERT INTO oxobject2attribute
                (
                    OXID,
                    OXOBJECTID,
                    OXATTRID,
                    OXVALUE
                    
                )
                VALUES
                (
                    '".$sNewOxid."',
                    '".$sCurrentOxid."',
                    '".$this->_sLvIGDBReleaseDateAttribute."',
                    '".$sReleaseDate."'
                )
            ";
            
            $this->_oLvDb->Execute( $sInsertQuery );
        }
    }
    
    
    /**
     * Checks if coverimages are available and sets them
     * 
     * @param string $sOxid
     * @param string $sCover
     * @return void
     */
    protected function _lvCheckAndSetMissingCover( $sOxid, $sCover ) {
        $sCover = $this->_sLvIgdbBaseUrl.$sCover;
        $aOxids = $this->_lvGetVariantIds( $sOxid );        
        
        foreach ( $aOxids as $sCurrentOxid ) {
            $sArticlesTable = getViewName( 'oxarticles' );
            $sQuery = "SELECT OXPIC1 FROM ".$sArticlesTable." WHERE OXID='".$sCurrentOxid."' LIMIT 1";
            
            $sPic = $this->_oLvDb->GetOne( $sQuery );
            
            $blReplace = false;
            if ( !$sPic || strtolower( $sPic != 'nopic.jpg' ) ) {
                $blReplace = true;
            }
            else if ( strpos( $sPic, 'http://' ) !== false &&  $this->_lvCheckIsAvailableUrl( $sPic ) == false ) {
                $blReplace = true;
            }            
            
            if ( strpos( $sPic, '/res.cloudinary.com' ) !== false && $this->_lvCheckIsAvailableUrl( $sPic ) == false ) {
                $sPic = str_replace( '/res.cloudinary.com/', 'http://res.cloudinary.com/', $sPic );
            }
            
            if ( $blReplace ) {
                $sQuery = "UPDATE oxarticles SET OXPIC1='".$sCover."' WHERE OXID='".$sCurrentOxid."' LIMIT 1";
                $this->_oLvDb->Execute( $sQuery );
            }
        }
    }
    
    
    /**
     * 
     * @param string $sOxid
     * @param string $sSummary
     * @return void
     */
    protected function _lvCheckAndSetMissingSummary( $sOxid, $sSummary ) {
        if ( strlen( $sSummary ) > 255 ) {
            $sSummary = substr( $sSummary, 0, 250 );
            $sSummary .= "...";
        }
        
        $aOxids = $this->_lvGetVariantIds( $sOxid );        
        
        foreach ( $aOxids as $sCurrentOxid ) {
            $sArticlesTable = getViewName( 'oxarticles' );
            $sQuery = "SELECT OXSHORTDESC FROM ".$sArticlesTable." WHERE OXID='".$sCurrentOxid."' LIMIT 1";
            
            $sShortDesc = $this->_oLvDb->GetOne( $sQuery );
            
            if ( !$sShortDesc || $sShortDesc == '' ) {
                $sQuery = "UPDATE oxarticles SET OXSHORTDESC=".$this->_oLvDb->quote( $sSummary )." WHERE OXID='".$sCurrentOxid."' LIMIT 1";
                $this->_oLvDb->Execute( $sQuery );
            }
        }
    }
    
    
    /**
     * Checks if an url exists
     * 
     * @param string $sUrl
     * @return boolean
     */
    protected function _lvCheckIsAvailableUrl( $sUrl ) {
        $aFileHeaders = @get_headers( $sUrl );

        if( $aFileHeaders[0] == 'HTTP/1.0 404 Not Found'){
            $blReturn = false;
        } 
        else if ( $$aFileHeaders[0] == 'HTTP/1.0 302 Found' && $aFileHeaders[7] == 'HTTP/1.0 404 Not Found' ) {
            $blReturn = false;
        } 
        else {
            $blReturn = true;
        }
        
        return $blReturn;
    }
            
    
    
    /**
     * Calculate relevance of title
     * 
     * @param double $dRating
     * @param string $sReleaseDate
     * @param string $sOxid
     */
    protected function _lvCalcRelevance( $dRating, $sReleaseDate, $sOxid ) {
        // the base of all is the user rating
        $dBaseValue         = $dRating * 100;
        
        // relevance bonus for amount of partners provide offers for title
        $iAmountOffers = $this->_lvGetAmountOffers( $sOxid );
        if ( $iAmountOffers > 0 ) {
            $dBaseValue     += ( $iAmountOffers * 10 );
        }
        
        // the older a title is the more it will lower the rating, so newer titles have the chance to compete
        $iTimeReleaseDate   = strtotime( $sReleaseDate );
        $iTimeNow           = time();
        $iTimeDelta         = $iTimeNow - $iTimeReleaseDate;
        
        // lets see if the release is upcoming which will increase the relevance the nearer the release is
        $blUpComingRelease = false;
        if ( $iTimeDelta < 0 ) {
            $blUpComingRelease = true;
        }
        
        // calculate weeks of it
        $iSeconds = abs( $iTimeDelta );
        $iWeeks   = round( ( $iSeconds/604800 ), 0 );
        
        if ( $blUpComingRelease && $dBaseValue > 0 && $iWeeks > 0 ) {
            // the shorter the release of the upcoming game is, the more it will be hyped here
            $iRelevance = (int)floor( $dBaseValue + ($dBaseValue/$iWeeks) );
        }
        else {
            // the more the game is in the past the more it will give a malus on the base value of rating
            $iRelevance = (int)floor( $dBaseValue - $iWeeks );
        }
        
        return $iRelevance;
    }
    
    
    /**
     * Method returns amount of available offers (variants) for given title
     * 
     * @param string $sOxid
     * @return int
     */
    protected function _lvGetAmountOffers( $sOxid ) {
        $sArticlesTable = getViewName( 'oxarticles' );
        $sQuery         = "SELECT count(*) FROM ".$sArticlesTable." WHERE OXPARENTID='".$sOxid."' AND OXACTIVE='1'";
        $iAmountOffers  = (int)$this->_oLvDb->GetOne( $sQuery );
        
        return $iAmountOffers;
    }

    /**
     * Method returns oxids of parent
     * 
     * @param string $sOxid
     * @return array
     */
    protected function _lvGetVariantIds( $sOxid ) {
        $sArticlesTable = getViewName( 'oxarticles' );
        $aOxids         = array();
        $sQuery         = "SELECT OXID FROM ".$sArticlesTable." WHERE OXPARENTID='".$sOxid."' AND OXACTIVE='1'";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) { 
            while ( !$oRs->EOF ) {
                $sOxid      = $oRs->fields['OXID'];
                $aOxids[]   = $sOxid;
                
                $oRs->moveNext();
            }
        }
        
        return $aOxids;
    }

    
    /**
     * Perfoms searches for the given articles and update its information, basic and details as well
     * 
     * @param void
     * @return void
     */
    protected function _lvRequestAndUpdateData() {
        foreach ( $this->_aAffectedArticles as $iIndex=>$aArticle ) {
            $sOxid          = $aArticle['OXID'];
            $sTitleToSearch = trim( $aArticle['TITLE'] );
            $aResponse =  $this->_lvRequestIgdbApi( 'search', $sTitleToSearch );
            $blMatch = false;
            if ( $aResponse && is_array( $aResponse ) && isset( $aResponse['games'] ) && count( $aResponse['games'] ) > 0 ) {
                foreach ( $aResponse['games'] as $aGame ) {
                    if ( $blMatch ) continue;
                    
                    if ( isset( $aGame['name'] ) && is_string( $aGame['name'] ) ) {
                        $iLvIgdbId      = (int)$aGame['id'];
                        $sTitleFound    = trim( $aGame['name'] );
                        
                        if ( $this->_blAffiliateToolsAvailable ) {
                            $sTitleFound    = $this->_oAffiliateTools->lvGetNormalizedName( $sTitleFound );
                            $aGame['name']  = $sTitleFound;
                        }
                        $sTitleToSearch = $this->_lvCleanSearchTerm( $sTitleToSearch );
                        if ( strtolower( $sTitleToSearch ) == strtolower( $sTitleFound ) ) {
                            $blMatch = true;
                            // add gameid to global array
                            $this->_aAffectedArticles[$iIndex]['LVIGDBID'] = $iLvIgdbId;
                            $this->_lvUpdateIgdbBasicInfo( $sOxid, $aGame );
                            $aDetailsResponse =  $this->_lvRequestIgdbApi( 'details', (string)$iLvIgdbId );
                            if ( $aDetailsResponse && is_array( $aDetailsResponse ) && isset( $aDetailsResponse['game'] ) && count( $aDetailsResponse['game'] ) > 0 ) {
                                $aGameDetails = $aDetailsResponse['game'];
                                $this->_lvIgdbUpdateDetails( (string)$iLvIgdbId, $sOxid, $aGameDetails );
                            }
                        }
                    }
                }
            }
            else if ( $aResponse && is_array( $aResponse ) && isset( $aResponse['error'] ) ) {
                exit( $aResponse['error']."\n" ); 
            }
            
            if ( !$blMatch ) {
                // if nothing has been found mark at least the last updated statement so it won't be requested over and over again
                $sQuery = "
                    UPDATE oxarticles SET
                        LVIGDB_LAST_UPDATED = NOW()
                    WHERE 
                        OXID = '".$sOxid."'
                    LIMIT 1
                ";
                $this->_oLvDb->Execute( $sQuery );
            }
        }
    }
    
    
    /**
     * Returns cleaned search term
     * 
     * @param string $sTitleToSearch
     * @return string
     */
    protected function _lvCleanSearchTerm( $sTitleToSearch ) {
        foreach ( $this->_aCleanupSearchTerms as $sTermToSearch ) {
            $sTitleToSearch = str_replace( $sTermToSearch, "", $sTitleToSearch );
            $sTitleToSearch = trim( $sTitleToSearch );
        }
        
        return $sTitleToSearch;
    }
    
    
    /**
     * Performs an update operation on the articles and lvigdb-table
     * 
     * @param string $sOxid
     * @param array $aGame
     * @return void
     */
    protected function _lvUpdateIgdbBasicInfo( $sOxid, $aGame ) {
        $sLvIgdbId          = (int)$aGame['id'];
        $sLvIgdbTitle       = $this->_oLvDb->quote( trim( $aGame['name'] ) );
        $sLvIgdbSlug        = $this->_oLvDb->quote( trim( $aGame['slug'] ) );
        $sLvIgdbReleaseDate = $aGame['release_date'];
        
        // first update oxarticles
        $sQuery = "
            UPDATE oxarticles SET
                LVIGDB_ID = '".$sLvIgdbId."',
                LVIGDB_RELEASE_DATE = '".$sLvIgdbReleaseDate."'
            WHERE 
                OXID = '".$sOxid."'
            LIMIT 1
        ";
        
        $this->_oLvDb->Execute( $sQuery );
        
        /**
         *  now add/update data in lvigdb table
         *  We won't touch the last updated statement due we gonna update this
         *  while entering details
         */
        $sQuery = "
            INSERT INTO ".$this->_sLvIgdbTable."
            (
                LVIGDBID,
                LVIGDB_NAME,
                LVIGDB_SLUG,
                LVIGDB_RELEASE_DATE
            )
            VALUES 
            (
                '".$sLvIgdbId."',
                ".$sLvIgdbTitle.",
                ".$sLvIgdbSlug.",
                '".$sLvIgdbReleaseDate."'
            )
            ON DUPLICATE KEY UPDATE  LVIGDB_SLUG = ".$sLvIgdbSlug."
        ";
        
        $this->_oLvDb->Execute( $sQuery );
    }
    
    
    /**
     * Performs a request of given type on the igdb api and returns result as array if max count request has
     * 
     * @param string $sSearchTerm
     * @return mixed array/boolean
     */
    protected function _lvRequestIgdbApi( $sType, $sRequestString='' ) {
        $blPerformRequest = true;
        
        $sRequest = $this->_sLvIgdbApiBaseUrl;
        if ( $sType == 'search' ) {
            $sRequest .= "search?q=".urlencode( $sRequestString );
        }
        else if ( $sType == 'details' && is_numeric( $sRequestString ) ) {
            $sRequest .= $sRequestString;
        }
        else {
            // unknown request type
            $mReturn = false;
            $blPerformRequest = false;
        }

        if ( $blPerformRequest && $this->_iLvIgdbRequestCounter <= $this->_iLvIgdbRequestMaxCount ) {
            $resCurl = curl_init( $sRequest );

            // set options
            curl_setopt($resCurl, CURLOPT_HTTPHEADER,       array('Accept: application/json','Authorization: Token token="'.$this->_sLvIgdbApiAuthToken.'"') );
            curl_setopt($resCurl, CURLOPT_RETURNTRANSFER,   true );


            $sResponse = curl_exec ( $resCurl );
            $aResponse = json_decode( $sResponse, true );
            
            $this->_iLvIgdbRequestCounter++;
            
            if ( $aResponse && is_array( $aResponse ) ) {
                $mReturn = $aResponse;
            }
            else {
                $mReturn = false;
            }
        }
        
        return $mReturn;    
    }
    
}
