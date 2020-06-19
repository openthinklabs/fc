<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Widgets;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\RequestStack;
use Webkul\UVDesk\CoreFrameworkBundle\Tickets\WidgetInterface;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Services\CustomFieldsService;
use Webkul\UVDesk\CoreFrameworkBundle\Services\UserService;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\User;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\UserInstance;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\SupportRole;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields as CustomFieldsEntity;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues;

class CustomFields implements WidgetInterface
{
    CONST SVG = <<<SVG
<svg 
 xmlns="http://www.w3.org/2000/svg"
 xmlns:xlink="http://www.w3.org/1999/xlink"
 width="20px" height="18px">
<path fill-rule="evenodd"  fill="rgb(51, 51, 51)"
 d="M15.000,14.000 L15.000,11.000 L17.000,11.000 L17.000,7.000 L15.000,7.000 L15.000,4.000 L20.000,4.000 L20.000,14.000 L15.000,14.000 ZM14.000,15.000 L16.000,15.000 L16.000,18.000 L9.000,18.000 L9.000,15.000 L11.000,15.000 L11.000,3.000 L9.000,3.000 L9.000,-0.000 L16.000,-0.000 L16.000,3.000 L14.000,3.000 L14.000,15.000 ZM10.000,7.000 L3.000,7.000 L3.000,11.000 L10.000,11.000 L10.000,14.000 L0.000,14.000 L0.000,4.000 L10.000,4.000 L10.000,7.000 Z"/>
</svg>
SVG;

    public function __construct(RequestStack $requestStack,
        TwigEnvironment $twig,
        EntityManagerInterface $entityManager,
        CustomFieldsService $customFieldsService,
        UserService $userService
        )
    {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->customFieldsService = $customFieldsService;
        $this->userService = $userService;
    }

    public static function getIcon()
    {
        return self::SVG;
    }

    public static function getTitle()
    {
        return "Custom Fields";
    }

    public static function getDataTarget()
    {
        return 'uv-customfield-view';
    }

    public function getTemplate()
    {   
        //get the request
        $request = $this->requestStack->getCurrentRequest();
        //get the ticket
        $ticket = $this->entityManager->getRepository('UVDeskCoreFrameworkBundle:Ticket')->findOneById($request->attributes->get('ticketId'));
		
		if ($this->userService->getCurrentUser()->getCurrentInstance()->getSupportRole()->getCode() === 'ROLE_CUSTOMER') {
			return $this->twig->render('@_uvdesk_extension_uvdesk_form_component\widgets\CustomFields\customFieldSnippetCustomer.html.twig', 
						$this->customFieldsService->getCustomerCustomFieldSnippet($ticket));
        } else {
			return $this->twig->render('@_uvdesk_extension_uvdesk_form_component\widgets\CustomFields\customFieldSnippet.html.twig', 
						$this->customFieldsService->getCustomFieldSnippet($ticket));
		} 
	}
}
