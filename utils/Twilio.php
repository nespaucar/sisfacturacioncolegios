<?php
 
namespace Utils;
 
class Twilio {
 
    private $sid = "ACff3a6e37530a2a78e20437834f80a8de"; // Your Account SID from www.twilio.com/console
    private $token = "a2220ec7f3ad9219dbb0d09193fca5f6"; // Your Auth Token from www.twilio.com/console
 
    private $client;
 
    public function __construct() {
        $this->client = new \Twilio\Rest\Client($this->sid, $this->token);
    }
 
    public function sendSMS($from, $body, $to) {
        $message = $this->client->messages->create($to, // Text this number
          array(
            'from' => $from, // From a valid Twilio number
            'body' => $body
          )
        );
        return $message;
    }

    public function sendWhatsAppSMS($from, $to, $body) {
 
        $message = $this->client->messages->create("whatsapp:" . $to, // to
          array(
            "from" => "whatsapp:" . $from,
            "body" => $body
          )
        );
        return $message;          
    }
    
}