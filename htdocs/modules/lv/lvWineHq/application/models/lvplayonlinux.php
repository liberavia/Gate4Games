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
     * Toggle yes by lang
     * @var type 
     */
    protected $_aLvToggleAttributeYesByLangAbbr = array(
        'de'    => 'Ja',
        'en'    => 'Yes',
    );
    
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
            $this->_lvFillScrapedAppsInDb( $aReturnApps );
        }
    }
    
    
    /**
     * Update product attributes with wine information
     * 
     * @param void
     * @return void
     */
    public function lvUpdateProductAttributes() {
        $sQuery = "SELECT OXID, LVAPPID, LVTITLE FROM ".$this->_sLvPOLTable;

        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid      = $oRs->fields['OXID'];
                $sLvAppId   = $oRs->fields['LVAPPID'];
                $sLvTitle   = $oRs->fields['LVTITLE'];
                
                $aArticleIds = $this->_lvGetArticleIdsByName( $sLvTitle );
                if ( count( $aArticleIds ) > 0 ) {
                    foreach ( $aArticleIds as $sArticleId ) {
                        $this->_lvAssignPOL( $sArticleId, $sLvAppId, $sLvTitle );
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
     * @param string $sTitle
     * @return void
     */
    protected function _lvAssignPOL( $sArticleId, $sAppId, $sTitle ) {
        $sLvPOLAttribute        = $this->_oLvConfig->getConfig( 'sLvPOLAttribute' );
        $sHtmlPOLDetailsLink    = $this->_lvGetHtmlPOLDetailsLink( $sAppId, $sTitle );
        $sAssignmentId          = $this->_lvGetExistingAssignmentId( $sArticleId, $sLvPOLAttribute );        
        
        if ( $sAssignmentId ) {
            $sQuery = "
                UPDATE oxobject2attribute
                SET 
                    OXVALUE='".$this->_aLvToggleAttributeYesByLangAbbr['de']."', 
                    OXVALUE_1='".$this->_aLvToggleAttributeYesByLangAbbr['en']."', 
                    LVATTRDESC=".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).", 
                    LVATTRDESC_1=".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).", 
                    LVATTRDESC_2=".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).", 
                    LVATTRDESC_3=".$this->_oLvDb->quote( $sHtmlPOLDetailsLink )."
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
                    '".$sLvPOLAttribute."',
                    '".$this->_aLvToggleAttributeYesByLangAbbr['de']."',
                    '0',
                    '".$this->_aLvToggleAttributeYesByLangAbbr['en']."',
                    '".$this->_aLvToggleAttributeYesByLangAbbr['en']."',
                    '".$this->_aLvToggleAttributeYesByLangAbbr['en']."',
                    NOW(),
                    ".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlPOLDetailsLink ).",
                    ".$this->_oLvDb->quote( $sHtmlPOLDetailsLink )."
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
    protected function _lvGetHtmlPOLDetailsLink( $sAppId, $sTitle ) {
        $sPolInstallLinkBase    = $this->_oLvConfig->getConfig( 'sLvPOLInstallLinkBase' );
        $sInstallText           = oxRegistry::getLang()->translateString( 'LVPOL_INSTALL_PROGRAM' );
        
        $sHtmlLink  = '<div class="lvPolInstallBox">';
        $sHtmlLink .= '     <a href="'.$sPolInstallLinkBase.$sAppId.'" class="lvPolInstallLink">';
        $sHtmlLink .= '         <img src="https://www.playonlinux.com/images/logos/logo48.png" alt="logo"/>'.$sInstallText;
        $sHtmlLink .= '     </a>';
        $sHtmlLink .= '</div>';
        $sHtmlLink .= '<div class="lvPolDetailsLinkBox">';
        $sHtmlLink .= '     <a class="lvPolDetailsLink" href="https://www.playonlinux.com/de/app-'.$sAppId.'-G4G.html" target="_blank">';
        $sHtmlLink .= '         PlayOnLinux: '.$sTitle;
        $sHtmlLink .= '     </a>';
        $sHtmlLink .= '</div>';
        
        
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
    protected function _lvFillScrapedAppsInDb( $aApps ) {
        foreach ( $aApps as $aApp ) {
            $sAppId = $aApp['id'];
            $sTitle = $aApp['title'];
            $sOxid  = $this->_lvGetExistingOxidByAppId( $sAppId );
            
            if ( $sOxid ) {
                $sQuery = "UPDATE ".$this->_sLvPOLTable." SET LVTITLE=".$this->_oLvDb->quote( $sTitle ).", LVLASTUPDATE=NOW() WHERE OXID='".$sOxid."' LIMIT 1";
            }
            else {
                $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
                $sNewId         = $oUtilsObject->generateUId();
                
                $sQuery = "
                    INSERT INTO ".$this->_sLvPOLTable."
                    (
                        OXID,
                        LVAPPID,
                        LVTITLE,
                        LVLASTUPDATE
                    )
                    VALUES
                    (
                        '".$sNewId."',
                        '".$sAppId."',
                        ".$this->_oLvDb->quote( $sTitle ).",
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
        $sQuery = "SELECT OXID FROM ".$this->_sLvPOLTable." WHERE LVAPPID='".$sAppId."' LIMIT 1";
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
       $aReturn = $aGameInfos = $aIdsAndTitles = array();
       
       preg_match_all( "/<a href='https:\/\/www.playonlinux.com\/de\/app-([0-9]*)-.*?'>(.*)<\/a>/", $sHtml, $aIdsAndTitles );
       
       $aIds    = $aIdsAndTitles[1];
       $aTitles = $aIdsAndTitles[2];
       
       foreach ( $aIds as $iIndex=>$sId ) {
           $sId     = trim( $sId );
           $sTitle  = trim($aTitles[$iIndex]);
           
           $aEntry = array( 'id'=>$sId, 'title'=>$sTitle );
           $aReturn[] = $aEntry;
       }
       
       return $aReturn;
    }
    
    
    /**
     * Performs a request on a webpage and delivers sourcecode back
     * 
     * @param string $sRequestUrl
     * @return array
     */
    protected function _lvGetRequestResult( $sRequestUrl ) {
        $sResponse = $this->_oAffiliateTools->lvGetRestRequestResult( true, $sRequestUrl, 'RAW'  );
        return $sResponse;
    }
    
}
