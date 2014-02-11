<?php

use Mockery as m;

use Trucker\Requests\Auth\BasicAuthenticator;
use Trucker\Facades\Config;

class BasicAuthenticatorTest extends TruckerTests
{
    public function testSetsAuthOnRequest()
    {
        $this->swapConfig([
            'trucker::auth.driver'         => 'basic',
            'trucker::auth.basic.username' => 'myUsername',
            'trucker::auth.basic.password' => 'myPassword'
        ]);
        Config::setApp($this->app);

        $request = m::mock('Guzzle\Http\Message\Request');
        $request->shouldReceive('setAuth')
            ->with('myUsername', 'myPassword')
            ->once();

        $auth = new BasicAuthenticator($this->app);
        $auth->authenticateRequest($request);
    }
}
