<?php

function POST($url, $data){
    while (true) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = ['content-type: application/json; charset=UTF-8',];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        global $ngix_error_bypass;
        if ($response != null & strpos($response, "The page you are looking for is temporarily unavailable") == false)
            {
                return $response;
            }
        else {
            continue;
        }
    }
}

function GET($url){
    return file_get_contents($url);
}

function keyMaker(string $auth){
    $b = "";
    $b .= substr($auth, 16, 8);
    $b .= substr($auth, 0, 8);
    $b .= substr($auth, 24);
    $b .= substr($auth, 8, 8);
    for ($i = 0; $i < strlen($b); $i++) {
        if ($b[$i] >= '0' && $b[$i] <= '9') {
            $b[$i] = chr((((ord($b[$i]) - 48) + 5) % 10) + 48);
        }
        if ($b[$i] >= 'a' && $b[$i] <= 'z') {
            $b[$i] = chr((((ord($b[$i]) - 97) + 9) % 26) + 97);
        }
    }
    return $b;
}

class Client {
    public $auth;
    public $key;
    public $iv;

    public function __construct(string $auth) {
        if (strlen($auth) !== 32) {
            exit("your auth is incorrect, please check and try again");
        }
        $this -> iv = str_repeat("\x00", 16);
        $this -> key = keyMaker($auth);
        $this -> auth = $auth;
    }

    public function decrypt(string $data) {
        return openssl_decrypt(base64_decode($data), "AES-256-CBC", $this -> key, OPENSSL_RAW_DATA, $this -> iv);
    }

    public function encrypt(string $data) {
        return base64_encode(openssl_encrypt($data, "AES-256-CBC", $this -> key, OPENSSL_RAW_DATA, $this -> iv));
    }

    public function makeAPI() {
        $dc = random_int(1, 11);
        settype($dc, "string");
        return "https://messengerg2c1" . $dc . ".iranlms.ir";
    }

    public function responseParser(string $response) {
        $response = json_decode($response, true);
        return json_decode($this -> decrypt($response["data_enc"]), true);
    }

    public function makeMethods($data, string $method) {
        $my_data = [
            "api_version" => "4",
            "auth" => $this -> auth,
            "client" => [
                "app_name" => "Main",
                "app_version" => "2.8.1",
                "lang_code" => "fa",
                "package" => "ir.resaneh1.iptv",
                "platform" => "Android"
            ],
            "data_enc" => $this -> encrypt(json_encode($data)),
            "method" => $method
        ];
        return json_encode($my_data);
    }

    public function request(string $method, $data) {
        $body = $this -> makeMethods($data, $method);
        $response = POST($this -> makeAPI(), $body);
        return $this -> responseParser($response);
    }

    public function sendText(string $object_guid, string $text, $reply_to_message_id=null) {
        $data = [
            "object_guid" => $object_guid,
            "rnd" => random_int(100000000, 999999999),
            "text" => $text,
            "reply_to_message_id" => $reply_to_message_id
        ];
        return $this -> request("sendMessage", $data);
    }

    public function getChatsUpdates() {
        $chats = $this -> request('getChatsUpdates', ['state' => time() - 200]);
        return $chats["chats"];
    }
}

?>