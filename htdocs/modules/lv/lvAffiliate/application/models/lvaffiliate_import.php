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
 * Model class for offering a uniform possibility to enter new products into shop 
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_import extends oxBase {
    
    /**
     * VendorId of current import
     * @var string
     */
    protected $_sLvVendorId = null;
    
    
    /**
     * Vendorname of current import
     * @var string
     */
    protected $_sLvVendorName = '';
    
    /**
     * Flag for main vendor
     * @var bool
     */
    protected $_blMainVendor = false;
    
    /**
     * Language abbreviation
     * @var string
     */
    protected $_sLvCurrentLangAbbr = null;

    /**
     * ManufacturerId of current importing article
     * @var string
     */
    protected $_sLvCurrentManufacturerId = null;
    
    /**
     * Current article id
     * @var string
     */
    protected $_sLvCurrentArticleId = null;

    /**
     * Current parent id
     * @var string
     */
    protected $_sLvCurrentParentId = null;
    
    /**
     * Current stockflag default is 4
     * @var int
     */
    protected $_iLvCurrentStockFlag = 4;

    /**
     * Manufacturer shortcut
     * @var string
     */
    protected $_sLvCurrentManufacturerShortCut = null;

    /**
     * Current set of article data needs to be importet
     * @var array
     */
    protected $_aLvCurrentArticleData = null;
    
    /**
     * Configuration for matching manufacturer logic
     * @var array
     */
    protected $_aLvField2MatchManufacturer = null;
    
    /**
     * Configuration for article matching logic
     * @var array
     */
    protected $_aLvField2MatchArticle = null;
    
    /**
     * Configuration for direct assignments from data array to tables and fields
     * @var array
     */
    protected $_aLvField2DirectTable = null;
    
    /**
     * Configuration for assigning to categories
     * @var array
     */
    protected $_aLvField2CategoryAssignment = null;
    
    /**
     * Configuration for assignin attribute values
     * @var array
     */
    protected $_aLvField2Attribute = null;
    
    /**
     * Flag that indicates if automatic articlereset should be performed
     * @var bool
     */
    protected $_blLvAffiliateResetActive = false;
    
    /**
     * Hour from which article reset is allowed
     * @var string
     */
    protected $_iLvAffiliateResetFromHour = null;
        
    /**
     * Hour to which article reset is allowed
     * @var string
     */
    protected $_iLvAffiliateResetToHour = null;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvaffiliate_import.log';
    
    /**
     * Is logging set to active
     * @var int
     */
    protected $_blLvAffiliateLogActive = false;
    
    /**
     * Configured loglevel
     * @var int
     */
    protected $_iLvAffiliateLogLevel = 1;
    
    /**
     * Affiliate Tools
     * @var object
     */
    protected $_oAffiliateTools = null;

    
    
    public function __construct() {
        // loading configuration into object
        $oConfig = $this->getConfig();
        // group assignment
        $this->_aLvField2MatchManufacturer      = $oConfig->getConfigParam( 'aLvField2MatchManufacturer' );
        $this->_aLvField2MatchArticle           = $oConfig->getConfigParam( 'aLvField2MatchArticle' );
        $this->_aLvField2DirectTable            = $oConfig->getConfigParam( 'aLvField2DirectTable' );
        $this->_aLvField2CategoryAssignment     = $oConfig->getConfigParam( 'aLvField2CategoryAssignment' );
        $this->_aLvField2Attribute              = $oConfig->getConfigParam( 'aLvField2Attribute' );
        // group maintenance
        $this->_blLvAffiliateResetActive        = $oConfig->getConfigParam( 'blLvAffiliateResetActive' );
        $this->_iLvAffiliateResetFromHour       = (int)$oConfig->getConfigParam( 'sLvAffiliateResetFromHour' );
        $this->_iLvAffiliateResetToHour         = (int)$oConfig->getConfigParam( 'sLvAffiliateResetToHour' );
        $this->_iLvCompleteDeleteDelayDays      = (int)$oConfig->getConfigParam( 'sLvCompleteDeleteDelayDays' );
        // group debug
        $this->_blLvAffiliateLogActive          = (bool)$oConfig->getConfigParam( 'blLvAffiliateLogActive' );
        $this->_iLvAffiliateLogLevel            = (int)$oConfig->getConfigParam( 'sLvAffiliateLogLevel' );
        $this->_oAffiliateTools                 = oxNew( 'lvaffiliate_tools' );
        
        
        parent::__construct();
    }
    
    
    /**
     * Setter for vendor id
     * 
     * @param string $sVendorId
     * @return void
     */
    public function lvSetVendorId( $sVendorId ) { 
        $oDb    = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $this->_sLvVendorId = $sVendorId;
        
        $sQuery = "SELECT OXTITLE, LVMAINVENDOR FROM oxvendor WHERE OXID='".$this->_sLvVendorId."' LIMIT 1";
        $aResult = $oDb->GetRow( $sQuery );
        $this->_sLvVendorName   = (string)$aResult['OXTITLE'];
        $this->_blMainVendor    = (bool)$aResult['LVMAINVENDOR'];
        $this->_lvCheckForVendorReset();
    }
    
    
    
    /**
     * Method deletes all products related to vendor
     * 
     * @param void
     * @return void
     */
    public function lvResetVendorArticles( $blDropArticles = false ) {
        $oDb    = oxDb::getDb( MODE_FETCH_ASSOC );
        
        if ( $this->_sLvVendorId ) {
            $sQuery = "SELECT OXID FROM oxarticles WHERE OXVENDORID='".$this->_sLvVendorId."'";
            
            $oRs = $oDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                $oArticle = oxNew( 'oxarticle' );
                while ( !$oRs->EOF ) {
                    $sOxid = $oRs->fields['OXID'];
                    if ( $sOxid  && $blDropArticles ) {
                        $oArticle->delete( $sOxid );
                    }
                    else {
                        $this->_lvDeactivateArticle( $sOxid );
                    }
                    
                    $oRs->moveNext();
                }
            }

            if ( $blDropArticles ) {
                // collect standalone parents and remove them
                $sQuery = "DELETE FROM `oxarticles` WHERE OXPARENTID='' AND OXVARCOUNT='0'";
            }
            else {
                // collect standalone parents and deactivate them
                $sQuery = "UPDATE oxarticles SET OXACTIVE='0' WHERE OXPARENTID='' AND OXVARCOUNT='0'";
            }
            
            $oDb->Execute( $sQuery );
        }
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
        
        if ( $iLogLevel <= $this->_iLvAffiliateLogLevel ) {
            $sPrefix        = "[".date( 'Y-m-d H:i:s' )."] ";
            $sFullMessage   = $sPrefix.$sMessage."\n";
            
            $oUtils->writeToLog($sFullMessage, $this->_sLogFile );
        }
    }

    
    /**
     * Method adds/updates article depending on configuration settings and article data
     * 
     * @param array $aArticleData
     * @return void
     */
    public function lvAddArticle( $aArticleData, $sLangAbbr ) {
        // reset data
        $this->_sLvCurrentArticleId         = null;
        $this->_sLvCurrentParentId          = null;
        $this->_sLvCurrentManufacturerId    = null;
        $this->_sLvCurrentLangAbbr          = $sLangAbbr;
        $this->_iLvCurrentStockFlag         = 4; // improvement would be to make this configurable indeed 4 is mostly needed
        
        $this->_aLvCurrentArticleData = $aArticleData;
        $this->_lvSetManufacturerId();
        
        $blCreateComplete = $this->_lvSetArticleIds();
        $this->_lvSetArticleData( $blCreateComplete );
        
        $this->_lvAssignCategories();

        $this->_lvAssignAttributes();
        
        // check for deprecated articles and cleanup garbage data
        $this->_lvCleanupDeprecatedEntries();
    }
    
    
    /**
     * Cleans all sale rankings of given vendor to be able to recalculate them
     * It's mandatory that vendorid has been set before
     * 
     * @param void
     * @return void
     */
    public function lvResetSalesRank() {
        if ( $this->_sLvVendorId != '' ) {
            $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );

            $sQuery = "UPDATE oxarticles SET LVSALESRANK='999999' WHERE OXVENDORID='".$this->_sLvVendorId."'";
            $oDb->Execute( $sQuery );
        }
    }

    /**
     * Sets sales rank by artnum
     * 
     * @param type $sArtNum
     * @param type $iRank
     * @return void
     */
    public function lvSetSalesRank( $sArtNum, $iRank ) {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $sQuery = "UPDATE oxarticles SET LVSALESRANK='".(string)$iRank."' WHERE OXARTNUM='".$sArtNum."' LIMIT 1";
        $oDb->Execute( $sQuery );
    }
    
    
    /**
     * Searches for deprecated entries and delete them from database
     * 
     * @param void
     * @return void
     */
    protected function _lvCleanupDeprecatedEntries() {
        if ( $this->_iLvCompleteDeleteDelayDays > 0 ) {
            $oDb                = oxDb::getDb( MODE_FETCH_ASSOC );
            $iMaxAgeTimestamp   = strtotime( "- ".$this->_iLvCompleteDeleteDelayDays." days" );
            $sArticlesView      = getViewName( 'oxarticles' );
            $aArticlesToDelete  = array();

            $sQuery = "SELECT OXID, OXTIMESTAMP FROM ".$sArticlesView." WHERE OXACTIVE='0'";
            
            $oRs = $oDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    $sOxid                  = $oRs->fields['OXID'];
                    $sTimeStamp             = $oRs->fields['OXTIMESTAMP'];
                    $iTimeStampOfArticleRow = strtotime();
                    
                    if ( $iTimeStampOfArticleRow <= $iMaxAgeTimestamp ) {
                        $aArticlesToDelete[] = $sOxid;
                    }
                    
                    $oRs->moveNext();
                }
            }
            
            foreach ( $aArticlesToDelete as $sOxid ) {
                $oArticle = oxNew( 'oxarticle' );
                $oArticle->load( $sOxid );
                
                if ( $oArticle ) {
                    $oArticle->delete();
                }
            }
        }
    }
    

    /**
     * Deactivates article and checks if all possible variants are inactive. If this is the case 
     * Parent will also be deactivated
     * 
     * @param string $sOxid
     * @return void
     */
    protected function _lvDeactivateArticle( $sOxid ) {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $sArticlesTable = getViewName( 'oxarticles' );
        
        $sQuery = "SELECT OXPARENTID FROM ".$sArticlesTable." WHERE OXID=".$oDb->quote( $sOxid )." LIMIT 1";
        $sParentId = $oDb->GetOne( $sQuery );
        
        // deactivate article
        $sQuery = "UPDATE oxarticles SET oxactive = '0' WHERE OXID=".$oDb->quote( $sOxid )." LIMIT 1";

        $oDb->Execute( $sQuery );
        
        // count active variants if article has parent. Deactivate parent if there are no other active variants
        if ( $sParentId ) {
            $sQuery = "SELECT count(OXID) FROM ".$sArticlesTable." WHERE OXPARENTID=".$oDb->quote( $sParentId )." AND OXACTIVE='1'";
            $iCountActiveVariants = $oDb->GetOne( $sQuery );
            if ( $iCountActiveVariants == 0 ) {
                // deactivate parent
                $sQuery = "UPDATE oxarticles SET oxactive = '0' WHERE OXID=".$oDb->quote( $sParentId )." LIMIT 1";
                
                $oDb->Execute( $sQuery );
            }
        }
    }


    /**
     * Method checks if current hour is foreseen for vendor article reset and if option is activated
     * Triggering reset if true
     * 
     * @param void
     * @return void
     */
    protected function _lvCheckForVendorReset() {
        $iHourNow = (int)date('H');
        $blResetTimeValid = (
                is_numeric( $this->_iLvAffiliateResetFromHour ) && 
                is_numeric( $this->_iLvAffiliateResetToHour ) && 
                $iHourNow >= $this->_iLvAffiliateResetFromHour &&
                $iHourNow <= $this->_iLvAffiliateResetToHour
        );
        if ( $blResetTimeValid && $this->_blLvAffiliateResetActive ) {
            $this->lvResetVendorArticles();
        }
    }
    
    
    /**
     * Assigning attributes of article
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignAttributes() {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
        
        foreach ( $this->_aLvField2Attribute as $sDataFieldTarget=>$sTargetAttributeId ) {
            $aDataFieldTarget = explode( "|", $sDataFieldTarget );
            
            /**
             * @todo needs to be improved to be more flexible => check if variable variables could also be build with array indexes
             */
            $sDataFieldValue = '';
            if ( count( $aDataFieldTarget ) == 2 ) {
                if ( isset( $this->_aLvCurrentArticleData[$aDataFieldTarget[0]][$aDataFieldTarget[1]] ) ) {
                    $sDataFieldValue = $this->_aLvCurrentArticleData[$aDataFieldTarget[0]][$aDataFieldTarget[1]];
                }
            }
            else {
                if ( isset( $this->_aLvCurrentArticleData[$sDataFieldTarget] ) ) {
                    $sDataFieldValue = $this->_aLvCurrentArticleData[$sDataFieldTarget];
                }
            }
            
            if ( $sDataFieldValue ) {
                $sAttributeAssignmentId = $this->_lvGetAttributeAssignmentId( $sTargetAttributeId );
                if ( !$sAttributeAssignmentId ) {
                    // generate new id then and insert value into oxobject2attribute
                    $sNewId = $oUtilsObject->generateUId();
                    $sQuery = "INSERT INTO oxobject2attribute ( OXID, OXATTRID, OXOBJECTID, OXVALUE ) VALUES ( '".$sNewId."', '".$sTargetAttributeId."', '".$this->_sLvCurrentArticleId."', '".$sDataFieldValue."' )";
                }
                else {
                    // update existing assignment
                    $sQuery = "UPDATE oxobject2attribute SET OXVALUE='".$sDataFieldValue."' WHERE OXID='".$sAttributeAssignmentId."' LIMIT 1";
                }
                
                $oDb->Execute( $sQuery );
            }
        }
    }


    /**
     * Checks if a given article-attribute combo exists or not. Returns id if it exists or false if not
     * 
     * @param string $sTargetAttributeId
     * @return mixed
     */
    protected function _lvGetAttributeAssignmentId( $sTargetAttributeId ) {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $sTargetTable   = getViewName( 'oxobject2attribute' );
        
        $sQuery = "SELECT OXID FROM ".$sTargetTable." WHERE OXATTRID='".$sTargetAttributeId."' AND OXOBJECTID='".$this->_sLvCurrentArticleId."' LIMIT 1";
        $sOxid  = $oDb->GetOne( $sQuery );
        
        $mReturn = false;
        if ( $sOxid ) {
            $mReturn = $sOxid;
        }
        
        return $mReturn;
    }


    /**
     * Method assigns current article to belonging categories
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignCategories() {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
        
        foreach ( $this->_aLvField2CategoryAssignment as $sDataFieldName=>$sConfigFieldValue ) {
            $aTargetTableField  = explode( "|", $sConfigFieldValue );
            $sTargetTable       = $aTargetTableField[0];
            $sTargetField       = $aTargetTableField[1];
            $blIsSale           = (bool)$aTargetTableField[2];
            
            $blCheckForRemoval = false;
            if ( $blIsSale === true ) {
                // check if tprice isset and bigger than price otherwise continue with next assignment
                if ( !isset( $this->_aLvCurrentArticleData['TPRICE'] ) ) {
                    continue;
                }
                
                $dPrice     = (double)$this->_aLvCurrentArticleData['PRICE'];
                $dTPrice    = (double)$this->_aLvCurrentArticleData['TPRICE'];
                
                if ( ( $dTPrice > $dPrice ) == false ) {
                    $blCheckForRemoval = true;
                }
            }

            if ( $this->_sLvCurrentParentId ) {
                if ( isset( $this->_aLvCurrentArticleData[$sDataFieldName] ) && is_array( $this->_aLvCurrentArticleData[$sDataFieldName] ) ) {
                    foreach ( $this->_aLvCurrentArticleData[$sDataFieldName] as $sTargetCategoryId ) {
                        $blAssignmentExists = $this->_lvCheckCategoryAssignmentExists( $sTargetCategoryId );

                        if ( $blAssignmentExists === false ) {
                            $sNewId = $oUtilsObject->generateUId();

                            $sQuery = "INSERT INTO oxobject2category ( OXID, OXOBJECTID, OXCATNID ) VALUES ( '".$sNewId."', '".$this->_sLvCurrentParentId."', '".$sTargetCategoryId."' )";
                            $oDb->Execute( $sQuery );
                        }
                        else if ( $blCheckForRemoval === true ) {
                            $sQuery = "DELETE FROM oxobject2category WHERE OXOBJECTID='".$this->_sLvCurrentParentId."' AND OXCATNID='".$sTargetCategoryId."' LIMIT 1";
                            $oDb->Execute( $sQuery );
                        }
                    }
                }
            }
        }
    }
    
    
    /**
     * Checks if a given article-category combo exists or not
     * 
     * @param string $sTargetCategoryId
     * @return bool
     */
    protected function _lvCheckCategoryAssignmentExists( $sTargetCategoryId ) {
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $sTargetTable   = getViewName( 'oxobject2category' );
        
        $sQuery = "SELECT OXID FROM ".$sTargetTable." WHERE OXCATNID='".$sTargetCategoryId."' AND OXOBJECTID='".$this->_sLvCurrentParentId."' LIMIT 1";
        $sOxid  = $oDb->GetOne( $sQuery );
        
        $blReturn = false;
        if ( $sOxid ) {
            $blReturn = true;
        }
        
        return $blReturn;
    }
    
    
    /**
     * Method tries to match an existing article Id
     * 
     * @param void
     * @return bool
     */
    protected function _lvSetArticleIds() {
        $oDb = oxDb::getDb( MODE_FETCH_ASSOC );
        $sArticleId = "";
        $sParentId  = "";
        
        // go through configuration
        $blMatch=false;
        foreach ( $this->_aLvField2MatchArticle as $sDataFieldName=>$sConfigFieldValue ) {
            if ( $blMatch ) continue;
            
            $aConfigFieldValue  = explode( "|", $sConfigFieldValue );
            $sConfigDbField     = $aConfigFieldValue[0];
            $sConfigFamily      = $aConfigFieldValue[1];
            $sValueToMatch      = $this->_aLvCurrentArticleData[$sDataFieldName];
            
            // we need to check if matching is by name. If so we need to normalize the name due vendors use different namings
            if ( $sConfigDbField == 'OXTITLE' ) {
                $sValueToMatch = $this->_oAffiliateTools->lvGetNormalizedName( $sValueToMatch );
            }
            
            $sQueryAdd = "";
            if ( $sConfigFamily == 'parent' ) {
                $sQueryAdd = " AND OXPARENTID='' ";
            }
            
            $sQuery     = "SELECT OXID, OXPARENTID FROM oxarticles WHERE ".$sConfigDbField."='".mysql_real_escape_string( $sValueToMatch )."' ".$sQueryAdd." LIMIT 1";
            $aResult    = $oDb->GetRow( $sQuery );
            
            $blCreateComplete = true;
            if ( $aResult && is_array( $aResult ) ) {
                if ( $sConfigFamily == 'child' ) {
                    $sArticleId     = $aResult['OXID'];
                    $sParentId      = $aResult['OXPARENTID'];
                }
                else {
                    $sParentId                  = $aResult['OXID'];
                    $this->_sLvCurrentParentId  = $sParentId;
                    
                    $sQuery     = "SELECT OXID FROM oxarticles WHERE OXPARENTID='".$sParentId."' AND  OXVENDORID='".$this->_sLvVendorId."' LIMIT 1";
                    $sArticleId = $oDb->GetOne( $sQuery );
                    
                    if ( !$sArticleId ) {
                        $blCreateComplete = false;
                    }
                }
                
                if ( $sArticleId && $sParentId ) {
                    $this->_sLvCurrentArticleId = $sArticleId;
                    $this->_sLvCurrentParentId  = $sParentId;
                    $blMatch = true;
                }
            }
        }
        
        return $blCreateComplete;
    }
    
    
    /**
     * Creating or updating article data (direct assignments)
     * 
     * @param bool $blCreateComplete
     * @return
     */
    protected function _lvSetArticleData( $blCreateComplete ) {
        if ( !$this->_sLvCurrentArticleId || $this->_sLvCurrentArticleId == "" ) {
            $this->_lvCreateArticle( $blCreateComplete );
        }
        else {
            $this->_lvUpdateArticle();
        }
    }
    
    
    /**
     * Creates a new article. If complete article including parent should be generated the param is set to true
     * 
     * @param bool $blComplete
     * @return void
     */
    protected function _lvCreateArticle( $blCreateComplete ) {
        $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        if ( $blCreateComplete === true && isset( $this->_aLvCurrentArticleData['TITLE'] ) ) {
            // the article is completely new and have not been created by another merchant import until now
            // we need to create parent article then
            $oParentArticle             = oxNew( 'oxarticle' );
            $this->_sLvCurrentParentId  = $oUtilsObject->generateUId();
            $sTitle                     = $this->_aLvCurrentArticleData['TITLE'];
            $iParentArtNumberNumeric    = $this->_lvGetNextArtNumCounter();
            $sParentArtNum              = $this->_sLvCurrentManufacturerShortCut.(string)$iParentArtNumberNumeric;
                    
            $oParentArticle->setId( $this->_sLvCurrentParentId );
            $oParentArticle->oxarticles__oxtitle            = new oxField( $this->_oAffiliateTools->lvGetNormalizedName( $sTitle ) );
            $oParentArticle->oxarticles__oxmanufacturerid   = new oxField( $this->_sLvCurrentManufacturerId );
            $oParentArticle->oxarticles__oxartnum           = new oxField( $sParentArtNum );
            $oParentArticle->oxarticles__oxartnum           = new oxField( $sParentArtNum );
            $oParentArticle->oxarticles__oxstockflag        = new oxField( $this->_iCurrentStockFlag );
            
            try {
                $oParentArticle->save();
            } 
            catch (Exception $ex) {
                $this->lvLog( "ERROR: Exception has been thrown while trying to save created parent article with ID".$this->_sLvCurrentParentId."\nException message was:\n".$e->message(), 1 );
            }
        }
        
        $this->_sLvCurrentArticleId     = $oUtilsObject->generateUId();
        
        $oArticle = oxNew( 'oxarticle' );
        $oArticle->setId( $this->_sLvCurrentArticleId );
        $oArticle->oxarticles__oxparentid       = new oxField( $this->_sLvCurrentParentId );
        $oArticle->oxarticles__oxvendorid       = new oxField( $this->_sLvVendorId );
        $oArticle->oxarticles__lvcoverpic       = new oxField( 'oxpic1' );
        $oArticle->oxarticles__oxstockflag      = new oxField( $this->_iCurrentStockFlag );
        $oArticle->oxarticles__oxvarselect      = new oxField( $this->_sLvVendorName );
        if ( $this->_blMainVendor ) {
            $this->_lvClearMastervariantForLang( $sPar );
            $oArticle->oxarticles__lvmastervariant  = new oxField( $this->_blMainVendor );
        }
        
        if ( $this->_sLvCurrentLangAbbr !== null ) {
            $oArticle->oxarticles__lvlangabbr       = new oxField( $this->_sLvCurrentLangAbbr );
        }
            
        foreach ( $this->_aLvField2DirectTable as $sDataFieldName=>$sAssignTableField ) {
            $aTargetTableField  = explode( "|", $sAssignTableField );
            $sTargetTable       = $aTargetTableField[0];
            $sTargetField       = $aTargetTableField[1];
            $sTarget            = strtolower( $sTargetTable )."__".strtolower( $sTargetField );

            if ( $sTarget == 'oxarticles__oxtitle' ) {
                $this->_aLvCurrentArticleData[$sDataFieldName] = $this->_oAffiliateTools->lvGetNormalizedName( $this->_aLvCurrentArticleData[$sDataFieldName] );
            }
            
            if ( isset( $this->_aLvCurrentArticleData[$sDataFieldName] ) ) {
                $oArticle->$sTarget = new oxField( $this->_aLvCurrentArticleData[$sDataFieldName] );
            }
            $this->lvLog( "Adding value ".$this->_aLvCurrentArticleData[$sDataFieldName]." to target ".$sTargetField." with ArticleID:".$this->_sLvCurrentArticleId, 3 );
        }
        
        try {
            $oArticle->save();
        } 
        catch (Exception $ex) {
            $this->lvLog( "ERROR: Exception has been thrown while trying to save created article with ID".$this->_sLvCurrentArticleId."\nException message was:\n".$e->message(), 1 );
        }
    }
    
    
    /**
     * Resets mastervariants for current lang
     * 
     * @param void
     * @return void
     */
    protected function _lvClearMastervariantForLang() {
        if ( $this->_sLvCurrentLangAbbr !== null ) {
            $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
            
            $sQuery = "UPDATE oxarticles SET LVMASTERVARIANT='0' WHERE OXPARENTID='".$this->_sLvCurrentParentId."' AND LVLANGABBR='".$this->_sLvCurrentLangAbbr."'";
            $oDb->Execute( $sQuery );
        }
    }
    
    
    /**
     * Updates existing article
     * 
     * @param void
     * @return void
     */
    protected function _lvUpdateArticle() {
        $oArticle = oxNew( 'oxarticle' );
        $oArticle->load( $this->_sLvCurrentArticleId );
        
        if ( $oArticle ) {
            foreach ( $this->_aLvField2DirectTable as $sDataFieldName=>$sAssignTableField ) {
                $aTargetTableField  = explode( "|", $sAssignTableField );
                $sTargetTable       = $aTargetTableField[0];
                $sTargetField       = $aTargetTableField[1];
                $sTarget            = strtolower( $sTargetTable )."__".strtolower( $sTargetField );
                if ( isset( $this->_aLvCurrentArticleData[$sDataFieldName] ) ) {
                    $oArticle->$sTarget = new oxField( $this->_aLvCurrentArticleData[$sDataFieldName] );
                }
            }
            
            // set article active
            $oArticle->oxarticles__oxactive = new oxField( '1' );
            
            // check if article has parent. If true make sure parent will be activated as well
            if ( $oArticle->oxarticles__oxparentid->value != '' ) {
                $oParentArticle = $oArticle->getParentArticle();
                if ( $oParentArticle ) {
                    $oParentArticle->oxarticles__oxactive = new oxField( '1' );
                    $oParentArticle->save();
                }
            }

            try {
                $oArticle->save();
            } 
            catch (Exception $ex) {
                $this->lvLog( "ERROR: Exception has been thrown while trying to save updated article with ID".$this->_sLvCurrentArticleId."\nException message was:\n".$e->message(), 1 );
            }
        }
        else {
            $this->lvLog("ERROR: Could not update Article with given ID".$this->_sLvCurrentArticleId."! Failed loading article.", 1);
        }
    }
    
    
    /**
     * Fetches next free artnum and increases number counter
     * 
     * @param void
     * @return int
     */
    protected function _lvGetNextArtNumCounter() {
        $oDb = oxDb::getDb( MODE_FETCH_ASSOC );
        
        /**
         * @todo possibly it would be saver to lock tables on select
         */
        
        $sQuery         = "SELECT OXCOUNT FROM oxcounters WHERE OXIDENT='oxArtnum' LIMIT 1";
        $sArtNum        = $oDb->GetOne( $sQuery);
        $iArtNum        = (int)$sArtNum;
        $iNextArtNum    = $iArtNum +1;
        
        // update counter
        $sQuery         = "UPDATE oxcounters SET OXCOUNT='".(string)$iNextArtNum."' WHERE OXIDENT='oxArtnum' LIMIT 1";
        $oDb->Execute( $sQuery );
        
        return $iArtNum;
    }
    
    
    /**
     * Method tries to match an existing manufacturer and sets id.
     * If manufacturer could not be matched it will be created and its ID will be used
     * 
     * @param void
     * @return void
     */
    protected function _lvSetManufacturerId() {
        $oDb = oxDb::getDb( MODE_FETCH_ASSOC );
        
        // go through configuration
        foreach ( $this->_aLvField2MatchManufacturer as $sDataFieldName=>$sConfigFieldValue ) {
            $aConfigFieldValue  = explode( "|", $sConfigFieldValue );
            $sConfigDbTable     = $aConfigFieldValue[0];
            $sConfigDbField     = $aConfigFieldValue[1];
            
            $sQuery = "SELECT OXID FROM ".$sConfigDbTable." WHERE ".$sConfigDbField."='".$this->_aLvCurrentArticleData[$sDataFieldName]."' LIMIT 1";
            
            $sManufacturerId = $oDb->GetOne( $sQuery );
            
            if ( !$sManufacturerId ) {
                $this->_lvCreateManufacturer( $this->_aLvCurrentArticleData[$sDataFieldName] );
            }
            else {
                $this->_sLvCurrentManufacturerId = $sManufacturerId;
            }
        }
        
        // finally set manufacturershortcut for later use
        $this->_lvSetCurrentManufacturerShortCutById();
    }
    
    
    /**
     * Sets current manufacturer shortcut for later use
     * 
     * @param void
     * @return void
     */
    protected function _lvSetCurrentManufacturerShortCutById() {
        $oDb                    = oxDb::getDb( MODE_FETCH_ASSOC );
        $sManufacturersTable    = getViewName( 'oxmanufacturers' );
        
        $sQuery = "SELECT LVSHORTCUT FROM ".$sManufacturersTable." WHERE OXID='".$this->_sLvCurrentManufacturerId."' LIMIT 1";
        $sManufacturerShortCut = $oDb->GetOne( $sQuery );
        
        if ( !$sManufacturerShortCut ) {
            $sManufacturerShortCut = "g4g";
        }
        
        $this->_sLvCurrentManufacturerShortCut = $sManufacturerShortCut;
    }
    
    
    /**
     * Creating new manufacturer with given name
     * 
     * @param string $sManufacturerName
     * @return void
     */
    protected function _lvCreateManufacturer( $sManufacturerName ) {
        $oUtilsObject   = oxRegistry::get( 'oxUtilsObject' );
        $sNewId         = $oUtilsObject->generateUId();
        $oManufacturer  = oxNew( 'oxmanufacturer' );
        
        $sShortCut = $this->_lvCreateShortCut( $sManufacturerName );
        
        $oManufacturer->setId( $sNewId );
        $oManufacturer->oxmanufacturers__oxtitle    = new oxField( $sManufacturerName );
        $oManufacturer->oxmanufacturers__lvshortcut = new oxField( $sShortCut );
        $oManufacturer->save();
        $this->_sLvCurrentManufacturerId = $sNewId;
    }
    
    
    /**
     * Creating shortcut from name
     * 
     * @param string $sLongName
     * @return string
     */
    protected function _lvCreateShortCut( $sLongName ) {
        $aWords = explode( " ", $sLongName );
        $iAmountWords = count( $aWords );
        if ( $iAmountWords >= 3 ) {
            $sShortCut = "";
            foreach ( $aWords as $sWord ) {
                $sShortCut .= strtoupper( substr( $sWord, 0, 1 ) );
            }
        }
        else {
            $sShortCut = "";
            foreach ( $aWords as $sWord ) {
                $sShortCut .= strtoupper( substr( $sWord, 0, 2 ) );
            }
        }
        
        /**
         * @todo check if shortcut exists and add a number as long it doesn't still exist
         */
        
        return $sShortCut;
    }
    
}
