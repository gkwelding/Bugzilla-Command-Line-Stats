<?php
/**
 * Retrieve stats about a user in bugzilla and their work.
 *
 * @author		Garry Welding
 * @link		https://github.com/gkwelding/Bugzilla-Command-Line-Stats
 */
$programName = "bugz";
$programVer = "0.2a";

require_once dirname(__FILE__).'/cli.php';
$cli = new Cli();
$cli->_init();

$robot = $cli->option("robot",false);

if($robot) {
	$nice = $cli->option("nice",false);
	
	if(!$nice) {
		$speech = $cli->option("speech",null);
		$robot = run($cli,$speech);
	} else {
		$robot = protect($cli);
	}
	
	$cli->write($robot);
	
	exit;
}

require_once dirname(__FILE__).'/bugzilla.php';

$bugz = new BugzillaStats();
$bugz->_init();

$n = $cli->option("name");

if($n) {
	$p = $bugz->getIdFromName($n);
	
	if(isset($p['userid']) && $p['userid']>0) {
		$l = $cli->option("to",null);
		
		if(is_null($l)) {
			$l = time();
		} else {
			$l = strtotime($l);
		}
		
		$lp = $l+86400;
		
		$c = $cli->option("duration",7);
		
		$d = array();
		
		for($i=1;$i<$c;$i++) {
			$d[] = time()-(86400*$i);
		}
		
		$f = array();
		
		$f[] = date('Y-m-d 00:00:00', $lp);
		$f[] = date('Y-m-d 00:00:00', $l);
		
		foreach($d as $_d) {
			$f[] = date('Y-m-d 00:00:00', $_d);
		}
		
		unset($d,$_d);
		
		$h = array();
		$cf = count($f)-1;
		
		$det = $cli->option("detail",false);
		
		for($k=0;$k<$cf;$k++) {
			$k1 = $k+1;
			$h[$k] = $bugz->getDetailedTime($p['userid'],$f[$k1],$f[$k]);
			if(!$h[$k]['added']>0) $h[$k]['added'] = 0;
			$h[$k]["display_time"] = $f[$k1];
		}
		
		unset($f,$k,$k1,$cf,$p);
			
		$t = 0;
		
		foreach($h as $_h) {
			if($det) {
				$pl = 128;
			} else {
				$pl = 32;
			}
			
			$fd = date('l jS F Y', strtotime($_h['display_time']));
			$fd = str_pad($fd, $pl, ' ');
			
			if($_h['added']<7.5) {
				$ha = $cli->color("{$_h['added']} hrs","light_red");
			} else {
				$ha = $cli->color("{$_h['added']} hrs","light_green");
			}
			
			$ln = $cli->color("{$fd} {$ha}","light_gray");
			
			$cli->write($ln);
			
			if($det) {
				while($hd = $_h['detail']->fetch_assoc()) {
					$desc = substr($hd['short_desc'],0,96);
					$dd = str_pad("    bug#{$hd['bug_id']} ({$hd['bug_status']}) - {$desc}", 128, " ");
					$dln = $cli->color("{$dd}     {$hd['added']} hrs","light_gray");
					$cli->write($dln);
				}
			}
			
			$t = $t+$_h['added'];
		}
		
		$tl = str_pad("Total:", $pl, ' ')." {$t} hrs";
		
		$cli->write();
		$cli->write($tl);
	} else {
		$cli->beep(1);
		$cli->error("Sorry, the user \"{$n}\" could not be found.");
		exit;
	}
} else {
	$cli->beep(1);
	$up = 128;
	
	$u = $cli->color(str_pad(" ", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    {$programName} v{$programVer}", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad(" ", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    USAGE: {$programName} [--option]=[value]", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    Eg. {$programName} --name=\"Garry Welding\"", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad(" ", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    OPTIONS:", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    --name         The name of the user. Eg. {$programName} --name=\"Garry Welding\"", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    --duration     How many days to get data for. Eg. {$programName} --name=\"Garry Welding\" --duration=14", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    --to           Which day to start on, default to today if not provided. Eg. {$programName} --name=\"Garry Welding\" --to=\"9-6-2011\"", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad("    --detail       The detail flag will give you a breakdown of the individual bugs worked on per day with a time count", $up, " "),"white","blue");
	$u .= "\n";
	$u .= $cli->color(str_pad(" ", $up, " "),"white","blue");
	
	$cli->write($u);
	exit;
}