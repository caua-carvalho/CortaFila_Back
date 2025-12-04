<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        echo json_encode(['message' => 'API OK']);
    }
}