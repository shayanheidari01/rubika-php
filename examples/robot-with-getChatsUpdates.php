<?php

include "rubika/client.php";

$bot = new Client("AUTH");
$answered = array();

while (true) {
    $updates = $bot -> getChatsUpdates();
    foreach ($updates as $update) {
        if ($update["last_message"]["type"] === "Text") {
            $new_id = $update["object_guid"] . $update["last_message"]["message_id"];
            if (!in_array($new_id, $answered)) {
                $text = $update["last_message"]["text"];
                if ($text === "تست") {
                    $bot -> sendText($update["object_guid"], "این پیام دارای هیچ محتوای خاصی نیست و صرفا جهت تست کتابخانه php است", $update["last_message"]["message_id"]);
                    array_push($answered, $new_id);
                }
                else {
                    array_push($answered, $new_id);
                }
            }
        }
    }
}

?>