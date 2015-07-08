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
 * Description of lvaffiliate_reset_saved
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_reset_saved extends oxBase {
    
    /**
     * DB-Object
     * @var object
     */
    protected $_oLvDb = null;
    
    /**
     * Config object
     * @var object
     */
    protected $_oLvConfig = null;
    
    
    /**
     * Where it all begins...
     */
    public function start() { 
        $this->_oLvDb       = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $this->_oLvConfig   = $this->getConfig();
        
        $sQuery = "UPDATE oxarticles SET LVSAVED=0";
        $this->_oLvDb->Execute( $sQuery );
        
        $sQuery = "SELECT OXID, OXVARMINPRICE FROM oxarticles WHERE OXPARENTID=''";
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while( !$oRs->EOF ) {
                $sOxid          = $oRs->fields['OXID'];
                $dVarMinPrice   = (double)$oRs->fields['OXVARMINPRICE'];
                if ( $sOxid ) {
                    $this->_lvSetSaveAmount( $sOxid, $dVarminPrice );
                }
                $oRs->moveNext();
            }
        }
    }
    
    
    /**
     * Sets save price for this article
     * 
     * @param string $sOxid
     * @param double $dVarMinPrice
     * @return void
     */
    protected function _lvSetSaveAmount( $sOxid, $dVarMinPrice ) {
        $sQuery = "SELECT MAX(OXTPRICE) FROM oxarticles WHERE OXPARENTID='".$sOxid."'";
        
        $dMaxTPrice = $this->_oLvDb->GetOne( $sQuery );
        
        if ( $dMaxTPrice > 0 && $dVarMinPrice > 0 && $dMaxTPrice > $dVarMinPrice ) {
            $dSaved = $dMaxTPrice-$dVarMinPrice;
            $sQuery = "UPDATE oxarticles SET LVSAVED='".(string)$dSaved."' WHERE OXID='".$sOxid."' LIMIT 1";
            $this->_oLvDb->Execute( $sQuery );
        }
    }
    
}

$oScript = new lvaffiliate_reset_saved();
$oScript->start();
