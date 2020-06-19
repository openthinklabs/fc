<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\UIComponents\Dashboard\Panel\Items\Settings;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Segments\PanelSidebarItemInterface;
use Webkul\UVDesk\CoreFrameworkBundle\UIComponents\Dashboard\Panel\Sidebars\Settings;

class CustomFields implements PanelSidebarItemInterface
{
    public static function getTitle() : string
    {
        return "Custom Fields";
    }

    public static function getRouteName() : string
    {
        return 'uvdesk_form_component_custom_fields';
    }

    public static function getSupportedRoutes() : array
    {
        return [
            'uvdesk_form_component_custom_fields',
            'uvdesk_form_component_custom_fields_xhraction',
            'uvdesk_form_component_custom_fields_reorder_action',
        ];
    }

    public static function getRoles() : array
    {
        return ['ROLE_AGENT_MANAGE_CUSTOM_FIELD'];
    }

    public static function getSidebarReferenceId() : string
    {
        return Settings::class;
    }
}
