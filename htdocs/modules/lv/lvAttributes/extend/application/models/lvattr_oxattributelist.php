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
 * Description of lvattr_oxattributelist
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvattr_oxattributelist extends lvattr_oxattributelist_parent {
    
    /**
     * Load attributes by article Id
     * LV: Added lvattrdesc field
     *
     * @param string $sArticleId article id
     * @param string $sParentId  article parent id
     */
    public function loadAttributes($sArticleId, $sParentId = null)
    {
        if ($sArticleId) {

            $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

            $sAttrViewName = getViewName('oxattribute');
            $sViewName = getViewName('oxobject2attribute');

            $sSelect = "select {$sAttrViewName}.`oxid`, {$sAttrViewName}.`oxtitle`, o2a.`oxvalue`, o2a.`lvattrdesc` from {$sViewName} as o2a ";
            $sSelect .= "left join {$sAttrViewName} on {$sAttrViewName}.oxid = o2a.oxattrid ";
            $sSelect .= "where o2a.oxobjectid = '%s' and o2a.oxvalue != '' ";
            $sSelect .= "order by o2a.oxpos, {$sAttrViewName}.oxpos";

            $aAttributes = $oDb->getAll(sprintf($sSelect, $sArticleId));

            if ($sParentId) {
                $aParentAttributes = $oDb->getAll(sprintf($sSelect, $sParentId));
                $aAttributes = $this->_mergeAttributes($aAttributes, $aParentAttributes);
            }

            $this->assignArray($aAttributes);
        }
    }
}
