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
 * Description of lvaffiliate_oxarticlelist
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxarticlelist extends lvaffiliate_oxarticlelist_parent {
    
    /**
     * Creates SQL Statement to load Articles, etc.
     * LVAFFILIATE: Added check for language abbreviation
     *
     * @param string $sFields        Fields which are loaded e.g. "oxid" or "*" etc.
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     */
    protected function _getCategorySelect($sFields, $sCatId, $aSessionFilter) {
        $sSelect = parent::_getCategorySelect($sFields, $sCatId, $aSessionFilter);
        
        // split existing query to implement own stuff in between
        $aSelectParts           = explode( 'ORDER BY', $sSelect );
        $sSelectConditions      = $aSelectParts[0];
        $sSelectOrdering        = " ORDER BY ".$aSelectParts[1];
        $sArticleTable          = getViewName('oxarticles');
        $oLang                  = oxRegistry::getLang();
        $sLangAbbr              = $oLang->getLanguageAbbr();

        $sAddSelect = " AND ".$sArticleTable.".LVLANGABBR LIKE '%".$sLangAbbr."%' ";
        
        $sNewSelect = $sSelectConditions.$sAddSelect.$sSelectOrdering;

        return $sNewSelect;
    }
    
}
