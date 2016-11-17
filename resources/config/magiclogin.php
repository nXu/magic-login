<?php

return [
    /**
     * The hash function used in the generation and verification
     * of the HMAC signature. Default is sha256. This is also the
     * default algorithm used in the mobile application as well.
     *
     * For PHP implementations see:
     * http://php.net/manual/en/function.hash-algos.php
     */
    'hash_algo' => 'sha256',

    /**
     * The auth guard to be used to authenticate the user upon
     * successful magic login.
     */
    'auth_guard' => 'web',

    /**
     * The user provider used to resolve the user from the database
     * using the identifier provided by the magic login request.
     */
    'user_provider' => 'users',

    /**
     * The controller used to handle the magic login requests.
     * Defaults to the controller provided by the magic login
     * package, but can be set to any custom written class if
     * it implements the HandlesMagicLoginRequest interface.
     */
    'controller' => \Nxu\MagicLogin\MagicLoginController::class,

    /**
     * The route for the magic login. This is where the app will
     * send magic login requests to.
     */
    'magic_login_route' => '/magic-login/auth',

    /**
     * The route for the client login. This is where the client
     * will send login keys to.
     */
    'client_login_route' => '/magic-login/login',
];
