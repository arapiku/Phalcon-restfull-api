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

// get messages
$app->get('/api/messages/{to_uid}', function ($to_uid) use ($app) {
	$phql = "SELECT * FROM msg WHERE to_uid = :to_uid: ORDER BY time";
	$messages = $app->modelsManager->executeQuery(
        $phql,
        array(
            'to_uid' => $to_uid,
            )
        );
	$response = new Response();

	if ($messages == false) {
		$response->setJsonContent(
			array(
				'status' => '0',
			)
		);
	} else {
        $data = array();
        foreach ($messages as $message) {

            $data[] = array(
                'time' => $message->time,
                'content' => $message->contents,
            );
            // move message to another table
            $phql = "INSERT INTO message (from_uid, to_uid, contents,time) VALUES (:from_uid:, :to_uid:, :contents:, :time:)";
            $status = $app->modelsManager->executeQuery($phql, array(
                'from_uid' => $message->from_uid,
                'to_uid' => $message->to_uid,
                'contents' => $message->contents,
                'time' => $message->time,
            ));
            // delete message
            if ($status->success() == true) {
                $phql = "DELETE FROM msg WHERE id = :id:";
                $app->modelsManager->executeQuery ($phql, array (
                    'id' => $message->id,
                ));
            }
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
	$messages = $app->request->getJsonRawBody();
	$phql = "INSERT INTO msg (from_uid, to_uid, contents,time) VALUES (:from_uid:, :to_uid:, :contents:, :time:)";

	$status = $app->modelsManager->executeQuery($phql, array(
		'from_uid' => $messages->from_user,
		'to_uid' => $messages->to_user,
		'contents' => $messages->content,
		'time' => $messages->time,
	));

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
