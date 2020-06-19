<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Routing;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Routing\ProtectedRoutingResourceInterface;

class ProtectedRoutingResource implements ProtectedRoutingResourceInterface
{
    public static function getResourcePath()
    {
        return __DIR__ . "/../Resources/config/routes/private.yaml";
    }

    public static function getResourceType()
    {
        return ProtectedRoutingResourceInterface::YAML_RESOURCE;
    }
}
