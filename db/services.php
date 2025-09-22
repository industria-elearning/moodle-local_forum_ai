<?php
$functions = [
    'local_forum_ai_get_details' => [
        'classname'   => 'local_forum_ai\\external\\get_details',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Obtiene detalles del debate y respuesta AI',
        'type'        => 'read',
        'ajax'        => true,
    ],

    'local_forum_ai_approve_response' => [
        'classname'   => 'local_forum_ai\\external\\approve_response',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Aprueba o rechaza respuesta AI',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_forum_ai_update_response' => [
        'classname'   => 'local_forum_ai\\external\\update_response',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Actualiza el mensaje generado por la IA en un pending',
        'type'        => 'write',
        'ajax'        => true,
    ],
];
