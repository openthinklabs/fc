<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_saved_custom_fields")
 */
class SavedCustomFields
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */  
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="integer", )
     * @Assert\NotBlank
     */  
    protected $formId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $arrayOfIds; 
    
   
    /**
     * Get arrayOfIds
     * @return string
     */
    public function getArrayOfIds()
    {
        return $this->arrayOfIds; 
    }

    /**
     * Set arrayOfIds
     * @param string $arrayOfIds
     * @return SavedCustomFields
     */
    public function setArrayOfIds($arrayOfIds)
    {
        $this->arrayOfIds = $arrayOfIds;
        return $this; 
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get formId
     * @return integer
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * Set formId
     *
     * @param string $formId
     * @return SavedCustomFields
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
        return $this; 
    }


  

    

}