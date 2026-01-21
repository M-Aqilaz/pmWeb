<?php

class Controller
{
    public function view($view, $data = [])
    {
        // Pakai __DIR__ untuk memanggil View
        require_once __DIR__ . '/../views/' . $view . '.php';
    }

    public function model($model)
    {
        // Pakai __DIR__ untuk memanggil Model
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }
}
