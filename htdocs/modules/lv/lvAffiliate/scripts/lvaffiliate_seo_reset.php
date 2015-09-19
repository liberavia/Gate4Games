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
 * Description of lvaffiliate_seo_reset
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


class lvaffiliate_seo_reset extends oxBase {
    
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
     * Start trigger for script
     * 
     * @param void
     * @return void
     */
    public function start() {
        $this->_oLvConfig           = $this->getConfig();
        $this->_oLvDb               = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $this->_lvTriggerSeoReset( 'oxarticle' );
    }
    
    
    /**
     * Method triggers reset of oxseodata optional using a certain type
     * 
     * @param string $sType
     * @return void
     */
    protected function _lvTriggerSeoReset( $sType = "" ) {
        if ( $sType == "" ) {
            $sQuery = "TRUNCATE TABLE oxseo";
        }
        else {
            $sQuery = "DELETE FROM oxseo WHERE OXTYPE=".$this->_oLvDb->quote( $sType );
        }
        
        $this->_oLvDb->Execute( $sQuery );
    }
}

$oScript = new lvaffiliate_seo_reset();
$oScript->start();
