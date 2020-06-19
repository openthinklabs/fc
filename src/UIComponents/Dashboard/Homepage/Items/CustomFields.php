<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\UIComponents\Dashboard\Homepage\Items;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Segments\HomepageSectionItem;
use Webkul\UVDesk\CoreFrameworkBundle\UIComponents\Dashboard\Homepage\Sections\Settings;

class CustomFields extends HomepageSectionItem
{
    CONST SVG = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="60px" height="60px" viewBox="0 0 60 60">
    <path fill-rule="evenodd" d="M51,57H9a6,6,0,0,1-6-6V9A6,6,0,0,1,9,3H51a6,6,0,0,1,6,6V51A6,6,0,0,1,51,57ZM36,14H10v6H36V14Zm0,13H10v6H36V27Zm0,13H10v6H36V40ZM51,15H48V12H44v3H41v4h3v3h4V19h3V15Zm0,13H48V25H44v3H41v4h3v3h4V32h3V28Zm0,13H48V38H44v3H41v4h3v3h4V45h3V41Z"></path>
</svg>
SVG;

    public static function getIcon() : string
    {
        return self::SVG;
    }

    public static function getTitle() : string
    {
        return "Custom Fields";
    }

    public static function getRouteName() : string
    {
        return 'uvdesk_form_component_custom_fields';
    }

    public static function getRoles() : array
    {
        return ['ROLE_AGENT_MANAGE_CUSTOM_FIELD'];
    }

    public static function getSectionReferenceId() : string
    {
        return Settings::class;
    }
}
