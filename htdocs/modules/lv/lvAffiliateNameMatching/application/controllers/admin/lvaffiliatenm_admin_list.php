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
 * Description of lvaffiliatenm_admin_list
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliatenm_admin_list extends lvaffiliatenm_admin_list_parent {
    
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'lvaffiliatenm_admin_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'lvaffiliatenm';

	/**
     * Sets default list sorting field (fctitle) and executes parent method parent::Init().
     *
     * @return null
     */
    public function init()
    {
        $oConfig = $this->getConfig();
        $this->_sDefSort = "lvfromname";
        $sSortCol = $oConfig->getRequestParameter( 'sort' );
        
        if ( !$sSortCol || $sSortCol == $this->_sDefSort ) {
            $this->_blDesc = false;
        }

        parent::Init();
    }
    
	/**
	 * (non-PHPdoc)
	 * @see oxAdminList::render()
	 */
    public function render ()
    {
        parent::render();

        $myConfig = $this->getConfig();
	
        $this->_aViewData['nameconcat'] = "][";
        $this->_aViewData['where'] = $this->_lvGetFilterValues();

        return $this->_sThisTemplate;
    }
	
    /**
     * returns the values which were used for filtering the list
     *
     * @return array
     */
    protected function _lvGetFilterValues() {
        $aReturn = array();
        $aWhere = $this->buildWhere();
        
        foreach ( $aWhere as $sKey=>$sValue ) {
            $aSplittedKey   = explode( ".", $sKey );
            $sValue         = str_replace( "%", "", $sValue );
            
            $aReturn[$aSplittedKey[0]][$aSplittedKey[1]] = $sValue;
        }
        
        return $aReturn;
    }
	
    
    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @param void
     * @return array
     */
    public function buildWhere()
    {
    	$aWhere     = parent::buildWhere();
    	$sShopId    = $this->getConfig()->getShopId();
    	
    	$aWhere['lvaffiliatenm.oxshopid'] = $sShopId;
        
    	$this->_aWhere = $aWhere;

    	return $this->_aWhere;
    }
    
    
}
