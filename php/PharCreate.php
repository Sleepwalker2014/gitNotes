<?php
/**
 * Created by PhpStorm.
 * User: marcel
 * Date: 27.05.17
 * Time: 12:30
 */
$phar = new Phar('gitNotes.phar', 0, 'gitNotes.phar');

$phar->buildFromDirectory(dirname(dirname(__FILE__)));
$phar->setStub("#!/usr/bin/env php".PHP_EOL.$phar->createDefaultStub('php/Cli.php'));
