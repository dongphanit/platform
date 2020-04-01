<?php

namespace Oro\Bundle\OrganizationBundle\Api\Model;

/**
 * The model for frontend API resource to retrieve API access key by customer user email and password.
 */
class Contact
{
    /** @var string */
    private $lstPhone;

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setLstPhone($lstPhone)
    {
        $this->lstPhone = $lstPhone;
    }

    /**
     * Gets the password.
     *
     * @return string
     */
    public function getLstPhone()
    {
        return $this->lstPhone;
    }

    /**
     * @var Collection|Customer[]
     */
    protected $data;

    /**
     * Gets the API access key that should be used for subsequent API requests.
     *
     * @return string|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the API access key belongs to the customer user with the given email and password.
     *
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }



}