<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_form")
 */
class Form
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */ 
    protected $formName;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $subject; 

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gdpr; 


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $order_no;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $file; 

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields", mappedBy="form")
     */
    protected $customFields;

     /**
     * Constructor
     */
    public function __construct()
    {
        $this->customFields = new \Doctrine\Common\Collections\ArrayCollection();
    
    }

    /**
     * Add customField
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields $customField
     * @return Form
     */
    public function addCustomField(CustomFields $customField)
    {
        $this->customFields->add($customField);

        return $this;
    }

    /**
     * Remove customField
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields $customField
     */
    public function removeCustomField(CustomFields $customField)
    {
        $this->customFields->removeElement($customField);
    }

    /**
     * Get customFields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomFields()
    {   
        
        return $this->customFields;
    }

    /**
     * Update customFields
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection
     * @return Form
     */
    public function updateCustomFields( ArrayCollection $collection)
    {
        $this->customFields->clear();
        $this->customFields = $collection;
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
     * Set formName
     *
     * @param string $formName
     * @return Form
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;
        return $this;
    }


    /**
     * Get formName
     *
     * @return string 
     */
    public function getFormName()
    {
        return $this->formName; 
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Form
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type; 
    }


    /**
     * Set type
     *
     * @param string $type
     * @return Form
     */
    public function setType($type)
    {
        $this->type = $type; 
        return $this; 
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject; 
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this; 
    }

    /**
     * Get gdpr
     *
     * @return string
     */
    public function getGDPR()
    {
        return $this->gdpr; 
    }

    /**
     * Set gdpr
     *
     * @param string $gdpr
     * @return Form
     */
    public function setGDPR($gdpr)
    {
        $this->gdpr = $gdpr; 
        return $this; 
    }

    /**
     * Get order_no
     *
     * @return string
     */
    public function getOrderNo()
    {
        return $this->order_no;
    }

    /**
     * Set order_no
     *
     * @param string $order_no
     * @return Form
     */
    public function setOrderNo($order_no)
    {
        $this->order_no = $order_no;
        return $this; 
    }
    
    /**
     * Get file
     *
     * @return string
     */

    public function getFile()
    {
        return $this->file; 
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Form
     */

    public function setFile($file)
    {
        $this->file = $file; 
        return $this; 
    }

    

}