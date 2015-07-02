<?php
/**
 * Description of lvtinymce_category_text
 *
 * @author andre
 */
class lvtinymce_category_text extends lvtinymce_category_text_parent {
    
    /**
     * Loads category object data, pases it to Smarty engine and returns
     * name of template file "category_text.tpl".
     *
     * @return string
     */
    public function render()
    {
        $sTemplate = parent::render();

        $oCategory = oxNew( 'oxcategory' );

        $soxId = $this->getEditObjectId();
        
        $sContent = "";
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $iCatLang = oxConfig::getParameter("catlang");

            if (!isset($iCatLang))
                $iCatLang = $this->_iEditLang;


            $oCategory->loadInLang( $iCatLang, $soxId );
            $sContent = $oCategory->oxcategories__oxlongdesc->value;
        }

        $oLvTinyMCE = oxNew( "lvtinymce" );

        $this->_aViewData["editor"] = $oLvTinyMCE->lvGetTinyMceEditor( "editor_oxcategories__oxlongdesc", $sContent , "oxcategories__oxlongdesc" );
        
        return $sTemplate;
    }
    
}
