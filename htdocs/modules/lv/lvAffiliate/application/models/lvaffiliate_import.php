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
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvaffiliate_import.log';

    
    
    public function __construct() {
        // loading configuration into object
        $oConfig = $this->getConfig();
        $this->_aLvField2MatchManufacturer = $oConfig->getConfigParam( 'aLvField2MatchManufacturer' );
        $this->_aLvField2MatchArticle = $oConfig->getConfigParam( 'aLvField2MatchArticle' );
        $this->_aLvField2DirectTable = $oConfig->getConfigParam( 'aLvField2DirectTable' );
        $this->_aLvField2CategoryAssignment = $oConfig->getConfigParam( 'aLvField2CategoryAssignment' );
        $this->_aLvField2Attribute = $oConfig->getConfigParam( 'aLvField2Attribute' );
        
        parent::__construct();
    }
    
    
    /**
     * Setter for vendor id
     * 
     * @param string $sVendorId
     * @return void
     */
    public function lvSetVendorId( $sVendorId ) { 
        $this->_sLvVendorId = $sVendorId;
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
     * Method adds/updates article depending on configuration settings and article data
     * 
     * @param array $aArticleData
     * @return void
     */
    public function lvAddArticle( $aArticleData ) {
        $this->_aLvCurrentArticleData = $aArticleData;
        $this->_lvSetManufacturerId();
        $this->_lvSetArticleIds();
die();        
//        $this->_lvSetArticleData();
//        $this->_lvAssignCategories();
//        $this->_lvAssignAttributes();
        
        print_r( $aArticleData );
    }
    
    
    /**
     * Method tries to match an existing article Id
     * 
     * @param void
     * @return void
     */
    protected function _lvSetArticleIds() {
        $oDb = oxDb::getDb( MODE_FETCH_ASSOC );
        $sArticleId = "";
        $sParentId  = "";
        
        // go through configuration
        foreach ( $this->_aLvField2MatchArticle as $sDataFieldName=>$sConfigFieldValue ) {
            $aConfigFieldValue  = explode( "|", $sConfigFieldValue );
            $sConfigDbField     = $aConfigFieldValue[0];
            $sConfigFamily      = $aConfigFieldValue[1];
            $sValueToMatch      = $this->_aLvCurrentArticleData[$sDataFieldName];
            
            $sQuery     = "SELECT OXID, OXPARENTID FROM oxarticles WHERE ".$sConfigDbField."='".$sValueToMatch."' LIMIT 1";
            $aResult    = $oDb->GetRow( $sQuery );
            
            $blCreateComplete = true;
            if ( $aResult && is_array( $aResult ) ) {
                if ( $sConfigFamily == 'child' ) {
                    $sArticleId     = $aResult['OXID'];
                    $sParentId      = $aResult['OXPARENTID'];
                }
                else {
                    $sParentId  = $aResult['OXID'];
                    $sQuery     = "SELECT OXID FROM oxarticles WHERE OXPARENTID='".$sParentId."' AND ".$this->_sLvVendorId." LIMIT 1";
                    $sArticleId = $oDb->GetOne( $sQuery );
                    if ( !$sArticleId ) {
                        $blCreateComplete = false;
                    }
                }
            }
        }
        
        if ( !$sArticleId || $sArticleId != "" ) {
            $this->_lvCreateArticle( $blComplete );
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
    protected function _lvCreateArticle( $blComplete ) {
        /**
         * @todo: Go further here next time
         */
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
                $sShortCut .= strtolower( substr( $sWord, 0, 1 ) );
            }
        }
        else {
            $sShortCut = "";
            foreach ( $aWords as $sWord ) {
                $sShortCut .= strtolower( substr( $sWord, 0, 2 ) );
            }
        }
        
        return $sShortCut;
    }
    
}
