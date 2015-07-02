<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'lvTinyMCE',
    'title'        => 'TinyMCE Editor',
    'description'  => array(
            'de'=>'WYSIWYG Editor f&uuml; die Admin Verwaltungsoberfläche',
            'en'=>'WYSIWYG Editor f&uuml; for Admin Interface',
     ),
    'thumbnail'    => '',
    'version'      => '1.0',
    'author'       => 'Liberavia',
    'extend'       => array(
        'article_main' => 'lvTinyMCE/admin/lvtinymce_article_main',
        'category_text' => 'lvTinyMCE/admin/lvtinymce_category_text',
        'content_main' => 'lvTinyMCE/admin/lvtinymce_content_main',
    ),
    'files' => array(
        'lvtinymce' => 'lvTinyMCE/core/lvtinymce.php',
    ),    
);