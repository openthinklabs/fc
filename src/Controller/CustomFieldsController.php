<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Collections\Criteria;


use UVDesk\CommunityPackages\UVDesk\FormComponent\Form\CustomFieldsType;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\Type;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Services\CustomFieldsService;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Services\FileUploadService;


class CustomFieldsController extends BaseController
{
    const ROLE_REQUIRED = 'ROLE_AGENT_MANAGE_CUSTOM_FIELD';
    const LIMIT = 100;

    protected function getFieldById($id)
    {  
       return $this->getDoctrine()
                   ->getRepository('UVDeskFormComponentPackage:CustomFields')
                   ->findOneById($id);
    }

    protected function getFields($request)
    {

        if((int)$request->query->get('page') < 0) return [];
        $queryBuilder = $this->getDoctrine()
                             ->getRepository('UVDeskFormComponentPackage:CustomFields')
                             ->createQueryBuilder('s');

        $limit = self::LIMIT;
        $page = abs((int)$request->query->get('page'));
        $offset = 0;

        if($request->query->get('init')) {
           
            $limit *= $page ? $page : 1;
        }        
   
        $qb = $queryBuilder->select('s,cfd,cfv')
                        ->leftJoin("s.customFieldsDependency",'cfd')
                        ->leftJoin("s.customFieldValues",'cfv')
                        ->where('s.name LIKE :name')
                        // ->andWhere('s.company= :company')
                        ->orderBy(
                                $request->query->get('sort') ? 's.'.$request->query->get('sort') : 's.sortOrder',
                                $request->query->get('direction') ? $request->query->get('direction') : Criteria::ASC
                            )
                         ->setParameters(
                            array(
                                    'name' => $request->query->get('search').'%',
                                )
                            )
                          ->setFirstResult($offset )
                          ->setMaxResults( $limit );
        if($request->query->get('agentType')) {
            $qb->andWhere('s.agentType = :agentType OR s.agentType = :both')
                ->setParameter('agentType', $request->query->get('agentType'))
                ->setParameter('both', 'both');
        }
        if($request->query->get('status')) {
            $qb->andWhere('s.status = :status')
                ->setParameter('status', $request->query->get('status') ? 1 : 0);
        }

        $results = $qb->getQuery()->getArrayResult();

        foreach ($results as $key => $result) {
            $results[$key]['validation'] = ($result['validation']) ? json_decode($result['validation']) : $result['validation'];
        }

        return $results;
    }

    protected function getAllFields()
    {
        return $this->getDoctrine()
                   ->getRepository('UVDeskFormComponentPackage:CustomFields')
                   ->findAll()
                   ;
    }

    // custom field view
    public function loadCustomFields(Request $request)
    {   
        // try {
        //     $this->isAuthorized(SELF::ROLE_REQUIRED);
        // } catch(\Exception $e) {
        //     $this->isAuthorized('ROLE_ADMIN');
        // }

        return $this->render('@_uvdesk_extension_uvdesk_form_component\CustomFields\customFieldsList.html.twig', [
            // 'list_items' => [],
            // 'information_items' => [],
            'types' => $this->getFieldTypes(),
        ]);
    }

    protected function getFieldTypes()
    {
        return $this->getDoctrine()
            ->getRepository('Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType')
            ->findBy(['isActive' => 1]);
    }

    protected function checkCustomFieldNameValues($field, $content)
    {
        $error = false;

        if(in_array($field->getFieldType(), ['select', 'checkbox', 'radio'])){
            if(isset($content['customFieldValues']) && ($fieldValues = $content['customFieldValues'])) {
                foreach ($fieldValues as $key => $fieldvalue) {
                    if(!$fieldvalue['name'])
                        unset($fieldValues[$key]);
                }
                if($fieldValues)
                    return $fieldValues;
            }
        } else
            return true;

        return $error;
    }

    /**
    * reorder customfield by sortable ui
    * Also used in api
    */
    public function customFieldsReorder(Request $request)
    {
        $json = [];
        $error = false;
        $data = $request->request->all()? : json_decode($request->getContent(), true);

        if($request->isXmlHttpRequest()) { //|| $this->get('api.token.service')->isThisApiRequest()) {
            if(!empty($data)) {
                $em = $this->getDoctrine()->getEntityManager();
                $fields = $this->getAllFields();

                foreach($fields as $field) {
                    if(!empty($data['sortorder'][$field->getId()])) {
                        $field->setSortOrder($data['sortorder'][$field->getId()]);
                        $em->persist($field);
                        $em->flush();
                    } else {
                        $error = true;
                    }
                }

                if(!$error) {
                    $json['alertClass'] = 'success';
                    $json['alertMessage'] = 'Success! Sort Order Updated successfully.';
                }
            } else {
                $json['alertClass'] = 'danger';
                $json['alertMessage'] = 'missing/invalid fields';
                $json['description'] =  'required: sortorder';
                $json['statusCode'] = Response::HTTP_BAD_REQUEST;
            }
        }
        if($error) {
            $json['alertClass']   = 'danger';
            $json['alertMessage'] = 'Error! Invalid Data.';
            $json['statusCode']   = Response::HTTP_BAD_REQUEST;
        }

        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    //  GET, ADD,EDIT, DELETE CUSTOM FIELDS
    public function customFieldsxhr(Request $request)
    {   
        $json = [];
        // if($request->getMethod() != 'GET') {
        //     $this->isAuthorized(self::ROLE_REQUIRED);
        // }
        
        if($request->isXmlHttpRequest()) { //|| $this->get('api.token.service')->isThisApiRequest()){
            if($request->getMethod() == 'GET') {
                $json = $this->getFields($request);
            } else if($request->getMethod() == 'DELETE' && $request->attributes->get('field')) {
                if($fieldBase = $this->getFieldById($request->attributes->get('field'))) {
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->remove($fieldBase);
                        $em->flush();

                        $json['alertClass'] = 'success';
                        $json['alertMessage'] = 'Success! Field has been deleted successfully.';
                } else {
                    $json['alertClass'] = 'danger';
                    $json['alertMessage'] = 'Resource not found.';
                    $json['statusCode'] = Response::HTTP_NOT_FOUND;
                }
                
            } else if($request->getMethod() == 'PATCH' && $request->attributes->get('field')) {
                if($field = $this->getFieldById($request->attributes->get('field'))) {
                        $em = $this->getDoctrine()->getEntityManager();

                        if($field->getStatus() == true)
                            $field->setStatus(false);
                        else
                            $field->setStatus(true);
                        $em->flush();

                        $json['status'] = $field->getStatus();
                        $json['alertClass'] = 'success';
                        $json['alertMessage'] = 'Success! Field Status has been updated successfully.';
                }

            } else if($request->getMethod() == 'PUT' || $request->getMethod() == 'POST') {

                $formErrors = $previousDependency = [];

                if($request->attributes->get('field')) {
                    $field = $this->getFieldById($request->attributes->get('field'));
                    $previousDependency = $field->getCustomFieldsDependency();
                } else
                    $field = new CustomFields();

                if(!$field)
                    $this->noResultFound();

                $form = $this->createForm(CustomFieldsType::class, $field,
                            array(
                                    'attr' => array(
                                                'novalidate' => 'novalidate',
                                                'renderData' => false,
                                                'allow_extra_fields' => true,
                                            ),
                                    'csrf_protection'   => false
                                )
                        );
                $content = $request->request->all()? : json_decode($request->getContent(), true);
                $form->submit($content);

                if($form->isValid()) {
                    $existingField = $this->getDoctrine()->getRepository('UVDeskFormComponentPackage:CustomFields')->findOneBy(array('name' => $content['name']));
                    if($existingField && ($existingField->getId() != $request->attributes->get('field'))) {
                        $json['alertClass'] = 'danger';
                        $json['alertMessage'] = 'Warning! Field with same name already exist';
                        $json['statusCode'] = Response::HTTP_CONFLICT;
                        $json['reload'] = 1;
                    } else {
                        $validation = $this->objectSerializer(isset($content['validation']) && $content['validation'] ? $content['validation'] : '{fieldtype: "text", minNo: "", maxNo: "", allowedDomain: "", restrictedDomain: "", maxFileSize: "",regex: "", restrictedDomain: "",}');

                        if($fieldValues = $this->checkCustomFieldNameValues($field, $content)) {
                                $em = $this->getDoctrine()->getEntityManager();

                                $currentDependency = [];
                                if(isset($content['customFieldsDependency']) && $content['customFieldsDependency']) {
                                    foreach ($content['customFieldsDependency'] as $dependency) {
                                        $currentDependency[] = $type = $this->getDoctrine()
                                                     ->getRepository('Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType')
                                                     ->findOneBy(['id' => $dependency]);

                                        if($type && (!$previousDependency || !in_array($type, $previousDependency->toArray())))
                                            $field->addCustomFieldsDependency($type);
                                    }
                                }

                                //remove previous dependency
                                if($previousDependency)
                                    foreach ($previousDependency->toArray() as $prevDependency) {
                                        if(!in_array($prevDependency, $currentDependency))
                                            $field->removeCustomFieldsDependency($prevDependency);
                                    }
                                $field->setValidation($validation);
                                $em->persist($field);

                                if(is_array($fieldValues)) {
                                    //if previous entry is not exists in this data then remove
                                    if($field->getCustomFieldValues()) {
                                        foreach ($field->getCustomFieldValues() as $customFieldValues) {
                                            if(!in_array($customFieldValues->getId(), array_column($fieldValues, 'id'))) {
                                                $em->remove($customFieldValues);
                                                $em->flush();
                                            }
                                        }
                                    }

                                    //add Custom Field Values
                                    foreach ($fieldValues as $fieldValue) {
                                        if(isset($fieldValue['id']) && $fieldValue['id']){
                                            if(!$newCustomFieldValues = $this->getDoctrine()
                                                                         ->getRepository('UVDeskFormComponentPackage:CustomFieldsValues')
                                                                         ->findOneBy(['customFields' => $field, 'id' => $fieldValue['id']])
                                                                        )
                                            $newCustomFieldValues = new CustomFieldsValues;
                                        } else
                                            $newCustomFieldValues = new CustomFieldsValues;

                                        $newCustomFieldValues->setName($fieldValue['name']);
                                        $newCustomFieldValues->setSortOrder($fieldValue['sortOrder']);
                                        $newCustomFieldValues->setCustomFields($field);
                                        $em->persist($newCustomFieldValues);
                                    }
                                } else {
                                    if($field->getCustomFieldValues()){
                                        foreach ($field->getCustomFieldValues() as $customFieldValues) {
                                            $em->remove($customFieldValues);
                                            $em->flush();
                                        }
                                    }
                                }

                                $em->flush();
                                if($request->attributes->get('field')){
                                    $json['alertClass'] = 'success';
                                    $json['alertMessage'] = $this->translate('Success! Field has been updated successfully.');
                                } else {
                                    $json['alertClass'] = 'success';
                                    $json['alertMessage'] = $this->translate('Success! Field has been added successfully.');
                                }
                                $json['id'] = $field->getId();
                        } else  {
                            $json['alertClass'] = 'warning';
                            $json['alertMessage'] = $this->translate('Warning! add Custom Fields Values (customFieldValues).');
                            return new JsonResponse($json, 400);
                        }
                    }
                } else {
                    $json['alertClass'] = 'danger';
                    $json['alertMessage'] = $this->translate('missing/invalid data.');
                    $json['statusCode'] = Response::HTTP_BAD_REQUEST;
                    $json['errors'] = [];
                    foreach ($form->getErrors(true) as $key => $error) {
                        $json['errors'][$error->getOrigin()->getName()] = $error->getMessage();
                    }
                }
            }

            $response = new Response(json_encode($json));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return new Response(json_encode($json));
    }

    public function ticketCustomFieldXHR(Request $request, CustomFieldsService $customFieldsService, FileUploadService $fileUploadService)
    {

        $ticketId = $request->request->get('ticketId');
        $entityManager = $this->getDoctrine()->getManager();
        $ticket = $entityManager->getRepository('UVDeskCoreFrameworkBundle:Ticket')->findOneById($ticketId);
        
        if (!empty($ticket)) {
            $submittedCustomFields = $request->request->get('customFields');
            $submittedCustomFileFields = $request->files->get('customFields');

            $validationStatus = $customFieldsService->customFieldsValidationWithoutRequired($request, $request->request->get('area') ?: 'user');
            if (!empty($validationStatus) && $validationStatus['errorMain'] == false && empty($validationStatus['formErrors'])) {
                $customFieldRepository = $entityManager->getRepository('UVDeskFormComponentPackage:CustomFields');
                $ticketCustomFieldsValuesCollection = $entityManager->getRepository('UVDeskFormComponentPackage:TicketCustomFieldsValues')->find($ticket);

                if (!empty($submittedCustomFields)) {
                    foreach ($submittedCustomFields as $customFieldId => $customFieldValue) {

                        $existingCustomFieldValue = null;
                        if (!empty($ticketCustomFieldsValuesCollection)) {
                            foreach ($ticketCustomFieldsValuesCollection as $ticketCustomField) {
                                if ($ticketCustomField->getTicketCustomFieldsValues()->getId() == $customFieldId) {
                                    $existingCustomFieldValue = $ticketCustomField;
                                    break;
                                }
                            }
                        }

                        $customField = $customFieldRepository->findOneById($customFieldId); 
                        $ticketCustomFieldValue = !empty($existingCustomFieldValue) ? $existingCustomFieldValue : new TicketCustomFieldsValues(); 
                        $ticketCustomFieldValue->setTicket($ticket); 
                        $ticketCustomFieldValue->setTicketCustomFieldsValues($customField); 
                        $ticketCustomFieldValue->setValue($customFieldValue); 

                        $entityManager->persist($ticketCustomFieldValue);
                        $entityManager->persist($ticket);
                    
                    }
                }
                
                if (!empty($submittedCustomFileFields)) {

                    // dump("inside 'if not empty submittedcustomfilefields'."); die; 
                    
                    $baseUploadPath = '/custom-fields/ticket/' . $ticket->getId() . '/';
                    $temporaryFiles = $request->files->get('customFields');
                    dump("dumping tempFIles");
                    dump($temporaryFiles); 
                    $uploadedFileCollection = [];
                    foreach($temporaryFiles as $key => $temporaryFile) {
                        $fileName = $fileUploadService->uploadFile($temporaryFile, $baseUploadPath, true);
                        $fileName['key'] = $key;
                        $uploadedFileCollection[] = $fileName;
                    }

                    dump("dumping ticketCFsValuesCollection");
                    dump($ticketCustomFieldsValuesCollection); 
                    
                    if (!empty($uploadedFileCollection)) {
                        foreach ($uploadedFileCollection as $uploadedFile) {
                            $existingCustomFieldValue = null;
                            if (!empty($ticketCustomFieldsValuesCollection)) {
                                foreach ($ticketCustomFieldsValuesCollection as $ticketCustomField) {
                                    if ($ticketCustomField->getTicketCustomFieldsValues()->getId() == $uploadedFile['key']) {
                                        $existingCustomFieldValue = $ticketCustomField;
                                        break;
                                    }
                                }
                            }

                            $uploadedAttachment = $customFieldsService->addFilesEntryToAttachmentTable([$uploadedFile]);
                            dump("dumping uploadedAttachment");
                            dump($uploadedAttachment);
                            if (!empty($uploadedAttachment[0])) {
                                $customField = $customFieldRepository->findOneById($uploadedFile['key']);
                                $ticketCustomFieldValue = !empty($existingCustomFieldValue) ? $existingCustomFieldValue : new TicketCustomFieldsValues();
                                // $ticketCustomFieldValue->setValue($resourceURL);
                                $ticketCustomFieldValue->setValue(json_encode(['name' => $uploadedAttachment[0]['name'], 'path' => $uploadedAttachment[0]['path'], 'id' => $uploadedAttachment[0]['id']]));
                                $ticketCustomFieldValue->setTicketCustomFieldsValues($customField);
                                $ticketCustomFieldValue->setTicket($ticket);

                                dump("dumping ticket custom field value");
                                // dump($ticketCustomFieldValue); die;

                                $entityManager->persist($ticketCustomFieldValue);
                                $entityManager->persist($ticket);
                            }
                        }
                    }
                }

                $entityManager->flush();

                $ticketCustomFieldsValuesCollection = $entityManager->getRepository('UVDeskFormComponentPackage:TicketCustomFieldsValues')->findBy(['ticket' => $ticket]);
                if (!empty($ticketCustomFieldsValuesCollection)) {
                    $ticketCustomFieldArrayCollection = [];

                    foreach ($ticketCustomFieldsValuesCollection as $ticketCustomField) {
                        $ticketCustomFieldArrayCollection[$ticketCustomField->getTicketCustomFieldsValues()->getId()] = [
                            'id' => $ticketCustomField->getId(),
                            'encrypted' => $ticketCustomField->getEncrypted() ? true : false,
                            'targetCustomField' => $ticketCustomField->getTicketCustomFieldsValues()->getId(),
                        ];

                        switch ($ticketCustomField->getTicketCustomFieldsValues()->getFieldType()) {
                            case 'select':
                            case 'radio':
                            case 'checkbox':
                                $fieldId = [];
                                $fieldValue = [];

                                if ($ticketCustomField->getEncrypted()) {
                                    $ticketCustomField->decryptEntity();
                                }

                                $fieldOptions = json_decode($ticketCustomField->getValue(), true);

                                if (empty($fieldOptions)) {
                                    $fieldOptions = explode(',', $ticketCustomField->getValue());
                                } else {
                                    if (!is_array($fieldOptions)) {
                                        $fieldOptions = [$fieldOptions];
                                    }
                                }

                                foreach ($ticketCustomField->getTicketCustomFieldsValues()->getCustomFieldValues() as $multipleFieldValue) {
                                    if (in_array($multipleFieldValue->getId(), $fieldOptions)) {
                                        $fieldId[] = $multipleFieldValue->getId();
                                        $fieldValue[] = $multipleFieldValue->getName();
                                    }
                                }

                                $ticketCustomFieldArrayCollection[$ticketCustomField->getTicketCustomFieldsValues()->getId()]['valueId'] = $fieldId;
                                $ticketCustomFieldArrayCollection[$ticketCustomField->getTicketCustomFieldsValues()->getId()]['value'] = $ticketCustomField->getEncrypted() ? null: implode('</br>', $fieldValue);
                                break;
                            default:
                                $ticketCustomFieldArrayCollection[$ticketCustomField->getTicketCustomFieldsValues()->getId()]['value'] = (!$ticketCustomField->getEncrypted()
                                    ? (is_array(trim($ticketCustomField->getValue(), '"'))
                                        ? json_encode(trim($ticketCustomField->getValue(), '"'))
                                        : strip_tags(htmlentities(trim($ticketCustomField->getValue(), '"')))
                                    )
                                    : null
                                );
                                break;
                        }
                    }
                          
                    $responseContent = [
                        'success' => true,
                        'ticketCustomFieldsValuesCollection' => $ticketCustomFieldArrayCollection,
                    ];
                } else {
                    $responseContent = [
                        'success' => true,
                        'ticketCustomFieldsValuesCollection' => [],
                    ];
                }
            } else {
                $responseContent = [
                    'success' => false,
                    'message' => $validationStatus['formErrors'],
                    'ticketCustomFieldsValuesCollection' => [],
                ];
            }
        }
    
        return new Response(json_encode($responseContent), 200);
    }

    public function decryptCustomFieldXHR($ticketId, Request $request)
    {
        $responseCode = 403;
        $responseContent = [
            'success' => false,
            'message' => $this->translate('An unexpected error occurred. Please try again later.')
        ];

        $entityManager = $this->getDoctrine()->getManager();
        $ticket = $entityManager->getRepository('UVDeskCoreFrameworkBundle:Ticket')->findOneById($ticketId);

        if (!empty($ticket)) {
            $ticketCustomFieldValueId = $request->request->get('id');
            $customFieldId = $request->request->get('targetCustomField');

            $ticketCustomFieldsValuesCollection = $entityManager->getRepository('UVDeskFormComponentPackage:TicketCustomFieldsValues')->findBy(['ticket' => $ticket]);
            foreach ($ticketCustomFieldsValuesCollection as $ticketCustomFieldValue) {
                if ($ticketCustomFieldValue->getId() == $ticketCustomFieldValueId) {
                    $targetCustomFieldValue = $ticketCustomFieldValue;
                    break;
                }
            }

            if (!empty($targetCustomFieldValue)) {
                $responseCode = 200;
                $customFieldDecryptLog = new CustomFieldDecryptLog();
                $customFieldDecryptLog->setCreatedAt(new \DateTime('now'))
                    ->setAccessGranted(true)
                    ->setUser($this->get('user.service')->getCurrentUser())
                    ->setUserType($request->request->get('area') ?: 'member')
                    ->setTicketCustomFieldsValues($targetCustomFieldValue);

                $entityManager->persist($customFieldDecryptLog);
                $entityManager->flush();

                $targetCustomFieldValue->decryptEntity();

                $responseContent = [
                    'id' => $ticketCustomFieldValueId,
                    'encrypted' => false,
                    'targetCustomField' => $customFieldId,
                ]; 

                switch ($targetCustomFieldValue->getTicketCustomFieldsValues()->getFieldType()) {
                    case 'select': 
                    case 'radio': 
                    case 'checkbox': 
                        $fieldId = []; 
                        $fieldValue = []; 

                        $fieldOptions = json_decode($targetCustomFieldValue->getValue(), true);

                        if (empty($fieldOptions)) {
                            $fieldOptions = explode(',', $targetCustomFieldValue->getValue());
                        } else {
                            if (!is_array($fieldOptions)) {
                                $fieldOptions = [$fieldOptions];
                            }
                        }

                        foreach ($targetCustomFieldValue->getTicketCustomFieldsValues()->getCustomFieldValues() as $multipleFieldValue) {
                            if (in_array($multipleFieldValue->getId(), $fieldOptions)) {
                                $fieldId[] = $multipleFieldValue->getId();
                                $fieldValue[] = $multipleFieldValue->getName();
                            }
                        }

                        $responseContent['valueId'] = $fieldId;
                        $responseContent['value'] = implode('</br>', $fieldValue);
                        break;
                    default:
                        $responseContent['value'] = (is_array(trim($targetCustomFieldValue->getValue(), '"'))
                            ? json_encode(trim($targetCustomFieldValue->getValue(), '"'))
                            : trim($targetCustomFieldValue->getValue(), '"')
                        );
                        break;
                }
            }
        }

        return new Response(json_encode($responseContent), $responseCode);
    }
}