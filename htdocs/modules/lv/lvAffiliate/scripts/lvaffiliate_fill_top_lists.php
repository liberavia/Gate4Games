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

/**
 * Description of lvaffiliate_fill_toplists
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */

if ( !function_exists( 'getShopBasePath' ) ) {
    function getShopBasePath() {
        return dirname(__FILE__)."/../../../../";
    }
}

require_once getShopBasePath()."bootstrap.php";


class lvaffiliate_fill_toplists extends oxBase {
    
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
     * Limit lists
     * @var int
     */
    protected $_iListLimit = 10;
    
    /**
     * Start trigger for script
     * 
     * @param void
     * @return void
     */
    public function start() {
        $this->_oLvConfig           = $this->getConfig();
        $this->_oLvDb               = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $this->_lvFillTopSeller();
        $this->_lvFillTopSale();
        $this->_lvFillLatest();
    }
    
    
    /**
     * Fills top seller list by given action id
     * 
     * @param void
     * @return void
     */
    protected function _lvFillTopSeller() { 
        $sActionList    = $this->_oLvConfig->getConfigParam( 'sLvListTopSellerId' );
        
        if ( $sActionList ) {
            $this->_lvEmptyList( $sActionList );
            $sTable = getViewName( 'oxarticles' );
            $sQuery = "
                SELECT OXPARENTID FROM ".$sTable." WHERE OXPARENTID != '' ORDER BY LVAMZSALESRANK ASC LIMIT ".$this->_iListLimit."
            ";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                $iSort = 0;
                while( !$oRs->EOF ) {
                    $sArticleId = $oRs->fields['OXPARENTID'];
                    
                    $this->_lvAddToList( $sActionList, $sArticleId, $iSort );
                    $iSort++;
                    
                    $oRs->moveNext();
                }
            }
        }
    }

    
    /**
     * Fills top sale list by given action id
     * 
     * @param void
     * @return void
     */
    protected function _lvFillTopSale() { 
        $sActionList    = $this->_oLvConfig->getConfigParam( 'sLvListTopSaleId' );
        
        if ( $sActionList ) {
            $this->_lvEmptyList( $sActionList );
            $sTable = getViewName( 'oxarticles' );
            $sQuery = "
                SELECT 
                    OXPARENTID, 
                    ( OXTPRICE - OXPRICE ) as LVSAVEDABS
                FROM 
                    ".$sTable."
                WHERE 
                    OXPARENTID != '' 
                    AND OXTPRICE !='0' 
                    AND OXTPRICE !='' 
                    AND OXTPRICE > OXPRICE 
                ORDER BY LVSAVEDABS DESC LIMIT ".$this->_iListLimit."
            ";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                $iSort = 0;
                while( !$oRs->EOF ) {
                    $sArticleId = $oRs->fields['OXPARENTID'];
                    
                    $this->_lvAddToList( $sActionList, $sArticleId, $iSort );
                    $iSort++;
                    
                    $oRs->moveNext();
                }
            }
        }
    }
    
    
    /**
     * Fills latest list by given action id
     * 
     * @param void
     * @return void
     */
    protected function _lvFillLatest() { 
        $sActionList    = $this->_oLvConfig->getConfigParam( 'sLvListLatestId' );
        $sAttriId       = $this->_oLvConfig->getConfigParam( 'sLvListLatestAttributeId' );
        
        if ( $sActionList ) {
            $this->_lvEmptyList( $sActionList );
            $sTable     = getViewName( 'oxarticles' );
            $sJoinTable = getViewName( 'oxobject2attribute' );
            
            $sQuery = "
                SELECT DISTINCT
                    oa.OXPARENTID, 
                    o2a.OXVALUE
                FROM 
                    ".$sTable." oa
                LEFT JOIN 
                    ".$sJoinTable." o2a ON ( oa.OXID=o2a.OXOBJECTID AND o2a.OXATTRID='".$sAttriId."' )
                WHERE 
                    oa.OXPARENTID != '' AND
                    o2a.OXVALUE <= NOW()
                ORDER BY 
                    o2a.OXVALUE DESC LIMIT ".$this->_iListLimit."
            ";
            
            $oRs = $this->_oLvDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                $iSort = 0;
                while( !$oRs->EOF ) {
                    $sArticleId = $oRs->fields['OXPARENTID'];
                    
                    $this->_lvAddToList( $sActionList, $sArticleId, $iSort );
                    $iSort++;
                    
                    $oRs->moveNext();
                }
            }
        }
    }
    
    
    /**
     * Clears current entries to refill list
     * 
     * @param string $sActionList
     * @return void
     */
    protected function _lvEmptyList( $sActionList ) {
        $sQuery = "DELETE FROM oxactions2article WHERE OXACTIONID='".$sActionList."'";
        $this->_oLvDb->Execute( $sQuery );
    }
    
    
    
    /**
     * Adds given article to given list
     * 
     * @param string $sActionList
     * @param string $sArticleId
     * @return void
     */
    protected function _lvAddToList( $sActionList, $sArticleId, $iSort ) {
        $oUtilsObject = oxRegistry::get( 'oxUtilsObject' );
        $sNewId = $oUtilsObject->generateUId();
        
        $sQuery = "
            INSERT INTO oxactions2article
            (
                OXID,
                OXSHOPID,
                OXACTIONID,
                OXARTID,
                OXSORT
            )
            VALUES
            (
                '".$sNewId."',
                'oxbaseshop',
                '".$sActionList."',
                '".$sArticleId."',
                '".(string)$iSort."'
            )
        ";
        
        $this->_oLvDb->Execute( $sQuery );
    }
    
    
}

$oScript = new lvaffiliate_fill_toplists();
$oScript->start();