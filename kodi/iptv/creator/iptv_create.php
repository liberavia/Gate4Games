#!/usr/bin/php
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
 * class for realising downloading extracting an building a daily new m3u list
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class iptv_creator {
    
    /**
     * Path to tmp dir
     * @var string
     */
    protected $_sTmpDir = "tmp/";
    
    /**
     * IPTV target template file
     * @var string
     */
    protected $_sTargetTemplateFile = "iptvdemain.tmpl";
    
    /**
     * Source of dynamic IPTV
     * @var string
     */
    protected $_sSourceDynList = "http://www.iptvsaga.com/play/tv2/playlist.m3u";
    
    /**
     * TmpFile for dynamic playlist
     * @var string
     */
    protected $_sDownloadTmpFile = "playlist.m3u";
    
    /**
     * Path to download file_sTargetFil
     * @var string
     */
    protected $_sDownloadPath = null;
    
    /**
     * Target file
     * @var string
     */
    protected $_sTargetFile = "iptvdemain.ink";
    
    /**
     * Mapping replacement to url indicator
     * @var array
     */
    protected $_aUrlMapping = array(
        '%%DYN_0003%%'  => '/1020/',
        '%%DYN_0004%%'  => '/1038/',
        '%%DYN_0005%%'  => '/1037/',
        '%%DYN_0006%%'  => '/1021/',
        '%%DYN_0007%%'  => '/1024/',
        '%%DYN_0008%%'  => '/1039/',
        '%%DYN_0009%%'  => '/1023/',
        '%%DYN_0011%%'  => '/1124/',
        '%%DYN_0012%%'  => '/1004/',
        '%%DYN_0013%%'  => '/1014/',
        '%%DYN_0020%%'  => '/1090/',
        '%%DYN_0021%%'  => '/1021/',
        '%%DYN_0022%%'  => '/1041/',
        '%%DYN_0023%%'  => '/1040/',
        '%%DYN_0028%%'  => '/104903/',
    );
    
    
    /**
     * Template lines
     * @var array
     */
    protected $_aTemplateLines = array();
    
    /**
     * List of Urls matching to mapping
     * @var array
     */
    protected $_aSourceUrls = array();
    
    /**
     * Trigger starting the job
     */
    public function start() {
        $this->_lvDownloadDynListToTmp(); 
        $this->_lvLoadTargetTemplate();
        $this->_lvLoadSourceDynListUrls();
        $this->_lvBuildTargetList();
    }
    
    
    /**
     * Building target file from template 
     * 
     * @param void
     * @return void
     */
    protected function _lvBuildTargetList() {
        // delete old file if existing
        $sTargetPath = __DIR__."/../".$this->_sTargetFile;
        if ( file_exists( $sTargetPath ) ) {
            unlink( $sTargetPath );
        }
        
        $oFile = fopen( $sTargetPath, 'w' );
        
        foreach ( $this->_aTemplateLines as $sCurrentTemplateLine ) {
            $sCurrentTemplateLine = trim( $sCurrentTemplateLine );
            foreach ( $this->_aUrlMapping as $sSearch=>$sReplace ) {
                if ( $sSearch == $sCurrentTemplateLine ) {
                    $sCurrentTemplateLine = $sReplace;
                }
            }
            
            fwrite( $oFile, $sCurrentTemplateLine."\n" );
        }
        
        fclose( $oFile );
    }
    
    
    
    /**
     * Loading template file
     * 
     * @param void
     */
    protected function _lvLoadTargetTemplate() {
        $oFile = fopen( $this->_sTargetTemplateFile, 'r' );
        
        while( !feof( $oFile ) ) {
            $sCurrentLine = fgets( $oFile, 1000 );
            $sCurrentLine = trim( $sCurrentLine );
            $this->_aTemplateLines[] = $sCurrentLine;
        }
        
        fclose( $oFile );
    }
    
    
    /**
     * Loading urls which are available in mapping
     * 
     * @param void
     * @return void
     */
    protected function _lvLoadSourceDynListUrls() {
        if ( file_exists( $this->_sDownloadPath ) ) {
            $oFile = fopen( $this->_sDownloadPath, 'r' );

            while( !feof( $oFile ) ) {
                $sCurrentLine = fgets( $oFile, 1000 );
                $sCurrentLine = trim( $sCurrentLine );
                // check if line is url
                
                if ( strpos( $sCurrentLine, 'http://' ) !== false ) {
                    // its an url, but is it in mapping list
                    foreach ( $this->_aUrlMapping as $sReplacement=>$sUrlPart ) {
                        if ( strpos( $sCurrentLine, $sUrlPart ) !== false ) {
                            $this->_aUrlMapping[$sReplacement] = $sCurrentLine;
                        }
                    }
                }
            }

            fclose( $oFile );
        }
    }
    
    
    /**
     * Downloading dynamic m3u file
     * 
     * @param void
     * @return void
     */
    protected function _lvDownloadDynListToTmp() {
        // copy foreign file to master folder
        $this->_sDownloadPath = __DIR__."/".$this->_sTmpDir.$this->_sDownloadTmpFile;
        
        if ( file_exists( $this->_sDownloadPath ) ) {
            unlink( $this->_sDownloadPath );
        }
        
        file_put_contents( $this->_sDownloadPath, file_get_contents( $this->_sSourceDynList ) );
    }
    
}

$oScript = new iptv_creator();
$oScript->start();
