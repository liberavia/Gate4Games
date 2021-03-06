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
 * Description of lvaffiliate_start
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_start extends lvaffiliate_start_parent {
    
    /**
     * List of top seller articles action
     * @var array
     */
    protected $_aTopSellerArticleList = null;

    /**
     * List of top sale articles action
     * @var array
     */
    protected $_aTopSaleArticleList = null;
    
    
    
    /**
     * Template variable getter. Returns start page top seller
     *
     * @return array
     */
    public function lvGetTopSeller()
    {
        if ($this->_aTopSellerArticleList === null) {
            $this->_aTopSellerArticleList = array();
            if ($this->_getLoadActionsParam()) {
                // start list
                $oArtList = oxNew('oxarticlelist');
                $oArtList->loadActionArticles('lvtopseller');
                if ($oArtList->count()) {
                    $this->_aTopSellerArticleList = $oArtList;
                }
            }
        }

        return $this->_aTopSellerArticleList;
    }
    
    /**
     * Template variable getter. Returns start page top sale offers
     *
     * @return array
     */
    public function lvGetTopSale()
    {
        if ($this->_aTopSaleArticleList === null) {
            $this->_aTopSaleArticleList = array();
            if ($this->_getLoadActionsParam()) {
                // start list
                $oArtList = oxNew('oxarticlelist');
                $oArtList->loadActionArticles('lvtopsale');
                if ($oArtList->count()) {
                    $this->_aTopSaleArticleList = $oArtList;
                }
            }
        }

        return $this->_aTopSaleArticleList;
    }
    
    
    /**
     * Template getter for top manufacturer list shall be shown in manufacturer slider
     * 
     * @param void
     * @return array
     */
    public function getManufacturerForSlider() {
        $oConfig    = $this->getConfig();
        $oDb        = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $blLvOnlyLoadTopManufacturer = (bool)$oConfig->getConfigParam( 'blLvOnlyLoadTopManufacturer' );
        
        if ( $blLvOnlyLoadTopManufacturer ) {
            $aList = array();
            $sViewName = getViewName( 'oxmanufacturers' );
            $sQuery = "SELECT OXID FROM ".$sViewName." WHERE LVTOPMANUFACTURER='1'";
            $oRs = $oDb->Execute( $sQuery );
            
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    $oManufacturer = oxNew( 'oxManufacturer' );
                    $oManufacturer->load( $oRs->fields['OXID'] );
                    $aList[] = $oManufacturer;
                    
                    $oRs->moveNext();
                }
            }
            
            $this->setManufacturerlist( $aList );
        }

        $aReturn = parent::getManufacturerForSlider();
        
        return $aReturn;
    }
}
