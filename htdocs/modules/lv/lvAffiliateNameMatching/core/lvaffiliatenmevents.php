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
 * Description of lvaffiliatenmevents
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliatenmevents {
    
    /**
     * Actions should take place on activation
     * 
     * @param void
     * @retrurn void
     */
    public static function onActivate() {
        // add additional field in oxnews table
        self::addNameMatchingTable();
        self::generateViews();
    }
    
    /**
     * Actions should take place on deactivation
     * 
     * @param void
     * @retrurn void
     */
    public static function onDeactivate() {
        // add additional field in oxnews table
    }
    
    
    /**
     * Method creates needed table for name matchings
     * 
     * @param void
     * @return void
     */
    public static function addNameMatchingTable() {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sQuery ="
            CREATE TABLE IF NOT EXISTS `lvaffiliatenm` (
              `OXID` CHAR(32) NOT NULL ,
              `OXSHOPID` VARCHAR( 32 ) NOT NULL,
              `LVACTIVE` TINYINT(1) NOT NULL DEFAULT 1,
              `LVFROMNAME` varchar(255) NOT NULL,
              `LVTONAME` varchar(255) NOT NULL,
              PRIMARY KEY (`OXID`),
              KEY `LVNAMEMATCH` ( `LVFROMNAME`, `LVACTIVE`, `OXSHOPID` )
            ) ENGINE=MyISAM;
        ";
        
        $oDb->Execute( $sQuery );
        
        $sQuery = "
            ALTER TABLE `lvaffiliatenm` ADD UNIQUE (
                `LVFROMNAME`
            );            
        ";
        
        $oDb->Execute( $sQuery );
    }
    
    /**
     * Method generates views
     * 
     * @param void
     * @return void
     */
    public static function generateViews() {
	$oShop = oxRegistry::get( 'oxshop' );
	$oShop->generateViews();
    }
    
}
