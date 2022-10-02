<?php
// https://github.com/steampixel/simplePHPRouter

include 'core/Request.php';
include 'core/Response.php';

class Route {

  private $routes = Array();
  private $NotFound = null;
  private $methodNotAllowed = null;
  private $anyMethods = ['GET','POST','PUT','PATCH','DELETE'];
  private $patterns = [
    '{all}' => '(.*)',
    '{any}' => '([^/]+)',
    '{id}' => '(\d+)',
    '{int}' => '(\d+)',
    '{number}' => '([+-]?([0-9]*[.])?[0-9]+)',
    '{bool}' => '(true|false|1|0)',
    '{string}' => '([\w\-_]+)',  
    
];


  public function any($expression, $function) {
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => $this->anyMethods,
    ));
  }
  public function get($expression, $function){
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'get'
    ));
  }
  public function post($expression, $function){
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'post'
    ));
  } 
  
  public function put($expression, $function){
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'put'
    ));
  } 
  public function patch($expression, $function){
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'patch'
    ));
  } 
  public function delete($expression, $function){
    array_push($this->routes, Array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'delete'
    ));
  }

  

  public function getAll(){
    return $this->routes;
  }

  public function NotFound($function) {
    $this->NotFound = $function;
  }

  public function methodNotAllowed($function) {
    $this->methodNotAllowed = $function;
  }

  public function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false) {

    $basepath = rtrim($basepath, '/');
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);

    $path = '/';

   
    if (isset($parsed_url['path'])) {
      if ($trailing_slash_matters) {
  		  $path = $parsed_url['path'];
  	  } else {
        if($basepath.'/'!=$parsed_url['path']) {
          $path = rtrim($parsed_url['path'], '/');
        } else {
          $path = $parsed_url['path'];
        }
  	  }
    }

  	$path = urldecode($path);

   $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach ($this->routes as $route) {

     if ($basepath != '' && $basepath != '/') {
        $route['expression'] = '('.$basepath.')'.$route['expression'];
      }

    
      $route['expression'] = '^'.$route['expression'].'$';
      $route['expression'] = str_replace(array_keys($this->patterns),array_values($this->patterns),$route['expression']);
  

     
      if (preg_match('#'.$route['expression'].'#'.($case_matters ? '' : 'i').'u', $path, $matches)) {
        $path_match_found = true;
 

       
        foreach ((array)$route['method'] as $allowedMethod) {
           
          
            if ((strtolower($method) == strtolower($allowedMethod))) {
            array_shift($matches); 

            if ($basepath != '' && $basepath != '/') {
              array_shift($matches); 
            }
            $req = new Request($matches);
            $res = new Response();
            if($return_value = call_user_func_array($route['function'], [$req,$res])) {
              echo $return_value;
            }

            $route_match_found = true;
            break;
          }
        }
      }

      if($route_match_found&&!$multimatch) {
        break;
      }

    }

    // No matching route was found
    if (!$route_match_found) {
     
      if ($path_match_found) {
        if ($this->methodNotAllowed) {
          call_user_func_array($this->methodNotAllowed, Array($path,$method));
        }
      } else {
        if ($this->NotFound) {
          call_user_func_array($this->NotFound, Array($path));
        }
      }

    }
  }

}
