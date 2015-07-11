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
 * Description of lvplayonlinux
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvplayonlinux extends oxBase {
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
    protected $_sLogFile = 'lvplayonlinux.log';
    
    /**
     * Table to put winehq data in
     * @var type 
     */
    protected $_sLvPOLTable = 'lvplayonlinux';
    
    /**
     * Affiliate Tools from affiliate module
     * @var object
     */
    protected $_oAffiliateTools = null;
    
    
    /**
     * Initiate needed objects and values
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oLvConfig       = $this->getConfig();
        $this->_oLvDb           = oxDb::getDb( MODE_FETCH_ASSOC );
        $this->_oAffiliateTools = oxNew( 'lvaffiliate_tools' );
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
     * Fill database list by configuration
     * 
     * @param void
     * @return void
     */
    public function lvFillLists() {
        $sLvPOLScrapeLink           = $this->_oLvConfig->getConfigParam( 'sLvPOLScrapeLink' );
        $sResponse                  = $this->_lvGetRequestResult( $sLvPOLScrapeLink );
        if ( $sResponse ) {
            $aReturnApps = $this->_lvGetScrapedResponse( $sResponse );
            // $this->_lvFillScrapedAppsInDb( $aReturnApps, $sRating );
        }
    }
    
    
    /**
     * Update product attributes with wine information
     * 
     * @param void
     * @return void
     */
    public function lvUpdateProductAttributes() {
        $sQuery = "SELECT OXID, LVAPPID, LVTITLE, LVRATING FROM ".$this->_sLvWineHqTable;

        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid      = $oRs->fields['OXID'];
                $sLvAppId   = $oRs->fields['LVAPPID'];
                $sLvTitle   = $oRs->fields['LVTITLE'];
                $sLvRating  = $oRs->fields['LVRATING'];
                
                $aArticleIds = $this->_lvGetArticleIdsByName( $sLvTitle );
                if ( count( $aArticleIds ) > 0 ) {
                    foreach ( $aArticleIds as $sArticleId ) {
                        $this->_lvAssignRating( $sArticleId, $sLvAppId, $sLvRating, $sLvTitle );
                    }
                }
                
                $oRs->moveNext();
            }
        }
    }
    
    
    
    /**
     * Assigns rating and link to certain article
     * 
     * @param string $sArticleId
     * @param string $sAppId
     * @param string $sRating
     * @param string $sTitle
     * @return void
     */
    protected function _lvAssignRating( $sArticleId, $sAppId, $sRating, $sTitle ) {
        $sLvWineRatingAttribute = $this->_oLvConfig->getConfigParam( 'sLvWineRatingAttribute' );
        $sAssignmentId          = $this->_lvGetExistingAssignmentId( $sArticleId, $sLvWineRatingAttribute );
        $sHtmlWineHqDetailsLink = $this->_lvGetHtmlWineHqDetailsLink( $sAppId, $sTitle );
        
        if ( $sAssignmentId ) {
            $sQuery = "
                UPDATE oxobject2attribute
                SET 
                    OXVALUE='".$sRating."', 
                    LVATTRDESC=".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).", 
                    LVATTRDESC_1=".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).", 
                    LVATTRDESC_2=".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).", 
                    LVATTRDESC_3=".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink )."
                WHERE 
                    OXID='".$sAssignmentId."'
                LIMIT 1
            ";
        }
        else {
            $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
            $sNewId         = $oUtilsObject->generateUId();
            
            $sQuery = "
                INSERT INTO oxobject2attribute
                (
                    OXID,
                    OXOBJECTID,
                    OXATTRID,
                    OXVALUE,
                    OXPOS,
                    OXVALUE_1,
                    OXVALUE_2,
                    OXVALUE_3,
                    OXTIMESTAMP,
                    LVATTRDESC,
                    LVATTRDESC_1,
                    LVATTRDESC_2,
                    LVATTRDESC_3
                )
                VALUES
                (
                    '".$sNewId."',
                    '".$sArticleId."',
                    '".$sLvWineRatingAttribute."',
                    '".$sRating."',
                    '0',
                    '".$sRating."',
                    '".$sRating."',
                    '".$sRating."',
                    NOW(),
                    ".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlWineHqDetailsLink )."
                )
            ";
        }
        
        $this->_oLvDb->Execute( $sQuery );
    }
    
    
    /**
     * Creates a html link that can be put into lvdesc for linking to details page
     * 
     * @param string $sAppId
     * @return string
     */
    protected function _lvGetHtmlWineHqDetailsLink( $sAppId, $sTitle ) {
        $sLvWineHqDetailsLinkBase = $this->_oLvConfig->getConfigParam( 'sLvWineHqDetailsLinkBase' );
        
        $sHtmlLink  = '<a href="'.$sLvWineHqDetailsLinkBase.$sAppId.'" target="_blank">';
        $sHtmlLink .= 'WineHQ: '.$sTitle;
        $sHtmlLink .= '</a>';
        
        return $sHtmlLink;
    }
    
    
    /**
     * Checks and returns id of possibly already existing attribute assignment
     * 
     * @param string $sArticleId
     * @param string $sLvWineRatingAttribute
     * @return mixed string/bool
     */
    protected function _lvGetExistingAssignmentId( $sArticleId, $sAttributeId ) {
        $sObject2AttributeTable = getViewName( 'oxobject2attribute' );
        
        $sQuery = "SELECT OXID FROM ".$sObject2AttributeTable." WHERE OXOBJECTID='".$sArticleId."' AND OXATTRID='".$sAttributeId."' LIMIT 1";
        
        $sAttributeAssignmentId = $this->_oLvDb->GetOne( $sQuery );
        
        return $sAttributeAssignmentId;
    }
    
    
    /**
     * Returns list of child article ids matching with given title
     * 
     * @param type $sTitle
     * @return array
     */
    protected function _lvGetArticleIdsByName( $sTitle ) {
        $aArticleIds    = array();
        $sArticleTable  = getViewName( 'oxarticles' );
        $sTitle         = $this->_oAffiliateTools->lvGetNormalizedName( $sTitle );
        
        $sQuery = "SELECT OXID FROM ".$sArticleTable." WHERE OXTITLE LIKE ".$this->_oLvDb->quote( $sTitle )." AND OXPARENTID != ''";

        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sArticleId = $oRs->fields['OXID'];
                if ( $sArticleId ) {
                    $aArticleIds[] = $sArticleId;
                }
                $oRs->moveNext();
            }
        }
        
        return $aArticleIds;
    }
    
    
    /**
     * Put scraped app data into database
     * 
     * @param array $aReturnApps
     * @param string $sRating
     */
    protected function _lvFillScrapedAppsInDb( $aApps, $sRating ) {
        foreach ( $aApps as $aApp ) {
            $sAppId = $aApp['id'];
            $sTitle = $aApp['title'];
            $sOxid  = $this->_lvGetExistingOxidByAppId( $sAppId );
            
            if ( $sOxid ) {
                $sQuery = "UPDATE ".$this->_sLvWineHqTable." SET LVTITLE=".$this->_oLvDb->quote( $sTitle ).", LVRATING='".$sRating."', LVLASTUPDATE=NOW() WHERE OXID='".$sOxid."' LIMIT 1";
            }
            else {
                $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
                $sNewId         = $oUtilsObject->generateUId();
                
                $sQuery = "
                    INSERT INTO ".$this->_sLvWineHqTable."
                    (
                        OXID,
                        LVAPPID,
                        LVTITLE,
                        LVRATING,
                        LVWINEVERSION,
                        LVLASTUPDATE
                    )
                    VALUES
                    (
                        '".$sNewId."',
                        '".$sAppId."',
                        ".$this->_oLvDb->quote( $sTitle ).",
                        '".$sRating."',
                        '',
                        NOW()
                    )
                ";
            }
            
            $this->_oLvDb->Execute( $sQuery );
        }
        
    }
    
    
    /**
     * Returns OXID of AppId if exists or false if not
     * 
     * @param type $sAppId
     * @return mixed string/bool
     */
    protected function _lvGetExistingOxidByAppId( $sAppId ) {
        $sQuery = "SELECT OXID FROM ".$this->_sLvWineHqTable." WHERE LVAPPID='".$sAppId."' LIMIT 1";
        $mOxid  = $this->_oLvDb->GetOne( $sQuery );

        return $mOxid;
    }
    
    /**
     * Returns needed information in a workable array format
     * 
     * @param string $sHtml
     * @return array
     */
    protected function _lvGetScrapedResponse( $sHtml ) {
       $aReturn = $aGameInfo = array();
       
       preg_match_all( "/<a href='https://www.playonlinux.com/de/app-([0-9])-.*?''><\/a>/", $sHtml, $aGameInfo );
       
print_r( $aGameInfo );       
die();
       return $aReturn;
        
        
//        $aReturn = $aGameTableRows0 = $aGameTableRows1 = array();
//        
//        preg_match_all( '/<tr class="color0".*?<\/tr>/', $sHtml, $aGameTableRows0 );
//        preg_match_all( '/<tr class="color1".*?<\/tr>/', $sHtml, $aGameTableRows1 );
//
//        $aGameTableRows = array_merge( $aGameTableRows0[0], $aGameTableRows1[0] );
//        
//        // parse game table rows 0
//        foreach ( $aGameTableRows as $sCurrentHtmlTableRow ) {
//            preg_match_all( '/<td.*?<\/td>/', $sCurrentHtmlTableRow, $aCurrentTableData );
//            
//            // first get the id
//            $sAppId = str_replace( "<td>", "", $aCurrentTableData[0][1] );
//            $sAppId = trim( str_replace( "</td>", "", $sAppId ) );
//            
//            // next fetch the title
//            preg_match_all( '/<a.*>(.+)<\/a>/', $aCurrentTableData[0][0], $aTitleData );
//            $sAppTitle = trim( $aTitleData[1][0] );
//            
//            $aFinalRowData = array(
//                'id'=>$sAppId,
//                'title'=>$sAppTitle,
//            );
//            
//            $aReturn[] = $aFinalRowData;
//        }
//    
//        return $aReturn;
    }
    
    
    /**
     * Performs a request on a webpage and delivers sourcecode back
     * 
     * @param string $sRequestUrl
     * @return array
     */
    protected function _lvGetRequestResult( $sRequestUrl ) {
        return $this->_oAffiliateTools->lvGetRestRequestResult( true, $sRequestUrl, 'RAW'  );
    }
    
}
