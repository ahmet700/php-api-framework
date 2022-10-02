<?php

/**
 * PHP API FRAMEWORK
 * Ahmet Ünal
 * ahmet700@gmail.com
 * 
 * 
 * 
 * 
 */
DEFINE('BASEPATH','/api/');

include 'core/Route.php';
include 'core/Db.php';

$app = new Route();

$db = new Db([
  'type' => 'mysql',
  'host' => 'localhost',
  'username' => 'root',
  'password' => '',
  'dbname' => 'vblog'
]);

$app->get('/blog/getall', function($req,$res) {
  global $db;
  $blogs = $db->get('posts');
  $res->json($blogs);
  
  });

  $app->get('/blog/get/{id}',function($req,$res){

    global $db;
    $blogs = $db->getOne('posts');
    $res->json($blogs);


  });

$app->post('/post',function($req,$res) {
  print_r($req->getBody());

});

$app->get('/page/(.*)/(.*)',function($id,$im){

  echo "Hello $id $im";
});

$app->get('/sa/{all}/{id}',function($req,$res){

  print_r($req->params(0));
  print_r($req->getParams());
  $res->send($req->params(0));
});





$app->get('/', function() {

  echo 'Welcome :-)';
});


$app->post('/index.php', function() {

  echo 'You are not really on index.php ;-)';
});



$app->methodNotAllowed(function(){
  http_response_code(400);
  echo "400 Wrong Metod";
});
$app->NotFound(function(){
  http_response_code(404);
  echo "404 Not Found";
});




$app->run(BASEPATH);