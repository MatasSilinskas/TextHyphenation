<?php

namespace TextHyphenation\Tests;

use ApiTester;

class GetPatternsCest
{
    public function _before(ApiTester $I)
    {
        $I->haveInDatabase('patterns', [
            'pattern' => 'pattern1',
            'id' => 1
        ]);

        $I->haveInDatabase('patterns', [
            'pattern' => 'pattern2',
            'id' => 2
        ]);
    }

    public function tryToTest(ApiTester $I)
    {
        $I->sendGET('/patterns');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['pattern' => 'pattern1', 'id' => 1],
            ['pattern' => 'pattern2', 'id' => 2],
        ]);
        $I->sendGET('/patterns/1');
        $I->seeResponseContainsJson([
            ['pattern' => 'pattern1', 'id' => 1],
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendGET('/patterns/3');
        $I->seeResponseCodeIs(404);
    }
}
