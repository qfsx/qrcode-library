<?php


class QrCodeActionCest
{

    // tests
    public function qrCodeWorks(FunctionalTester $I)
    {
        $I->wantTo('Ensure that QrCodeAction and QrComponent works.');
        $I->amGoingTo('Call the configured action "qr" and should receive the "png" image.');
        $I->amOnRoute('/site/qr');
        $source = $I->grabPageSource();
        $I->openFile(codecept_data_dir('data-action.png'));
        $I->seeInThisFile($source);
    }
}
