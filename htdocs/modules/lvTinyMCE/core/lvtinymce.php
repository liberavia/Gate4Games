<?php
/**
 * Description of lvtinymce_oxadmindetails
 *
 * @author andre
 */
class lvtinymce extends oxI18n {
    
    /**
     * Returns the tinymce Editor component with given data
     * 
     * @param string $sDbFieldName Name of the containing Database field
     * @param string $sContent Content of TinyMCE Editor
     * @param string $sContent Content of TinyMCE Editor
     * @return string Full HTML of editor component
     * @access public
     */
    public function lvGetTinyMceEditor( $sIdent ,$sContent, $sDbFieldName = "", $sWidth = "100%", $sHeight="300px" ) {
        $sEditor = "";
        $sEditor .= $this->_lvGetTinyMceEditorConfig();
        if ( $sDbFieldName ) {
            $sDbFieldName = 'name="editval['.$sDbFieldName.']"';
        }
        $sEditor .= '
            <textarea id="'.$sIdent.'" '.$sDbFieldName.' style="width:'.$sWidth.'; height:'.$sHeight.';">'.$sContent.'</textarea>
        ';
        
        return $sEditor;
    }
    
    /**
     * Returns inline script settings for tinymce editor component
     * 
     * @param void
     * @return string Inline JS Settings
     * @access protected
     */
    protected function _lvGetTinyMceEditorConfig() {
        $oConfig = $this->getConfig();
        $sShopUrl = $oConfig->getShopUrl();
        /**
         * @todo: currenty I used the full feature example, this should be all configurable
         */
        $sEditorConfig = '
            <script type="text/javascript" src="'.$sShopUrl.'modules/lvTinyMCE/lib/tiny_mce/tiny_mce.js"></script>
            <script type="text/javascript">
                tinyMCE.init({
                    // General options
                    mode : "textareas",
                    theme : "advanced",
                    plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

                    // Theme options
                    theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_statusbar_location : "bottom",
                    theme_advanced_resizing : true,

                    // Example content CSS (should be your site CSS)
                    content_css : "css/content.css",

                    // Drop lists for link/image/media/template dialogs
                    template_external_list_url : "lists/template_list.js",
                    external_link_list_url : "lists/link_list.js",
                    external_image_list_url : "lists/image_list.js",
                    media_external_list_url : "lists/media_list.js",
            ';
            $sEditorConfig .= "  
                    // Style formats
                    style_formats : [
                        {title : 'Bold text', inline : 'b'},
                        {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
                        {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
                        {title : 'Example 1', inline : 'span', classes : 'example1'},
                        {title : 'Example 2', inline : 'span', classes : 'example2'},
                        {title : 'Table styles'},
                        {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
                    ],
                });
            </script>
        ";
        
        return $sEditorConfig;
    }
    
    
}
