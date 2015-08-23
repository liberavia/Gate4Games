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
 * Description of lvnews_overview
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvnews_overview extends oxUBase {
    /**
     * Newslist
     *
     * @var object
     */
    protected $_oNewsList = null;
    /**
     * Current class login template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'lvnews_overview.tpl';

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;


    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Number of possible pages.
     *
     * @var integer
     */
    protected $_iCntPages = null;
    
    
    /**
     * Return generic page title
     * 
     * @param void
     * @return void
     */
    public function getPageTitle() {
        $oConfig        = $this->getConfig();
        $oLang          = oxRegistry::getLang();
        $iBaseLanguage  = $oLang->getBaseLanguage();
        $aPageTitles    = $oConfig->getConfigParam( 'aLvPageTitles' );
        
        if ( isset( $aPageTitles[$iBaseLanguage] ) ) {
            $sPageTitle = $aPageTitles[$iBaseLanguage];
        }
        else {
            $sPageTitle = "News";
        }
        
        return $sPageTitle;
    }
    
    
    /**
     * Return generic page meta description
     * 
     * @param void
     * @return void
     */
    public function getMetaDescription() {
        $oConfig        = $this->getConfig();
        $oLang          = oxRegistry::getLang();
        $iBaseLanguage  = $oLang->getBaseLanguage();
        $aPageMetaDesc  = $oConfig->getConfigParam( 'aLvPageMetaDesc' );
        
        if ( isset( $aPageMetaDesc[$iBaseLanguage] ) ) {
            $sPageMetaDesc = $aPageMetaDesc[$iBaseLanguage];
        }
        else {
            $sPageMetaDesc = "News";
        }
        
        return $sPageMetaDesc;
    }
    

    /**
     * Return generic page meta keywords
     * 
     * @param void
     * @return void
     */
    public function getMetaKeywords() {
        $oConfig        = $this->getConfig();
        $oLang          = oxRegistry::getLang();
        $iBaseLanguage  = $oLang->getBaseLanguage();
        $aPageMetaKey   = $oConfig->getConfigParam( 'aLvPageMetaKey' );
        
        if ( isset( $aPageMetaKey[$iBaseLanguage] ) ) {
            $sPageMetaKey = $aPageMetaKey[$iBaseLanguage];
        }
        else {
            $sPageMetaKey = "News";
        }
        
        return $sPageMetaKey;
    }

    /**
     * Template variable getter. Returns newslist
     *
     * @return object
     */
    public function lvGetNews()
    {
        if ($this->_oNewsList === null) {
            $this->_oNewsList = false;

            $iPerPage = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
            $iPerPage = $iPerPage ? $iPerPage : 10;

            $oActNews = oxNew('oxnewslist');

            if ($iCnt = $oActNews->getCount()) {

                $this->_iCntPages = round($iCnt / $iPerPage + 0.49);

                $oActNews->loadNews($this->getActPage() * $iPerPage, $iPerPage);
                $this->_oNewsList = $oActNews;
            }
        }

        return $this->_oNewsList;
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

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }

    /**
     * Page title
     *
     * @return string
     */
    public function lvGetTitle()
    {
        $oLang = oxRegistry::getLang();
        $iBaseLanguage = $oLang->getBaseLanguage();
        $sTranslatedString = $oLang->translateString('LATEST_NEWS_AND_UPDATES_AT', $iBaseLanguage, false);

        return $sTranslatedString . ' ' . $this->getConfig()->getActiveShop()->oxshops__oxname->value;
    }
    
    
}
