<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Routing;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Routing\ExposedRoutingResourceInterface;

class UnprotectedRoutingResource implements ExposedRoutingResourceInterface
{
    public static function getResourcePath()
    {
        return __DIR__ . "/../Resources/config/routes/public.yaml";
    }

    public static function getResourceType()
    {
        return ExposedRoutingResourceInterface::YAML_RESOURCE;
    }
}
