<?php
/**
 * CMSmsMetadata – content metadata overview for CMS Made Simple.
 *
 * Displays a single-page table of all content elements with their
 * metadata fields (tab index, title attribute, access key, meta description)
 * and any extra content properties stored in the content_props table.
 *
 * @package  CMSmsMetadata
 * @author   smithdesign77
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('CMS_VERSION')) {
    exit();
}

require_once __DIR__ . '/lib/MetadataHelper.php';

class CMSmsMetadata extends CMSModule
{
    public function GetName()
    {
        return 'CMSmsMetadata';
    }

    public function GetFriendlyName()
    {
        return $this->Lang('friendly_name');
    }

    public function GetVersion()
    {
        return '1.1.0';
    }

    public function GetDescription()
    {
        return $this->Lang('module_description');
    }

    public function GetHelp()
    {
        return $this->Lang('module_help');
    }

    public function GetAuthor()
    {
        return 'smithdesign77';
    }

    public function GetAuthorEmail()
    {
        return '';
    }

    public function IsAdminOnly()
    {
        return true;
    }

    public function HasAdmin()
    {
        return true;
    }

    public function GetAdminDescription()
    {
        return $this->Lang('module_description');
    }

    /**
     * Place the module in the Content section of the admin navigation,
     * alongside Content Manager, Add Content, etc.
     */
    public function GetAdminSection()
    {
        return 'content';
    }

    public function InstallPostMessage()
    {
        return $this->Lang('postinstall');
    }

    public function UninstallPreMessage()
    {
        return $this->Lang('confirm_uninstall');
    }

    public function MinimumCMSVersion()
    {
        return '2.0';
    }
}
