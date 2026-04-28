<?php
/**
 * defaultadmin action – entry point for the CMS Made Simple admin panel.
 *
 * CMSMS calls the 'defaultadmin' action when opening any module that has
 * HasAdmin() === true.  Delegate straight to the main overview action.
 *
 * @package CMSmsMetadata
 */

if (!defined('CMS_VERSION')) {
    exit();
}

include __DIR__ . '/action.default.php';
