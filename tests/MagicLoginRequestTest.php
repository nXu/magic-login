<?php

use Carbon\Carbon;
use Nxu\MagicLogin\Contracts\CanLoginMagically;
use Nxu\MagicLogin\MagicLoginRequest;
use Orchestra\Testbench\TestCase;

class MagicLoginRequestTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('magiclogin.hash_algo', 'sha256');
    }

    /** @test */
    public function it_doesnt_verify_wrong_tokens()
    {
        Carbon::setTestNow(new Carbon('2016-01-01 18:41:15'));

        $user = $this->getMockedUser('abcd');

        $request = $this->getRequest([
            'user_id' => 16,
            'token' => 'definitely-wrong-token',
            'channel' => 16136779
        ]);

        $this->assertFalse($request->verify($user));
    }

    /** @test */
    public function it_verifies_for_current_time()
    {
        Carbon::setTestNow(new Carbon('2016-01-01 18:40:00'));
        $secret = str_random(24);
        $channel = str_random(24);
        $timestamp = Carbon::now()->tz('UTC')->timestamp;

        $token = hash_hmac('sha256', $timestamp . '-' . $channel, $secret);

        $request = $this->getRequest([
            'user_id' => 1,
            'token' => $token,
            'channel' => $channel
        ]);

        $user = $this->getMockedUser($secret);

        $this->assertTrue($request->verify($user));
    }

    /** @test */
    public function it_verifies_for_previous_30s()
    {
        Carbon::setTestNow(new Carbon('2016-01-01 18:40:00'));
        $secret = str_random(24);
        $channel = str_random(24);
        $timestamp = Carbon::now()->subSeconds(30)->tz('UTC')->timestamp;
        $token = hash_hmac('sha256', $timestamp . '-' . $channel, $secret);

        $request = $this->getRequest([
            'user_id' => 1,
            'token' => $token,
            'channel' => $channel
        ]);

        $user = $this->getMockedUser($secret);

        $this->assertTrue($request->verify($user));
    }


    /** @test */
    public function it_verifies_for_next_30s()
    {
        Carbon::setTestNow(new Carbon('2016-01-01 18:40:00'));
        $secret = str_random(24);
        $channel = str_random(24);
        $timestamp = Carbon::now()->addSeconds(30)->tz('UTC')->timestamp;
        $token = hash_hmac('sha256', $timestamp . '-' . $channel, $secret);

        $request = $this->getRequest([
            'user_id' => 1,
            'token' => $token,
            'channel' => $channel
        ]);

        $user = $this->getMockedUser($secret);

        $this->assertTrue($request->verify($user));
    }

    /**
     * @return CanLoginMagically
     */
    protected function getMockedUser($secret)
    {
        return Mockery::mock(CanLoginMagically::class)
            ->shouldReceive('getMagicLoginSecret')
            ->andReturn($secret)
            ->getMock();
    }

    /** @return MagicLoginRequest */
    protected function getRequest($params)
    {
        return MagicLoginRequest::create('/my/path', 'POST', $params);
    }
}
