<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

/**
 * TicketCustomFieldsValues
 * @ORM\Entity
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_ticket_custom_fields_values")
 */
class TicketCustomFieldsValues
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */  
    private $id;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @var \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields
     * @ORM\ManyToOne(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields", inversedBy="ticketValues")
     * @ORM\JoinColumn(name="custom_field_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    private $ticketCustomFieldsValues;

    /**
     * @var \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues
     * @ORM\ManyToOne(targetEntity="UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues", inversedBy="ticketValues")
     * @ORM\JoinColumn(name="custom_field_value_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    private $ticketCustomFieldValueValues;

    /**
     * @var \Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket
     * @ORM\ManyToOne(targetEntity="Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket", inversedBy="customFieldValues")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */ 
    private $ticket;

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
     * Set value
     *
     * @param string $value
     * @return TicketCustomFieldsValues
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
     * Set ticketCustomFieldsValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields $ticketCustomFieldsValues
     * @return TicketCustomFieldsValues
     */
    public function setTicketCustomFieldsValues(\UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields $ticketCustomFieldsValues = null)
    {
        $this->ticketCustomFieldsValues = $ticketCustomFieldsValues;

        return $this;
    }

    /**
     * Get ticketCustomFieldsValues
     *
     * @return \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields 
     */
    public function getTicketCustomFieldsValues()
    {
        return $this->ticketCustomFieldsValues;
    }

    /**
     * Set ticketCustomFieldValueValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues $ticketCustomFieldValueValues
     * @return TicketCustomFieldsValues
     */
    public function setTicketCustomFieldValueValues(\UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues $ticketCustomFieldValueValues = null)
    {
        $this->ticketCustomFieldValueValues = $ticketCustomFieldValueValues;

        return $this;
    }

    /**
     * Get ticketCustomFieldValueValues
     *
     * @return \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFieldsValues 
     */
    public function getTicketCustomFieldValueValues()
    {
        return $this->ticketCustomFieldValueValues;
    }

    /**
     * Set ticket
     *
     * @param \Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket $ticket
     * @return TicketCustomFieldsValues
     */
    public function setTicket(\Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $encrypted;


    /**
     * Set encrypted
     *
     * @param boolean $encrypted
     * @return TicketCustomFieldsValues
     */
    public function setEncrypted($encrypted)
    {
        $this->encrypted = $encrypted;

        return $this;
    }

    /**
     * Get encrypted
     *
     * @return boolean 
     */
    public function getEncrypted()
    {
        return $this->encrypted;
    }

    public function encryptEntity()
    {
        $customField = $this->getTicketCustomFieldsValues();
        if ($customField->getEncryption() == true) {
            $key = Key::loadFromAsciiSafeString('def0000050b51ede76fb17881efa16d2224a0fca077f936075910b7498e22684c0adced7f1f886eb738538bb5e1ed5b6d4be3bd6c00d20aff310c4ab705d5bf082db3466');

            $this->setValue(Crypto::encrypt($this->getValue(), $key));
            $this->setEncrypted(true);
        }
    }

    public function decryptEntity()
    {
        if ($this->getEncrypted() == true) {
            $key = Key::loadFromAsciiSafeString('def0000050b51ede76fb17881efa16d2224a0fca077f936075910b7498e22684c0adced7f1f886eb738538bb5e1ed5b6d4be3bd6c00d20aff310c4ab705d5bf082db3466');

            try {
                $this->setValue(Crypto::decrypt($this->getValue(), $key));
            } catch (WrongKeyOrModifiedCiphertextException $e) {
                // Do nothing. Probably not encrypted
            }
        }
    }

    private $visibleValues;

    public function getVisibleValues()
    {
        if(in_array($this->getTicketCustomFieldsValues()->getFieldType(), ['checkbox'])) {
            $visibleValues = array_map([$this, 'getVisibleValue'], explode(',', $this->value));
        } elseif(in_array($this->getTicketCustomFieldsValues()->getFieldType(), ['select', 'radio'])) {
            $visibleValues = $this->getVisibleValue($this->value);
        }

        return !empty($visibleValues) ? $visibleValues : trim($this->value, '"');
    }

    private function getVisibleValue($index)
    {
        $result = '';
        $index = trim($index, '[]"');
        foreach($this->ticketCustomFieldsValues->getCustomFieldValues() as $tcf) {
            if($tcf->getId() == $index) {
                $result = $tcf->getName();
                break;
            }
        }

        return $result;
    }

    private $fieldName;

    public function getFieldName()
    {
        return $this->ticketCustomFieldsValues->getName();
    }
}
