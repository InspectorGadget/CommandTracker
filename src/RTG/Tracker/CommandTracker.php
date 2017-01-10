<?php

namespace RTG\Tracker;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class CommandTracker extends PluginBase implements Listener {
	
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->logs = new Config($this->getDataFolder() . "logs.yml");
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
				if($m === "/kick" or $m === "/ban" or $m === "/pardon") {
					$this->logs->set($time, "Player: " . $n . " | Command: " . $msg);
					$this->logs->save();
				}
			}
	}
	
	public function onDisable() {
	}
	
}