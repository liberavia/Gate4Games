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
        self::addStaticNewsOverviewLinks();
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
        self::removeStaticNewsOverviewLinks();
        self::generateViews();
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function addNewsFields() {
        $oDb                = oxDb::getDb();
        $sTable             = 'oxnews';
        $aFields            = array( 'LVTEASERTEXT' );
        $blFieldsExisting   = self::checkFieldsExisting( $sTable, $aFields );
        
        if ( !$blFieldsExisting ) {
            $sQuery = "ALTER TABLE `oxnews` ADD `LVTEASERTEXT` TEXT NOT NULL AFTER `OXLONGDESC_3`";
            $oDb->Execute( $sQuery );
        }
    }
    
    
    /**
     * Performs module activation query
     * 
     * @param void
     * @return void
     */
    public static function removeNewsFields() {
        $oDb                = oxDb::getDb();
        $sTable             = 'oxnews';
        $aFields            = array( 'LVTEASERTEXT' );
        $blFieldsExisting   = self::checkFieldsExisting( $sTable, $aFields );
        
        if ( $blFieldsExisting ) {
            $sQuery = "ALTER TABLE `oxnews`  DROP `LVTEASERTEXT`";
            $oDb->Execute( $sQuery );
        }
    }
    

    /**
     * Method checks if ALL of the given fields of the given table are existing
     * 
     * @param string $sTable
     * @param array $aFields
     * @return bool
     */
    public static function checkFieldsExisting( $sTable, $aFields ) {
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
    
    /**
     * Adds Overview seolinks for all languages
     * 
     * @param void
     * @return void
     */
    public static function addStaticNewsOverviewLinks() {
        $oLang              = oxRegistry::getLang();
        $oSeoEncoder        = oxRegistry::get( 'oxseoencoder' );
        
        $sOxid              = "lvnewsoverview";
        $sStdUrl            = "index.php?cl=lvnews_overview";
        $sSeoUrlBase        = "News";
        $aLanguageIds       = $oLang->getActiveShopLanguageIds();
        
        foreach ( $aLanguageIds as $sLangId ) {
            $iCurrentLangId = (int)$sLangId;
            $aReturnUrls[]  = $oSeoEncoder->lvGetDynamicNewsUrl( $sStdUrl, $sSeoUrlBase, $sOxid, $iCurrentLangId );
        }
        
        foreach ( $aReturnUrls as $sReturnUrl ) {
            echo "Added link: ".$sReturnUrl."<br>";
        }
    }
    
    
    /**
     * Removes formerly created Links
     * 
     * @param void
     * @return void
     */
    public static function removeStaticNewsOverviewLinks() {
        $oDb                = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sOxid              = "lvnewsoverview";
        
        $sQuery = "DELETE FROM oxseo WHERE OXIDENT='".$sOxid."'";
        $oDb->Execute( $sQuery );
    }

    
}
