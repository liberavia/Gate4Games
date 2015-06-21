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
 * Description of lvaffiliate_calc_salesrank
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_calc_salesrank extends oxBase {
    
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
        $this->_lvAssignSalesRank();
    }
    
    
    /**
     * Go through parent articles and assign average sales rank of variants
     * 
     * @param void
     * @return void
     */
    protected function _lvAssignSalesRank() {
        $sTable = getViewName( 'oxarticles' );
        
        foreach ( $this->_aParentArticles as $sParentOxid ) {
            // get all variant langabbreviations
            $aSalesRank             = array();
            $sParentSalesRank       = '';

            $sQuery = "SELECT LVSALESRANK FROM ".$sTable." WHERE OXPARENTID='".$sParentOxid."'";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    $iCurrentSalesRank = (int)$oRs->fields['LVSALESRANK'];
                    
                    if ( $iCurrentSalesRank > 0 && $iCurrentSalesRank < 999999 ) {
                        $aSalesRank[] = $iCurrentSalesRank;
                    }
                    
                    $oRs->moveNext();
                }
                
                
                if ( count( $aSalesRank ) > 0 ) {
                    $iSumRank           = array_sum( $aSalesRank );
                    $iAmountSalesRank   = count( $aSalesRank );
                    
                    if ( $iAmountSalesRank > 0 ) {
                        $iAvgSalesRank = abs( $iSumRank/$iAmountSalesRank ); 
                    }
                    else {
                        $iAvgSalesRank = 999999;
                    }
                    $sParentSalesRank = (string)$iAvgSalesRank;
                }
            }
            
            $this->_lvAssignCertainSalesRank( $sParentOxid, $sParentSalesRank );
        }
    }
    
    /**
     * Sets calculated sales rank to article
     * 
     * @param string $sOxid
     * @param string $sParentLangAbbr
     */
    protected function _lvAssignCertainSalesRank( $sOxid, $sParentSalesRank ) {
        $sQuery = "UPDATE oxarticles SET LVSALESRANK='".$sParentSalesRank."' WHERE OXID='".$sOxid."' LIMIT 1";
        
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


$oScript = new lvaffiliate_calc_salesrank();
$oScript->start();