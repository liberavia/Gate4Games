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
    
    
    /**
     * Due they were too stupid to put this code into model I needed to copy most of it from admin pricealarm send. Bravissimo :( 
     * 
     * @param void
     * @return void
     */
    public function lvCheckAndSendPricealarm( $iStart = false  ) {
        $myConfig   = $this->getConfig();
        $oDB        = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $sSelect = "select oxpricealarm.oxid, oxpricealarm.oxemail, oxpricealarm.oxartid, oxpricealarm.oxprice " .
                   "from oxpricealarm, oxarticles where oxarticles.oxid = oxpricealarm.oxartid " .
                   "and oxpricealarm.oxsended = '0000-00-00 00:00:00'";
        
        if ( $iStart === false && is_numeric( $iStart ) ) {
            $rs = $oDB->SelectLimit($sSelect, $myConfig->getConfigParam('iCntofMails'), $iStart);
        } else {
            $rs = $oDB->Execute($sSelect);
        }

        $iAllCntTmp = 0;

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $oArticle = oxNew("oxarticle");
                $oArticle->load($rs->fields['oxid']);
                if ($oArticle->getPrice()->getBruttoPrice() <= $rs->fields['oxprice']) {
                    $this->lvSendMail(
                        $rs->fields['oxemail'],
                        $rs->fields['oxartid'],
                        $rs->fields['oxid'],
                        $rs->fields['oxprice']
                    );
                    $iAllCntTmp++;
                }
                $rs->moveNext();
            }
        }
    }
    
    
    /**
     * creates and sends email with pricealarm information
     * LV: Copy of pricealarm_send::sendeMail
     *
     * @param string $sEMail        email address
     * @param string $sProductID    product id
     * @param string $sPricealarmID price alarm id
     * @param string $sBidPrice     bidded price
     */
    public function lvSendMail($sEMail, $sProductID, $sPricealarmID, $sBidPrice)
    {
        $myConfig = $this->getConfig();
        $oAlarm = oxNew("oxpricealarm");
        $oAlarm->load($sPricealarmID);

        $oLang = oxRegistry::getLang();
        $iLang = (int) $oAlarm->oxpricealarm__oxlang->value;

        $iOldLangId = $oLang->getTplLanguage();
        $oLang->setTplLanguage($iLang);

        $oEmail = oxNew('oxemail');
        $blSuccess = (int) $oEmail->lvSendPricealarmToCustomer($sEMail, $oAlarm);

        $oLang->setTplLanguage($iOldLangId);

        if ($blSuccess) {
            $oAlarm->oxpricealarm__oxsended = new oxField(date("Y-m-d H:i:s"));
            $oAlarm->save();
        }
    }
    
    
}
