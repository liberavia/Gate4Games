<?php
/**
 * for shop versions 4.7.x / 5.0.x
 * # copy the source-themefolders in application/views/tpl and /out and rename
 * # Adjust values for Source and Target below
 * # Put file into rootlevel
 * # run this script
 * # delete this file after usage
 */


// ADJUST THEME NAMES HERE *************************************************
$scriptConfig = (object) array(
    'from' => 'azure',           // Source
    'to'   => 'gate4games'          // Target
);
// *************************************************************************

ini_set('display_errors', 1);

if (!defined('OX_BASE_PATH')) {
    define('OX_BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR );
}

// custom functions file
require_once OX_BASE_PATH . 'modules/functions.php';

// Generic utility method file including autoloading definition
require_once OX_BASE_PATH . 'core/oxfunctions.php';

//sets default PHP ini params
setPhpIniParams();

//strips magics quote if any is set
// stripGpcMagicQuotes();

//init config.inc.php file reader
$oConfigFile = new oxConfigFile( OX_BASE_PATH . "config.inc.php" );

oxRegistry::set("oxConfigFile", $oConfigFile );

$oDb      = oxDb::getDb(true);
$oShopControl = oxNew('oxShopControl');
$shopConfig = $oShopControl->getConfig();
$sShopId = $shopConfig->getBaseShopId();


$sSql = "
    SELECT
        `cfg`.`oxid`                                                   AS `id`,
        `cfg`.`oxmodule`                                               AS `theme`,
        `cfg`.`oxvarname`                                              AS `name`,
        `cfg`.`oxvartype`                                              AS `type`,
        DECODE( `cfg`.`oxvarvalue`, '" . $shopConfig->getConfigParam( 'sConfigKey' ) . "') AS `value`,
        `cfgd`.`oxgrouping`                                            AS `group`,
        `cfgd`.`oxvarconstraint`                                       AS `constraint`,
        `cfgd`.`oxpos`                                                 AS `pos`
    FROM
        `oxconfig`        AS `cfg`,
        `oxconfigdisplay` AS `cfgd`
    WHERE
        `cfg`.`oxshopid` = '" . $sShopId . "'
    AND
        `cfg`.`oxmodule` = 'theme:" . $scriptConfig->from ."'
    AND(
        `cfgd`.`oxcfgmodule` = `cfg`.`oxmodule`
        AND
        `cfgd`.`oxcfgvarname` = `cfg`.`oxvarname`
    )
";

$aThemeValues = $oDb->getAll( $sSql );

if( is_array( $aThemeValues )
    && !empty( $aThemeValues ) )
{
    // delete existing values for theme
    $oDb->Execute("
        DELETE FROM
            `oxconfig`
        WHERE
            `oxshopid`  = '" . $sShopId . "'
        AND
            `oxmodule` = 'theme:" . $scriptConfig->to . "'
    ");

    // delete existing values for theme
    $oDb->Execute("
        DELETE FROM
            `oxconfigdisplay`
        WHERE
            `oxcfgmodule` = 'theme:" . $scriptConfig->to . "'
    ");

    foreach( $aThemeValues as $aConfig )
    {
        $cfg = $aConfig;       
       
        $sSql = "
            REPLACE INTO
                `oxconfig`
            SET
                `OXID`       = '" . $scriptConfig->to . "." . $cfg[2] . "',
                `OXSHOPID`   = '" . $sShopId . "',
                `OXMODULE`   = 'theme:" . $scriptConfig->to . "',
                `OXVARNAME`  = '" . $cfg[2] . "',
                `OXVARTYPE`  = '" . $cfg[3] . "',
                `OXVARVALUE` = ENCODE( " . $oDb->quote( $cfg[4] ) . ", " . $oDb->quote( $shopConfig->getConfigParam( 'sConfigKey' ) ) . " )
        ";      
       
        $oDb->Execute($sSql);
       
        // display
        $sSql = "
            REPLACE INTO
                `oxconfigdisplay`
            SET
                `oxid`            = '" . $scriptConfig->to . "." . $cfg[2] . "',
                `oxcfgmodule`     = 'theme:" . $scriptConfig->to . "',
                `oxcfgvarname`    = '" . $cfg[2] . "',
                `oxgrouping`      = '" . $cfg[5] . "',
                `oxvarconstraint` = '" . $cfg[6] . "',
                `oxpos`           = '" . $cfg[7] . "'
      ";
       
        $oDb->Execute($sSql);
    }
} else {
    exit('Exception. No entries found for "'. $scriptConfig->from . '"' );
} 
