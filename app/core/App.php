<?php

class App
{
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Cek Controller (Pakai __DIR__ biar ketemu filenya)
        // __DIR__ . '/../controllers/' artinya: dari folder core, mundur ke app, masuk controllers
        if (isset($url[0])) {
            if (file_exists(__DIR__ . '/../controllers/' . ucfirst($url[0]) . '.php')) {
                $this->controller = ucfirst($url[0]);
                unset($url[0]);
            }
        }

        // 2. Panggil Controller (Ini yang bikin error tadi)
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        
        // Instansiasi Controller
        $this->controller = new $this->controller;

        // 3. Cek Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 4. Params
        $this->params = $url ? array_values($url) : [];

        // 5. Jalankan
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return []; 
    }
}
