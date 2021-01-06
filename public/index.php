<?php
function coreAutoload($class)
{
    $root = '../core/';
    $prefix = 'Core\\';

    // remove prefix
    $classWithoutPrefix = preg_replace('/^' . preg_quote($prefix) . '/', '', $class);
    // Thay thế \ thành /
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix) . '.php';

    $path = $root . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}
spl_autoload_register('coreAutoload');


use Core\Request;
use Core\Router;

/// khởi tạo đối tượng router
$router = new Router(new Request);

// chú ý: trong đối tượng router hoàn toàn không có method get, post, put gì cả
/// nhưng ở đây mình vẫn gọi 1 method get => trong php nó sẽ chạy vào hàm __call 
$router->get('/', function () {
    return "Hello world";
});

/// tương tự khi gọi method post mà router không có method post nên sẽ chạy vào hàm __call
$router->post('/data', function ($request) {

    return json_encode($request->getBody());
});


//// kết thúc hoàn toàn quá trình
/// tại đây hàm __destruct được gọi, vì hàm hủy được chạy khi hệ thống chương trình hủy 1 đối tượng
/// lúc này là lúc ta thực thi code cần thiết theo từng router