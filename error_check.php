<?php
$lines = file('storage/logs/laravel.log');
$lastLines = array_slice($lines, -150);
echo implode("", $lastLines);
