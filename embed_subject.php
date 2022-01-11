<?php
/*
 * Copyright (c) 2022. AMO | Корпоративный мессенджер.
 * Это  приложение - демо для документации по API.
 * Вы можете использовать данный код в своих проектах без сохранения этого копирайта.
 *
 * @Author Mike Eremin <meremin@team.amocrm.com>
 */

use Amo\Sdk\AmoClient;
use Amo\Sdk\Models\Profile;
use Amo\Sdk\Models\Team;
use Amo\Sdk\Models\TeamProps;
use Ramsey\Uuid\Uuid;

require_once 'vendor/autoload.php';

$redis = new Predis\Client(getenv('REDIS_URL'));

$clientId = getenv('CLIENT_ID') ?? null;
$clientSecret = getenv('CLIENT_SECRET') ?? null;

$sdk = new AmoClient([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
]);

$appScopedSdk = $sdk->withToken($sdk->getApplicationToken(['teams', 'profiles']));

$teamID = $redis->get("T4_TEAM_ID");

if (!$teamID) {
    // Создали команду
    $newTeam = $appScopedSdk->team()->create(new Team([
        'title' => 'testTeamName'
    ]));

    $teamID = $newTeam->getId();
    $redis->set("T4_TEAM_ID", $teamID);
}

// Для работы с командой нам понадобится токен команды
$teamTokenJson = $redis->get("{$teamID}_TOKEN");
if ($teamTokenJson) {
    $teamToken = new \League\OAuth2\Client\Token\AccessToken(
        json_decode($teamTokenJson, true)
    );
    $teamService = $sdk->withToken($teamToken)->team($teamID);
} else {
    // Токена нет в сторе, создадим новый
    $teamService = $appScopedSdk->team($teamID)->scope();
    $redis->set("{$teamID}_TOKEN", json_encode($teamService->getAccessToken()));
}

$profileID = $redis->get("T4_PROFILE_ID");

//
if (!$profileID) {
    // Создали профиль
    $createdProfile = $appScopedSdk->profile()->create(new Profile([
        'name' => 'Tim',
        'email' => 'tim@example.com',
        'external_id' => Uuid::uuid4(),
    ]));

    // Приглашаем профиль в команду
    $invitedUser = $teamService->invite($createdProfile->getId(), new TeamProps([
        'is_admin' => true,
        'position' => 'CEO'
    ]));

    $profileID = $createdProfile->getId();
    $redis->set("T4_PROFILE_ID", $profileID);
}

echo "
Team ID: $teamID\n
Profile ID: $profileID
"
//
//// Восстановим сервис для работы с командой
//$teamTokenJson = $redis->get("{$teamID}_TOKEN");
//if ($teamTokenJson) {
//    $teamToken = new \League\OAuth2\Client\Token\AccessToken(
//        json_decode($teamTokenJson, true)
//    );
//    $teamService = $sdk->withToken($teamToken)->team($teamID);
//} else {
//    // Токена нет в сторе, создадим новый
//    $teamService = $appScopedSdk->team($teamID)->scope();
//    $redis->set("{$teamID}_TOKEN", json_encode($teamService->getAccessToken()));
//}
//
//if (!$subjectID) {
//    $subjectService = $appScopedSdk->team($teamID)->scope()->subject();
//    $newSubject = $subjectService->create(new Subject([
//        'title' => 'A new patient request',
//        'external_link' => 'https://example.com/',
//        'author' => Participant::user($createdProfile),
//        'participants' => new ParticipantCollection([
//            Participant::user($createdProfile->getId()),
//        ]),
//        'subscribers' => new ParticipantCollection([
//            Participant::user($createdProfile->getId()),
//        ]),
//        'threads' => new SubjectThreadCollection([
//            new SubjectThread([
//                'title' => 'Patient #100',
//                'avatar_url' => 'https://picsum.photos/600'
//            ]),
//        ]),
//        'status' => new SubjectStatusCollection([
//            SubjectStatus::status('Заявка с сайта', '#F9F6EE'),
//        ])
//    ]));
//}
//
//
//$subjectService = $teamService->subject();
//$newSubject = $subjectService->create(new Subject([
//    'title' => 'A new patient request',
//    'external_link' => 'https://example.com/',
//    'author' => Participant::user($createdProfile),
//    'participants' => new ParticipantCollection([
//        Participant::user($createdProfile->getId()),
//    ]),
//    'subscribers' => new ParticipantCollection([
//        Participant::user($createdProfile->getId()),
//    ]),
//    'threads' => new SubjectThreadCollection([
//        new SubjectThread([
//            'title' => 'Patient #100',
//            'avatar_url' => 'https://picsum.photos/600'
//        ]),
//    ]),
//    'status' => new SubjectStatusCollection([
//        SubjectStatus::status('Заявка с сайта', '#F9F6EE'),
//    ])
//]));
//
//$subjectService = $teamService->subject($newSubject->getId());
//
//$htmlPath = realpath(__DIR__ . '/embed_subject/dist/index.html');
//$htmlContent = file_get_contents($htmlPath);
//
//$amoWscParams = [
//  'appId' => $clientId,
//  'teamId' => $newTeam->getId(),
//  'subjectId' => $newSubject->getId(),
//  'userId' => $createdProfile->getId(),
//  'userToken' => $subjectService->embedUserToken($createdProfile),
//];
//
//$htmlContent = str_replace("\"%AMO_WSC_PARAMS%\"", json_encode($amoWscParams), $htmlContent);
//
//echo $htmlContent

?>
