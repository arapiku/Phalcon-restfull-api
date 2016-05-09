<?php
include_once './lib/Loger.php';
include './lib/DBMongo.php';

$collection = new DBMongo();
if ( ! $collection->connect('mongodb://127.0.0.1:27017', 'gridfs', 'fs.files'))
{
	echo 111;

	return false;
}
    $image = $collection->grid_find_one_bytes("rBACFFYvdTLRBMppAAEM8iWOHvU385_200x200_3.png");
    header('Content-type: image/png');
   	echo $image;
