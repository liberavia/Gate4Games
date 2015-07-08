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
    protected $_aCatId2Attr2Catid = null;




    public function __construct() {
        parent::__construct();
        $this->_oLvConfig           = $this->getConfig();
        $this->_oLvDb               = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $this->_oUtilsObject        = oxRegistry::get( 'oxUtilsObject' );
        $this->_sMainCategory       = $this->_oLvConfig->getConfigParam( 'sLvAffiliateMainCategory' );
        $this->_aCatId2Attr2Catid   = $this->_oLvConfig->getConfigParam( 'aLvCatId2Attr2CatId' );
    }

    /**
     * Where the assignment begins
     */
    public function start() {
        if ( $this->_sMainCategory ) {
            $this->_lvProcessMainCategory();
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