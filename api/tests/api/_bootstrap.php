<?php

use Codeception\Util\Fixtures;

/*
 * Используем класс Fixtures для хранения фикстур
 * FIXTURES_DIR - константа заданная в главном файле _bootstrap
 */
Fixtures::add('users', require(FIXTURES_DIR . 'users.php'));
Fixtures::add('accounts', require(FIXTURES_DIR . 'accounts.php'));
