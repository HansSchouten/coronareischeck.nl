<?php

function e($value, $doubleEncode = true)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}

function dd($var)
{
    print_r($var);
    exit();
}
