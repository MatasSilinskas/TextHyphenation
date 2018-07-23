<?php


class PostPatternsCest
{
    private $data = [
        'pattern' => 'post'
    ];

    public function _after(ApiTester $I)
    {
        $I->sendDELETE('/patterns', json_encode($this->data));
    }

    public function tryToTest(ApiTester $I)
    {
        $I->seeNumRecords(0, 'patterns');
        $I->sendPOST('/patterns', json_encode($this->data));
        $I->seeResponseCodeIs(200);
        $I->sendPOST('/patterns', json_encode($this->data));
        $I->seeResponseCodeIs(409);
        $I->seeNumRecords(1, 'patterns');
    }
}
