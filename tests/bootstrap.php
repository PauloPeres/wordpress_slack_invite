<?php

// Bootstrap file for PHPUnit tests.
// Load WordPress option stubs so tests can run without WordPress.
require_once __DIR__ . '/wp-option-stubs.php';

// Additional bootstrap logic (autoload, etc.) can be placed here.
require_once __DIR__ . '/../includes/slack-interface/class-slack-access.php';
require_once __DIR__ . '/../includes/slack-interface/class-slack-team.php';
