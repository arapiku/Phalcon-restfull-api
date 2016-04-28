<?php
/**
 * Created by PhpStorm.
 * User: zhoubo
 * Date: 16/4/28
 * Time: 下午4:04
 */
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

// Define the routes here
$app->get('/api/messages', function () use ($app) {
    $phql = "SELECT * FROM msg ORDER BY time";
    $messages = $app->modelsManager->executeQuery($phql);

    $data = array();
    foreach ($messages as $message) {
        $data[] = array(
            'id' => $message->id,
            'content' => $message->contents,
        );
    }

    echo json_encode($data);

});

// Searches for messages with $content in their content
$app->get('/api/messages/search/{content}', function ($content) use ($app) {
    $phql = "SELECT * FROM msg WHERE contents LIKE :content: ORDER BY contents";
    $messages = $app->modelsManager->executeQuery(
        $phql,
        array(
            'content' => '%' . $content . '%',
        )
    );

    $data = array();
    foreach ($messages as $robot) {
        $data[] = array(
            'id' => $robot->id,
            'content' => $robot->contents,
        );
    }

    echo json_encode($data);
});

// Retrieves messages based on primary key
$app->get('/api/msg/{id:[0-9]+}', function ($id) use ($app) {
    $phql = "SELECT * FROM msg WHERE id = :id:";
    $robot = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
    ))->getFirst();

    // Create a response
    $response = new Response();

    if ($robot == false) {
        $response->setJsonContent(
            array(
                'status' => 'NOT-FOUND',
            )
        );
    } else {
        $response->setJsonContent(
            array(
                'status' => 'FOUND',
                'data' => array(
                    'id' => $robot->id,
                    'name' => $robot->name,
                ),
            )
        );
    }

    return $response;
});

// Adds a new robot
$app->post('/api/robots', function () use ($app) {
    $robot = $app->request->getJsonRawBody();
    $phql = "INSERT INTO Robots (name, type, year) VALUES (:name:, :type:, :year:)";

    $status = $app->modelsManager->executeQuery($phql, array(
        'name' => $robot->name,
        'type' => $robot->type,
        'year' => $robot->year,
    ));

    // Create a response
    $response = new Response();

    // Check if the insertion was successful
    if ($status->success() == true) {

        // Change the HTTP status
        $response->setStatusCode(201, "Created");

        $robot->id = $status->getModel()->id;

        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data' => $robot,
            )
        );

    } else {

        // Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        // Send errors to the client
        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
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

// Updates robots based on primary key
$app->put('/api/robots/{id:[0-9]+}', function () {
    $robot = $app->request->getJsonRawBody();

    $phql = "UPDATE Robots SET name = :name:, type = :type:, year = :year: WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'name' => $robot->name,
        'type' => $robot->type,
        'year' => $robot->year,
    ));

    // Create a response
    $response = new Response();

    // Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(
            array(
                'status' => 'OK',
            )
        );
    } else {

        // Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
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

// Deletes robots based on primary key
$app->delete('/api/robots/{id:[0-9]+}', function () {
    $phql = "DELETE FROM Robots WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
    ));

    // Create a response
    $response = new Response();

    if ($status->success() == true) {
        $response->setJsonContent(
            array(
                'status' => 'OK',
            )
        );
    } else {

        // Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
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
$app->get('/api/messages/{id:[0-9]+}', function ($id) use ($app) {
    $phql = "SELECT * FROM msg WHERE id = :id:";
    $message = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
    ))->getFirst();

    // Create a response
    $response = new Response();

    if ($message == false) {
        $response->setJsonContent(
            array(
                'status' => 'NOT-FOUND',
            )
        );
    } else {
        $response->setJsonContent(
            array(
                'status' => 'FOUND',
                'data' => array(
                    'id' => $message->id,
                    'content' => $message->contents,
                ),
            )
        );
    }

    return $response;
});
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
$app->handle();
