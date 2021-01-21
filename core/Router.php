<?php
namespace Core;

class Router
{

    private $request;
    private $supportedHttpMethods = array(
        
        Request::GET_METHOD,
        Request::POST_METHOD
    );

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    function __call($name, $args)
    {
        list($route, $method) = $args;

        /// kiểm tra phương thức có được support không ? 
        /// hiện tại chỉ có GET và POST
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {

            /// nếu phương thức chưa được hộ trợ thì return 405 header
            $this->invalidMethodHandler();
        }

        //// nạp Closure function vào router
        /// get:array(1)
        /// "/" : Closure
        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    /**
     * Resolves a route
     * là hàm xử lý chính cho router trước khi hàm __destruct success
     * hàm này sẽ gọi function được declare trong file router để chạy nội dung
     */
    function resolve()
    {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formatedRoute    = $this->formatRoute($this->request->requestUri);
        $method           = isset($methodDictionary[$formatedRoute])? $methodDictionary[$formatedRoute] : null;

        if (is_null($method)) {

            /// nếu không tìm thấy route nào phù hợp thì sẽ trả ra header 404
            $this->defaultRequestHandler();
            return;
        }
        
        /// hàm thần thánh để thực thi code của function Closure trong file index.php
        echo call_user_func_array($method, array($this->request));
    }

    function __destruct()
    {
        $this->resolve();
    }
}