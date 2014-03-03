<?php

namespace Oro\Bundle\FilterBundle\Expression\Date;

class Token
{
    const TYPE_OPERATOR    = 'TYPE_OPERATOR';
    const TYPE_INTEGER     = 'TYPE_INTEGER';
    const TYPE_VARIABLE    = 'TYPE_VARIABLE';
    const TYPE_PUNCTUATION = 'TYPE_PUNCTUATION';
    const TYPE_TIME        = 'TYPE_TIME';
    const TYPE_DATE        = 'TYPE_DATE';

    /** @var int */
    private $type;

    /** @var mixed */
    private $value;

    /**
     * @param $type
     * @param $value
     */
    public function __construct($type, $value)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * Check whenever current token is instance of given type
     *
     * @param int   $type
     * @param mixed $value
     *
     * @return bool
     */
    public function is($type, $value = null)
    {
        return $this->getType() === $type && (null === $value || $this->getValue() == $value);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
