<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomFieldDecryptLog
 * @ORM\Entity(repositoryClass="UVDesk\CommunityPackages\UVDesk\FormComponent\Repository\CustomFieldDescryptLogRepository")
 * @ORM\Table(name="uv_pkg_uvdesk_form_component_custom_field_decrypt_log")
 * @ORM\HasLifecycleCallbacks
 */
class CustomFieldDecryptLog
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $accessGranted;

    /**
     * @var \Webkul\UVDesk\CoreFrameworkBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="\Webkul\UVDesk\CoreFrameworkBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;

    /**
     * @var \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues
     * @ORM\ManyToOne(targetEntity="TicketCustomFieldsValues")
     * @ORM\JoinColumn(name="ticket_custom_field_values_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $ticketCustomFieldsValues;


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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CustomFieldDecryptLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set accessGranted
     *
     * @param boolean $accessGranted
     * @return CustomFieldDecryptLog
     */
    public function setAccessGranted($accessGranted)
    {
        $this->accessGranted = $accessGranted;

        return $this;
    }

    /**
     * Get accessGranted
     *
     * @return boolean 
     */
    public function getAccessGranted()
    {
        return $this->accessGranted;
    }

    /**
     * Set user
     *
     * @param \Webkul\UVDesk\CoreFrameworkBundle\Entity\User $user
     * @return CustomFieldDecryptLog
     */
    public function setUser(\Webkul\UVDesk\CoreFrameworkBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \Webkul\UVDesk\CoreFrameworkBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ticketCustomFieldsValues
     *
     * @param \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues $ticketCustomFieldsValues
     * @return CustomFieldDecryptLog
     */
    public function setTicketCustomFieldsValues(\UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues $ticketCustomFieldsValues = null)
    {
        $this->ticketCustomFieldsValues = $ticketCustomFieldsValues;
        return $this;
    }

    /**
     * Get ticketCustomFieldsValues
     *
     * @return \UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\TicketCustomFieldsValues 
     */
    public function getTicketCustomFieldsValues()
    {
        return $this->ticketCustomFieldsValues;
    }
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $userType;

    /**
     * Set userType
     *
     * @param string $userType
     * @return CustomFieldDecryptLog
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType()
    {
        return $this->userType;
    }

}
