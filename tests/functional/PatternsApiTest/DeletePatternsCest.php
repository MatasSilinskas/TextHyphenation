<?php


class DeletePatternsCest
{
    public function _before(ApiTester $I)
    {
        $I->haveInDatabase('patterns', [
            'pattern' => 'delete',
            'id' => 1
        ]);
    }

    public function tryToTest(ApiTester $I)
    {
        $data = [
            'pattern' => 'delete'
        ];
        $I->sendDELETE('/patterns', json_encode($data));
        $I->seeResponseCodeIs(200);
        $I->dontSeeInDatabase('patterns', $data);
        $I->sendDELETE('/patterns', json_encode($data));
        $I->seeResponseCodeIs(404);
    }
}
