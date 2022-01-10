<?php
/*
 * Copyright (c) 2022. AMO | Корпоративный мессенджер.
 * Это  приложение - демо для документации по API.
 * Вы можете использовать данный код в своих проектах без сохранения этого копирайта.
 *
 * @Author Mike Eremin <meremin@team.amocrm.com>
 */

use Amo\Sdk\AmoClient;
use Amo\Sdk\Models\Participant;
use Amo\Sdk\Models\ParticipantCollection;
use Amo\Sdk\Models\Profile;
use Amo\Sdk\Models\Subject;
use Amo\Sdk\Models\SubjectStatus;
use Amo\Sdk\Models\SubjectStatusCollection;
use Amo\Sdk\Models\SubjectThread;
use Amo\Sdk\Models\SubjectThreadCollection;
use Amo\Sdk\Models\Team;
use Amo\Sdk\Models\TeamProps;
use Ramsey\Uuid\Uuid;

require_once 'vendor/autoload.php';

$clientId = getenv('AMO_CLIENT_ID') ?? null;
$clientSecret = getenv('AMO_CLIENT_SECRET') ?? null;

$sdk = new AmoClient([
    'clientID' => $clientId,
    'clientSecret' => $clientSecret,
]);

$appScopedSdk = $sdk->withToken($sdk->getApplicationToken(['teams', 'profiles']));

// Создали команду
$newTeam = $appScopedSdk->team()->create(new Team([
    'title' => 'testTeamName'
]));

// Создали профиль
$createdProfile = $appScopedSdk->profile()->create(new Profile([
    'name' => 'Tim',
    'email' => 'tim@example.com',
    'external_id' => Uuid::uuid4(),
]));

$teamService = $appScopedSdk->team($newTeam->getId())->scope();

// Приглашаем профиль в команду
$invitedUser = $teamService->invite($createdProfile->getId(), new TeamProps([
    'is_admin' => true,
    'position' => 'CEO'
]));

$subjectService = $teamService->subject();
$newSubject = $subjectService->create(new Subject([
    'title' => 'A new patient request',
    'external_link' => 'https://example.com/',
    'author' => Participant::user($createdProfile),
    'participants' => new ParticipantCollection([
        Participant::user($createdProfile->getId()),
    ]),
    'subscribers' => new ParticipantCollection([
        Participant::user($createdProfile->getId()),
    ]),
    'threads' => new SubjectThreadCollection([
        new SubjectThread([
            'title' => 'Patient #100',
            'avatar_url' => 'https://picsum.photos/600'
        ]),
    ]),
    'status' => new SubjectStatusCollection([
        SubjectStatus::status('Заявка с сайта', '#F9F6EE'),
    ])
]));

$subjectService = $teamService->subject($newSubject->getId());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Test embeded chat</title>
    <link rel="stylesheet" href="style.css" />
    <script>
        window.AMO_WSC_PARAMS = {
            appId: '<?=$clientId?>',
            teamId: '<?=$newTeam->getId()?>>',
            subjectId: '<?=$newSubject->getId()?>',
            userId: '<?=$createdProfile->getId()?>',
            userToken: '<?=$subjectService->embedUserToken($createdProfile)?>',
        }
    </script>
</head>
<body>
<div id="app"></div>
<script type="module" src="index.jsx">
    const app = document.getElementById('app');
    ReactDOM.render(<App />, app);
</script>
</body>
</html>


