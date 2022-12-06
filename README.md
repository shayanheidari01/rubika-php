# rubika-php
A library for making selfies and bots in Rubika messenger with php programming language

**An Exmaple:**
``` php
<?php

include "rubika/client.php";

$bot = new Client("AUTH");

$test = $bot -> sendText("chat_id->object_guid", "text-message");
print_r($test);

?>

```

**Other examples:**
  https://github.com/shayanheidari01/rubika-php/tree/main/examples


**Thanks From:**
- https://t.me/HajiApi
