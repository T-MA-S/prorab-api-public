<?php


namespace app\commands;

use yii\console\Controller;
use app\models\Account;
use app\models\User;
use Workerman\Worker;

class SocketController extends Controller
{
    public $users = [];
    public $wsWorker;

    public function actionRun()
    {
        $this->wsWorker = new Worker("websocket://0.0.0.0:2000");
        $this->wsWorker->count = 1;

        $this->wsWorker->onConnect = function ($connection) {
            $connection->onWebSocketConnect = function ($connection) {
                // try {
                //     $account = Account::findIdentityByAccessToken($_GET['token']);
                //     $connection->uid = $account->user_id;
                // } catch (\Throwable $e) {
                //     echo 'Caught exception: ',  $e->getMessage(), "\n";
                // }
            };
        };

        $this->wsWorker->onMessage = function ($connection, $data) {

            $dataArr = json_decode($data, true);

            foreach ($this->wsWorker->connections as $conn) {
                $response = json_encode([
                    'fromUser'  => $dataArr['fromUser'],  // от кого
                    'toUser'    => $dataArr['toUser'],    // кому
                    'message'   => $dataArr['message'],   // сообщение
                    'messageId' => $dataArr['messageId'], // ID сообщения
                    'file'      => $dataArr['file'],      // ссылка на файл
                    'filename'  => $dataArr['filename'],  // имя файла
                    'viewed'    => $dataArr['viewed'],    // сообщение просмотрено
                ]);
                $conn->send($response);
            }
        };

        $this->wsWorker->onClose = function ($connection) {
        };

        Worker::runAll();
    }
}
