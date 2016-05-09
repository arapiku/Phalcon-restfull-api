<?php
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
// Use Loader() to autoload our model/data/src/cphalcon/build/64bits/modules/phalcon.so
$loader = new Loader();

$loader->registerDirs(
            array(
                __DIR__ . '/models/',
            )
        )->register();

$di = new FactoryDefault();
// Set up the database service
$di->set('db', function () {
    require (__DIR__ . '/db.php');
	return new PdoMysql($config);
});
//
// Create and bind the DI to the application
$app = new Micro($di);

$app->post('/api/user/login', function () use ($app) {
    $re = $app->request->getJsonRawBody();
    $email = $re->email;
    $password = $re->password;
    checkUser($email,$password);


});
// get messages
function checkUser($email,$password) {

    $message = User::findByEmail($email);
    if ($message == false) {
        echo 'user not found';
    }else{
        include_once (__DIR__ . '/lib/Toolkit.class.php');
        if (Toolkit::validatePassword($password, $message->password_hash)){
            echo 'success';
        }
    }
}
$app->get('/api/messages/{to_uid}', function ($to_uid) use ($app) {

	$response = new Response();
    $messages = Msg::getMsg($to_uid);
//    var_dump($messages);exit;
	if ($messages == false) {
		$response->setJsonContent(
			array(
				'status' => '0',
			)
		);
	} else {
        $data = array();
        foreach ($messages as $message) {
//            print_r($message->id);continue;
            $data[] = array(
                'time' => $message->time,
                'content' => $message->contents,
            );
            // move message to another table
            Message::moveToMessage($message);
            // delete message

            Msg::delMsgById($message->id);

        }
		$response->setJsonContent(
			array(
				'status' => '1',
				'data' => $data,
				)
		);
	}

	return $response;


});

// post message
$app->post('/api/messages', function () use ($app) {
    $header = $app->request->getHeaders();
    $file = $app->request->getUploadedFiles();
    var_dump($file);
    print_r($header);exit;
	$messages = $app->request->getJsonRawBody();
    $status = Msg::saveMsg($messages);

	// Create a response
	$response = new Response();

	// Check if the insertion was successful
	if ($status->success() == true) {

		// Change the HTTP status
		$response->setStatusCode(201, "Created");

		$messages->id = $status->getModel()->id;

		$response->setJsonContent(
			array(
				'status' => 'OK',
				'data' => $messages,
			)
		);

	} else {

		// Change the HTTP status
		$response->setStatusCode(409, "Conflict");

		// Send errors to the client
		$errors = array();
		foreach ($status->getMessages() as $messages) {
			$errors[] = $messages->getMessage();
		}

		$response->setJsonContent(
			array(
				'status' => 'ERROR',
				'messages' => $errors,
			)
		);
	}

	return $response;
});
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});
$app->handle();
