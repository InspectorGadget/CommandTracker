<?php


/**
*	
* .___   ________ 
* |   | /  _____/ 
* |   |/   \  ___ 
* |   |\    \_\  \
* |___| \______  /
*              \/ 
*
* All rights reserved InspectorGadget (c) 2018
*
*
**/


namespace InspectorGadget\CommandTracker;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;


// Event
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class MainHandler extends PluginBase implements Listener {

	public function onEnable(): void {

		if (!is_dir($this->getDataFolder())) {
			@mkdir($this->getDataFolder());
		} else if (!is_dir($this->getDataFolder() . "players")) {
			@mkdir($this->getDataFolder() . "players");
		}

		if (!is_file($this->getDataFolder() . "config.yml")) {
			$this->saveDefaultConfig();
		}

		if ($this->getConfig()->get("enable") !== true) {
			$this->getLogger()->info("Well, I've been set Disable in config.yml!");
			$this->getServer()->getPluginManager()->disablePlugin($this); // Disabling anyways!
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("I'm ready!");

	}

	public function onFilter(PlayerCommandPreprocessEvent $event) {
		$message = $event->getMessage();
		$player = $event->getPlayer();
		$username = $player->getName();

		$list = $this->getConfig()->get("commands", []);
		$array = explode(" ", trim($message));
		$input = $array[0];
		$relay = intval(time());
		$time = date("m-d-Y H:i:s", $relay);

		if (!$player->isOp() || !$player->hasPermission("commandtrack.exempt")) {
			if (in_array($input, $list)) {
				$draft = "Player: $username | Command: $input";
				$this->logs = new Config($this->getDataFolder() . "players/" . strtolower($username) . ".yml", Config::YAML);
				$this->logs->set($time, $draft);
				$this->logs->save();
			}
		}

	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		switch(strtolower($command->getName())) {
			case "ct":
				$sender->sendMessage(TF::GREEN . "CommandTracker is running perfectly!");
				return true;
			break;
		}
	}

	public function onDisable() {
		$this->getConfig()->save();
		$this->getLogger()->info("Killing myself!");
	}

}