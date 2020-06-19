<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomFieldsValues
 * 
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_custom_fields_values")
 * @ORM\Entity()
 */
class CustomFieldsValues
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=255)
     */
    private $name;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $sortOrder;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues",
     *      mappedBy="ticketCustomFieldsValues")
     * 
     */ 
    private $ticketValues;

    /**
     * @var \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields
     * 
     * @ORM\ManyToOne(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields", inversedBy="customFields")
     * @ORM\JoinColumn(name="custom_field_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */ 
    private $customFields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ticketValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CustomFieldsValues
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
     * Set sortOrder
     *
     * @param integer $sortOrder
     * @return CustomFieldsValues
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
     * Add ticketValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues $ticketValues
     * @return CustomFieldsValues
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
     * Set customFields
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields $customFields
     * @return CustomFieldsValues
     */
    public function setCustomFields(CustomFields $customFields = null)
    {
        $this->customFields = $customFields;

        return $this;
    }

    /**
     * Get customFields
     *
     * @return \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields 
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }
}
