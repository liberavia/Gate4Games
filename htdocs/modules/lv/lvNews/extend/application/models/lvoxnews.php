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
 * Description of lvoxnews
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvoxnews extends lvoxnews_parent {

    /**
     * Method returns teasertext parsed through smarty
     * 
     * @param void
     * @return string
     */
    public function lvGetTeaserText() {
        $oUtilsView = oxRegistry::get("oxUtilsView");
        return $oUtilsView->parseThroughSmarty($this->oxnews__lvteasertext->getRawValue(), $this->getId() . $this->getLanguage(), null, true);
    }
    
    
    /**
     * Returns news teaser text as plain text
     * 
     * @param void
     * @return string
     */
    public function lvGetPlainTeaserText() {
        $sTeaserText = $this->lvGetTeaserText();
        $sPlainTeaserText = $this->lvGetPlain( $sTeaserText );
        
        return $sPlainTeaserText;
    }
    
    
    /**
     * Returns details url of 
     * 
     * @param void
     * @return string
     */
    public function lvGetNewsDetailsLink() {
        $sOxid              = $this->getId();
        $iCurrentLangId     = oxRegistry::getLang()->getBaseLanguage();        
        $oSeoEncoder        = oxNew( 'oxseoencoder' );
        $sStdUrl            = "index.php?cl=lvnews_details&lvnewsid=".$sOxid;
        $sSeoUrlBase        = "News/".$this->oxnews__oxshortdesc->value;
        $sReturnUrl         = $oSeoEncoder->lvGetDynamicNewsUrl( $sStdUrl, $sSeoUrlBase, $sOxid, $iCurrentLangId );
        
        return $sReturnUrl;
    }
    
    
    /**
     * Returns title of article
     * 
     * @param void
     * @return string
     */
    public function lvGetTitle() {
        return $this->oxnews__oxshortdesc->value;
    }
    
    
    /**
     * Returns plain text for given HTML-Code
     *
     * @param string
     * @return string
    */
    public function lvGetPlain( $sHtml ) {
        $sPlain = strip_tags( $sHtml );
        $sPlain = str_replace( '&nbsp;', '',$sPlain );
        $sPlain = html_entity_decode( $sPlain, ENT_QUOTES, "" );
        $sPlain = str_replace( '&bdquo;', '*', $sPlain );
        $sPlain = str_replace( '&ldquo;', '*', $sPlain );
        $sPlain = trim( preg_replace( '/\s+/', ' ', $sPlain ) );
        $sPattern = "/[^A-Za-z0-9\ ".$this->_lvGetAllowedChars()."\.\!\?\:\/\,\-\&]/";
        $sPlain = preg_replace( $sPattern, "",  $sPlain );
        
        return $sPlain;
    }

    
    /**
     * Returns allowed chars that should not be put directly into code
     *
     * @param void
     * @return string
     */
    protected function _lvGetAllowedChars() {
        $sChars = chr(252).chr(220).chr(228).chr(196).chr(246).chr(214).chr(223);
        
        return $sChars;
    }
    
}
