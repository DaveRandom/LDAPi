<?php

namespace LDAPi;

use ArrayObject;

/**
 * Class Modification
 * @package LDAPi
 * @property string $attributeName
 * @property int $operation
 * @property \ArrayObject|array $data
 * @property mixed $value
 */
class Modification
{
    const OP_ADD        = LDAP_MODIFY_BATCH_ADD;
    const OP_REMOVE     = LDAP_MODIFY_BATCH_REMOVE;
    const OP_REMOVE_ALL = LDAP_MODIFY_BATCH_REMOVE_ALL;
    const OP_REPLACE    = LDAP_MODIFY_BATCH_REPLACE;

    /**
     * @var array
     */
    private $data = [
        'attributeName' => null,
        'operation'     => null,
        'values'        => null,
    ];

    public function __construct()
    {
        $this->data['values'] = new ArrayObject;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'attributeName':
                $this->data['attributeName'] = (string)$value;
                break;

            case 'operation':
                if (!in_array($value, [self::OP_ADD, self::OP_REMOVE, self::OP_REMOVE_ALL, self::OP_REPLACE])) {
                    throw new InvalidModeException('Operation must be one of the Modification::OP_* constants');
                }

                $this->data['operation'] = (int)$value;

                if ($this->data['operation'] === self::OP_REMOVE_ALL) {
                    $this->data['values'] = new ArrayObject;
                }
                break;

            case 'values':
                if ($this->data['operation'] === self::OP_REMOVE_ALL) {
                    throw new InvalidModeException('REMOVE_ALL operations cannot include a value set');
                }

                if ($value instanceof ArrayObject) {
                    $this->data['values'] = $value;
                } else if (is_array($value)) {
                    $this->data['values'] = new ArrayObject($value);
                } else {
                    throw new InvalidValueSetException('Value set must be specified as an array or an ArrayObject');
                }
                break;

            default:
                throw new NonExistentPropertyException('Property ' . $name . ' not defined for ' . get_class($this));
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new NonExistentPropertyException('Property ' . $name . ' not defined for ' . get_class($this));
        }

        return $this->data[$name];
    }
}
