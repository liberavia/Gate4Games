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
 * Description of lvattr_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvattr_oxarticle extends lvattr_oxarticle_parent {
    /**
     * Configuration for compatibility icons
     * @var array
     */
    protected $_aLvCompatibilityValue2Icon = null;
    
    /**
     * Configuration for age icons
     * @var array
     */
    protected $_aLvAgeValue2Icon = null;
    
    /**
     * Returns all compatibility informaton array 
     * 
     * @param void
     * @return array
     */
    public function lvGetCompatibilityInformation() {
        $aCompatibilityIcons    = array();
        $oLang                  = oxRegistry::getLang();
        $iCurrentLangId         = $oLang->getBaseLanguage();
        $oViewConf              = oxRegistry::get( 'oxViewConfig' );
        
        if ( $this->_aLvCompatibilityValue2Icon === null ) {
            $this->_lvSetCompatibilityConfiguration();
        }

        $aAttributes = $this->getAttributes();
        
        foreach ( $this->_aLvCompatibilityValue2Icon as $sAttrOxid=>$sAttrConfig ) {
            // attribute set?
            if ( isset( $aAttributes[$sAttrOxid] ) ) {
                // get value
                $sAttributeValue = $aAttributes[$sAttrOxid]->oxattribute__oxvalue->value;
                
                // split current configuration
                $aConfigSections = explode( '|', $sAttrConfig );
                foreach ( $aConfigSections as $sConfigValues ) {
                    $aConfigValues = explode( ':', $sConfigValues );
                    if ( count( $aConfigValues ) == 3 ) {
                        $sCheckValue    = trim( $aConfigValues[0] );
                        $sIconName      = trim( $aConfigValues[1] );
                        $sLangConst     = trim( $aConfigValues[2] );
                        
                        if ( $sAttributeValue == $sCheckValue ) {
                            // we have a match!
                            $sTitle         = $oLang->translateString( $sLangConst, $iCurrentLangId );
                            $sModuleUrl     = $oViewConf->getModuleUrl( 'lvAttributes' );
                            $sModuleImgPath = "out/img/";
                            $sIconUrl       = $sModuleUrl.$sModuleImgPath.$sIconName;
                            $sLvAttrDesc    = $aAttributes[$sAttrOxid]->oxattribute__lvattrdesc->value;
                            
                            $aCompatibilityIcons[$sAttrOxid]['iconurl']         = $sIconUrl;
                            $aCompatibilityIcons[$sAttrOxid]['title']           = $sTitle;
                            $aCompatibilityIcons[$sAttrOxid]['description']     = $sLvAttrDesc;
                            $aCompatibilityIcons[$sAttrOxid]['targetsys_trans'] = $this->_lvGetTargetSystemTranslationByAttrId( $sAttrOxid );
                        }
                    }
                }
            }
        }
        
        return $aCompatibilityIcons;
    }
    
    
    /**
     * Template getter returns an array with age icons
     * 
     * @param void
     * @return array
     */
    public function lvGetAgeIcons() {
        $aAgeIcons    = array();
        $oLang                  = oxRegistry::getLang();
        $iCurrentLangId         = $oLang->getBaseLanguage();
        $oViewConf              = oxRegistry::get( 'oxViewConfig' );
        
        if ( $this->_aLvAgeValue2Icon === null ) {
            $this->_lvSetAgeConfiguration();
        }
        
        $aAttributes = $this->getAttributes();
        
        foreach ( $this->_aLvAgeValue2Icon as $sAttrOxid=>$sAttrConfig ) {
            // attribute set?
            if ( isset( $aAttributes[$sAttrOxid] ) ) {
                // get value
                $sAttributeValue = $aAttributes[$sAttrOxid]->oxattribute__oxvalue->value;
                // split current configuration
                $aConfigSections = explode( '|', $sAttrConfig );
                foreach ( $aConfigSections as $sConfigValues ) {
                    $aConfigValues = explode( ':', $sConfigValues );
                    if ( count( $aConfigValues ) == 3 ) {
                        $sCheckValue    = trim( $aConfigValues[0] );
                        $sIconName      = trim( $aConfigValues[1] );
                        $sLangConst     = trim( $aConfigValues[2] );
                        
                        if ( $sAttributeValue == $sCheckValue ) {
                            // we have a match!
                            $sTitle         = $oLang->translateString( $sLangConst, $iCurrentLangId );
                            $sModuleUrl     = $oViewConf->getModuleUrl( 'lvAttributes' );
                            $sModuleImgPath = "out/img/";
                            $sIconUrl       = $sModuleUrl.$sModuleImgPath.$sIconName;
                            
                            $aAgeIcons[$sAttrOxid]['url'] = $sIconUrl;
                            $aAgeIcons[$sAttrOxid]['title'] = $sTitle;
                        }
                    }
                }
            }
        }
        
        return $aAgeIcons;
    }

    
    /**
     * Sets the configuration for compatibility icons
     * 
     * @param void
     * @return void
     */
    protected function _lvSetCompatibilityConfiguration() {
        $oConfig = $this->getConfig();
        
        $aCompatibilityConfig = $oConfig->getConfigParam( 'aLvCompatibilityValue2Icon' );
        
        if ( is_array( $aCompatibilityConfig ) && count( $aCompatibilityConfig ) > 0 ) {
            $this->_aLvCompatibilityValue2Icon = $aCompatibilityConfig;
        }
        else {
            $this->_aLvCompatibilityValue2Icon = array();
        }
    }
    

    /**
     * Sets the configuration for age icons
     * 
     * @param void
     * @return void
     */
    protected function _lvSetAgeConfiguration() {
        $oConfig = $this->getConfig();
        
        $aAgeConfig = $oConfig->getConfigParam( 'aLvAgeValue2Icon' );
        
        if ( is_array( $aAgeConfig ) && count( $aAgeConfig ) > 0 ) {
            $this->_aLvAgeValue2Icon = $aAgeConfig;
        }
        else {
            $this->_aLvAgeValue2Icon = array();
        }
    }
    
    
    /**
     * Returns translated target system text by attribute id
     * 
     * @param string $sAttrOxid
     * @return string
     */
    protected function _lvGetTargetSystemTranslationByAttrId( $sAttrOxid ) {
        $sReturn                = '';
        
        $sTranslateTargetSystem = '';
        switch( $sAttrOxid ) {
            case 'CompatibilityTypeWine':
                $sTranslateTargetSystem = "LV_ATTR_WINE";
                break;
            case 'CompatibilityTypeWin':
                $sTranslateTargetSystem = "LV_ATTR_WINE";
                break;
            case 'CompatibilityTypeMac':
                $sTranslateTargetSystem = "LV_ATTR_MAC";
                break;
            case 'CompatibilityTypeLin':
                $sTranslateTargetSystem = "LV_ATTR_LIN";
                break;
            case 'CompatibilityTypePOL':
                $sTranslateTargetSystem = "LV_ATTR_POL";
                break;
        }
        
        if ( $sTranslateTargetSystem != '' ) {
            $oLang                  = oxRegistry::getLang();
            $sLangTargetSystem      = $oLang->translateString( $sTranslateTargetSystem );
            $sLangSysReqPrefix      = $oLang->translateString( 'LV_ATTR_SYSREQUIREMENTS_FOR' );
            
            $sReturn = $sLangSysReqPrefix." ".$sLangTargetSystem;
        }
        
        return $sReturn;
    }
}
