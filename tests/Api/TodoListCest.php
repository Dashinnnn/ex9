<?php 
    namespace tests\Api;
    use tests\Support\ApiTester; 

    class TodoListCest
    {
        public function _before(ApiTester $I) 
        {}

        //scenario 2

        public function iShouldCreateNewTask(ApiTester $I) {
            $I->haveHttpHeader('Content-Type', 'application/json');

            $I->sendPost('/', [
                'task_title' => 'Task 1',
                'task_name' => 'Onboarding Task',
                'time' => '2024-07-25 22:00:00'
            ]);

            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);

            $I -> sendPost('/', [
                  'task_title' => 'Task 2',
                  'task_name' => 'Team Task 1',
                  'time' => '2024-07-25 23:00:00'
            ]);

            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }

        //scenario 3

        public function iShouldViewTask(ApiTester $I) {
            $I -> sendGet ('/tasks');
            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }

        //scenario 4 

        public function iShouldMoveFinishedTask(ApiTester $I) {
            $I->haveHttpHeader('Content-Type', 'application/json');

            $I -> sendPut('/update-task/1', [
                'id' => 1,
                'status' => 'done'
            ]);

            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }

        //scenario 5

        public function iShouldMoveBackDoneTask(ApiTester $I) {
            $I->haveHttpHeader('Content-Type', 'application/json');

            $I -> sendPut('/update-task/1', [
                'id' => 1,
                'status' => 'in-progress'
            ]);

            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }

        //scenario 6
        public function iShouldEditTask(ApiTester $I) {
            $I->haveHttpHeader('Content-Type', 'application/json');

            $I -> sendPut('/update-task/1', [
                'id' => 1,
                'task_title' => 'Updated Task',
                'task_name'  => 'Updated task name',
                'time' => '2024-07-25 00:00:00'
            ]);
            
            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }

        //scenario 7 
        public function iShoulDelete(ApiTester $I) {
            $I->haveHttpHeader('Content-Type', 'application/json');

            $I -> sendDelete('/delete-task', ['id' => 1]);
            $I -> seeResponseCodeIs(200);
            $I -> seeResponseIsJson();
            $I -> seeResponseContainsJson(['status' => 'success']);
        }
    }
?>
