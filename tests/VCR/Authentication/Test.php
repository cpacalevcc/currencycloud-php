<?php

namespace CurrencyCloud\Tests\VCR\Authentication;

use CurrencyCloud\Model\Beneficiaries;
use CurrencyCloud\Tests\BaseCurrencyCloudVCRTestCase;

class Test extends BaseCurrencyCloudVCRTestCase
{

    /**
     * @vcr Authentication/can_be_closed.yaml
     * @test
     */
    public function canBeClosed()
    {

        $client = $this->getClient();
        //$client->getSession()->setAuthToken('2ec8a86c8cf6e0378a20ca6793f3260c');
        $client->authenticate()->close();

        $this->assertNull($client->getSession()->getAuthToken());
    }

    /**
     * @vcr Authentication/can_use_just_a_token.yaml
     * @test
     */
    public function canUseJustToken()
    {
        $auth = $this->getClient();

        $client = $this->getAuthenticatedClient($auth->getSession()->getAuthToken());

        $beneficiaries = $client->beneficiaries()->find();

        $this->assertTrue($beneficiaries instanceof Beneficiaries);
    }

    /**
     * @vcr Authentication/happens_lazily.yaml
     * @test
     */
    public function happensLazily()
    {
        // authenticate
        $client = $this->getClient();
        // set the auth token to null
        $client->getSession()->setAuthToken(null);
        // make a request
        $beneficiaries = $client->beneficiaries()->find();
        // check the response
        $this->assertTrue($beneficiaries instanceof Beneficiaries);
    }

}
