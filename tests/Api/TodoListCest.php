<?php
namespace Tests\Api;
use Tests\Support\ApiTester;

class TodoListCest
{
    public function _before(ApiTester $I)
    {
    }

    // Scenario 2
    public function iShouldCreateNewTasks(ApiTester $I) {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/ex9/API.php', [
            'title'=> 'Task 1',
            'task_name' => 'Onboarding Task',
            'time' => '10:00 PM'
        ]);
        $I->sendPost('/ex9/API.php', [
            'title'=> 'Task 2',
            'task_name' => 'Team Task 1',
            'time' => '11:00 PM'
        ]);

        $I->sendPost('/ex9/API.php/save', []);
        $I->sendGet('/ex9/API.php/in-progress');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'task_name' => 'Test',
                    'time' => '2024-07-25 12:00:00',
                    'id' => 3, 
                    'task_title' => 'Example Task',
                    'status' => 'Inprogress'
                ]
            ],
            'method' => 'GET',
            'status' => 'success'
        ]);
    }

    // Scenario 3
    public function iShouldViewTasks(ApiTester $I) {
        $I->sendGet('/ex9/API.php/tasks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'task_name' => 'Test',
                    'time' => '2024-07-25 12:00:00'
                ]
            ],
            'method' => 'GET',
            'status' => 'success'
        ]);
    }

    // Scenario 4
    public function iShouldMoveFinishedTask(ApiTester $I) {
        $I->sendPost('/ex9/API.php/update-task', [
            'task_id' => 1,
            'status' => 'done'
        ]);

        $I->sendGet('/ex9/API.php/done');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'task_name' => 'Test',
                    'time' => '2024-07-25 12:00:00'
                ]
            ],
            'method' => 'GET',
            'status' => 'success'
        ]);
    }

    // Scenario 5
    public function iShouldMoveBackDoneTask(ApiTester $I) {
        $I->sendPost('/ex9/API.php/update-task', [
            'task_id' => 1,
            'status' => 'in-progress'
        ]);
        $I->sendGet('/ex9/API.php/in-progress');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'task_name' => 'Test',
                    'time' => '2024-07-25 12:00:00'
                ]
            ],
            'method' => 'GET',
            'status' => 'success'
        ]);
    }

    // Scenario 6
    public function iShouldEditTask(ApiTester $I) {
        $I->sendPost('/ex9/API.php/update-task', [
            'task_id' => 1,
            'title' => 'Update Task',
            'task_name' => 'Update Name',
            'time' => '12:00 AM'
        ]);
        $I->sendGet('/ex9/API.php/tasks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'task_name' => 'Test',
                    'time' => '2024-07-25 12:00:00',
                    'id' => 3, 
                    'task_title' => 'Example Task',
                    'status' => 'Inprogress'
                ]
            ],
            'method' => 'GET',
            'status' => 'success'
        ]);
    }

    // Scenario 7
    public function iShouldDelete(ApiTester $I) {
        $I->sendDelete('/ex9/API.php/delete-task', ['task_id' => 1]);
        $I->sendGet('/ex9/API.php/tasks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJson(['task_id' => 1]);
    }
}
