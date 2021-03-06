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
 * Description of lvpegi
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvpegi extends oxBase {
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
    protected $_sLogFile = 'lvpegi.log';
    
    /**
     *
     * @var type 
     */
    protected $_sLvPegiTable = 'lvpegi';
    
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
     * Trigger to start importing initial csv file from configured path
     * 
     * @param void
     * @return void
     */
    public function lvInitialImportFromCsvFile() {
        $sImportFile    = $this->_oLvConfig->getConfigParam( 'sLvPegiInitImportFile' );
        $sImportFolder  = $this->_oLvConfig->getConfigParam( 'sLvPegiInitImportFolder' );
        
        $sImportPath = getShopBasePath().$sImportFolder.$sImportFile;
        if ( file_exists( $sImportPath ) ) {
            $resCsvFile = fopen( $sImportPath, 'r' );
            
            $iIndex = 0;
            while ( ( $aData = fgetcsv( $resCsvFile, 1000, ';' ) ) != false ) {
                if ( $iIndex == 0 ) {
                    $iIndex++;
                    continue;
                }
                $this->_lvImportInitData( $aData );
                $iIndex++;
            }
            fclose( $resCsvFile );
        }
        
        $this->_lvAssignProducts2Pegi();
    }

    
    /**
     * Trigger for importing latest game information from confuigured values
     * 
     * @param void
     * @return void
     */
    public function lvImportNew() {
        $iLvPegiRequestPastMonths   = (int)$this->_oLvConfig->getConfigParam( 'sLvPegiRequestPastMonths' );
        $iCurrentMonth              = date( 'n' );
        $sCurrentYear               = date( 'Y' );
        $iMaxPastMonth              = $iCurrentMonth-$iLvPegiRequestPastMonths-1;
        
        $aRequestUris = array(
            $sCurrentYear.'/monthly/latest.xml',
        );
        
        
        if ( $iMaxPastMonth <= 0 ) {
            $iMaxPastMonth= 1;
        }


        for ( $iProcessMonth = $iCurrentMonth-1; $iProcessMonth > $iMaxPastMonth; $iProcessMonth-- ) {
            $sProcessMonth = str_pad( $iProcessMonth, 2,'0', STR_PAD_LEFT );
            $aRequestUris[] = $sCurrentYear.'/monthly/'.$sProcessMonth.".xml";
        }
        
        $this->_lvPerformRequests( $aRequestUris );
        $this->_lvAssignProducts2Pegi();
    }
    
    
    /**
     * Performs requests on PEGI export xmls
     * 
     * @param array $aRequestUris
     * @return void
     */
    protected function _lvPerformRequests( $aRequestUris ) {
        $sLvPegiRequestBase = $this->_oLvConfig->getConfigParam( 'sLvPegiRequestBase' );
        
        foreach ( $aRequestUris as $sRequestUri ) {
            $sRequestUrl = $sLvPegiRequestBase.$sRequestUri;
            $oResponse = $this->_lvGetRequestResult( $sRequestUrl );
            if ( $oResponse ) {
                $this->_lvHandleResponse( $oResponse );
            }
        }
    }
    
    
    /**
     * Handles XML response
     * 
     * @param simplexml $oRespone
     * @return void
     */
    protected function _lvHandleResponse( $oRespone ) {
        foreach ( $oRespone->item as $oItem ) {
            $sUrn           = (string)$oItem->urn;
            $sTitle         = trim( (string)$oItem->title );
            $sAgeCategory   = (string)$oItem->ageCategory;
            $sPlatform      = (string)$oItem->platform;

            if ( strtoupper( trim( $sPlatform ) ) == 'PC' ) {
                $blTitleExists = $this->_lvCheckTitleExists( $sTitle );
                if ( !$blTitleExists ) {
                    $aData = array(
                        'urn'           =>$sUrn,
                        'title'         =>$sTitle,
                        'ageCategory'   =>$sAgeCategory,
                        'platform'      =>$sPlatform,
                    );
                    $this->_lvImportNewData( $aData );
                }
            }
        }
    }
    
    
    /**
     * Adds xml fetched data into pegi table
     * 
     * @param array $aData
     * @return void
     */
    protected function _lvImportNewData( $aData ) {
            $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
            $sNewId         = $oUtilsObject->generateUId();

            $sQuery = "
                INSERT INTO ".$this->_sLvPegiTable."
                (
                    OXID,
                    OXOBJECTID,
                    LVURN,
                    LVGAMETITLE,
                    LVPLATFORM,
                    LVBASEAGECATEGORY
                )
                VALUES
                (
                    '".$sNewId."',
                    '',
                    '".$aData['urn']."',
                    ".$this->_oLvDb->quote( $aData['title'] ).",
                    '".$aData['platform']."',
                    '".$aData['ageCategory']."'
                )
            ";
            
            $this->_oLvDb->Execute( $sQuery );
    }


    /**
     * Performs the REST Request  and returns simplexml object
     * 
     * @param string $sRequestUrl
     * @return object
     */
    protected function _lvGetRequestResult( $sRequestUrl ) {
        $resCurl = curl_init();
        
        // configuration
        curl_setopt_array( 
            $resCurl, 
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $sRequestUrl,
            )
        );

        $sXmlResponse = false;
        try {
            $sXmlResponse = curl_exec( $resCurl );
        } 
        catch ( Exception $e ) {
            $this->lvLog( 'ERROR: Requesting signed url '.$sRequestUrl.'ended up with the following error:'.$e->getMessage(), 1 );
        }
        curl_close( $resCurl );
        
        // process xml with simplexml
        $oResponse = null;
        if ( $sXmlResponse ) {
            $oResonse = new SimpleXMLElement( $sXmlResponse );
        }
        
        return $oResonse;
    }

    
    /**
     * Assigning data to existing products
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignProducts2Pegi() {
        $sQuery = "SELECT OXID, LVGAMETITLE, LVBASEAGECATEGORY FROM lvpegi WHERE OXOBJECTID=''";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid = $oRs->fields['OXID'];
                $sLvGameTitle = trim( $oRs->fields['LVGAMETITLE'] );
                $iLvBaseAgeCategory = (int)trim( $oRs->fields['LVBASEAGECATEGORY'] );
                
                $aObjectIds = $this->_lvFetchArticleIdsByTitle( $sLvGameTitle );
                
                if ( count($aObjectIds) > 0 ) {
                    $sAssignObjectId = $aObjectIds[0];
                    $sQuery = "UPDATE ".$this->_sLvPegiTable." SET OXOBJECTID='".$sAssignObjectId."' WHERE LVGAMETITLE=".$this->_oLvDb->quote( $sLvGameTitle )."";
                    $oRsUpdate = $this->_oLvDb->Execute( $sQuery );
                    
                    // assign attributes
                    $sAttributeId = $this->_oLvConfig->getConfigParam( 'sLvPegiAttributeId' );
                    foreach ( $aObjectIds as $sOxid ) {
                        
                        $blAssignmentExists = $this->_lvCheckAttributeAssignmentExists( $sOxid, $sAttributeId );
                        
                        if ( !$blAssignmentExists ) {
                            $oUtilsObject           = oxRegistry::get( 'oxUtilsObject' );
                            $sNewId                 = $oUtilsObject->generateUId();
                            
                            $sQuery = "
                                INSERT INTO oxobject2attribute
                                (
                                    OXID,
                                    OXOBJECTID,
                                    OXATTRID,
                                    OXPOS,
                                    OXVALUE,
                                    OXVALUE_1,
                                    OXVALUE_2,
                                    OXVALUE_3
                                )
                                VALUES
                                (
                                    '".$sNewId."',
                                    '".$sOxid."',
                                    '".$sAttributeId."',
                                    '9999',
                                    '".(string)$iLvBaseAgeCategory."',
                                    '".(string)$iLvBaseAgeCategory."',
                                    '".(string)$iLvBaseAgeCategory."',
                                    '".(string)$iLvBaseAgeCategory."'
                                )
                            ";
                            
                            $this->_oLvDb->Execute( $sQuery );
                        }
                    }
                }
                
                
                $oRs->moveNext();
            }
        }
    }
    
    
    /**
     * Checks if attribute assignment already exists
     * 
     * @param string $sOxid
     * @param string $sAttributeId
     * @return bool
     */
    protected function _lvCheckAttributeAssignmentExists( $sOxid, $sAttributeId ) {
        $sObject2AttributeTable = getViewName( 'oxobject2attribute' );
        $sQuery = "SELECT OXID FROM ".$sObject2AttributeTable." WHERE OXOBJECTID='".$sOxid."' AND OXATTRID='".$sAttributeId."'";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $blReturn = true;
        }
        else {
            $blReturn = false;
        }
        
        return $blReturn;
    }
    
    
    /**
     * Checks product for title already exists and returns its list of oxids
     * 
     * @param string $sLvGameTitle
     * @return array
     */
    protected function _lvFetchArticleIdsByTitle( $sLvGameTitle ) {
        $aIds = array();
        $sArticleTable = getViewName( 'oxarticles' );
        
        $sQuery = "SELECT OXID FROM ".$sArticleTable." WHERE OXPARENTID!='' AND LVMASTERVARIANT='1' AND OXTITLE=".$this->_oLvDb->quote( $sLvGameTitle )."";

        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid = $oRs->fields['OXID'];
                $aIds[] = $sOxid;
                $oRs->moveNext();
            }
        }
        
        return $aIds;
    }
    
    
    /**
     * Importing a csv row into database, if row hasn't been imported yet
     * 
     * @param array $aData
     * @return void
     */
    protected function _lvImportInitData( $aData ) {
        if ( is_array( $aData ) && count( $aData ) == 15 ) {
            $sLvGameTitle       = $aData[0];
            $sLvReleaseDate     = $aData[1];
            $sLvWebAddress      = $aData[2];
            $sLvPlatform        = $aData[3];
            $sLvGamesPublisher  = $aData[4];
            $sLvBaseAgeCategory = $aData[5];
            $sLvViolence        = $aData[6];
            $sLvSex             = $aData[7];
            $sLvDrugs           = $aData[8];
            $sLvFear            = $aData[9];
            $sLvDiscrimination  = $aData[10];
            $sLvBadLanguage     = $aData[11];
            $sLvGambling        = $aData[12];
            $sLvOnlineGameplay  = $aData[13];
            $sLvHorror          = $aData[14];
            
            if ( !empty( $sLvGameTitle ) &&  strtoupper( trim( $sLvPlatform ) ) == 'PC' ) {
                $blTitleExists = $this->_lvCheckTitleExists( $sLvGameTitle );
                if ( !$blTitleExists ) {
                    $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
                    $sNewId         = $oUtilsObject->generateUId();
                    
                    $sQuery = "
                        INSERT INTO ".$this->_sLvPegiTable."
                        (
                            OXID,
                            OXOBJECTID,
                            LVURN,
                            LVGAMETITLE,
                            LVRELEASEDATE,
                            LVWEBADDRESS,
                            LVPLATFORM,
                            LVGAMESPUBLISHER,
                            LVBASEAGECATEGORY,
                            LVVIOLENCE,
                            LVSEX,
                            LVDRUGS,
                            LVFEAR,
                            LVDISCRIMINATION,
                            LVBADLANGUAGE,
                            LVGAMBLING,
                            LVONLINEGAMEPLAY,
                            LVHORROR
                        )
                        VALUES
                        (
                            '".$sNewId."',
                            '',
                            '',
                            ".$this->_oLvDb->quote($sLvGameTitle).",
                            '".$sLvReleaseDate."',
                            '".$sLvWebAddress."',
                            '".$sLvPlatform."',
                            '".$sLvGamesPublisher."',
                            '".$sLvBaseAgeCategory."',
                            '".$sLvViolence."',
                            '".$sLvSex."',
                            '".$sLvDrugs."',
                            '".$sLvFear."',
                            '".$sLvDiscrimination."',
                            '".$sLvBadLanguage."',
                            '".$sLvGambling."',
                            '".$sLvOnlineGameplay."',
                            '".$sLvHorror."'
                        )
                    ";
                    
                    $this->_oLvDb->Execute( $sQuery );
                }
            }
        }
    }
    
    
    /**
     * Checks if game title already exists in database
     * 
     * @param string $sLvGameTitle
     * @return boolean
     */
    protected function _lvCheckTitleExists( $sLvGameTitle ) {
        $sLvGameTitle = $this->_oAffiliateTools->lvGetNormalizedName( $sLvGameTitle );
        $sQuery = "SELECT OXID FROM ".$this->_sLvPegiTable." WHERE LVGAMETITLE=".$this->_oLvDb->quote($sLvGameTitle)." LIMIT 1";
        $sDbGameTitle = $this->_oLvDb->GetOne( $sQuery );
        
        if ( $sDbGameTitle ) {
            $blReturn = true;
        }
        else {
            $blReturn = false;
        }
        
        return $blReturn;
    }
}
