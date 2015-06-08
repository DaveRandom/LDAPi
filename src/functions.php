<?php

namespace {
    if (!function_exists('ldap_escape')) {
        define('LDAP_ESCAPE_FILTER', 0x01);
        define('LDAP_ESCAPE_DN',     0x02);

        /**
         * Escape strings for safe use in LDAP filters and DNs
         *
         * @param string $subject
         * @param string $ignore
         * @param int    $flags
         * @return string
         */
        function ldap_escape($subject, $ignore = '', $flags = 0)
        {
            $subject = (string) $subject;
            $ignore = (string) $ignore;
            $flags = (int) $flags;

            if ($subject === '') {
                return '';
            }

            $charList = [];
            if ($flags & LDAP_ESCAPE_FILTER) {
                $charList = ["\\", "*", "(", ")", "\x00"];
            }
            if ($flags & LDAP_ESCAPE_DN) {
                $charList = array_merge($charList, ["\\", ",", "=", "+", "<", ">", ";", "\"", "#"]);
            }
            if (!$charList) {
                for ($i = 0; $i < 256; $i++) {
                    $charList[] = chr($i);
                }
            }
            $charList = array_flip($charList);

            for ($i = 0; isset($ignore[$i]); $i++) {
                unset($charList[$ignore[$i]]);
            }

            foreach ($charList as $key => &$value) {
                $value = sprintf('\%02x', ord($key));
            }

            return strtr($subject, $charList);
        }
    }

    if (!function_exists('ldap_modify_batch')) {
        define('LDAP_MODIFY_BATCH_ADD',        1);
        define('LDAP_MODIFY_BATCH_REMOVE',     2);
        define('LDAP_MODIFY_BATCH_REMOVE_ALL', 18);
        define('LDAP_MODIFY_BATCH_REPLACE',    3);
    }
}
