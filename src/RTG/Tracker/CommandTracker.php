<?php

/*
 * All rights reserved RTGNetworkkk
 * Please give credits :)
*/

namespace RTG\Tracker;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CommandTracker extends PluginBase implements Listener {
	
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . "players");
	}
	
	public function onFilter(PlayerCommandPreprocessEvent $e) {
		$msg = $e->getMessage();
		$carray = explode(" ",trim($msg));
		$m = $carray[0];
		$p = $e->getPlayer();
		$n = $p->getName();
		$r = intval(time());
		$time = date("m-d-Y H:i:s", $r);
			
			if($p->isOp() or $p->hasPermission("system.track")) {
				if($m === "/kick" or $m === "/ban" or $m === "/pardon" or $m === "/me") {
					$this->logs = new Config($this->getDataFolder() . "players/" . strtolower($p->getName()) . ".yml", Config::YAML);
					$this->logs->set($time, "Player: " . $n . " | Command: " . $msg);
					$this->logs->save();
				}
			}
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $param) {
		switch(strtolower($cmd->getName())) {
			
			case "check":
				$sender->sendMessage("CommandTracker is running perfectly!");
				return true;
			break;
			
		}
	}		
	
	public function onDisable() {
		$this->logs->save();
	}
	
}