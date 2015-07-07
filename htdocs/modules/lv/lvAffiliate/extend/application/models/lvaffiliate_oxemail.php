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
 * Description of lvaffiliate_oxemail
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxemail extends lvaffiliate_oxemail_parent {
    
    /**
     * Sends price alarm to customer.
     * Returns true on success.
     *
     * @param string       $sRecipient      email
     * @param oxPriceAlarm $oAlarm          oxPriceAlarm object
     * @param string       $sBody           optional mail body
     * @param bool         $sReturnMailBody returns mail body instead of sending
     *
     * @return bool
     */
    public function lvSendPricealarmToCustomer($sRecipient, $oAlarm, $sBody = null, $sReturnMailBody = null)
    {
        $this->_clearMailer();

        $oShop = $this->_getShop();
        $oLang = oxRegistry::getLang();

        if ($oShop->getId() != $oAlarm->oxpricealarm__oxshopid->value) {
            $oShop = oxNew("oxshop");
            $oShop->load($oAlarm->oxpricealarm__oxshopid->value);
            $this->setShop($oShop);
        }

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($oShop);

        // create messages
        $oSmarty = $this->_getSmarty();

        $this->setViewData("product", $oAlarm->getArticle());
        $this->setViewData("oPriceAlarm", $oAlarm);
        $this->setViewData("bidprice", $oAlarm->getFProposedPrice());
        $this->setViewData("currency", $oAlarm->getPriceAlarmCurrency());

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setRecipient($sRecipient, $sRecipient);
        $oArticle = $oAlarm->getArticle();
        
        $sPriceAlarmFor = $oLang->translateString( 'LVPRICEALARMFOR' );
        $sTitle         = $oArticle->oxarticles__oxtitle->value;
        $sPriceAlarmIn  = $oLang->translateString( 'LVIN' );
        
        $sSubject = $sPriceAlarmFor." ".$sTitle." ".$sPriceAlarmIn." ".$oShop->oxshops__oxname->value;
        
        $this->setSubject( $sSubject );

        if ($sBody === null) {
            $sTemplatePath = getShopBasePath()."modules/lv/lvAffiliate/application/views/frontend/tpl/email/html/lvpricealarm_customer.tpl";
            $sBody = $oSmarty->fetch( $sTemplatePath );
        }

        $this->setBody($sBody);

        $this->addAddress($sRecipient, $sRecipient);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        if ($sReturnMailBody) {
            return $this->getBody();
        } else {
            return $this->send();
        }
    }
    
}
