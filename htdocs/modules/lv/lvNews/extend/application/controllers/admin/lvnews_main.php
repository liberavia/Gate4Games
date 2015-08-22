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
 * Description of lvnews_main
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvnews_main extends lvnews_main_parent {
    
    /**
     * Seo Url
     * @var string
     */
    protected $_sLvSeoUrl = '';
    
    
    public function render() {
        $sTemplate = parent::render();
        
        $sOxid = $this->getEditObjectId();
        $oNews = oxNew( "oxnews" );
        
        if ( $sOxid != "-1" && isset( $sOxid ) ) {
            // load object
            $oNews->loadInLang( $this->_iEditLang, $sOxid );
            
            // get/generate dynamic seo address for this news article
            $oSeoEncoder        = oxNew( 'oxseoencoder' );
            $sStdUrl            = "index.php?cl=lvnews_details&lvnewsid=".$sOxid;
            $sSeoUrlBase        = "News/".$oNews->oxnews__oxshortdesc->value;
            $this->_sLvSeoUrl   = $oSeoEncoder->lvGetDynamicNewsUrl( $sStdUrl, $sSeoUrlBase, $sOxid, $this->_iEditLang );
        }
        
        // adding editor component for teasertext
        $sEditorHtml                = $this->_generateTextEditor( "100%", 200, $oNews, "oxnews__lvteasertext" );
        $sEditorHtml                = str_replace( '<textarea' , '<textarea name="editval[oxnews__lvteasertext]"', $sEditorHtml );
        $this->_aViewData["editor"] = $sEditorHtml;

        return $sTemplate;
    }
    
    public function lvGetSeoUrl() {
        return $this->_sLvSeoUrl;
    }
    
}
