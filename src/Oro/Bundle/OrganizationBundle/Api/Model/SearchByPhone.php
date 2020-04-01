<?php

namespace Oro\Bundle\OrganizationBundle\Api\Model;

/**
 * The model for frontend API resource to retrieve API access key by customer user email and password.
 */
class SearchByPhone
{
    /** @var string */
    private $phone;

     /** @var string */
     private $countryCode;

    /**
     * Sets the phone.
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Gets the password.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the Country Code.
     *
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Gets the password.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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