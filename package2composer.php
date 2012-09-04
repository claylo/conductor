#!/usr/bin/env php
<?php
/**
 * This file is part of the Conductor package
 *
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://opensource.org/licenses/MIT MIT License
 */

// installed yet?
if (strpos('@php_dir@', '@php_dir') === 0) {
    ini_set('include_path', __DIR__);
}
require_once 'Conductor/Autoload.php';

return Conductor\Converter\Package2XmlToComposer::main();