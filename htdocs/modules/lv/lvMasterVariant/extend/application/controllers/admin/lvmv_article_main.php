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
 * Description of lvmv_article_main
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvmv_article_main extends lvmv_article_main_parent {
    
    /**
     * Saves changes of article parameters.
     * 
     * G4G: Before standard saving we need to check if master variant option has been choosen
     * If true we need to deactivate other possible checked master variants
     * This field desperately needs to be exclusive
     * 
     */
    public function save() {
        $oConfig = $this->getConfig();
        $aParams = $oConfig->getRequestParameter( "editval" );
        
        if ( $aParams['oxarticles__lvmastervariant'] == '1' ) {
            $this->_lvUnflagVariants();
        }
        
        parent::save(); 
    }
    
    
    /**
     * Unflags all variants related to parent article
     * 
     */
    protected function _lvUnflagVariants() {
        $sOxid  = $this->getEditObjectId();

        $oArticle = oxNew("oxarticle");
        $oArticle->setLanguage($this->_iEditLang);
        
        if ( $sOxid != "-1" ) {
            $oArticle->loadInLang( $this->_iEditLang, $sOxid );
            $oParent = $oArticle->getParentArticle();
            
            if ( $oParent ) {
                $sParentId  = $oParent->getId();
                $oDb        = oxDb::getDb();
                $sQuery = "UPDATE oxarticles SET LVMASTERVARIANT='0' WHERE OXPARENTID='".$sParentId."'";
                $oDb->Execute( $sQuery );
            }
        }
    }
}
