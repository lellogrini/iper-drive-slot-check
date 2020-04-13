#!/usr/bin/env php
<?php

include __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$store = new IperStore(getenv('STORE_ID'), getenv('STORE_PDR_ID'));
$availabilities = $store->getAvailabilities();

$telegram = new TelegramNotifier(getenv('TELEGRAM_BOT_ID'), getenv('TELEGRAM_CHANNEL_ID'));
foreach ($availabilities as $date => $availability) {
    if (count($availability) > 0) {
        $telegram->sendMessage("IPER DRIVE".PHP_EOL."Hurray! There are some available slots for $date, go to ".$store->getStoreURL());
    }
}

?>