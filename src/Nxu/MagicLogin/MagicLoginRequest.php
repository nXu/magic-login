<?php
namespace Nxu\MagicLogin;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Nxu\MagicLogin\Contracts\CanLoginMagically;

class MagicLoginRequest extends FormRequest
{
    /**
     * Defines the validation rules of the login request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'token' => 'required',
            'channel' => 'required'
        ];
    }

    /**
     * Verifies the magic login request.
     *
     * @param CanLoginMagically $user
     * @return bool
     */
    public function verify(CanLoginMagically $user) : bool
    {
        $secret = $user->getMagicLoginSecret();
        $now = $this->getTokenGenerationTime();
        $token = $this->get('token');

        // Try current time span
        if ($this->getExpectedValue($now->timestamp, $secret) == $token) {
            return true;
        }

        // Try previous period
        $previous = $this->getTokenGenerationTime()->subSeconds(30)->timestamp;
        if ($this->getExpectedValue($previous->timestamp, $secret) == $token) {
            return true;
        }

        // Try next period
        $next = $this->getTokenGenerationTime()->addSeconds(30)->timestamp;
        if ($this->getExpectedValue($next->timestamp, $secret) == $token) {
            return true;
        }

        return false;
    }

    /**
     * Gets the expected value of the magical authentication token.
     *
     * @param $timestamp
     * @param string $key
     * @return string
     */
    protected function getExpectedValue($timestamp, $key) : string
    {
        $algorithm = config('magiclogin.hash_algo');
        return hash_hmac($algorithm, $timestamp, $key);
    }

    /**
     * Gets the time of the token generation.
     *
     * @return Carbon
     */
    protected function getTokenGenerationTime() : Carbon
    {
        $now = Carbon::now();

        if ($now->second >= 30) {
            return $now->setTime($now->hour, $now->minute, 30);
        } else {
            return $now->setTime($now->hour, $now->minute, 0);
        }
    }}
