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
 * Description of lvckeditor
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvckeditor extends oxBase {
    
    /**
     * Relative path to kcfinder
     * @var string
     */
    protected $_sMediaBrowserDirRel = 'modules/lv/lvCKEditor/lib/kcfinder/';
    
    /**
     * Relative path to ckeditor
     * @var string
     */
    protected $_sCKEditorPath = 'modules/lv/lvCKEditor/lib/ckeditor/ckeditor.js';

    /**
     * Relative path to ckeditor config
     * @var string
     */
    protected $_sCKEditorConfigPath = 'modules/lv/lvCKEditor/lib/ckeditor/config.js';
    
    
    
    /**
     * Returns HTML for CKEditor
     * 
     * @param int $iWidth
     * @param int $iHeight
     * @param object $oObject
     * @param string $sField
     * @param string $sStylesheet
     * @retrurn string
     */
    public function lvGetCKEditor( $sField ) {
        // define all settings
        $oConfig                    = $this->getConfig();
        $oSession                   = oxRegistry::getSession();
        $sEditorId                  = "editor_".$sField;
        $sShopUrl                   = $oConfig->getShopUrl();
        $sEditorUrl                 = $sShopUrl.$this->_sCKEditorPath;
        $sEditorConfigUrl           = $sShopUrl.$this->_sCKEditorConfigPath;
        $sUploadPathRel             = $oConfig->getConfigParam( 'sLvUploadPath' );
        $sMediaUploadDir            = getShopBasePath().$sUploadPathRel;
        $sMediaBrowserBrowse        = $sShopUrl.$this->_sMediaBrowserDirRel."browse.php?opener=ckeditor&type=files";
        $sMediaBrowserUpload        = $sShopUrl.$this->_sMediaBrowserDirRel."upload.php?opener=ckeditor&type=files";
        
        // include editor
        $sHtml = "";
        $sHtml .= '<script src="'.$sEditorUrl.'"></script>';
        $sHtml .= '<script src="'.$sEditorConfigUrl.'"></script>';
        $sHtml .= "
            <script>
                window.onload = function() {
                    CKEDITOR.replace( '".$sEditorId."', {
                        filebrowserBrowseUrl: '".$sMediaBrowserBrowse."',
                        filebrowserUploadUrl: '".$sMediaBrowserUpload."'
                    });
                };
            </script>            
        ";
        
        // put configuration for kcfinder into session
        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['KCFINDER']['uploadURL'] = $sUploadPathRel;
        
        return $sHtml;
    }
}
