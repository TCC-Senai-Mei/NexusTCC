<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        
        #return view('minha_primeira_pagina');
         return view('welcome_message');

    }

   

    
}
