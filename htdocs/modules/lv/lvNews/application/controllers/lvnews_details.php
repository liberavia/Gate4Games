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
 * Description of lvnews_details
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvnews_details extends oxUBase {
    
    /**
     * Instance of news object
     * @var object
     */
    protected $_oNews = null;

    /**
     * Current class login template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'lvnews_details.tpl';


    /**
     * Return generic page title
     * 
     * @param void
     * @return void
     */
    public function getPageTitle() {
        $oNews = $this->lvGetNewsArticle();
        $sHeadline = $oNews->oxnews__oxshortdesc->value;
        
        $sPageTitle = $sHeadline;
        
        return $sPageTitle;
    }
    
    
    /**
     * Return generic page meta description
     * 
     * @param void
     * @return void
     */
    public function getMetaDescription() {
        $oNews = $this->lvGetNewsArticle();
        $sTeaserText = $oNews->lvGetPlainTeaserText();
        
        $sMetaDescription = $sTeaserText;
        
        return $sPageTitle;
    }
    

    /**
     * Return generic page meta keywords
     * 
     * @param void
     * @return void
     */
    public function getMetaKeywords() {
        $oNews = $this->lvGetNewsArticle();
        $sHeadline = $oNews->oxnews__oxshortdesc->value;
        $aHeadlineParts = explode( " ", $sHeadline );
        $sKeywords = implode( ",", $aHeadlineParts );
        
        return $sKeywords;
    }

    
    /**
     * Template variable getter. Returns news
     *
     * @return object
     */
    public function lvGetNewsArticle() {
        if ( $this->_oNews === null ) {
            $oConfig = $this->getConfig();
            $sNewsId = $oConfig->getRequestParameter( 'lvnewsid' );
            if ( $sNewsId ) {
                $this->_oNews = oxNew( 'oxnews' );
                $this->_oNews->load( $sNewsId );
            }
        }
        
        return $this->_oNews;
    }


    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath = array();

        $oLang = oxRegistry::getLang();
        $iBaseLanguage = $oLang->getBaseLanguage();
        $sTranslatedString = $oLang->translateString('LATEST_NEWS_AND_UPDATES_AT', $iBaseLanguage, false);

        $aPath['title'] = $sTranslatedString . ' ' . $this->getConfig()->getActiveShop()->oxshops__oxname->value;
        $aPath['link'] = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }

}
