<?php
/**
 * Description of lvtinymce_content_main
 *
 * @author andre
 */
class lvtinymce_content_main extends lvtinymce_content_main_parent {
    /**
     * Loads contents info, passes it to Smarty engine and
     * returns name of template file "content_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $sTemplate = parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        
        
        $oContent = oxNew( "oxcontent" );
        $sContent = "";
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oContent->loadInLang( $this->_iEditLang, $soxId );
            $sContent = $oContent->oxcontents__oxcontent->value;
        }
        // $this->_aViewData["editor"]  = $this->_generateTextEditor( "100%", 300, $oContent, "oxcontents__oxcontent", $sCSS);
        $oLvTinyMCE = oxNew( "lvtinymce" );
        $this->_aViewData["editor"] = $oLvTinyMCE->lvGetTinyMceEditor( "editor_oxcontents__oxcontent", $sContent, "oxcontents__oxcontent" );
        

        return $sTemplate;
    }
}
