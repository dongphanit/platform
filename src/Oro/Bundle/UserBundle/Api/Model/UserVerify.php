<?php
namespace Oro\Bundle\UserBundle\Api\Model;

/**
 * The model for frontend API resource to retrieve API access key by customer user id and token.
 */
class UserVerify
{
    /** @var string */
    private $id;

    /** @var string */
    private $token;

    /**
     * Gets the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token.
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

}
