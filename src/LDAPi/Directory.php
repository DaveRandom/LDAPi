<?php

namespace LDAPi;

class Directory
{
    /**
     * @var resource ext/ldap link resource
     */
    private $link;

    /**
     * @var bool Whether the connection is currently bound to a directory server
     */
    private $bound = false;

    private function checkConnected()
    {
        if (!$this->link) {
            throw new UnavailableException('An active connection to the directory is not available');
        }
    }

    private function checkBound()
    {
        if (!$this->bound) {
            throw new UnavailableException('An active bound connection to the directory is not available');
        }
    }

    private function createResultSet($result)
    {
        return new ResultSet($this->link, $result);
    }

    public function __destruct()
    {
        if ($this->bound) {
            $this->unbind();
        }
    }

    /**
     * @param string $dn
     * @param array  $entry
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function add($dn, array $entry)
    {
        $this->checkBound();

        if (!@ldap_add($this->link, $dn, $entry)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param string $password
     * @throws UnavailableException
     * @throws BindFailureException
     */
    public function bind($dn = null, $password = null)
    {
        $this->checkConnected();

        if (!@ldap_bind($this->link, $dn, $password)) {
            throw new BindFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        $this->bound = true;
    }

    /**
     * @param string $dn
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     * @throws UnavailableException
     * @throws ReadFailureException
     */
    public function compare($dn, $attribute, $value)
    {
        $this->checkBound();

        if (-1 === $result = @ldap_compare($this->link, $dn, $entry)) {
            throw new ReadFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $result;
    }

    /**
     * @param string $host
     * @param int    $port
     * @throws AlreadyAvailableException
     * @throws ConnectFailureException
     */
    public function connect($host, $port = 389)
    {
        if ($this->link) {
            throw new AlreadyAvailableException('An active connection to the directory is already available');
        }

        if (!$this->link = @ldap_connect($host, $port)) {
            throw new ConnectFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param int    $pageSize
     * @param bool   $isCritical
     * @param string $cookie
     * @throws UnavailableException
     * @throws PaginationFailureException
     */
    public function controlPagedResult($pageSize, $isCritical = false, $cookie = '')
    {
        $this->checkBound();

        if (!@ldap_control_paged_result($this->link, $pageSize, $isCritical, $cookie)) {
            throw new PaginationFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function delete($dn)
    {
        $this->checkBound();

        if (!@ldap_delete($this->link, $dn)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param int $opt
     * @return mixed
     * @throws UnavailableException
     * @throws OptionFailureException
     */
    public function getOption($opt)
    {
        $this->checkConnected();

        if (!@ldap_get_option($this->link, $opt, $value)) {
            throw new OptionFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $value;
    }

    /**
     * @param string $dn
     * @param string $filter
     * @param array  $attributes
     * @param bool   $attrsOnly
     * @param int    $sizeLimit
     * @param int    $timeLimit
     * @param int    $deRef
     * @return ResultSet
     * @throws UnavailableException
     * @throws ReadFailureException
     */
    public function listChildren($dn, $filter, array $attributes = null, $attrsOnly = false, $sizeLimit = 0, $timeLimit = 0, $deRef = LDAP_DEREF_NEVER)
    {
        $this->checkBound();

        if (!$result = @ldap_list($this->link, $dn, $filter, (array)$attributes, (int)(bool)$attrsOnly, $sizeLimit, $timeLimit, $deRef)) {
            throw new ReadFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $this->createResultSet($result);
    }

    /**
     * @param string $dn
     * @param array  $entry
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function modAdd($dn, array $entry)
    {
        $this->checkBound();

        if (!@ldap_mod_add($this->link, $dn, $entry)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param array  $entry
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function modDel($dn, array $entry)
    {
        $this->checkBound();

        if (!@ldap_mod_del($this->link, $dn, $entry)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param array  $entry
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function modReplace($dn, array $entry)
    {
        $this->checkBound();

        if (!@ldap_mod_replace($this->link, $dn, $entry)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param array  $entry
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function modify($dn, array $entry)
    {
        $this->checkBound();

        if (!@ldap_modify($this->link, $dn, $entry)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param string $filter
     * @param array  $attributes
     * @param bool   $attrsOnly
     * @param int    $sizeLimit
     * @param int    $timeLimit
     * @param int    $deRef
     * @return ResultSet
     * @throws UnavailableException
     * @throws ReadFailureException
     */
    public function read($dn, $filter, array $attributes = null, $attrsOnly = false, $sizeLimit = 0, $timeLimit = 0, $deRef = LDAP_DEREF_NEVER)
    {
        $this->checkBound();

        if (!$result = @ldap_read($this->link, $dn, $filter, (array)$attributes, (int)(bool)$attrsOnly, $sizeLimit, $timeLimit, $deRef)) {
            throw new ReadFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $this->createResultSet($result);
    }

    /**
     * @param string $dn
     * @param string $newRDN
     * @param string $newParent
     * @param bool   $deleteOldRDN
     * @throws UnavailableException
     * @throws WriteFailureException
     */
    public function rename($dn, $newRDN, $newParent, $deleteOldRDN = true)
    {
        $this->checkBound();

        if (!@ldap_rename($this->link, $dn, $newRDN, $newParent, $deleteOldRDN)) {
            throw new WriteFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param string $dn
     * @param string $password
     * @param string $saslMech
     * @param string $saslRealm
     * @param string $saslAuthcId
     * @param string $saslAuthzId
     * @param string $props
     * @throws UnavailableException
     * @throws BindFailureException
     */
    public function saslBind($dn = null, $password = null, $saslMech = null, $saslRealm = null, $saslAuthcId = null, $saslAuthzId = null, $props = null)
    {
        $this->checkConnected();

        if (!@ldap_sasl_bind($this->link, $dn, $password, $saslMech, $saslRealm, $saslAuthcId, $saslAuthzId, $props)) {
            throw new BindFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        $this->bound = true;
    }

    /**
     * @param string $dn
     * @param string $filter
     * @param array  $attributes
     * @param bool   $attrsOnly
     * @param int    $sizeLimit
     * @param int    $timeLimit
     * @param int    $deRef
     * @return ResultSet
     * @throws UnavailableException
     * @throws ReadFailureException
     */
    public function search($dn, $filter, array $attributes = null, $attrsOnly = false, $sizeLimit = 0, $timeLimit = 0, $deRef = LDAP_DEREF_NEVER)
    {
        $this->checkBound();

        if (!$result = @ldap_search($this->link, $dn, $filter, (array)$attributes, (int)(bool)$attrsOnly, $sizeLimit, $timeLimit, $deRef)) {
            throw new ReadFailureException(ldap_error($this->link), ldap_errno($this->link));
        }

        return $this->createResultSet($result);
    }

    /**
     * @param int   $opt
     * @param mixed $value
     * @throws UnavailableException
     * @throws OptionFailureException
     */
    public function setOption($opt, $value)
    {
        $this->checkConnected();

        if (!@ldap_set_option($this->link, $opt, $value)) {
            throw new OptionFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @param callable $callback
     * @throws UnavailableException
     * @throws OptionFailureException
     */
    public function setRebindProc(callable $callback)
    {
        $this->checkConnected();

        if (!@ldap_set_rebind_proc($this->link, $callback)) {
            throw new OptionFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @throws UnavailableException
     * @throws AlreadyAvailableException
     * @throws EncryptionFailureException
     */
    public function startTLS()
    {
        $this->checkConnected();
        if ($this->bound) {
            throw new AlreadyAvailableException('An active bound connection to the directory is already available');
        }

        if (!@ldap_start_tls($this->link)) {
            throw new EncryptionFailureException(ldap_error($this->link), ldap_errno($this->link));
        }
    }

    /**
     * @throws UnavailableException
     */
    public function unbind()
    {
        $this->checkBound();

        @ldap_unbind($this->link);

        $this->link = null;
        $this->bound = false;
    }
}
