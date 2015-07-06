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
 * Description of lvaffiliate_oxpricealarm
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxpricealarm extends lvaffiliate_oxpricealarm_parent {
    
    /**
     * Returns pricealarm article original price
     *
     * @return double
     */
    public function getPrice()
    {
        if ($this->_dPrice == null) {
            $this->_dPrice = false;
            $oArticle = $this->getArticle();
            
            if ( $oArticle->oxarticles__oxparentid->value == '' ) {
                $myUtils = oxRegistry::getUtils();
                $oThisCurr = $this->getPriceAlarmCurrency();

                // #889C - Netto prices in Admin
                // (we have to call $oArticle->getPrice() to get price with VAT)
                $dArtPrice = $oArticle->getVarMinPrice()->getBruttoPrice() * $oThisCurr->rate;
                $dArtPrice = $myUtils->fRound($dArtPrice);

                $this->_dPrice = $dArtPrice;
            }
            else {
                $this->_dPrice = parent::getPrice();
            }
        }

        return $this->_dPrice;
    }
    
}
