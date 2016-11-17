<?php

namespace Nxu\MagicLogin\Contracts;

use Illuminate\Http\Request;
use Nxu\MagicLogin\MagicLoginRequest;

interface HandlesMagicLoginRequests
{
    /**
     * Handles the incoming magic login request.
     *
     * @param MagicLoginRequest $request
     * @return Response
     */
    public function handleAppLogin(MagicLoginRequest $request);

    /**
     * Handles the client login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleClientLogin(Request $request);
}
