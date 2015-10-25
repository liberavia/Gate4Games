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
class lvigdbevents  {
    
    /**
     * Actions should take place on activation
     * 
     * @param void
     * @retrurn void
     */
    public static function onActivate() {
        // add additional field in oxnews table
        self::addIGDBFields();
        self::addIGDBTable();
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
        self::removeIGDBFields();
        self::generateViews();
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function addIGDBFields() {
        $oUtils             = oxRegistry::getUtils();
        $oDb                = oxDb::getDb();
        $sTable             = 'oxarticles';
        $aFields            = array( 'LVIGDB_ID'=>'INT(11)', 'LVIGDB_RELEASE_DATE'=>'DATE', 'LVIGDB_RATING'=>'DOUBLE', 'LVIGDB_RELEVANCE'=>'INT(11)', 'LVIGDB_LAST_UPDATED'=>'DATE' );
        $blFieldsExisting   = self::checkFieldsExisting( $sTable, $aFields );
        if ( !$blFieldsExisting ) {
            foreach ( $aFields as $sField=>$sType ) {
                $sQuery = "ALTER TABLE `".$sTable."` ADD `".$sField."` ".$sType." NOT NULL";
                $oUtils->writeToLog( $sQuery."\n", 'lvigdb_install.log' );
                $oDb->Execute( $sQuery );
            }
        }
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function removeIGDBFields() {
        $oDb                = oxDb::getDb();
        $sTable             = 'oxarticles';
        $aFields            = array( 'LVIGDB_ID'=>'INT(11)', 'LVIGDB_RELEASE_DATE'=>'DATE', 'LVIGDB_RATING'=>'DOUBLE', 'LVIGDB_RELEVANCE'=>'INT(11)', 'LVIGDB_LAST_UPDATED'=>'DATE' );
        $blFieldsExisting   = self::checkFieldsExisting( $sTable, $aFields );
        
        if ( $blFieldsExisting ) {
            foreach ( $aFields as $sField=>$sType ) {
                $sQuery = "ALTER TABLE `".$sTable."` DROP `".$sField;
                $oDb->Execute( $sQuery );
            }
        }
    }
    

    
    /**
     * Performs adding needed table
     * 
     * @param void
     * @return void
     */
    public static function addIGDBTable() {
        $oUtils             = oxRegistry::getUtils();
        $oDb                = oxDb::getDb();
        $sQuery = "
            CREATE TABLE IF NOT EXISTS `lvigdb` 
            ( 
                `LVIGDBID` INT(11) NOT NULL , 
                `LVIGDB_NAME` VARCHAR(255) NOT NULL , 
                `LVIGDB_SLUG` VARCHAR(255) NOT NULL , 
                `LVIGDB_RATING` DOUBLE NOT NULL , 
                `LVIGDB_RELEASE_DATE` DATE NOT NULL , 
                `LVIGDB_SUMMARY` TEXT NOT NULL , 
                `LVIGDB_GENRES` VARCHAR(255) NOT NULL , 
                `LVIGDB_THEMES` VARCHAR(255) NOT NULL , 
                `LVIGDB_COMPANIES` TEXT NOT NULL , 
                `LVIGDB_COVER` VARCHAR(255) NOT NULL , 
                `LVIGDB_SCREENSHOTS` TEXT NOT NULL , 
                `LVIGDB_VIDEOS` TEXT NOT NULL , 
                `LVIGDB_LAST_UPDATED` DATE NOT NULL , 
                PRIMARY KEY (`LVIGDBID`), 
                INDEX (`LVIGDB_NAME`)
            ) ENGINE = InnoDB;            
        ";
        
        $oUtils->writeToLog( $sQuery."\n", 'lvigdb_install.log' );
        $oDb->Execute( $sQuery );
    }

    
    /**
     * Method checks if ALL of the given fields of the given table are existing
     * 
     * @param string $sTable
     * @param array $aFields
     * @return bool
     */
    public static function checkFieldsExisting( $sTable, $aFields ) {
        $aFields            = array_keys( $aFields );
        $oDb                = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $blFieldsExisting   = true;
        $aAvailableFields   = array();
        
        $sQuery = "SHOW fields FROM ".$sTable;
        $oRs = $oDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sAvailableField = $oRs->fields['Field'];
                if ( $sAvailableField ) {
                    $aAvailableFields[] = $sAvailableField;
                }
                $oRs->moveNext();
            }
        }
        foreach ( $aFields as $sField ) {
            if ( !in_array( $sField, $aAvailableFields ) ) {
                $blFieldsExisting = false;
            }
        }
        
        return $blFieldsExisting;
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
