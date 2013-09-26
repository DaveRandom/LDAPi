<?php

namespace LDAPi;

class Entry
{
    /**
     * @var resource ext/ldap link resource
     */
    private $link;

    /**
     * @var resource ext/ldap entry resource
     */
    private $entry;

    /**
     * @param resource $link
     * @param resource $result
     */
    public function __construct($link, $entry)
    {
        $this->link = $link;
        $this->entry = $entry;
    }

    /**
     * @return Entry|null
     * @throws EntryRetrievalFailureException
     */
    public function nextEntry()
    {
        if (!$entry = ldap_next_entry($this->link, $this->entry)) {
            if (0 !== $errNo = ldap_errno($this->link)) {
                throw new EntryRetrievalFailureException(ldap_error($this->link), $errNo);
            }

            return null;
        }

        return new Entry($this->link, $entry);
    }

    /**
     * @return array
     * @throws ValueRetrievalFailureException
     */
    public function getValues($attribute)
    {
        if (!$values = ldap_get_values($this->link, $this->entry, $attribute)) {
            throw new ValueRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $values;
    }

    /**
     * @return array
     * @throws ValueRetrievalFailureException
     */
    public function getAttributes()
    {
        if (!$attributes = ldap_get_attributes($this->link, $this->entry)) {
            throw new ValueRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $attributes;
    }

    /**
     * @return string
     * @throws ValueRetrievalFailureException
     */
    public function getDN()
    {
        if (!$dn = ldap_get_dn($this->link, $this->entry)) {
            throw new ValueRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $dn;
    }
}
