<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//$headers = "From: <info@starterok.ua>";


if (mail('3gs@ukr.net', 'Test mail from Starterok.ua', 'Проверка отправки почты с сайта Starterok.ua')) {
    echo "Mail to 3gs@ukr.net was sent<br>";
} else {
    echo "error";
  }

  if (mail('a_savinih@ukr.net', 'Test mail from Starterok.ua', 'Проверка отправки почты с сайта Starterok.ua')) {
    echo "Mail to a_savinih@ukr.net was sent<br>";
} else {
    echo "error";
  }
?>