<?php

namespace TextHyphenation\Tests;

use ApiTester;

class UpdatePatternsCest
{
    public function _before(ApiTester $I)
    {
        $I->haveInDatabase('patterns', [
            'pattern' => 'update',
            'id' => 1
        ]);
    }

    public function tryToTest(ApiTester $I)
    {
        $data = [
            'oldPattern' => 'update',
            'newPattern' => 'updated',
        ];

        $I->sendPUT('/patterns', json_encode($data));
        $I->seeResponseCodeIs(200);
        $I->cantSeeInDatabase('patterns', ['pattern' => 'update']);
        $I->canSeeInDatabase('patterns', ['pattern' => 'updated']);
    }
}
