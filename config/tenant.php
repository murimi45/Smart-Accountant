<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-tenant security logging
    |--------------------------------------------------------------------------
    |
    | When enabled, suspected cross-tenant access attempts are written to the
    | "security" log channel (see config/logging.php).
    |
    */

    'security_log' => env('TENANT_SECURITY_LOG', true),

    /*
    |--------------------------------------------------------------------------
    | Platform administrator role
    |--------------------------------------------------------------------------
    |
    | Platform users have role "platform" and school_id = null. They must use
    | PlatformContext::runAsPlatform() for elevated access — never bypass scopes
    | implicitly.
    |
    */

    'platform_role' => 'platform',

];
