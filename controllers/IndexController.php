<?php

namespace controllers;

class IndexController {

    public function index(){
        view('index.index');
    }

    public function top(){
        view('index.top');
    }

    public function main(){
        view('index.main');
    }

    public function menu(){
        view('index.menu');
    }
}