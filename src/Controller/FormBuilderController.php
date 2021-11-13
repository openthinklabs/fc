<?php 

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Controller;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\Collection; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse; 
use Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType; 
use Webkul\UVDesk\CoreFrameworkBundle\Entity\SupportRole; 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\Form;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields;  
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\SavedCustomFields; 
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Services\FileUploadService;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Services\CustomFieldsService;
use UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues;

class FormBuilderController extends BaseController
{

    public function loadFormBuilders(Request $request)
    {
       
        return $this->render('@_uvdesk_extension_uvdesk_form_component\FormBuilders\formBuilderListConfigurations.html.twig', []);
    }

    public function createFormBuilderConfiguration(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $customFields =  $em->getRepository('UVDeskFormComponentPackage:CustomFields')->findAll();
        $customFieldsArray = [];

        foreach($customFields as $k => $v)
        {
            $cfRepo = $this->getDoctrine()->getRepository(CustomFieldsValues::class); 
            $cfValues = $cfRepo->findOneBy([
                'customFields' => $v->getId(),
            ]);

            if($v->getStatus() == true)
            {
                $temp = [
                    'id'        => $v->getId(),
                    'name'      => $v->getName(),
                    'fieldType' => $v->getFieldType(),
                    'value'     => $v->getValue(),
                    'required'  => $v->getRequired(),
                    'validation'=> json_decode($v->getValidation(), true),
                ]; 
                $customFieldsArray[$k] = $temp; 
            } 
        }


        if($request->getMethod() == 'POST')
        {
  
            $entityManager = $this->getDoctrine()->getManager();

            $form = new Form(); 
            $form->setFormName($request->request->get('form_name')); 
            $form->setName( $request->request->get('name') == '1' ? '1': '0') ;
            $form->setType($request->request->get('type') == '1' ? '1': '0');
            $form->setSubject($request->request->get('subject') == '1' ? '1': '0');
            $form->setGDPR( $request->request->get('gdpr') == '1' ? '1' : '0' );  
            $form->setOrderNo($request->request->get('order_no') == '1' ? '1': '0');
            $form->setFile($request->request->get('file') == '1' ? '1': '0');        
            $entityManager->persist($form);
            $entityManager->flush(); 


            $savedCustomFields = new SavedCustomFields(); 
            $savedCustomFields->setFormId($form->getId());
            $arrayOfIds = []; 
            if( !empty($request->request->get('cf')) )
            {
              
                foreach( $request->request->get('cf') as $cfId)
                {
                    $arrayOfIds[] = $cfId; 
                }
            } 
            $savedCustomFields->setArrayOfIds(serialize($arrayOfIds)); 
            $entityManager->persist($savedCustomFields);
            $entityManager->flush(); 

            $this->addFlash('success', 'Form successfully created.');
            return new RedirectResponse($this->generateUrl('formbuilder_settings'));

        }

        return $this->render('@_uvdesk_extension_uvdesk_form_component\FormBuilders\manageConfigurations.html.twig', [
                'formbuilder' => [],
                'customfields' => $customFieldsArray ?? null,
        ]);
    }

    public function updateFormConfigurations($id , Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $form =  $em->getRepository('UVDeskFormComponentPackage:Form')->find($id);
        $data = [
            'id' => $form->getId(),
            'form_name' => $form->getFormName(),
            'name' => $form->getName(),
            'subject' => $form->getSubject(),
            'gdpr' =>  $form->getGDPR(),
            'order_no' => $form->getOrderNo(),
            'file' => $form->getFile(),
            'type' => $form->getType(),
        ];  


        $savedCFRepo = $this->getDoctrine()->getRepository(SavedCustomFields::class);
        $savedCF = $savedCFRepo->findOneBy([
            'formId' => $id,
        ]);
        $arrayOfIds = unserialize($savedCF->getArrayOfIds()); 


        $customFields =  $em->getRepository('UVDeskFormComponentPackage:CustomFields')->findAll();
        $customFieldsArray = [];
        $multiOptions = ['select', 'radio', 'checkbox']; 

        foreach($customFields as $k => $v)
        {

            $cfRepo = $this->getDoctrine()->getRepository(CustomFieldsValues::class); 
            $cfValues = $cfRepo->findOneBy([
                'customFields' => $v->getId(),
            ]);
            $temp = [];

            if($v->getStatus() == true)
            {
              
                if(in_array($v->getId() ,$arrayOfIds))
                {
                    $options = [];
                    if(in_array($v->getFieldType(), $multiOptions))
                    {
                       
                        foreach($v->getCustomFieldValues() as $cfv)
                        {
                            $options[] = $cfv->getName();
                        }
                      
                        $temp = [
                            'id'        => $v->getId(),
                            'name'      => $v->getName(),
                            'fieldType' => $v->getFieldType(),
                            'value'     => $v->getValue(),
                            'required'  => $v->getRequired(),
                            'validation'=> json_decode($v->getValidation(), true),
                            'checked'   => true,
                            'options'   => $options,
                        ]; 
                
                    } else {

                        $temp = [
                            'id'        => $v->getId(),
                            'name'      => $v->getName(),
                            'fieldType' => $v->getFieldType(),
                            'value'     => $v->getValue(),
                            'required'  => $v->getRequired(),
                            'validation'=> json_decode($v->getValidation(), true),
                            'checked'   => true,
                            'options'   =>  $options,
                        ]; 

                    }
                }
             
                $customFieldsArray[$k] = $temp; 
            } 
            
        } 

    
        if($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getEntityManager();
            $form = $em->getRepository('UVDeskFormComponentPackage:Form')->find($id);

            $formName = (empty($request->request->get('form_name')) == true ? "No Form Name Given" : $request->request->get('form_name'));

            // $form->setFormName($request->request->get('form_name'));
            $form->setFormName($formName);
            $form->setName($request->request->get('name') == '1' ? '1' : '0');
            $form->setType($request->request->get('type') == '1' ? '1' : '0');
            $form->setSubject($request->request->get('subject') == '1' ? '1' : '0');
            $form->setGDPR($request->request->get('gdpr') == '1' ? '1' : '0');
            $form->setOrderNo($request->request->get('order_no') == '1' ? '1' : '0');
            $form->setFile($request->request->get('file') == '1' ? '1' : '0'); 
            $em->persist($form);
            $em->flush();


            $savedCFRepo = $this->getDoctrine()->getRepository(SavedCustomFields::class);
            $savedCustomFields = $savedCFRepo->findOneBy([
                'formId' => $id,
            ]); 

            $arrayOfIds = []; 
            if( !empty($request->request->get('cf')) )
            {
              
                foreach( $request->request->get('cf') as $cfId)
                {
                    $arrayOfIds[] = $cfId; 
                }
            } 
            $savedCustomFields->setArrayOfIds(serialize($arrayOfIds)); 
            $em->persist($savedCustomFields);
            $em->flush(); 


            $this->addFlash('success', 'Form successfully updated.');
            return new RedirectResponse($this->generateUrl('formbuilder_settings'));
        }


        return $this->render('@_uvdesk_extension_uvdesk_form_component\FormBuilders\manageConfigurations.html.twig',[
            'formbuilder' => $data ?? null,
            'arrayOfIds' => $arrayOfIds,
            'selectedCustomFields' => $customFieldsArray,
            'customFields' => $customFieldsArray,
        ]);
    }

    public function loadFormsXHR(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $records = $em->getRepository("UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\Form")->findAll();
        $arrayOfForms = [];
        foreach($records as $k=>$v)
        {
            $temp = ['id' => $v->getId(),
            'form_name' => $v->getFormName(),
            'name' => $v->getName(),
            'type' => $v->getType(),
            'subject' => $v->getSubject(),
            'gdpr' => $v->getGDPR(),
            'order_no' => $v->getOrderNo(),
            'file' => $v->getFile()];
            $arrayOfForms[$k] = $temp;        
        }
        return new JsonResponse($arrayOfForms); 
    }

    public function removeFormConfiguration($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $form = $em->getRepository('UVDeskFormComponentPackage:Form')->find($id);
        $em->remove($form);
        $em->flush();  

        return new JsonResponse([
            'alertClass' => 'success',
            'alertMessage' => 'Form configuration removed successfully.',
        ]);
    }
 
    public function previewForm($id, Request $request, FileUploadService $fileUploadService , CustomFieldsService $customFieldsService )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $form = $em->getRepository('UVDeskFormComponentPackage:Form')->find($id);

        $data = [
            'id' => $form->getId(),
            'form_name' => $form->getFormName(),
            'name' => $form->getName(),
            'subject' => $form->getSubject(),
            'gdpr' =>  $form->getGDPR(),
            'order_no' => $form->getOrderNo(),
            'file' => $form->getFile(),
            'type' => $form->getType(),
        ];

        $savedCFRepo = $this->getDoctrine()->getRepository(SavedCustomFields::class);
        $savedCF = $savedCFRepo->findOneBy([
            'formId' => $id,
        ]);

        $customFieldsArray = [];
        $arrayOfIds = unserialize($savedCF->getArrayOfIds()); 
        $customFields =  $em->getRepository('UVDeskFormComponentPackage:CustomFields')->findAll();
        $multiOptions = ['select', 'radio', 'checkbox']; 
        //preview-original cf array
        foreach($customFields as $k => $v)
        {
            $cfRepo = $this->getDoctrine()->getRepository(CustomFieldsValues::class); 
            $cfValues = $cfRepo->findOneBy([
                'customFields' => $v->getId(),
            ]);
            $temp = [];
            
            if($v->getStatus() == true)
            {
                if(in_array($v->getId() ,$arrayOfIds))
                {
                    if(in_array($v->getFieldType(), $multiOptions))
                    {
                        $options = []; 
                        foreach($v->getCustomFieldValues() as $cfv)
                        {
                            $options[] = $cfv->getName(); 
                        }


                        $temp = [
                            'id'        => $v->getId(),
                            'name'      => $v->getName(),
                            'fieldType' => $v->getFieldType(),
                            'value'     => $v->getValue(),
                            'required'  => $v->getRequired(),
                            'validation'=> json_decode($v->getValidation(), true),
                            'checked'   => true,
                            'options'   => $options,
                        ]; 

                    } else {
                        $temp = [
                            'id'        => $v->getId(),
                            'name'      => $v->getName(),
                            'fieldType' => $v->getFieldType(),
                            'value'     => $v->getValue(),
                            'required'  => $v->getRequired(),
                            'validation'=> json_decode($v->getValidation(), true),
                            'checked'   => true,
                        ]; 
                    }

                }
             
                $customFieldsArray[$k] = $temp; 
            } 

        }


        if($request->getMethod() == 'POST')
        {

            $params = $request->request->all();
            $role = $this->getDoctrine()->getRepository(SupportRole::class)->find(4);
            $userInstance = $this->get('user.service')->createUserInstance($params['email'], $params['name'], $role);
            $params['from'] = $params['email'];
            $params['subject'] = ( isset($params['subject']) ? $params['subject'] : 'null'); 
            $params['reply'] = $params['reply'];
            $params['fullname'] = $params['name'];
            $params['createdBy'] = 'customer';
            $params['threadType'] = 'create';
            $params['source'] = 'formbuilder';
            $params['role'] = 4;
            $params['customer'] = $userInstance;
            $params['user'] = $userInstance; 
            $params['type'] = $this->getDoctrine()->getRepository(TicketType::class)->find(1); 
            $params['message'] = $params['reply']; 
            $params['gdpr'] = ( isset($params['gdpr']) ? $params['gdpr'] : 'null');
            $params['order_no'] = ( isset($params['order_no']) ? $params['order_no'] : 'null');
            $params['file']  = ( isset($params['file']) ? $params['file'] : 'null');
           
            $ticketId = $this->get('ticket.service')->createTicketBase($params)->getTicket()->getId(); 
            $ticket = $em->getRepository('UVDeskCoreFrameworkBundle:Ticket')->findOneById($ticketId);

            if(!empty($ticket))
            {

                $customField = null;
                $ticketCustomFieldValue = null;
    
                if(!empty($request->request->get('customFields')))
                {
                    foreach($request->request->get('customFields') as $key=>$value)
                    {
                        $cf_id = $key;  
                        $cfRepo = $this->getDoctrine()->getRepository(CustomFields::class); 
                        $customField = $cfRepo->findOneBy([
                            'id' => $cf_id,
                        ]);
                        $cfvRepo = $this->getDoctrine()->getRepository(CustomFieldsValues::class); 
                        $cfValues = $cfvRepo->findOneBy([
                            'customFields' => $customField->getId(),
                        ]);    
                        $customField->setValue($value);
                        $ticketCustomFieldValue =  new TicketCustomFieldsValues();
                        $ticketCustomFieldValue->setTicket($ticket); 
                        $ticketCustomFieldValue->setTicketCustomFieldsValues($customField);
                        $ticketCustomFieldValue->setTicketCustomFieldValueValues($cfValues);
                        $ticketCustomFieldValue->setValue($value);
                        $em->persist($ticketCustomFieldValue);
                        $em->persist($ticket);
                        $em->flush(); 
                    } 
                }

                $submittedCustomFieldFiles = $request->files->get('customFields'); 
          
    
                if( !empty($submittedCustomFieldFiles) )
                {

    
                    $baseUploadPath = '/custom-fields/ticket/' . $ticket->getId() . '/';
                    $temporaryFiles = $request->files->get('customFields');
                
                    $uploadedFileCollection = [];
    
    
                    foreach($temporaryFiles as $key => $temporaryFile) {
                        $fileName = $fileUploadService->uploadFile($temporaryFile, $baseUploadPath, true);
                        $fileName['key'] = $key;
                        $uploadedFileCollection[] = $fileName;
                    }

                    $cfRepo = $this->getDoctrine()->getRepository(CustomFields::class); 

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
                            if (!empty($uploadedAttachment[0])) {

                                // $customField = $customFieldRepository->findOneById($uploadedFile['key']);
                                $customField = $cfRepo->findOneById($uploadedFile['key']);
                                $ticketCustomFieldValue = !empty($existingCustomFieldValue) ? $existingCustomFieldValue : new TicketCustomFieldsValues();
                                // $ticketCustomFieldValue->setValue($resourceURL);
                                $ticketCustomFieldValue->setValue(json_encode(['name' => $uploadedAttachment[0]['name'], 'path' => $uploadedAttachment[0]['path'], 'id' => $uploadedAttachment[0]['id']]));
                                $ticketCustomFieldValue->setTicketCustomFieldsValues($customField);
                                $ticketCustomFieldValue->setTicket($ticket);

                                $em->persist($ticketCustomFieldValue);
                                $em->persist($ticket);
                                $em->flush();
                            }
                        }
                    }
                } //end of file processing
        

            } // end of !empty ticket

            $this->addFlash('success', 'Ticket Created Successfully!');
            return new RedirectResponse($this->generateUrl('helpdesk_member_ticket_collection'));
        } 

        return $this->render('@_uvdesk_extension_uvdesk_form_component\FormBuilders\previewForm.html.twig', [
            'formbuilder' => $data,
            'customFields' => $customFieldsArray,
        ]);
    }
}