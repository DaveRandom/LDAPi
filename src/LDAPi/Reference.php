<?php

namespace LDAPi;

class Reference
{
    /**
     * @var resource ext/ldap link resource
     */
    private $link;

    /**
     * @var resource ext/ldap reference resource
     */
    private $reference;

    /**
     * @param resource $link
     * @param resource $reference
     */
    public function __construct($link, $reference)
    {
        $this->link = $link;
        $this->reference = $reference;
    }

    /**
     * @return Reference|null
     * @throws ReferenceRetrievalFailureException
     */
    public function nextReference()
    {
        if (!$reference = ldap_next_reference($this->link, $this->reference)) {
            if (0 !== $errNo = ldap_errno($this->link)) {
                throw new ReferenceRetrievalFailureException(ldap_error($this->link), $errNo);
            }

            return null;
        }

        return new Reference($this->link, $reference);
    }

    /**
     * @return string[]
     * @throws ValueRetrievalFailureException
     */
    public function parse()
    {
        if (!ldap_parse_reference($this->link, $this->reference, $referrals)) {
            throw new ValueRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $referrals;
    }
}
