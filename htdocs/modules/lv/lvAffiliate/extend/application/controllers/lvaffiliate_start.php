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
     * Template variable getter. Returns start page top seller
     *
     * @return array
     */
    public function lvGetTopSeller()
    {
        if ($this->_aArticleList === null) {
            $this->_aArticleList = array();
            if ($this->_getLoadActionsParam()) {
                // start list
                $oArtList = oxNew('oxarticlelist');
                $oArtList->loadActionArticles('lvtopseller');
                if ($oArtList->count()) {
                    $this->_aArticleList = $oArtList;
                }
            }
        }

        return $this->_aArticleList;
    }
    
    /**
     * Template variable getter. Returns start page top sale offers
     *
     * @return array
     */
    public function lvGetTopSale()
    {
        if ($this->_aArticleList === null) {
            $this->_aArticleList = array();
            if ($this->_getLoadActionsParam()) {
                // start list
                $oArtList = oxNew('oxarticlelist');
                $oArtList->loadActionArticles('lvtopsale');
                if ($oArtList->count()) {
                    $this->_aArticleList = $oArtList;
                }
            }
        }

        return $this->_aArticleList;
    }
}
