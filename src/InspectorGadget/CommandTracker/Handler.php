<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 1/26/2019
 * Time: 4:13 PM
 */

namespace InspectorGadget\CommandTracker;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Internet;

class Handler extends PluginBase implements Listener {

    public function onEnable(): void {
        if (!is_dir($this->getDataFolder())) { @mkdir($this->getDataFolder()); }
        if (!is_dir($this->getDataFolder() . "players/")) { @mkdir($this->getDataFolder() . "players"); }
        if (!is_file($this->getDataFolder() . "config.yml")) { $this->saveDefaultConfig(); }

        if ($this->getConfig()->get("enable") !== true) {
            $this->getLogger()->info('Shutting myself down!');
            $this->getServer()->getPluginManager()->disablePlugin($this);
        } else {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            if ($this->hasDiscordSupport()) { $this->getLogger()->info('Discord Support has been enabled!'); }
            $this->getLogger()->warning('I\'m up and running!');
            $this->checkForUpdates();
        }
    }

    public function returnDiscordHandler() {
        return (new DiscordHandler($this, $this->getConfig()->get('discord')['webhook']));
    }

    public function pingDiscord(string $message) {
        # You can hard code this section, I didn't add a config for a reason!
        $data = array(
          'username' => 'InspectorGadget',
          'content' => $message,
          'avatar_url' => 'https://cdn.discordapp.com/avatars/186484096447414272/188fc15737388fd945b8fe30154624cd.png'
        );
        # Enjoy!
        $this->returnDiscordHandler()->sendMessage($data);
    }

    public function checkForUpdates() {
        $api = 'http://api.rtgnetworks.com/mcpe/commandtracker';
        $getURL = Internet::getURL($api);
        $decode = json_decode($getURL);
        $currentVersion = $this->getDescription()->getVersion();

        if ($currentVersion < $decode->version) {
            $this->getLogger()->warning("New version available! Version: {$decode->version}");
        }
        elseif ($currentVersion == $decode->version) {
            $this->getLogger()->warning("You are rocking the latest version! | Version: {$currentVersion}");
        }
    }

    public function hasDiscordSupport(): bool {
        $config = $this->getConfig()->get("discord");
        if ($config['enable'] !== false && $config['webhook'] !== "") {
            return true;
        } else {
            return false;
        }
    }

    public function isAllowedToSpam(): bool {
        $config = $this->getConfig()->get("discord");
        if ($config['spam-logs'] !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function onExecute(PlayerCommandPreprocessEvent $event) {
        $message = $event->getMessage();
        $player = $event->getPlayer();
        $username = $player->getName();

        $list = $this->getConfig()->get("commands", []);
        $array = explode(" ", trim($message));
        $input = $array[0];
        $relay = intval(time());
        $time = date('m-d-Y H:i:s', $relay);

        if (!$player->isOp() || !$player->hasPermission('commandtrack.exempt')) {
            if (in_array($input, $list)) {
                $draft = "Player: $username | Command: $message";
                $discord = "[$time] Player: $username | Usage of restricted Command: $message";
                if ($this->hasDiscordSupport()) { $this->pingDiscord($discord); }
                $logs = new Config($this->getDataFolder() . "players/" . strtolower($username) . ".yml", Config::YAML);
                $logs->set($time, $draft);
                $logs->save();
            }
        }
    }

    public function onDisable(): void {
        $this->getLogger()->info("Goodbye <3");
    }

}