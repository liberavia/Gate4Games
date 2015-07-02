<?php
/**
 * Description of lvtinymce_article_main
 *
 * @author andre
 */
class lvtinymce_article_main extends lvtinymce_article_main_parent {
    
    /**
     * Loads article parameters and passes them to Smarty engine, returns
     * name of template file "article_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $sTemplate = parent::render();
        
        $oArticle = oxNew( 'oxarticle' );

        $soxId = $this->getEditObjectId();
        
        $sContent = "";
        if ( $soxId && $soxId != "-1" ) {
            $oArticle->loadInLang( $this->_iEditLang, $soxId );
            $sContent = $oArticle->getLongDescription()->getRawValue();
        }
        $oLvTinyMCE = oxNew( "lvtinymce" );
        $this->_aViewData["editor"] = $oLvTinyMCE->lvGetTinyMceEditor( "editor_oxarticles__oxlongdesc", $sContent, "oxarticles__oxlongdesc" );

        return $sTemplate;
    }
    
}
