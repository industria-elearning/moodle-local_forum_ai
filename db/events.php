<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_forum\event\discussion_created',
        'callback'    => '\local_forum_ai\observer::discussion_created',
        'includefile' => '/local/forum_ai/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999,
    ],
];
