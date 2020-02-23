<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 1/26/2019
 * Time: 10:22 PM
 */

namespace InspectorGadget\CommandTracker;


class DiscordHandler {

    public $hook;
    public $plugin;

    public function __construct(Handler $plugin, $hook) {
        $this->plugin = $plugin;
        $this->hook = $hook;
    }

    public function getHook() {
        return $this->hook;
    }

    public function sendMessage(array $data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->getHook(),
	    CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $response = curl_exec($curl);
        $curl_error = curl_error($curl);

        if ($response === false) {
            $this->plugin->getLogger()->info("Discord Webhook error: {$curl_error}");
        } else {
            if ($this->plugin->isAllowedToSpam()) { $this->plugin->getLogger()->info("Message has been sent!"); }
        }
    }

}
