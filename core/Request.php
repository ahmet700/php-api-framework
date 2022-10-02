<?php
//https://www.section.io/engineering-education/getting-started-with-php7-without-using-a-framework/

class Request
{
    public $params;
    public $getparas;
    public $req_method;
    public $content_type;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->getparams = isset($_GET) ? $_GET : [];
        $this->req_method = trim($_SERVER['REQUEST_METHOD']);
        $this->content_type = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }

    public function params($param=null){
        return is_null($param) ? $this->params : (isset($this->params[$param]) ? $this->params[$param] : null);
    }
    public function getParams($param=null) {
        
        return is_null($param) ? $this->getparams : (isset($this->getparams[$param]) ? $this->getparams[$param] : null);
    }
    public function getBody($postparam=null)
    {
        if ($this->req_method !== 'POST') {
            return ;
        }

        if(!is_null($postparam)) {
            return isset($_POST[$postparam]) ?   $_POST[$postparam] : null;
        }

        $post_body = [];
        foreach ($_POST as $key => $value) {
            $post_body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $post_body;
    }

    public function getJSON()
    {

 
        if ($this->req_method !== 'POST') {
            return [];
        }

       /* if (strcasecmp($this->content_type, 'application/json') !== 0) {
            return [];
        }
*/
        // Receive the RAW post data.
        $post_content = trim(file_get_contents("php://input"));
        $p_decoded = json_decode($post_content,true);
        return $p_decoded;
    }
}