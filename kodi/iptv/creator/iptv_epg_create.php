#!/usr/bin/php
<?php
error_reporting(0);
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
 * Description of iptv_epg_create
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class iptv_epg_create {
    
    /**
     * Path to tmp dir
     * @var string
     */
    protected $_sTmpDir = "tmp/";
    
    /**
     * Path to download file_sTargetFil
     * @var string
     */
    protected $_sDownloadPath = null;
    
    /**
     * Agent for faking a real user
     * 
     * @var string
     */
    protected $_sAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36';
    
    /**
     * Define max age of epg source files in tmp
     * @var string
     */
    protected $_sThisMaxAgeTmpFile = "- 2 days";

    /**
     * Target file
     * @var string
     */
    protected $_sTargetFile = "iptvdemainepg.xml";
    
    
    /**
     * TMP Target file
     * @var string
     */
    protected $_sTargetFileTmp = "iptvdemainepg_tmp.xml";
    
    
    /**
     * TMP Target file descriptor
     * @var object
     */
    protected $_oTargetFileTmp = null;

    
    /**
     * List of all 
     * @var string
     */
    protected $_sLogoBaseUrl = 'http://kodi.gate4games.com/logos/';
    
    /**
     * Url to EPG
     * @var string
     */
    protected $_sCompleteEpgGz = 'http://www.vuplus-community.net/rytec/rytecxmltvGermany.gz';
    
    /**
     * Basename of epg
     * @var string
     */
    protected $_sDownloadedTmpBaseName = 'rytecxmltvGermany';
    
    /**
     * Source XML
     * @var object
     */
    protected $_oSourceXml = null;
    
    /**
     * List of all source information
     * 
     * @var array
     */
    protected $_aSources = array(
        'daserste.de'       => array( 'display-name'=>'Das Erste',          'url' => 'http://www.ard.de',           'icon' => 'ard_das_erste.png',          'sourceid' => 'ARD.de'                  ),
        'zdf.de'            => array( 'display-name'=>'ZDF',                'url' => 'http://www.zdf.de',           'icon' => 'zdf.png',                    'sourceid' => 'ZDF.de'                  ),
        'rtl.ch'            => array( 'display-name'=>'RTL',                'url' => 'http://www.rtl.de',           'icon' => 'rtl.png',                    'sourceid' => 'RTL.de'                  ),
        'sat1.ch'           => array( 'display-name'=>'SAT 1',              'url' => 'http://www.sat1.de',          'icon' => 'sat1.png',                   'sourceid' => 'Sat1.ch'                 ),
        'prosieben.ch'      => array( 'display-name'=>'Pro 7',              'url' => 'http://www.prosieben.de',     'icon' => 'pro_sieben.png',             'sourceid' => 'Pro7.ch'                 ),
        'vox.ch'            => array( 'display-name'=>'VOX',                'url' => 'http://www.vox.de',           'icon' => 'vox.png',                    'sourceid' => 'Vox.de'                  ),
        'rtl2.ch'           => array( 'display-name'=>'RTL II',             'url' => 'http://www.rtl2.de',          'icon' => 'rtl2.png',                   'sourceid' => 'RTL2.de'                 ),
        'superrtl.ch'       => array( 'display-name'=>'SuperRTL',           'url' => 'http://www.superrtl.de',      'icon' => 'kabel_eins.png',             'sourceid' => 'SuperRTL.de'             ),
        'kika.de'           => array( 'display-name'=>'KIKA',               'url' => 'http://www.kika.de',          'icon' => 'kika.png',                   'sourceid' => 'Kika.de'                 ),
        'disney.de'         => array( 'display-name'=>'Disney Channel',     'url' => 'http://www.disneychannel.de', 'icon' => 'disney_channel.png',         'sourceid' => 'disneychannel.de'        ),
        'nick.de'           => array( 'display-name'=>'Nickelodeon',        'url' => 'http://www.nickelodeon.de',   'icon' => 'nickelodeon.png',            'sourceid' => 'Nickelodeon.de'          ),
        'viva.de'           => array( 'display-name'=>'Comedy Central',     'url' => 'http://www.comedycentral.de', 'icon' => 'comedy_central.png',         'sourceid' => 'ComedyCentral/VIVA.de'   ),
        'ntv.de'            => array( 'display-name'=>'n-tv',               'url' => 'http://www.ntv.de',           'icon' => 'ntv.png',                    'sourceid' => 'ntv.de'                  ),
        'tagesschau24.de'   => array( 'display-name'=>'tagesschau24',       'url' => 'http://www.tagesschau.de',    'icon' => 'tagesschau24.png',           'sourceid' => 'tagesschau24'            ),
        'n24.de'            => array( 'display-name'=>'N24',                'url' => 'http://www.n24.de',           'icon' => 'n24.png',                    'sourceid' => 'n24.de'                  ),
        'zdfinfo.de'        => array( 'display-name'=>'ZDFinfo',            'url' => 'http://www.zdfinfo.de',       'icon' => 'zdf_info.png',               'sourceid' => 'ZDFinfo.de'              ),
        'phoenix.de'        => array( 'display-name'=>'Phoenix',            'url' => 'http://www.phoenix.de',       'icon' => 'phoenix.png',                'sourceid' => 'phoenix.de'              ),
        'zdfneo.de'         => array( 'display-name'=>'ZDFneo',             'url' => 'http://www.zdfneo.de',        'icon' => 'zdf_neo.png',                'sourceid' => 'ZDFneo.de'               ),
        'tele5.de'          => array( 'display-name'=>'Tele 5',             'url' => 'http://www.tele5.de',         'icon' => 'tele5.png',                  'sourceid' => 'Tele5.de'                ),
        'rtlnitro.de'       => array( 'display-name'=>'RTL NITRO',          'url' => 'http://www.rtlnitro.de',      'icon' => 'rtl_nitro.png',              'sourceid' => 'RTLNitro.de'             ),
        'prosiebenmaxx.de'  => array( 'display-name'=>'ProSieben Maxx',     'url' => 'http://www.pro7maxx.de',      'icon' => 'pro_sieben_maxx.png',        'sourceid' => 'ProSiebenMaxx.de'        ),
        'sixx.de'           => array( 'display-name'=>'sixx',               'url' => 'http://www.sixx.de',          'icon' => 'sixx.png',                   'sourceid' => 'Sixx.de'                 ),
        'arte.de'           => array( 'display-name'=>'arte',               'url' => 'http://www.arte.de',          'icon' => 'arte.png',                   'sourceid' => 'ARTE.de'                 ),
        '3sat.de'           => array( 'display-name'=>'3sat',               'url' => 'http://www.3sat.de',          'icon' => '3sat.png',                   'sourceid' => '3sat.de'                 ),
        'alpha.de'          => array( 'display-name'=>'ARD alpha',          'url' => 'http://www.alpha.de',         'icon' => 'ard_alpha.png',              'sourceid' => 'ARD-alpha.de'            ),
        'zdfkultur.de'      => array( 'display-name'=>'ZDFkultur',          'url' => 'http://www.zdfkultur.de',     'icon' => 'zdf_kultur.png',             'sourceid' => 'ZDFtheater.de'           ),
    );
    
    
    /**
     * Trigger starting the job
     */
    public function start() {
        // $this->_lvDownloadXmlTvEpgsToTmp(); 
        $this->_lvDownloadSourceEpgGzToTmp();
        $this->_lvUnzipSourceEpg();
        $this->_lvLoadSourceEpgXml();
        if ( $this->_oSourceXml ) {
            $this->_lvInitTargetFile();
            if ( $this->_oTargetFileTmp ) {
                $this->_lvWriteChannels();
                $this->_lvParseSourceEpg2Target();
                $this->_lvFinishTargetFile();
            }
            
        }
    }
    
    
    
    /**
     * Write Channel informations
     * 
     * @param void
     * @return void
     */
    protected function _lvWriteChannels() {
        foreach ( $this->_aSources as $sTargetId => $aChannelData ) {
            fwrite( $this->_oTargetFileTmp, "\t".'<channel id="'.$sTargetId.'">'."\n" );
            fwrite( $this->_oTargetFileTmp, "\t\t".'<display-name>'.$aChannelData['display-name'].'</display-name>'."\n" );
            fwrite( $this->_oTargetFileTmp, "\t\t".'<url>'.$aChannelData['url'].'</url>'."\n" );
            fwrite( $this->_oTargetFileTmp, "\t\t".'<icon src="'.$this->_sLogoBaseUrl.$aChannelData['icon'].'" />'."\n" );
            fwrite( $this->_oTargetFileTmp, "\t".'</channel>'."\n" );
        }
    }


    /**
     * Parsing the channel section 
     * 
     * @param void
     * @return void
     */
    protected function _lvParseSourceEpg2Target() {
        foreach ( $this->_aSources as $sTargetChannelId => $aChannelData ) {
            $sSourceChannelId = $aChannelData['sourceid'];
           
            foreach ( $this->_oSourceXml->programme as $oProgramme ) {
                if ( (string)$oProgramme['channel'] == $sSourceChannelId ) {
                    $sStart             = (string)$oProgramme['start'];
                    $sStop              = (string)$oProgramme['stop'];
                    $sTitle             = (string)$oProgramme->title;
                    $sSubTitle          = null;
                    if ( isset( $oProgramme->{'sub-title'} ) ) {
                        $sSubTitle      = (string)$oProgramme->{'sub-title'};
                    }
                    if ( isset( $oProgramme->{'desc'} ) ) {
                        $sDescription   = (string)$oProgramme->{'desc'};
                    }

                    $sProgrammeNode         = "";
                    $sProgrammeNode        .= "\t".'<programme start="'.$sStart.'" stop="'.$sStop.'" channel="'.$sTargetChannelId.'">'."\n";
                    $sProgrammeNode        .= "\t\t".'<title lang="de">'.$this->_lvCleanupString( $sTitle) .'</title>'."\n";
                    if ( isset( $oProgramme->{'sub-title'} ) ) {
                        $sProgrammeNode    .= "\t\t".'<sub-title lang="de">'.$this->_lvCleanupString( $sSubTitle ).'</sub-title>'."\n";
                    }
                    if ( isset( $oProgramme->{'desc'} ) ) {
                        $sProgrammeNode    .= "\t\t".'<desc lang="de">'.$this->_lvCleanupString( $sDescription ). '</desc>'."\n";
                    }
                    $sProgrammeNode        .= "\t".'</programme>'."\n";
                    
                    fwrite( $this->_oTargetFileTmp, $sProgrammeNode );
                }
            }
            
        }
    }
    
    
    /**
     * Cleansup all bad signs not allowed in epg
     * 
     * @param string $sInString
     * @return string
     */
    protected function _lvCleanupString( $sInString ) {
        $sOutString = htmlspecialchars( $sInString, ENT_COMPAT, 'UTF-8' );
        // $sOutString = str_replace( "<wbr>", "",$sInString );
        
        return $sOutString;
    }
    
    
    /**
     * Initiates creation of targetfile
     * 
     * @param void
     * @return void
     */
    protected function _lvInitTargetFile() {
        $this->_oTargetFileTmp = fopen( __DIR__."/".$this->_sTargetFileTmp, 'w' );
        fwrite( $this->_oTargetFileTmp,  '<?xml version="1.0" encoding="utf-8" ?>'."\n" );
        fwrite( $this->_oTargetFileTmp,  "<tv>\n" );
    }

    
    /**
     * Do final operations on tmp file
     * 
     * @param void
     * @return void
     */
    protected function _lvFinishTargetFile() {
        fwrite( $this->_oTargetFileTmp, "</tv>\n" );
        fclose( $this->_oTargetFileTmp );
        rename( $this->_sTargetFileTmp, __DIR__."/../".$this->_sTargetFile );
        system( 'cd '.__DIR__."/../"." && "."gzip -f ".$this->_sTargetFile );
    }


    /**
     * Loading EPG XML
     * 
     * @param void
     * @return void
     */
    protected function _lvLoadSourceEpgXml() {
        $sXmlFile = __DIR__."/".$this->_sTmpDir.$this->_sDownloadedTmpBaseName.".xml";
        
        if ( file_exists( $sXmlFile ) ) {
            $this->_oSourceXml = simplexml_load_file( $sXmlFile );
        }
    }
    
    /**
     * Downloads complete EPG file as Source for creating new one
     * Will check fi file exists and is old enough
     * 
     * @param void
     * @return void
     */
    protected function _lvDownloadSourceEpgGzToTmp() {
        $this->_sDownloadPath   = __DIR__."/".$this->_sTmpDir.$this->_sDownloadedTmpBaseName.".gz";
        
        $blRefreshTmpFile = $this->_sCheckRefreshFile();
        
        if ( $blRefreshTmpFile ) {
            if ( file_exists( $this->_sDownloadPath ) ) {
                unlink( $this->_sDownloadPath );
            }
            
            file_put_contents( $this->_sDownloadPath, file_get_contents( $this->_sCompleteEpgGz ) );
        }
    }
    
    
    /**
     * Unzips downloaded epg
     * 
     * @param void
     */
    protected function _lvUnzipSourceEpg() {
        $sTmpDir = __DIR__."/".$this->_sTmpDir;
        system( 'cd '.$sTmpDir." && "."gunzip -c ".$this->_sDownloadedTmpBaseName.".gz > ".$this->_sDownloadedTmpBaseName.".xml" );
    }



    /**
     * Checks if current file needs to be refreshed. This is the case when file not exists yet or if its too old
     * 
     * @param void
     * @return boolean
     */
    protected function _sCheckRefreshFile() {
        $blReturn = false;
        
        if ( file_exists( $this->_sDownloadPath ) ) {
            $iFileTime      = filemtime( $this->_sDownloadPath );
            $iMaxAgeFile    = strtotime( $this->_sMaxAgeTmpFile );
            
            if ( $iFileTime < $iMaxAgeFile  ) {
                $blReturn = true;
            }
        }
        else {
            $blReturn = true;
        }
        
        return $blReturn;
    }
    
}


$oScript = new iptv_epg_create();
$oScript->start();