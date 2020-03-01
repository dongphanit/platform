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
    protected $output;

    /**
     * Gets the API access key that should be used for subsequent API requests.
     *
     * @return string|null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the API access key belongs to the customer user with the given email and password.
     *
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }



}