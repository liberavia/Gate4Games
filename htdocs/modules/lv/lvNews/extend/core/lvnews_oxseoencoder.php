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
 * Description of lvnews_oxseoencoder
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvnews_oxseoencoder extends lvnews_oxseoencoder_parent {
    
    
    /**
     * Returns and processes SEO-URL for news article
     * 
     * @param string $sStdUrl
     * @param string $sSeoUrl
     * @param string $sObjectId
     * @param int $iLang
     * 
     * @return string
     */
    public function lvGetDynamicNewsUrl( $sStdUrl, $sSeoUrl, $sObjectId, $iLang ) {
        startProfile("lvGetDynamicNewsUrl");
        $sDynNewsUrl = $this->_getFullUrl($this->_lvGetDynamicNewsUri( $sStdUrl, $sSeoUrl, $sObjectId, $iLang ), $iLang, strpos($sStdUrl, "https:") === 0);
        stopProfile("lvGetDynamicNewsUrl");

        return $sDynNewsUrl;
    }
    
    
    /**
     * Returns dynamic news object SEO URI
     *
     * @param string $sStdUrl standard url
     * @param string $sSeoUrl seo uri
     * @param string $sObjectId Id of news article
     * @param int    $iLang   active language
     *
     * @return string
     */
    protected function _lvGetDynamicNewsUri( $sStdUrl, $sSeoUrl, $sObjectId, $iLang ) {
        $iShopId = $this->getConfig()->getShopId();

        $sStdUrl = $this->_trimUrl($sStdUrl);
        $sSeoUrl = $this->_prepareUri($this->addLanguageParam($sSeoUrl, $iLang), $iLang);

        //load details link from DB
        $sOldSeoUrl = $this->_loadFromDb( 'dynamic', $sObjectId, $iLang );
        if ( $sOldSeoUrl === $sSeoUrl ) {
            $sSeoUrl = $sOldSeoUrl;
        } 
        else {

            if ( $sOldSeoUrl ) {
                // old must be transferred to history
                $this->_copyToHistory($sObjectId, $iShopId, $iLang, 'dynamic');
            }

            // creating unique
            $sSeoUrl = $this->_processSeoUrl($sSeoUrl, $sObjectId, $iLang);

            // inserting
            $this->_saveToDb( 'dynamic', $sObjectId, $sStdUrl, $sSeoUrl, $iLang, $iShopId );
        }

        return $sSeoUrl;
    }
}
