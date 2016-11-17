<?php

namespace Nxu\MagicLogin;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nxu\MagicLogin\Contracts\HandlesMagicLoginRequests;

class MagicLoginController implements HandlesMagicLoginRequests
{
    use RedirectsUsers, ValidatesRequests;

    /**
     * @var Store
     */
    protected $cache;

    /**
     * MagicLoginController constructor.
     * @param Store $cache
     */
    public function __construct(Store $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handles the incoming magic login request.
     *
     * @param MagicLoginRequest $request
     * @return Response
     */
    public function handleAppLogin(MagicLoginRequest $request)
    {
        // Get the user
        abort_unless($user = $this->getUser($request->get('user_id')), 404);

        // Verify the token
        abort_unless($request->verify($user), 403);

        // Generate a login key and save it to cache along with the user id
        $channel = $request->get('channel');
        $loginKey = str_random(60);
        $this->saveLoginKeyToCache($channel, $loginKey, $user->getAuthIdentifier());

        // Broadcast the result
        $loginSuccess = new MagicLoginSuccess($channel, $loginKey);
        event($loginSuccess);

        return response('', 204);
    }

    /**
     * Handles the client login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleClientLogin(Request $request)
    {
        $this->validate($request, [
            'channel' => 'required',
            'login_key' => 'required'
        ]);

        // Save redirect path
        if ($request->has('redirect_to')) {
            $this->redirectTo = $request->get('redirect_to');
        }

        // Get the saved key and user id from cache
        $channel = $request->get('channel');
        abort_unless($login = $this->cache->get("magiclogin:$channel"), 403);

        list($key, $userId) = $login;

        // Validate key
        abort_unless($key == $request->get('login_key'), 403);

        // Login user
        $this->guard()->loginUsingId($userId);

        // Clear key from cache
        $this->cache->get("magiclogin:$channel");

        // Redirect user
        return redirect()->to($this->redirectPath());
    }

    /**
     * Gets the user identified by its identifier.
     *
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function getUser($identifier)
    {
        /** @var UserProvider $provider */
        $provider = app('magiclogin.user-provider');

        return $provider->retrieveById($identifier->get('user_id'));
    }

    /**
     * Gets the auth manager.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard|mixed
     */
    protected function guard()
    {
        return app('magiclogin.guard');
    }

    /**
     * Saves the login key to cache.
     *
     * @param string $channel
     * @param string $key
     * @param mixed $userId
     */
    protected function saveLoginKeyToCache($channel, $key, $userId)
    {
        $this->cache->put(
            "magiclogin:$channel",
            [
                "key" => $key,
                "user" => $userId
            ],
            10
        );
    }
}
