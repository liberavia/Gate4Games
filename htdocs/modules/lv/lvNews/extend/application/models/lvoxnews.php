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
     * Returns details url of 
     * 
     * @param void
     * @return string
     */
    public function lvGetNewsDetailsLink() {
        $oConfig = $this->getConfig();
        
        $sShopUrl = $oConfig->getShopUrl();
        $sNewsUri = $this->oxnews__lvseourl->getRawValue();
        
        $sReturnUrl = $sShopUrl.$sNewsUri;
        
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
}
