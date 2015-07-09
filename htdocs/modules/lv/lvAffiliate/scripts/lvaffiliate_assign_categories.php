#!/usr/bin/php
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

if ( !function_exists( 'getShopBasePath' ) ) {
    function getShopBasePath() {
        return dirname(__FILE__)."/../../../../";
    }
}

require_once getShopBasePath()."bootstrap.php";

/**
 * Description of lvaffiliate_assign_categories
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_assign_categories extends oxBase {
    
    /**
     * Config object
     * @var object
     */
    protected $_oLvConfig = null;
    
    /**
     * Database object
     * @var object
     */
    protected $_oLvDb = null;
    
    /**
     * UtilsView object
     * @var object
     */
    protected $_oUtilsObject = null;
    
    /**
     * Main category where each product will be assigned to
     * @var string
     */
    protected $_sMainCategory = '';
    
    /**
     * List of attribute based assignments
     * @var array
     */
    protected $_aCatId2Attr2CatId = null;
    
    /**
     * Set of currently processing source article ids
     * @var array
     */
    protected $_aSourceArticles = array();


    /**
     * Initiate needed things
     */
    public function __construct() {
        parent::__construct();
        $this->_oLvConfig           = $this->getConfig();
        $this->_oLvDb               = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $this->_oUtilsObject        = oxRegistry::get( 'oxUtilsObject' );
        $this->_sMainCategory       = $this->_oLvConfig->getConfigParam( 'sLvAffiliateMainCategory' );
        $this->_aCatId2Attr2CatId   = $this->_oLvConfig->getConfigParam( 'aLvCatId2Attr2CatId' );
    }

    /**
     * Where the assignment begins
     */
    public function start() {
        if ( $this->_sMainCategory ) {
            // $this->_lvProcessMainCategory();
        }
        
        if ( is_array( $this->_aCatId2Attr2CatId ) &&  count( $this->_aCatId2Attr2CatId ) > 0 ) {
            $this->_lvProcessCategoryDefinitions();
        }
    }
    
    
    /**
     * Method sorts process definitions and triggers the handling of those definitions
     * 
     * @param void
     * @return void
     */
    protected function _lvProcessCategoryDefinitions() {
        foreach ( $this->_aCatId2Attr2CatId as $sSourceCategoryId => $aTargetDefinitions  ) {
            // fill source ids
            $this->_lvSetSourceArticles( $sSourceCategoryId );
            
            // split target definitions
            $aCopyRules = explode( "|", $aTargetDefinitions );
            if ( is_array( $aCopyRules ) && count( $aCopyRules ) > 0 ) {
                foreach ( $aCopyRules as $sCopyRule ) {
                    $aRuleParts = explode( ":" , $sCopyRule );
                    if ( is_array( $aRuleParts ) && count( $aRuleParts ) == 2 ) {
                        $sConditionLine     = $aRuleParts[0];
                        $sTargetCategoryId  = $aRuleParts[1];
                        
                        // there maybe more than one condition attribute or special check mode by split sign #=OR *=AND
                        
                        if ( strpos( $sConditionLine, '#' ) ) {
                            $aConditions = explode( "#" , $sConditionLine );
                            $sMode = "OR";
                        }
                        else {
                            $aConditions = explode( "*" , $sConditionLine );
                            $sMode = "AND";
                        }
                        $this->_lvProcessDefinition( $sTargetCategoryId, $aConditions, $sMode );
                    }
                }
            }
        }
    }
    
    
    /**
     * This method processes a certain rule definition for now available source and target category
     * 
     * @param type $sTargetCategoryId
     * @param type $aConditions
     * @return void
     */
    protected function _lvProcessDefinition( $sTargetCategoryId, $aConditions, $sMode ) {
        // empty target category assignments
        $this->_lvCleanCategoryAssignment( $sTargetCategoryId );
        
        // loop all available articles to check each against all conditions
        // if they pass conditions add them to target category
        foreach ( $this->_aSourceArticles as $sArticleOxid ) {
            $blCanBeAssigned = false;
            foreach ( $aConditions as $sCondition ) {
                // if assignment has been approved and we are in mode OR we can skip next check
                if ( $blCanBeAssigned && $sMode == 'OR' ) continue;
                
                if ( $sCondition == "LVISSALE" ) {
                    // check if sale
                    $blCanBeAssigned = $this->_lvCheckArticleIsSale( $sArticleOxid );
                }
                else if ( $sCondition != "LVNOATTR" ) {
                    // check if article is assigned to attribute
                    $blCanBeAssigned = $this->_lvCheckAssignedToAttribute( $sArticleOxid, $sCondition );
                }
            }
            if ( $blCanBeAssigned === true ) {
                $this->_lvAssignToCategory( $sArticleOxid, $sTargetCategoryId );
            }
        }
    }
    
    
    /**
     * Checks if current article is a sale article
     * 
     * @param string $sOxid
     * @return boolean
     */
    protected function _lvCheckArticleIsSale( $sOxid ) {
        $sQuery = "SELECT MAX(OXTPRICE) FROM oxarticles WHERE OXPARENTID=".$this->_oLvDb->quote( $sOxid );
        $dMaxTPrice     = $this->_oLvDb->GetOne( $sQuery ); 
        
        $sQuery = "SELECT MIN(OXPRICE) FROM oxarticles WHERE OXPARENTID=".$this->_oLvDb->quote( $sOxid );
        $dVarMinPrice   = $this->_oLvDb->GetOne( $sQuery ); 

        if ( $dMaxTPrice > $dVarMinPrice ) {
            $blReturn = true;
        }
        else {
            $blReturn = false;
        }
        
        return $blReturn;
    }
    

    /**
     * Method checks if any child article is assigned to given 
     * 
     * @param string $sArticleOxid
     * @param string $sCondition
     * @return boolean
     */
    protected function _lvCheckAssignedToAttribute( $sArticleOxid, $sAttributeId ) {
        // first we need to get an array
        $sQuery = "SELECT OXID FROM oxarticles WHERE OXPARENTID=".$this->_oLvDb->quote( $sArticleOxid );
        $aResult = $this->_oLvDb->GetAll( $sQuery );
        
        $aOxids = array();
        foreach ( $aResult as $aSingleResult ) {
            $aOxids[] = $aSingleResult['OXID'];
        }
        $sInOxids = "'".implode( "','", $aOxids )."'";
        
        $sQuery = "SELECT count(*) FROM oxobject2attribute WHERE OXOBJECTID IN (".$sInOxids.") AND OXATTRID=".$this->_oLvDb->quote( $sAttributeId );

        $iAssignmentsFound = (int)$this->_oLvDb->GetOne( $sQuery );
        
        if ( $iAssignmentsFound > 0 ) {
            $blReturn = true;
        }
        else {
            $blReturn = false;
        }
        
        return $blReturn;
    }


    /**
     * Method sets IDs of affected products
     * 
     * @param type $sSourceCategoryId
     * @return void
     */
    protected function _lvSetSourceArticles( $sSourceCategoryId ) {
        $this->_aSourceArticles = array();
        
        $sQuery = "SELECT OXOBJECTID FROM oxobject2category WHERE OXCATNID='".$sSourceCategoryId."'";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid = $oRs->fields['OXOBJECTID'];
                if ( $sOxid ) {
                    $this->_aSourceArticles[] = $sOxid;
                }
                $oRs->moveNext();
            }
        }
    }
    
    
    /**
     * Process assigning of all prodducts to main category
     * 
     * @param void
     * @return void
     */
    protected function _lvProcessMainCategory() {
        $this->_lvCleanCategoryAssignment( $this->_sMainCategory );
        $this->_lvAssignAllGamesToMainCategory();
    }
    
    
    /**
     * Assign all products to defined main category
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignAllGamesToMainCategory() {
        $sQuery = "SELECT OXID FROM oxarticles WHERE OXPARENTID='' AND OXACTIVE='1'";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while( !$oRs->EOF ) {
                $sObjectId = $oRs->fields['OXID'];
                
                $this->_lvAssignToCategory( $sObjectId, $this->_sMainCategory );
                
                $oRs->moveNext();
            }
        }
    }
    
    
    /**
     * Assigns product to a certain category
     * 
     * @param string $sObjectId
     * @param string $sCategoryId
     * @return void
     */
    protected function _lvAssignToCategory( $sObjectId, $sCategoryId ) {
        $sNewId = $this->_oUtilsObject->generateUId();
        
        $sQuery = "
            INSERT INTO oxobject2category
            (
                OXID,
                OXOBJECTID,
                OXCATNID,
                OXPOS,
                OXTIME,
                OXTIMESTAMP
            )
            VALUES
            (
                '".$sNewId."',
                '".$sObjectId."',
                '".$sCategoryId."',
                '0',
                NOW(),
                NOW()
            )
        ";
        
        $this->_oLvDb->Execute( $sQuery );
    }
    
    /**
     * Deletes all assigments for given category id
     * 
     * @param type $sCategoryId
     * @return void
     */
    protected function _lvCleanCategoryAssignment( $sCategoryId ) {
        $sQuery = "DELETE FROM oxobject2category WHERE OXCATNID='".$sCategoryId."'";
        $this->_oLvDb->Execute( $sQuery );
    }
    
}

$oScript = new lvaffiliate_assign_categories();
$oScript->start();