<?php 
namespace VanguardLTE\Games\HotSpinDeluxeISB
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
        {
            \DB::transaction(function() use ($request, $game)
            {
                try
                {
                    $userId = \Auth::id();
                    if( $userId == null ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid login"}';
                        exit( $response );
                    }
                    $slotSettings = new SlotSettings($game, $userId);
                    $postData = $_POST;
                    $result_tmp = [];
                    $aid = '';
                    if( !isset($postData['cmd']) ) 
                    {
                        $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid request"}';
                        exit( $response );
                    }
                    $postData['command'] = explode("\n", $postData['cmd']);
                    $postData['command'] = trim($postData['command'][0]);
                    $aid = (string)$postData['command'];
                    switch( $aid ) 
                    {
                        case '1':
                            $result_tmp[0] = '{ "rootdata": { "uid": "undefined" , "data": { "logout": "1" } } }';
                            break;
                        case '5814':
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $lastEvent = $slotSettings->GetHistory();
                            $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', 0);
                            $slotSettings->SetGameData($slotSettings->slotId . 'miniBonus', 0);
                            if( $lastEvent != 'NULL' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->bonusWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                $slotSettings->SetGameData($slotSettings->slotId . 'miniBonus', $lastEvent->serverResponse->miniBonus);
                                $reels = $lastEvent->serverResponse->reelsSymbols;
                                $lines = $lastEvent->serverResponse->slotLines;
                                $bet = $lastEvent->serverResponse->slotBet * 100;
                            }
                            else
                            {
                                $lines = 10;
                                $bet = $slotSettings->Bet[0] * 100;
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $bonusWin = $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin');
                                $fsCur = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame');
                                $fsTot = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                                $freeSpinsStr = '"gameSpecific": { "modifierId": "' . $lastEvent->serverResponse->miniBonus . '" },"freeGames": { "left": "' . ($fsTot - $fsCur) . '", "total": "' . $fsTot . '", "totalFreeGamesWinnings": "' . round($bonusWin * 100) . '", "totalFreeGamesWinningsMoney": "' . round($bonusWin * 100) . '", "multiplier": "1", "totalMultiplier": "1" },';
                            }
                            else
                            {
                                $freeSpinsStr = '';
                            }
                            $result_tmp[0] = '{ "rootdata": { "uid": "5640f7f3-4693-4059-841a-dc3afd3f1925", "data": { "version": { "versionServer": "2.2.0.1-1", "versionGMAPI": "8.1.16 GS:2.5.1 FR:v4" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0", "ccyCode": "EUR", "ccyDecimal": { }, "ccyThousand": { }, "ccyPrefix": { }, "ccySuffix": { }, "ccyDecimalDigits": { } }, "id": { "roundId": "25520020301588680413177316196800814753252699" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "initial": { "money": "' . $balanceInCents . '", "coins": "1", "coinValue": "' . $gameBets[0] . '", "lines": "20", "currentState": "beginGame", "lastGame": { "endGame": { ' . $freeSpinsStr . '"money": "' . $balanceInCents . '", "bet": "' . ($gameBets[0] * 20) . '", "symbols": { "line": [ "-1--1--1--1--1", "-1--1--1--1--1", "-1--1--1--1--1" ] }, "lines": { }, "totalWinnings": "0", "totalWinningsMoney": "0", "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "totalMultiplier": { }, "bonusRequest": { } } } } } } }';
                            break;
                        case '192837':
                            $result_tmp[0] = '{ "rootdata": { "uid": "419427a2-d300-41d9-8d67-fe4eec61be5f", "data": "1" } }';
                            break;
                        case '5347':
                            $result_tmp[0] = '{ "rootdata": { "uid": "26d1001c-15ff-4d21-af9e-45dde0260165", "data": { "success": "" } } }';
                            break;
                        case '8':
                            $result_tmp[0] = '{ "rootdata": { "uid": "5f634f2d-d2dd-475b-a84a-48e3d0fdb30c", "data": { "bonusChoice": { "bonusId": "1", "choicesOrder": "0-0-0-0-0-0-0-0-0", "bonusGain": "0-0-0-0-0-0-0-0-0", "bonusGainMoney": "0-0-0-0-0-0-0-0-0", "choicesWinnings": "4-5-6-7-8-9-10-12-15", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesMult": "1-1-1-1-1-1-1-1-1", "totalMultiplier": "1", "choicesFreeGames": "1-1-1-1-1-1-1-1-1", "modifierId": "0", "choicesModifier": "0-0-0-0-0-0-0-0-0-0-0-0-0" } } } }';
                            break;
                        case '9':
                            $miniBonuses = [
                                1, 
                                2, 
                                3, 
                                4, 
                                5, 
                                6, 
                                7, 
                                8, 
                                9, 
                                11, 
                                12
                            ];
                            $choicesFreeGames = [
                                4, 
                                5, 
                                6, 
                                7, 
                                8, 
                                9, 
                                10, 
                                12, 
                                15
                            ];
                            shuffle($choicesFreeGames);
                            shuffle($miniBonuses);
                            $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $choicesFreeGames[0]);
                            $slotSettings->SetGameData($slotSettings->slotId . 'miniBonus', $miniBonuses[0]);
                            $balanceInCents = round($slotSettings->GetBalance() * 100);
                            $result_tmp[0] = '{ "rootdata": { "uid": "5f634f2d-d2dd-475b-a84a-48e3d0fdb30c", "data": { "bonusWin": { "money": "' . $balanceInCents . '", "bonusRequest": "0", "bonusId": "1", "bonusesLeft": "0", "totalFreeGames": "' . $choicesFreeGames[0] . '", "multiplier": "1", "totalMultiplier": "1", "choicesFreeGames": "1-1-1-1-1-1-1-1-1", "totalBonusWinnings": "0", "totalBonusWinningsMoney": "0", "choicesOrder": "1-0-0-0-0-0-0-0-0", "choicesWinnings": "4-5-6-7-8-9-10-12-15", "choicesMult": "1-1-1-1-1-1-1-1-1", "modifierId": "' . $miniBonuses[0] . '", "choicesModifier": "0-0-0-0-0-0-0-0-0-0-0-1-0", "totalWinnings": "0", "totalWinningsMoney": "0", "freeGamesWin": { "total": "' . $choicesFreeGames[0] . '", "multiplier": "1" } }, "gameSpecific": { "modifierId": "' . $miniBonuses[0] . '" }, "doubleWin": { "totalWinnings": "0", "totalWinningsMoney": "0", "money": "' . $balanceInCents . '" }, "balance": { "cashBalance": "' . $balanceInCents . '", "freeBalance": "0" } } } }';
                            break;
                        case '2':
                            $gDat = explode("\n", $postData['cmd']);
                            $linesId = [];
                            $linesId[0] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[2] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[4] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[5] = [
                                1, 
                                1, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[6] = [
                                3, 
                                3, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[7] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[8] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[9] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[10] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[11] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[12] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[13] = [
                                2, 
                                2, 
                                1, 
                                2, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                2, 
                                3, 
                                2, 
                                2
                            ];
                            $linesId[15] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[17] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[19] = [
                                1, 
                                3, 
                                3, 
                                3, 
                                1
                            ];
                            $lines = 20;
                            $betline = trim($gDat[1]) / 100;
                            $betlineRaw = trim($gDat[1]);
                            $allbet = $betline * $lines;
                            $postData['slotEvent'] = 'bet';
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                            }
                            if( $slotSettings->GetBalance() < $allbet && $postData['slotEvent'] == 'bet' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance "}';
                                exit( $response );
                            }
                            if( $allbet <= 0 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet "}';
                                exit( $response );
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $slotSettings->SetGameData($slotSettings->slotId . 'miniBonus', 0);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            if( $winType == 'bonus' && $postData['slotEvent'] == 'freespin' ) 
                            {
                                $winType = 'none';
                            }
                            $balanceInCentsStart = round($slotSettings->GetBalance() * 100);
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $cWins = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $wild = ['9'];
                                $scatter = '10';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                $miniBonuses = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    5, 
                                    6, 
                                    7, 
                                    8, 
                                    9, 
                                    11, 
                                    12, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                shuffle($miniBonuses);
                                $miniBonusStr = '';
                                $miniBonusStr0 = '';
                                if( $postData['slotEvent'] == 'freespin' ) 
                                {
                                    $miniBonuses[0] = $slotSettings->GetGameData($slotSettings->slotId . 'miniBonus');
                                }
                                if( $miniBonuses[0] != 0 && $winType !== 'bonus' ) 
                                {
                                    if( $miniBonuses[0] == 11 || $miniBonuses[0] == 12 ) 
                                    {
                                        $dummyModifierSymbolsMask = [];
                                        $rowWilds = [
                                            1, 
                                            2, 
                                            3, 
                                            4, 
                                            5
                                        ];
                                        if( $miniBonuses[0] == 11 ) 
                                        {
                                            $rowWildsLimit = rand(1, 3);
                                        }
                                        else
                                        {
                                            $rowWildsLimit = rand(2, 4);
                                        }
                                        $wilds = [];
                                        shuffle($rowWilds);
                                        $rwMasks = [];
                                        $rwMasks[0] = '';
                                        $rwMasks[1] = ' [ "1-0-0-0-0", "1-0-0-0-0", "1-0-0-0-0" ]';
                                        $rwMasks[2] = ' [ "0-1-0-0-0", "0-1-0-0-0", "0-1-0-0-0" ]';
                                        $rwMasks[3] = ' [ "0-0-1-0-0", "0-0-1-0-0", "0-0-1-0-0" ]';
                                        $rwMasks[4] = ' [ "0-0-0-1-0", "0-0-0-1-0", "0-0-0-1-0" ]';
                                        $rwMasks[5] = ' [ "0-0-0-0-1", "0-0-0-0-1", "0-0-0-0-1" ]';
                                        for( $r = 1; $r <= 5; $r++ ) 
                                        {
                                            if( $rowWildsLimit < $r ) 
                                            {
                                                if( $reels['reel' . $rowWilds[$r - 1]][0] == '9' || $reels['reel' . $rowWilds[$r - 1]][1] == '9' || $reels['reel' . $rowWilds[$r - 1]][2] == '9' ) 
                                                {
                                                    $reels['reel' . $rowWilds[$r - 1]][0] = '9';
                                                    $reels['reel' . $rowWilds[$r - 1]][1] = '9';
                                                    $reels['reel' . $rowWilds[$r - 1]][2] = '9';
                                                    $wilds[] = '{ "type": "1", "id": "9", "pos": { }, "mask": { "line":' . $rwMasks[$rowWilds[$r - 1]] . ' } }';
                                                }
                                            }
                                            else
                                            {
                                                $reels['reel' . $rowWilds[$r - 1]][0] = '9';
                                                $reels['reel' . $rowWilds[$r - 1]][1] = '9';
                                                $reels['reel' . $rowWilds[$r - 1]][2] = '9';
                                                $wilds[] = '{ "type": "1", "id": "9", "pos": { }, "mask": { "line":' . $rwMasks[$rowWilds[$r - 1]] . ' } }';
                                            }
                                        }
                                        $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "wilds": { "wild": [ ' . implode(',', $wilds) . '] }';
                                    }
                                    if( $miniBonuses[0] == 3 ) 
                                    {
                                        $colossalSym = rand(1, 8);
                                        $colossalPos = rand(1, 2);
                                        if( $colossalPos == 1 ) 
                                        {
                                            $reels['reel1'][0] = $colossalSym;
                                            $reels['reel1'][1] = $colossalSym;
                                            $reels['reel1'][2] = $colossalSym;
                                            $reels['reel2'][0] = $colossalSym;
                                            $reels['reel2'][1] = $colossalSym;
                                            $reels['reel2'][2] = $colossalSym;
                                            $reels['reel3'][0] = $colossalSym;
                                            $reels['reel3'][1] = $colossalSym;
                                            $reels['reel3'][2] = $colossalSym;
                                            $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "colossals": { "colossal": { "symbol": "' . $colossalSym . '", "overlay": { "line": [ "1-1-1-0-0", "1-1-1-0-0", "1-1-1-0-0" ] } } }';
                                        }
                                        else
                                        {
                                            $reels['reel4'][0] = $colossalSym;
                                            $reels['reel4'][1] = $colossalSym;
                                            $reels['reel4'][2] = $colossalSym;
                                            $reels['reel2'][0] = $colossalSym;
                                            $reels['reel2'][1] = $colossalSym;
                                            $reels['reel2'][2] = $colossalSym;
                                            $reels['reel3'][0] = $colossalSym;
                                            $reels['reel3'][1] = $colossalSym;
                                            $reels['reel3'][2] = $colossalSym;
                                            $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "colossals": { "colossal": { "symbol": "' . $colossalSym . '", "overlay": { "line": [ "0-1-1-1-0", "0-1-1-1-0", "0-1-1-1-0" ] } } }';
                                        }
                                    }
                                    if( $miniBonuses[0] == 1 || $miniBonuses[0] == 2 ) 
                                    {
                                        $syncReels = [
                                            1, 
                                            1, 
                                            2, 
                                            2, 
                                            3, 
                                            3, 
                                            4, 
                                            4, 
                                            5, 
                                            5
                                        ];
                                        $syncReelArr = [
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                        $syncReelCur = rand(1, 5);
                                        if( $miniBonuses[0] == 1 ) 
                                        {
                                            $syncReelsCnt = rand(2, 3);
                                        }
                                        if( $miniBonuses[0] == 2 ) 
                                        {
                                            $syncReelsCnt = rand(3, 5);
                                        }
                                        shuffle($syncReels);
                                        $syncReelArr[$syncReelCur - 1] = 1;
                                        for( $r = 1; $r <= $syncReelsCnt; $r++ ) 
                                        {
                                            $syncReelArr[$syncReels[$r] - 1] = 1;
                                            $reels['reel' . $syncReels[$r]] = $reels['reel' . $syncReelCur];
                                        }
                                        $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "insync": { "sync": { "from": "0-0-0-0-0", "to": "' . implode('-', $syncReelArr) . '", "mask": "' . implode('-', $syncReelArr) . '" } }';
                                    }
                                    if( $miniBonuses[0] == 7 || $miniBonuses[0] == 8 || $miniBonuses[0] == 9 ) 
                                    {
                                        if( $miniBonuses[0] == 7 ) 
                                        {
                                            $rWildsLimit = 12;
                                        }
                                        else if( $miniBonuses[0] == 8 ) 
                                        {
                                            $rWildsLimit = 6;
                                        }
                                        else if( $miniBonuses[0] == 9 ) 
                                        {
                                            $rWildsLimit = 8;
                                        }
                                        $mystSym = rand(1, 8);
                                        $mystSymArr = [
                                            [
                                                1, 
                                                0
                                            ], 
                                            [
                                                1, 
                                                1
                                            ], 
                                            [
                                                1, 
                                                2
                                            ], 
                                            [
                                                2, 
                                                0
                                            ], 
                                            [
                                                2, 
                                                1
                                            ], 
                                            [
                                                2, 
                                                2
                                            ], 
                                            [
                                                3, 
                                                0
                                            ], 
                                            [
                                                3, 
                                                1
                                            ], 
                                            [
                                                3, 
                                                2
                                            ], 
                                            [
                                                4, 
                                                0
                                            ], 
                                            [
                                                4, 
                                                1
                                            ], 
                                            [
                                                4, 
                                                2
                                            ], 
                                            [
                                                5, 
                                                0
                                            ], 
                                            [
                                                5, 
                                                1
                                            ], 
                                            [
                                                5, 
                                                2
                                            ]
                                        ];
                                        shuffle($mystSymArr);
                                        for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                        {
                                            $msc = $mystSymArr[$p];
                                            $reels['reel' . $msc[0]][$msc[1]] = -1;
                                        }
                                        $scPos = [
                                            [], 
                                            [], 
                                            []
                                        ];
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == -1 ) 
                                                {
                                                    $reels['reel' . $r][$p] = $mystSym;
                                                    $scPos[$p][] = '1';
                                                }
                                                else
                                                {
                                                    $scPos[$p][] = '0';
                                                }
                                            }
                                        }
                                        $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "wilds": { "wild": { "type": "2", "id": "' . $mystSym . '", "pos": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] }, "mask": { "line": [ "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[1]) . '", "' . implode('-', $scPos[2]) . '" ] } } }';
                                    }
                                    if( $miniBonuses[0] == 4 || $miniBonuses[0] == 5 || $miniBonuses[0] == 6 ) 
                                    {
                                        if( $miniBonuses[0] == 4 ) 
                                        {
                                            $rWildsLimit = rand(2, 5);
                                        }
                                        else if( $miniBonuses[0] == 5 ) 
                                        {
                                            $rWildsLimit = rand(2, 7);
                                        }
                                        else if( $miniBonuses[0] == 6 ) 
                                        {
                                            $rWildsLimit = rand(2, 10);
                                        }
                                        $mystSym = 9;
                                        $mystSymArr = [
                                            [
                                                1, 
                                                0
                                            ], 
                                            [
                                                1, 
                                                1
                                            ], 
                                            [
                                                1, 
                                                2
                                            ], 
                                            [
                                                2, 
                                                0
                                            ], 
                                            [
                                                2, 
                                                1
                                            ], 
                                            [
                                                2, 
                                                2
                                            ], 
                                            [
                                                3, 
                                                0
                                            ], 
                                            [
                                                3, 
                                                1
                                            ], 
                                            [
                                                3, 
                                                2
                                            ], 
                                            [
                                                4, 
                                                0
                                            ], 
                                            [
                                                4, 
                                                1
                                            ], 
                                            [
                                                4, 
                                                2
                                            ], 
                                            [
                                                5, 
                                                0
                                            ], 
                                            [
                                                5, 
                                                1
                                            ], 
                                            [
                                                5, 
                                                2
                                            ]
                                        ];
                                        shuffle($mystSymArr);
                                        for( $p = 0; $p < $rWildsLimit; $p++ ) 
                                        {
                                            $msc = $mystSymArr[$p];
                                            $reels['reel' . $msc[0]][$msc[1]] = -1;
                                        }
                                        $scPos = [
                                            [], 
                                            [], 
                                            []
                                        ];
                                        for( $p = 0; $p <= 2; $p++ ) 
                                        {
                                            for( $r = 1; $r <= 5; $r++ ) 
                                            {
                                                if( $reels['reel' . $r][$p] == -1 ) 
                                                {
                                                    $reels['reel' . $r][$p] = $mystSym;
                                                    $scPos[$p][] = '1';
                                                }
                                                else
                                                {
                                                    $scPos[$p][] = '0';
                                                }
                                            }
                                        }
                                        $miniBonusStr = ',"gameSpecific": {"modifierId": "' . $miniBonuses[0] . '" }, "wilds": { "wild": { "type": "2", "id": "' . $mystSym . '", "pos": { "line": [ "0-0-0-0-0", "0-0-0-0-0", "0-0-0-0-0" ] }, "mask": { "line": [ "' . implode('-', $scPos[0]) . '", "' . implode('-', $scPos[1]) . '", "' . implode('-', $scPos[2]) . '" ] } } }';
                                    }
                                }
                                for( $k = 0; $k < $lines; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = (string)$slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $s = [];
                                            $sp = [];
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            $sp[0] = $linesId[$k][0];
                                            $sp[1] = $linesId[$k][1];
                                            $sp[2] = $linesId[$k][2];
                                            $sp[3] = $linesId[$k][3];
                                            $sp[4] = $linesId[$k][4];
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '000", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '00", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '0", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{ "pos": "' . $k . '", "initialValue": { }, "initialValueMoney": { }, "value": "' . round(($cWins[$k] * 100) / $betlineRaw) . '", "valueMoney": "' . ($cWins[$k] * 100) . '", "hl": "' . $sp[0] . '' . $sp[1] . '' . $sp[2] . '' . $sp[3] . '' . $sp[4] . '", "symbolId": "22", "multiplier": "' . $bonusMpl . '" }';
                                                }
                                            }
                                        }
                                    }
                                    if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                    {
                                        array_push($lineWins, $tmpStringWin);
                                        $totalWin += $cWins[$k];
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scPos = [];
                                for( $p = 0; $p <= 2; $p++ ) 
                                {
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '1';
                                        }
                                        else
                                        {
                                            $scPos[] = '0';
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet;
                                $sgwin = 0;
                                $bonusRequest = 0;
                                if( $scattersCount >= 3 ) 
                                {
                                    $sgwin = $slotSettings->slotFreeCount;
                                    $bonusRequest = 1;
                                    $scattersStr = ', "bonuses": { "bonus": { "id": "1", "hl": "' . implode('', $scPos) . '" }  }';
                                }
                                $totalWin += $scattersWin;
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $allbet) ) 
                                    {
                                    }
                                    else if( $scattersCount >= 3 && $winType != 'bonus' ) 
                                    {
                                    }
                                    else if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
                                    {
                                        $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        if( $cBank < $spinWinLimit ) 
                                        {
                                            $spinWinLimit = $cBank;
                                        }
                                        else
                                        {
                                            break;
                                        }
                                    }
                                    else if( $totalWin > 0 && $totalWin <= $spinWinLimit && $winType == 'win' ) 
                                    {
                                        $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        if( $cBank < $spinWinLimit ) 
                                        {
                                            $spinWinLimit = $cBank;
                                        }
                                        else
                                        {
                                            break;
                                        }
                                    }
                                    else if( $totalWin == 0 && $winType == 'none' ) 
                                    {
                                        break;
                                    }
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + $slotSettings->slotFreeCount);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"miniBonus":' . $slotSettings->GetGameData($slotSettings->slotId . 'miniBonus') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $hist = $slotSettings->GetGameData($slotSettings->slotId . 'Cards');
                            $curReels = '';
                            $curReels .= ('"' . $reels['reel1'][0] . '-' . $reels['reel2'][0] . '-' . $reels['reel3'][0] . '-' . $reels['reel4'][0] . '-' . $reels['reel5'][0] . '"');
                            $curReels .= (',"' . $reels['reel1'][1] . '-' . $reels['reel2'][1] . '-' . $reels['reel3'][1] . '-' . $reels['reel4'][1] . '-' . $reels['reel5'][1] . '"');
                            $curReels .= (',"' . $reels['reel1'][2] . '-' . $reels['reel2'][2] . '-' . $reels['reel3'][2] . '-' . $reels['reel4'][2] . '-' . $reels['reel5'][2] . '"');
                            $isJack = 'false';
                            if( $totalWin > 0 ) 
                            {
                                $state = 'gamble';
                            }
                            else
                            {
                                $state = 'idle';
                            }
                            if( !isset($sgwin) ) 
                            {
                                $fs = 0;
                            }
                            else if( $sgwin > 0 ) 
                            {
                                $fs = $sgwin;
                            }
                            else
                            {
                                $fs = 0;
                            }
                            $balanceInCentsEnd = round($slotSettings->GetBalance() * 100);
                            $gameBets = [];
                            for( $i = 0; $i < count($slotSettings->Bet); $i++ ) 
                            {
                                $gameBets[] = $slotSettings->Bet[$i] * 100;
                            }
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "d01cf0d7-074f-4524-86be-3361658789f3", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "25520003501587914269973255464480393810966409" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "1", "readValue": "0" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [  ' . $curReels . '  ] }, "lines": { "line": [ ' . $winString . ' ] }, "freeGames": { "left": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') - $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame')) . '", "total": "' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . '", "totalFreeGamesWinnings": "' . (($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) / $betlineRaw) . '", "totalFreeGamesWinningsMoney": "' . ($slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') * 100) . '", "multiplier": "1", "totalMultiplier": "1" }, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": "0" ' . $miniBonusStr . '  }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                            }
                            else
                            {
                                $result_tmp[0] = '{ "rootdata": { "uid": "c85dcfe8-c826-40a3-862e-d1297d53859a", "data": { "balance": { "cashBalance": "' . $balanceInCentsEnd . '", "freeBalance": "0" }, "id": { "roundId": "255703301588696204682728443064353372761745" }, "coinValues": { "coinValueList": "' . implode(',', $gameBets) . '", "coinValueDefault": "' . $gameBets[0] . '", "readValue": "1" }, "endGame": { "money": "' . $balanceInCentsStart . '", "bet": "20", "symbols": { "line": [ ' . $curReels . ' ] }, "lines": {  "line": [' . $winString . ']}, "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "totalMultiplier": "1", "bonusRequest": "' . $bonusRequest . '"' . $scattersStr . '  }, "doubleWin": { "totalWinnings": "' . round(($totalWin * 100) / $betlineRaw) . '", "totalWinningsMoney": "' . round($totalWin * 100) . '", "money": "' . $balanceInCentsEnd . '" } } } }';
                                $slotSettings->SetGameData($slotSettings->slotId . 'LastResponse', $result_tmp[0]);
                            }
                            break;
                    }
                    $response = $result_tmp[0];
                    $slotSettings->SaveGameData();
                    $slotSettings->SaveGameDataStatic();
                    echo $response;
                }
                catch( \Exception $e ) 
                {
                    $slotSettings->InternalErrorSilent($e);
                }
            }, 5);
        }
    }

}
