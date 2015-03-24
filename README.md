LDAPi
=====

A simple object oriented wrapper around PHP's LDAP extension. No frills, just a slightly cleaner API for use in object oriented applications.

Requirements
------------

 - PHP 5.4.0 or higher
 - ext/ldap

Installation
------------

Preferably via [Composer](http://getcomposer.org/).

Example usage
-------------

    <?php

    $link = new LDAPi\Directory;

    try {
        $link->connect('127.0.0.1', 389);
        $link->bind('Manager', 'managerpassword');

        $result = $link->search('cn=Users', 'objectClass=User', ['cn']);

        $entry = $result->firstEntry();
        do {
            print_r($entry->getAttributes());
        } while($entry = $entry->nextEntry());
    } catch(LDAPi\DirectoryOperationFailureException $e) {
        exit('An error occurred: ' . $e->getMessage());
    }
