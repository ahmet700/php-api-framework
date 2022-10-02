<?php
class Response
{
    private $p_status = 200;

    public function p_status(int $p_code)
    {
        $this->p_status = $p_code;
        return $this;
    }
    
    public function json($data = [])
    {
       
        http_response_code($this->p_status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    public function send($data,$header_code = 200)
    {
        http_response_code($header_code);
          echo $data;
    }
}