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
 * Assigns all language abbreviatations as comma-seperated list to parent article to be able to deliver 
 * products that belong to language
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_assign_langabbr extends oxBase {
    
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
     * Array of parent OXIDs
     * @var array
     */
    protected $_aParentArticles = array();
    
    /**
     * Script start point
     * 
     * @param void
     * @return void
     */
    public function start() {
        $this->_oLvConfig           = $this->getConfig();
        $this->_oLvDb               = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $this->_lvSetparentOxids();
        $this->_lvAssignLangAbbr();
    }
    
    
    /**
     * Go through parent articles and assign collected lang abbr
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignLangAbbr() {
        $sTable = getViewName( 'oxarticles' );
        
        foreach ( $this->_aParentArticles as $sParentOxid ) {
            // get all variant langabbreviations
            $aLangAbbr          = $aTmpLangAbbr =  array();
            $sParentLangAbbr    = '';

            $sQuery = "SELECT LVLANGABBR FROM ".$sTable." WHERE OXPARENTID='".$sParentOxid."'";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    $sCurrentLangAbbr = $oRs->fields['LVLANGABBR'];
                    
                    if ( $sCurrentLangAbbr ) {
                        $aTmpLangAbbr[$sCurrentLangAbbr] = 'dummy';
                    }
                    
                    $oRs->moveNext();
                }
                
                $aLangAbbr = array_keys( $aTmpLangAbbr );
                
                if ( count( $aLangAbbr ) > 0 ) {
                    $sParentLangAbbr = implode( ',', $aLangAbbr );
                }
            }
            
            $this->_lvAssignCertainLangAbbr( $sParentOxid, $sParentLangAbbr );
        }
    }
    
    /**
     * Sets collected langabbreviations to article
     * 
     * @param string $sOxid
     * @param string $sParentLangAbbr
     */
    protected function _lvAssignCertainLangAbbr( $sOxid, $sParentLangAbbr ) {
        $sQuery = "UPDATE oxarticles SET LVLANGABBR='".$sParentLangAbbr."' WHERE OXID='".$sOxid."' LIMIT 1";
        
        $this->_oLvDb->Execute( $sQuery );
    }

    /**
     * Sets parent OXIDs to perform action on
     * 
     * @param void
     * @return void
     */
    protected function _lvSetparentOxids() {
        $this->_aParentArticles = array();
        $sTable = getViewName( 'oxarticles' );
        $sQuery ="SELECT OXID FROM ".$sTable." WHERE oxparentid = '' AND oxactive='1'";
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $this->_aParentArticles[] = $oRs->fields['OXID'];
                $oRs->moveNext();
            }
        }
    }
}

$oScript = new lvaffiliate_assign_langabbr();
$oScript->start();