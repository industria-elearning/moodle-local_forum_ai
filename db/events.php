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
    [
        'eventname'   => '\core\event\course_module_created',
        'callback'    => '\local_forum_ai\observer::course_module_created',
        'includefile' => '/local/forum_ai/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999,
    ],
];
