<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: image/jpeg");

$code = '';
for ($i=0; $i < 5; $i++) { $code .= mt_rand(0, 9); }
$_SESSION['captcha'] = $code;

$font = realpath(__DIR__.'/private/captcha.ttf');
$fontSize = 20;
$angle = rand(0, 6) - 3;
$x = 7;
$y = 30;

$img = imagecreatetruecolor(100, 50);
$bgColor = imagecolorallocate($img, 255, 255, 255);
$textColor = imagecolorallocate($img, 0, 0, 0);
imagefilledrectangle($img, 0, 0, 200, 100, $bgColor);

for ($i=0; $i < 5; $i++) {
  $tx = rand(0, 20) - 10;
  $ty = rand(0, 20) - 10;

  imagettftext(
    $img,
    $fontSize,
    $angle, $x + $tx, $y + $ty,
    $textColor,
    $font,
    $code
  );
}

imagettftext(
  $img,
  $fontSize,
  $angle, $x, $y,
  $bgColor,
  $font,
  $code
);

imagejpeg($img, null, 5);
