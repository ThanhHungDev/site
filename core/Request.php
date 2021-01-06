<?php
namespace Core;

class Request
{
    const GET_METHOD  = "GET";
    const POST_METHOD = "POST";

    function __construct()
    {
        $this->bootstrapSelf();
    }
    
    /**
     * bootstrapSelf là hàm lấy tất cả param của $_SERVER đổ vào cho đối tượng gốc.
     * sau này việc sử dụng 1 router sẽ không cần sử dụng biến global của PHP
     * thay vào đó chúng ta sẽ truyền đối tượng request vào
     * các key của biến $_SERVER sẽ được format theo dạng CamelCase 
     * đây là cú pháp lạc đà
     *
     * @return void
     */
    private function bootstrapSelf()
    {
        foreach ($_SERVER as $key => $value) {

            $this->{$this->toCamelCase($key)} = $value;
        }
    }
    
    /**
     * toCamelCase hàm này để format string bình thường thành cấu trúc lạc đà
     *
     * @param  mixed $string type dạng lạc đà 
     * @return void
     */
    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {

            $c      = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }
    
    /**
     * getBody 
     * hàm này không có giá trị trả ra khi phương thức là GET
     * Bạn có thể handle thêm ở đây khi bạn cần các phương thức khác như PUT, PATCH, DELETE,...
     *
     * @return void $body được trả ra để trong Closure function handle chính sẽ gọi đến
     */
    public function getBody()
    {
        if ($this->requestMethod === Request::GET_METHOD ) {
            return;
        }


        if ($this->requestMethod == Request::POST_METHOD ) {

            $body = array();
            foreach ($_POST as $key => $value) {

                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            return $body;
        }
    }
}