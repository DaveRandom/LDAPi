<?php

namespace LDAPi;

class ResultSet
{
    /**
     * @var resource ext/ldap link resource
     */
    private $link;

    /**
     * @var resource ext/ldap result resource
     */
    private $result;

    /**
     * @param resource $link
     * @param resource $result
     */
    public function __construct($link, $result)
    {
        $this->link = $link;
        $this->result = $result;
    }

    public function __destruct()
    {
        ldap_free_result($this->result);
    }

    /**
     * @param string $name
     */
    public function __get($name)
    {
        throw new NonExistentPropertyException('Property ' . $name . ' not defined for ' . get_class($this));
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        throw new NonExistentPropertyException('Property ' . $name . ' not defined for ' . get_class($this));
    }

    /**
     * @param int $estimated
     * @return string
     * @throws PaginationFailureException
     */
    public function controlPagedResult(&$estimated = null)
    {
        if (!ldap_control_paged_result_response($this->link, $this->result, $cookie, $estimated)) {
            throw new PaginationFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $cookie;
    }

    /**
     * @return int
     * @throws EntryCountRetrievalFailureException
     */
    public function entryCount()
    {
        if (!$result = ldap_count_entries($this->link, $this->result)) {
            throw new EntryCountRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $result;
    }

    /**
     * @return Entry|null
     * @throws EntryRetrievalFailureException
     */
    public function firstEntry()
    {
        if (!$entry = ldap_first_entry($this->link, $this->result)) {
            if (0 !== $errNo = ldap_errno($this->link)) {
                throw new EntryRetrievalFailureException(ldap_error($this->link), $errNo);
            }

            return null;
        }

        return new Entry($this->link, $entry);
    }

    /**
     * @return Reference|null
     * @throws ReferenceRetrievalFailureException
     */
    public function firstReference()
    {
        if (!$reference = ldap_first_reference($this->link, $this->result)) {
            if (0 !== $errNo = ldap_errno($this->link)) {
                throw new ReferenceRetrievalFailureException(ldap_error($this->link), $errNo);
            }

            return null;
        }

        return new Reference($this->link, $reference);
    }

    /**
     * @return array
     * @throws InformationRetrievalFailureException
     */
    public function parse()
    {
        if (!ldap_parse_result($this->link, $this->result, $errCode, $matchedDN, $errMsg, $referrals)) {
            throw new InformationRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return [
            'errcode' => $errCode,
            'errmsg' => $errMsg,
            'matcheddn' => $matchedDN,
            'referrals' => $referrals,
        ];
    }

    /**
     * @return array
     * @throws ValueRetrievalFailureException
     */
    public function getEntries()
    {
        if (!$entries = ldap_get_entries($this->link, $this->result)) {
            throw new ValueRetrievalFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $entries;
    }
}
