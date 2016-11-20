<?php

namespace Nxu\MagicLogin\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nxu\MagicLogin\MagicLoginRequest;

interface HandlesMagicLoginRequests
{
    /**
     * Handles the incoming magic login request.
     *
     * @param MagicLoginRequest $request
     *
     * @return Response
     */
    public function handleAppLogin(MagicLoginRequest $request) : Response;

    /**
     * Handles the client login.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleClientLogin(Request $request) : Response;
}
