<?php

namespace CurrencyCloud\Tests\VCR\Update;
use CurrencyCloud\Model\Beneficiary;
use CurrencyCloud\Tests\BaseCurrencyCloudVCRTestCase;

class Test extends BaseCurrencyCloudVCRTestCase
{
    /**
     * @vcr Update/does_nothing_if_nothing_has_changed.yaml
     * @test
     */
    public function doesNothingIfNothingHasChanged()
    {
        $client = $this->getAuthenticatedClient();
        $beneficiary = Beneficiary::create('Acmea GmbH', 'DE', 'EUR', 'John Doe')
            ->setBeneficiaryCountry('DE')
            ->setBicSwift('COBADEFF')
            ->setIban('DE79370400440532013000');

        // create the beneficiary
        $beneficiary = $client->beneficiaries()->create($beneficiary);
        // retrieve the beneficiary using the UUID
        $retrievedBeneficiary = $client->beneficiaries()->retrieve($beneficiary->getId());

        // perform and update but dont modify anything
        $beneficiary = $client->beneficiaries()->update($retrievedBeneficiary);

        // assert that the updatedAt and createdAt are the same - this proves that the record
        // has not been updated.
        $this->assertSame($retrievedBeneficiary->getUpdatedAt()->format('Y-m-d H:i:s'), $beneficiary->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    /**
     * @vcr Update/only_updates_changed_records.yaml
     * @test
     */
    public function onlyUpdatesChangedRecords()
    {
        $client = $this->getAuthenticatedClient();

        $beneficiary = Beneficiary::create('Acme GmbH', 'DE', 'EUR', 'John Doe', 'adsdasasdasd@adsdsaads.com')
            ->setBeneficiaryCountry('DE')
            ->setBicSwift('COBADEFF')
            ->setIban('DE89370400440532013000');

        // create the beneficiary
        $beneficiary = $client->beneficiaries()->create($beneficiary);
        // retrieve the beneficiary using the UUID
        $retrievedBeneficiary = $client->beneficiaries()->retrieve($beneficiary->getId());

        // modify the record updating the bank account holders name and beneficiary first name
        $retrievedBeneficiary->setBankAccountHolderName('Test User 2')->setBeneficiaryFirstName('Dave');

        // pause the script for 2 seconds to check that the active record updated date changes
        sleep(2);
        // update the record
        $beneficiary = $client->beneficiaries()->update($retrievedBeneficiary);

        // assert that the bank account holders name is the same as the update sent
        $this->assertSame($beneficiary->getBankAccountHolderName(), $retrievedBeneficiary->getBankAccountHolderName());
        // assert that the first name is the same as the update sent
        $this->assertSame($beneficiary->getBeneficiaryFirstName(), $retrievedBeneficiary->getBeneficiaryFirstName());
        // finally validate that the updated date does not match the created date
        // this is how we know the record has been modified programatically
        $this->assertNotSame($retrievedBeneficiary->getUpdatedAt()->format('Y-m-d H:i:s'), $beneficiary->getUpdatedAt()->format('Y-m-d H:i:s'));

    }
}
