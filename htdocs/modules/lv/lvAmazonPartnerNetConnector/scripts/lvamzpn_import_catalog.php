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
 * Description of lvamzpn_import_catalog
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


class lvamzpn_import_catalog extends oxBase {
    
    
    public function start() {
        $oConfig                    = $this->getConfig();
        $oApiConnector              = oxNew( 'lvamzpnapiconnector' );
        $oAffiliateImport           = oxNew( 'lvaffiliate_import' );
        $sVendorId                  = $oConfig->getConfigParam( 'sLvAmzPnVendorId' );
        $aLvAmzPnAWSService2Lang    = $oConfig->getConfigParam( 'aLvAmzPnAWSService2Lang' );
        $aLvAmzPnBrowseNodes        = $oConfig->getConfigParam( 'aLvAmzPnBrowseNodes' );
        $aLvAmzPnPriceRanges        = $oConfig->getConfigParam( 'aLvAmzPnPriceRanges' );
        $iMaxPageResult             = $oApiConnector->lvGetMaxPageResult();
        
        
        $oAffiliateImport->lvSetVendorId( $sVendorId );
        
        foreach ( $aLvAmzPnAWSService2Lang as $sLangAbbr=>$sAmazonService ) {
            $sBrowseNodes                   = $aLvAmzPnBrowseNodes[$sLangAbbr];
            $sPriceRanges                   = $aLvAmzPnPriceRanges[$sLangAbbr];

            $aBrowseNodes                   = explode( '|', $sBrowseNodes );
            $aPriceRanges                   = explode( '|', $sPriceRanges );
            
            $iMaxBrowseNodeIndex            = count( $aBrowseNodes )-1;
            $iMaxPriceRangeIndex            = count( $aPriceRanges )-1;
            
            // now that we know max indexes we can iterate through them
            for( $iBrowseNodeIndex=0; $iBrowseNodeIndex <= $iMaxBrowseNodeIndex; $iBrowseNodeIndex++ ) {
                
                for ( $iPriceRangeIndex=0; $iPriceRangeIndex <= $iMaxPriceRangeIndex; $iPriceRangeIndex++ ) {
                    
                    $iPageAmount    = $oApiConnector->lvGetSearchPageAmount( $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex );
                    
                    if ( $iPageAmount <= $iMaxPageResult ) {
                        for ( $iPage=1; $iPage<=$iPageAmount; $iPage++ ) {
                            $aSearchDetails = $oApiConnector->lvGetItemSearchAsinDetails( $sLangAbbr, $iBrowseNodeIndex, $iPriceRangeIndex, $iPage );
                            foreach ( $aSearchDetails as $aArticleData ) {
                                $oAffiliateImport->lvAddArticle( $aArticleData, $sLangAbbr );
                            }
                        }
                    }
                    
                }
                
            }
        }
    }
}

$oScript = new lvamzpn_import_catalog();
$oScript->start();