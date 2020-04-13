<?php


class TelegramNotifier
{
    private $botId;
    private $chatId;

    function __construct(string $botId, string $chatId)
    {
        $this->botId    = $botId;
        $this->chatId   = $chatId;
    }

    public function sendMessage(string $message) : bool {
        $message = urlencode($message);

        $webHook = "https://api.telegram.org/bot$this->botId/sendMessage?chat_id=-$this->chatId&text=$message";

        try{
            file_get_contents($webHook);
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}