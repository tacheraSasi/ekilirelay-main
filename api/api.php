<?php

class Method{
    public static function POST(){
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }
    
    public static function GET(){
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }
}