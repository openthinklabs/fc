<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * CustomFields
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_custom_fields")
 * @ORM\Entity(repositoryClass="UVDesk\CommunityPackages\UVDesk\FormComponent\Repository\CustomFieldsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CustomFields
{
    /**
     * @var integer
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please select a FieldType")
     */ 
    private $fieldType;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $required;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $sortOrder;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateUpdated;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues", mappedBy="customFields")
     */
    private $customFieldValues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     */
    private $customFieldValuesSorted;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues", mappedBy="ticketCustomFieldsValues")
     */
    private $ticketValues;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\ManyToMany(targetEntity="Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType")
     * @ORM\JoinTable(name="uv_pkg_uvdesk_form_component_custom_fields_types",
     *      joinColumns={@ORM\JoinColumn(name="custom_fields_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ticket_type_id", referencedColumnName="id")})
     * 
     */
    private $customFieldsDependency;

    /**
     * @var \Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType
     */
    public $dependency;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->customFieldValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ticketValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->customFieldsDependency = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return CustomFields
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
     * Set fieldType
     *
     * @param string $fieldType
     * @return CustomFields
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * Get fieldType
     *
     * @return string 
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return CustomFields
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return CustomFields
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return CustomFields
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     * @return CustomFields
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer 
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return CustomFields
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return CustomFields
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Add customFieldValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues $customFieldValues
     * @return CustomFields
     */
    public function addCustomFieldValue(CustomFieldsValues $customFieldValues)
    {
        $this->customFieldValues[] = $customFieldValues;

        return $this;
    }

    /**
     * Remove customFieldValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues $customFieldValues
     */
    public function removeCustomFieldValue(CustomFieldsValues $customFieldValues)
    {
        $this->customFieldValues->removeElement($customFieldValues);
    }

    public function sortCustomFieldValues($a, $b)
    {
        return strcmp($a->getSortOrder(), $b->getSortOrder());
    }

    /**
     * Get customFieldValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomFieldValues($force = false)
    {   
        if(!$this->customFieldValuesSorted || $force){
            $this->customFieldValuesSorted = $this->customFieldValues->toArray();
            usort($this->customFieldValuesSorted , array($this, "sortCustomFieldValues"));
        }
        
        return $this->customFieldValuesSorted;
    }

    /**
     * Add ticketValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues $ticketValues
     * @return CustomFields
     */
    public function addTicketValue(TicketCustomFieldsValues $ticketValues)
    {
        $this->ticketValues[] = $ticketValues;
        return $this;
    }

    /**
     * Remove ticketValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues $ticketValues
     */
    public function removeTicketValue(TicketCustomFieldsValues $ticketValues)
    {
        $this->ticketValues->removeElement($ticketValues);
    }

    /**
     * Get ticketValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTicketValues()
    {
        return $this->ticketValues;
    }

    /**
     * Add customFieldsDependency
     *
     * @param \Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType $customFieldsDependency
     * @return CustomFields
     */
    public function addCustomFieldsDependency(\Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType $customFieldsDependency)
    {
        $this->customFieldsDependency[] = $customFieldsDependency;

        return $this;
    }

    /**
     * Remove customFieldsDependency
     *
     * @param \Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType $customFieldsDependency
     */
    public function removeCustomFieldsDependency(\Webkul\UVDesk\CoreFrameworkBundle\Entity\TicketType $customFieldsDependency)
    {
        $this->customFieldsDependency->removeElement($customFieldsDependency);
    }

    /**
     * Get customFieldsDependency
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomFieldsDependency()
    {
        return $this->customFieldsDependency;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->dateAdded = new \DateTime();
        $this->dateUpdated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->dateUpdated = new \DateTime();
    }
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please select a Agent Type")
     */
    private $agentType;

    /**
     * Set agentType
     *
     * @param string $agentType
     * @return CustomFields
     */
    public function setAgentType($agentType)
    {
        $this->agentType = $agentType;

        return $this;
    }

    /**
     * Get agentType
     *
     * @return string 
     */
    public function getAgentType()
    {
        return $this->agentType;
    }
    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $validation;


    /**
     * Set validation
     *
     * @param array $validation
     * @return CustomFields
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation
     *
     * @return array 
     */
    public function getValidation()
    {
        return $this->validation;
    }
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     */
    private $encryption;


    /**
     * Set encryption
     *
     * @param boolean $encryption
     * @return CustomFields
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;

        return $this;
    }

    /**
     * Get encryption
     *
     * @return boolean 
     */
    public function getEncryption()
    {
        return $this->encryption;
    }
}
