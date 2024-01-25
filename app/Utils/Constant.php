<?php

namespace App\Utils;

class Constant
{
    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';
    
    const ADMIN_SCOPE = [
        'create-user',
        'read-user',
        'update-user',
        'delete-user',
        'create-table',
        'read-table',
        'update-table',
        'delete-table',
        'create-booking',
        'read-booking',
        'update-booking',
        'delete-booking',
        'create-offline-booking',
        'read-offline-booking',
        'update-offline-booking',
        'delete-offline-booking',
    ];

    const USER_SCOPE = [
        'read-user',
        'update-user',
        'read-table',
        'create-booking',
        'read-booking',
        'update-booking',
        'delete-booking',
    ];
}