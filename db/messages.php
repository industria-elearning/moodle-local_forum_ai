<?php

defined('MOODLE_INTERNAL') || die();

$messageproviders = array(
    'ai_approval_request' => array(
        // 'capability' => 'mod/forum:replypost',
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED,
            'email' => MESSAGE_PERMITTED,
        ),
    ),
    'ai_response_approved' => array(
        // 'capability' => 'mod/forum:replypost',
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED,
            'email' => MESSAGE_DISALLOWED,
        ),
    ),
);
