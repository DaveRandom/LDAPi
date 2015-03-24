<?php

namespace LDAPi;

class Entry
{
    const MODE_BINARY = 0b01;
    const MODE_TEXT   = 0b10;

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
     * @param resource $entry
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
     * @param string $attribute
     * @param int $mode
     * @return array
     * @throws ValueRetrievalFailureException
     */
    public function getValues($attribute, $mode = self::MODE_BINARY)
    {
        $mode = (int)$mode;

        if ($mode === self::MODE_BINARY) {
            $func = 'ldap_get_values_len';
        } else if ($mode === self::MODE_TEXT) {
            $func = 'ldap_get_values';
        } else {
            throw new InvalidModeException($mode . ' is not a recognised value retrieval mode');
        }

        if (!$values = $func($this->link, $this->entry, $attribute)) {
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
