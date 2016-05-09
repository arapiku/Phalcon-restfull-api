<?php
include_once (__DIR__ . '/config/constant.php');
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
    require (__DIR__ . '/config/db.php');
	return new PdoMysql($config);
});
//
// Create and bind the DI to the application
$app = new Micro($di);

$app->post('/api/user/login', function () use ($app) {
    $re = $app->request->getJsonRawBody();
    $response = new Response();
    if ($re == NULL) {
		$response->setJsonContent(
			array(
				'status' => LOGIN_STATE_FAILD,
			)
		);
	} else {
    	$email = isset($re->email)?$re->email:'';
    	$password = isset($re->password)?$re->password:'';
    	if($email=='' || $password=='')
    	{
    		$response->setJsonContent(
				array(
					'status' => LOGIN_EMAIL_PASSWOD_EMPTY,
				)
			);
			return $response;
    	}
    	include_once (__DIR__ . '/lib/Toolkit.class.php');
    	if (!Toolkit::checkEmail($email)){
    		$response->setJsonContent(
				array(
					'status' => LOGIN_EMAIL_INVALID,
				)
			);
			return $response;
    	}
    	$dbUser = User::findByEmail($email);
    	if(!$dbUser){
    		$response->setJsonContent(
				array(
					'status' => LOGIN_USER_NOFOUND,
				)
			);
			return $response;
    	}
    	if (!Toolkit::validatePassword($password, $dbUser->password_hash))
    	{
    		$response->setJsonContent(
				array(
					'status' => LOGIN_PASSWORD_INVALID,
				)
			);
			return $response;
    	}
    	$data = array(
    		'frendId' => 123,
    		);
    	$response->setJsonContent(
			array(
				'status' => LOGIN_PASSWORD_INVALID,
				'data' => $data,
			)
		);
	}
	return $response;
});

// get messages
$app->get('/api/messages/{to_uid}', function ($to_uid) use ($app) {

	$response = new Response();
    $messages = Msg::getMsg($to_uid);

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
