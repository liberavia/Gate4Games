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
 * Description of lvnewsevents
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvnewsevents  {
    
    /**
     * Actions should take place on activation
     * 
     * @param void
     * @retrurn void
     */
    public static function onActivate() {
        // add additional field in oxnews table
        self::addNewsFields();
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
        self::removeNewsFields();
        self::generateViews();
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function addNewsFields() {
        $oDb = oxDb::getDb();
        $sQuery = "ALTER TABLE `oxnews` ADD `LVTEASERTEXT` TEXT NOT NULL AFTER `OXLONGDESC_3`, ADD `LVSEOURL` VARCHAR(2048) NOT NULL AFTER `LVTEASERTEXT`";
        $oDb->Execute( $sQuery );
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function removeNewsFields() {
        $oDb = oxDb::getDb();
        $sQuery = "ALTER TABLE `oxnews`  DROP `LVTEASERTEXT`,  DROP `LVSEOURL`";
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
