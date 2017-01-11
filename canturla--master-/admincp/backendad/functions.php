<?php
error_reporting(E_ALL ^ E_NOTICE);

// Prefer unescaped. Data will be escaped as needed.
if (ini_get("magic_quotes_gpc")) {
    $_POST = array_map_recursive("unesc", $_POST);
    $_GET = array_map_recursive("unesc", $_GET);
    $_REQUEST = array_map_recursive("unesc", $_REQUEST);
    $_COOKIE = array_map_recursive("unesc", $_COOKIE);
}

if (function_exists("date_default_timezone_set"))
    date_default_timezone_set("Europe/Paris"); // Do NOT change this. All times are converted to user's chosen timezone.

define("BASEPATH", str_replace("backendad", "", dirname(__FILE__)));
$BASEPATH = BASEPATH;
define("BACKEND", dirname(__FILE__));
$BACKEND = BACKEND;

require_once("/admincp/backendad/mysql.php"); //Get MYSQL Connection Info
require_once(BACKEND."/config.php");  //Get Site Settings and Vars ($site_config)
require_once(BACKEND."/mail.php"); // Mail functions
require_once(BACKEND."/mysql.class.php");

$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));


/**
 * Connect to the database and load user details
 *
 * @param $autoclean
 *   (optional) boolean - Check whether or not to run cleanup (default: false)
 */
function dbconn() {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $site_config;

    if (!ob_get_level()) {
        if (extension_loaded('zlib') && !ini_get('zlib.output_compression'))
            ob_start('ob_gzhandler');
        else
            ob_start();
    }

    //header("Content-Type: text/html;charset=$site_config[CHARSET]");

    function_exists("mysqli_connect") or die("MySQL support not available.");

    @($GLOBALS["___mysqli_ston"] = mysqli_connect($mysql_host,  $mysql_user,  $mysql_pass)) or die('DATABASE: mysql_connect: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    @((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $mysql_db)) or die('DATABASE: mysql_select_db: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

    unset($mysql_pass); //security

    userlogin(); //Get user info

    //Get language and theme
    $CURUSER = $GLOBALS["CURUSER"];
}

// IP Validation
function validip($ip) {
    if (extension_loaded("filter")) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    if (preg_match('![:a-f0-9]!i', $ip))
        return true;

    if (!empty($ip) && $ip == long2ip(ip2long($ip))) {
        $reserved_ips = array (
                array('0.0.0.0','2.255.255.255'),
                array('10.0.0.0','10.255.255.255'),
                array('127.0.0.0','127.255.255.255'),
                array('169.254.0.0','169.254.255.255'),
                array('172.16.0.0','172.31.255.255'),
                array('192.0.2.0','192.0.2.255'),
                array('192.168.0.0','192.168.255.255'),
                array('255.255.255.0','255.255.255.255')
        );

        foreach ($reserved_ips as $r) {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
        }
        return true;
    }
        return false;
}

function getip() {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
          $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
      else
          $ipaddress = 'UNKNOWN';

      return $ipaddress;
}

function userlogin() {
    $ip = getip();
    // If there's no IP a script is being ran from CLI. Any checks here will fail, skip all.
    if ($ip == "") return;

    GLOBAL $CURUSER;
    unset($GLOBALS["CURUSER"]);

    //Check IP bans
    if (is_ipv6($ip))
        $nip = ip2long6($ip);
    else
        $nip = ip2long($ip);
    $res = SQL_Query_exec("SELECT * FROM bans");
    while ($row = mysqli_fetch_assoc($res)) {
        $banned = false;
        if (is_ipv6($row["first"]) && is_ipv6($row["last"]) && is_ipv6($ip)) {
            $row["first"] = ip2long6($row["first"]);
            $row["last"] = ip2long6($row["last"]);
            $banned = bccomp($row["first"], $nip) != -1 && bccomp($row["last"], $nip) != -1;
        } else {
            $row["first"] = ip2long($row["first"]);
            $row["last"] = ip2long($row["last"]);
            $banned = $nip >= $row["first"] && $nip <= $row["last"];
        }
        if ($banned) {
            header("HTTP/1.0 403 Forbidden");
            echo "<html><head><title>Forbidden</title></head><body><h1>Forbidden</h1>Unauthorized IP address.<br />".
            "Reason for banning: $row[comment]</body></html>";
            die;
        }
    }

    //Check The Cookie and get CURUSER details
    if (strlen($_COOKIE["pass"]) != 40 || !is_numeric($_COOKIE["uid"])) {
        logoutcookie();
        return;
    }

    //Get User Details And Permissions
    $res = SQL_Query_exec("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE id=$_COOKIE[uid] AND users.enabled='yes' AND users.status = 'confirmed'");
    $row = mysqli_fetch_assoc($res);

    if (!$row || sha1($row["id"].$row["secret"].$row["password"].$ip.$row["secret"]) != $_COOKIE["pass"]) {
        logoutcookie();
        return;
    }

    $where = where ($_SERVER["SCRIPT_FILENAME"], $row["id"], 0);
    SQL_Query_exec("UPDATE users SET last_access='" . get_date_time() . "', ip=".sqlesc($ip).", page=".sqlesc($where)." WHERE id=" . $row["id"]);


    $GLOBALS["CURUSER"] = $row;
    unset($row);
}

function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff) {
    $hash = sha1($id.$secret.$password.getip().$secret);
    setcookie("pass", $hash, $expires, "/");
    setcookie("uid", $id, $expires, "/");

    if ($updatedb)
        SQL_Query_exec("UPDATE users SET last_login = '".get_date_time()."' WHERE id = $id");
}

function logoutcookie() {
    setcookie("pass", null, time(), "/");
    setcookie("uid", null, time(), "/");
}

function stdhead($title = "") {
    global $site_config, $CURUSER, $THEME, $LANGUAGE;  //Define globals

    //site online check
    if (!$site_config["SITE_ONLINE"]){
        if ($CURUSER["control_panel"] != "yes") {
            echo '<br /><br /><br /><center>'. stripslashes($site_config["OFFLINEMSG"]) .'</center><br /><br />';
            die;
        }else{
            echo '<br /><br /><br /><center><b><font color="#ff0000">SITE OFFLINE, STAFF ONLY VIEWING! DO NOT LOGOUT</font></b><br />If you logout please edit backendad/config.php and set SITE_ONLINE to true </center><br /><br />';
        }
    }
    //end check

    if (!$CURUSER)
        guestadd();

    if ($title == ""){
        $title = $site_config['SITENAME'];
    }else{
        $title = $site_config['SITENAME']. " : ". htmlspecialchars($title);
    }
    require_once("template/header.php");
    require_once("template/menu.php");
}

function stdfoot() {
    global $site_config, $CURUSER, $THEME, $LANGUAGE;
    require_once("template/footer.php");
    ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify"){
    global $site_config;
    print("<div class='widget'>
	<h4>
		$caption
	</h4>
	<div id='$togglediv' class=''>");

}

//END FRAME
function end_frame() {
    global $THEME, $site_config;
    print("<div class='clear'></div>
	</div>
</div>");
}

function show_error_msg($title, $message, $wrapper = "1") {
    if ($wrapper) {
        ob_start();
        ob_clean();
        stdhead($title);
    }

    echo '<p class="error">'.$message.'</p>';

    if ($wrapper){
        stdfoot();
        die();
    }
}

// MySQL escaping
function sqlesc($x) {
   if (!is_numeric($x)) {
       $x = "'".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'";
   }
   return $x;
}


function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function escape_url($url) {
    $ret = '';
    for($i = 0; $i < strlen($url); $i+=2)
    $ret .= '%'.$url[$i].$url[$i + 1];
    return $ret;
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;

    $t = array();
    $t["day"] = floor($s / 86400);
    $s -= $t["day"] * 86400;

    $t["hour"] = floor($s / 3600);
    $s -= $t["hour"] * 3600;

    $t["min"] = floor($s / 60);
    $s -= $t["min"] * 60;

    $t["sec"] = $s;

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function gmtime() {
    return sql_timestamp_to_unix_timestamp(get_date_time());
}

function loggedinonly() {
    global $CURUSER;
    if (!$CURUSER) {
        header("Refresh: 0; url=account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    if (function_exists("filter_var"))
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    return preg_match('/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i', $email);
}

function mksecret($len = 20) {
    $chars = array_merge(range(0, 9), range("A", "Z"), range("a", "z"));
    shuffle($chars);
    $x = count($chars) - 1;
    for ($i = 1; $i <= $len; $i++)
        $str .= $chars[mt_rand(0, $x)];
    return $str;
}

// Added DELETE FROM bookmarks and friends by mobman
function deleteaccount($userid)
{
    SQL_Query_exec("DELETE FROM users WHERE id = $userid");
    SQL_Query_exec("DELETE FROM warnings WHERE userid = $userid");
    SQL_Query_exec("DELETE FROM ratings WHERE user = $userid");
    SQL_Query_exec("DELETE FROM peers WHERE userid = $userid");
    SQL_Query_exec("DELETE FROM completed WHERE userid = $userid");
    SQL_Query_exec("DELETE FROM reports WHERE addedby = $userid");
    SQL_Query_exec("DELETE FROM reports WHERE votedfor = $userid AND type = 'user'");
    SQL_Query_exec("DELETE FROM forum_readposts WHERE userid = $userid");
    SQL_Query_exec("DELETE FROM pollanswers WHERE userid = $userid");
    SQL_Query_exec("DELETE FROM `friends` WHERE `userid` = $userid");
    SQL_Query_exec("DELETE FROM `friends` WHERE `friendid` = $userid");
    SQL_Query_exec("DELETE FROM bookmarks WHERE userid = $userid");
}

function genrelist() {
    $ret = array();
    $res = SQL_Query_exec("SELECT id, name, parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC");
    while ($row = mysqli_fetch_assoc($res))
        $ret[] = $row;
    return $ret;
}

function is_valid_id($id){
    return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function is_valid_int($id){
    return is_numeric($id) && (floor($id) == $id);
}

function sql_timestamp_to_unix_timestamp($s){
    return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

function write_log($text){
    $text = sqlesc($text);
    $added = sqlesc(get_date_time());
    SQL_Query_exec("INSERT INTO log (added, txt) VALUES($added, $text)");
}

function get_elapsed_time($ts){
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks wk" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours hr" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}

function guestadd() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = gmtime();
    SQL_Query_exec("INSERT INTO `guests` (`ip`, `time`) VALUES ('$ip', '$time') ON DUPLICATE KEY UPDATE `time` = '$time'");
}

function getguests() {
    $past = (gmtime() - 2400);
    SQL_Query_exec("DELETE FROM `guests` WHERE `time` < $past");
    return get_row_count("guests");
}

function time_ago($addtime) {
   $addtime = get_elapsed_time(sql_timestamp_to_unix_timestamp($addtime));
   return $addtime;
}

function CutName ($vTxt, $Car) {
    if (strlen($vTxt) > $Car) {
        return substr($vTxt, 0, $Car) . "...";
    }
    return $vTxt;
}

function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function get_row_count($table, $suffix = "") {
    $res = SQL_Query_exec("SELECT COUNT(*) FROM $table $suffix");
    $row = mysqli_fetch_row($res);
    return $row[0];
}

function get_date_time($timestamp = 0){
    if ($timestamp)
        return date("Y-m-d H:i:s", $timestamp);
    else
        return gmdate("Y-m-d H:i:s");
}

// Convert UTC to user's timezone
function utc_to_tz ($timestamp=0) {
    GLOBAL $CURUSER, $tzs;

    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp)
            $timestamp = get_date_time();
        $date = new DateTime($timestamp, new DateTimeZone("UTC"));

        $date->setTimezone(new DateTimeZone($CURUSER ? $tzs[$CURUSER["tzoffset"]][1] : "Europe/London"));
        return $date->format('Y-m-d H:i:s');
    }
    if (!is_numeric($timestamp))
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    if ($timestamp == 0)
        $timestamp = gmtime();

    $timestamp = $timestamp + ($CURUSER['tzoffset']*60);
    if (date("I")) $timestamp += 3600; // DST Fix
    return date("Y-m-d H:i:s", $timestamp);
}

function utc_to_tz_time ($timestamp=0) {
    GLOBAL $CURUSER, $tzs;

    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp)
            $timestamp = get_date_time();
        $date = new DateTime($timestamp, new DateTimeZone("UTC"));
        $date->setTimezone(new DateTimeZone($CURUSER ? $tzs[$CURUSER["tzoffset"]][1] : "Europe/London"));
        return sql_timestamp_to_unix_timestamp($date->format('Y-m-d H:i:s'));
    }

    if (!is_numeric($timestamp))
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    if ($timestamp == 0)
        $timestamp = gmtime();

    $timestamp = $timestamp + ($CURUSER['tzoffset']*60);
    if (date("I")) $timestamp += 3600; // DST Fix

    return $timestamp;
}

function encodehtml($s, $linebreaks = true) {
      $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
      if ($linebreaks)
        $s = nl2br($s);
      return $s;
}


function format_urls($s){
    return preg_replace(
    "/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i",
    "\\1<a href='http://kpaste.net/redir/\\2' target='_blank'>\\2</a>", $s);
}

function format_comment($text) {
    global $site_config, $smilies;

    $s = $text;

    $s = htmlspecialchars($s);
    $s = format_urls($s);

    // [*]
    $s = preg_replace("/\[\*\]/", "<li>", $s);

    // [b]Bold[/b]
    $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);

    // [i]Italic[/i]
    $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);

    // [u]Underline[/u]
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);

    // [u]Underline[/u]
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s);

    // [img]http://www/image.gif[/img]
    $s = preg_replace("/\[img\]((http|https):\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\[\/img\]/i", "<img border='0' src=\"\\1\" alt='' />", $s);

    // [img=http://www/image.gif]
    $s = preg_replace("/\[img=((http|https):\/\/[^\s'\"<>]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\]/i", "<img border='0' src=\"\\1\" alt='' />", $s);

    // [color=blue]Text[/color]
    $s = preg_replace(
        "/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
        "<font color='\\1'>\\2</font>", $s);

    // [color=#ffcc99]Text[/color]
    $s = preg_replace(
        "/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
        "<font color='\\1'>\\2</font>", $s);

    // [url=http://www.example.com]Text[/url]
    $s = preg_replace(
        "/\[url=((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
        "<a href='http://kpaste.net/redir/\\1' target='_blank'>\\3</a>", $s);

    // [url]http://www.example.com[/url]
    $s = preg_replace(
        "/\[url\]((http|ftp|https|ftps|irc):\/\/[^<>\s]+?)\[\/url\]/i",
        "<a href='http://kpaste.net/redir/\\1' target='_blank'>\\1</a>", $s);

    // [size=4]Text[/size]
    $s = preg_replace(
        "/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i",
        "<font size='\\1'>\\2</font>", $s);

    // [font=Arial]Text[/font]
    $s = preg_replace(
        "/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
        "<font face=\"\\1\">\\2</font>", $s);

    //[quote]Text[/quote]
    while (preg_match("/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", $s))
    $s = preg_replace(
        "/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
        "<p class='sub'><b>Quote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br />", $s);

    //[quote=Author]Text[/quote]
    while (preg_match("/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", $s))
    $s = preg_replace(
        "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
        "<p class='sub'><b>\\1 wrote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br />", $s);

    // [spoiler]Text[/spoiler]
    $r = substr(md5($text), 0, 4);
    $i = 0;
    while (preg_match("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i", $s)) {
        $s = preg_replace("/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i",
        "<br /><img src='images/plus.gif' id='pic$r$i' title='Spoiler' onclick='klappe_torrent(\"$r$i\")' alt='' /><div id='k$r$i' style='display: none;'>\\1<br /></div>", $s);
        $i++;
    }

    // [spoiler=Heading]Text[/spoiler]
    while (preg_match("/\[spoiler=(.+?)\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i", $s)) {
        $s = preg_replace("/\[spoiler=(.+?)\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i",
        "<br /><img src='images/plus.gif' id='pic$r$i' title='Spoiler' onclick='klappe_torrent(\"$r$i\")' alt='' /><b>\\1</b><div id='k$r$i' style='display: none;'>\\2<br /></div>", $s);
        $i++;
    }

     //[hr]
        $s = preg_replace("/\[hr\]/i", "<hr />", $s);

     //[hr=#ffffff] [hr=red]
        $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);

        //[swf]http://somesite.com/test.swf[/swf]
        $s = preg_replace("/\[swf\]((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\[\/swf\]/i",
        "<param name='movie' value='\\1'/><embed width='470' height='310' src='\\1'></embed>", $s);

        //[swf=http://somesite.com/test.swf]
        $s = preg_replace("/\[swf=((www.|http:\/\/|https:\/\/)[^\s]+(\.swf))\]/i",
        "<param name='movie' value='\\1'/><embed width='470' height='310' src='\\1'></embed>", $s);

    // Linebreaks
    $s = nl2br($s);

    // Maintain spacing
    $s = str_replace("  ", " &nbsp;", $s);

    // Smilies
    require_once("smilies.php");
    reset($smilies);
    while (list($code, $url) = each($smilies))
        $s = str_replace($code, '<img border="0" src="'.$site_config["SITEURL"].'/images/smilies/'.$url.'" alt="'.$code.'" title="'.$code.'" />', $s);

    if($site_config["OLD_CENSOR"])
    {
    $r = SQL_Query_exec("SELECT * FROM censor");
    while($rr=mysqli_fetch_row($r))
    $s = preg_replace("/".preg_quote($rr[0])."/i", $rr[1], $s);
    }
    else
    {

    $f = @fopen("censor.txt","r");

    if ($f && filesize("censor.txt") != 0) {

       $bw = fread($f, filesize("censor.txt"));
       $badwords = explode("\n",$bw);

       for ($i=0; $i<count($badwords); ++$i)
           $badwords[$i] = trim($badwords[$i]);
       $s = str_replace($badwords, "<img src='images/censored.png' border='0' alt='Censored' title='Censored' />", $s);
    }
    @fclose($f);
    }

    return $s;
}

function pager($rpp, $count, $href, $opts = array()) {
    $pages = ceil($count / $rpp);

    if (!$opts["lastpagedefault"])
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    }
    else
        $page = $pagedefault;

    $pager = "";

    $mp = $pages - 1;
    $as = "<b>&lt;&lt;&nbsp;".T_("PREVIOUS")."</b>";
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;
    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $as = "<b>".T_("NEXT")."&nbsp;&gt;&gt;</b>";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}page=" . ($page + 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "...";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;
            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page)
                $pagerarr[] = "<a href=\"{$href}page=$i\"><b>$text</b></a>";
            else
                $pagerarr[] = "<b>$text</b>";
        }
        $pagerstr = join(" | ", $pagerarr);
        $pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
        $pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
    }
    else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function commenttable($res, $type = null) {
    global $site_config, $CURUSER, $THEME, $LANGUAGE;  //Define globals

    while ($row = mysqli_fetch_assoc($res)) {

        $postername = htmlspecialchars($row["username"]);
        if ($postername == "") {
            $postername = T_("DELUSER");
            $title = T_("DELETED_ACCOUNT");
            $avatar = "";
            $usersignature = "";
            $userdownloaded = "";
            $useruploaded = "";
        }else {
            $privacylevel = $row["privacy"];
            $avatar = htmlspecialchars($row["avatar"]);
            $title =  format_comment($row["title"]);
            $usersignature = stripslashes(format_comment($row["signature"]));
            $userdownloaded = mksize($row["downloaded"]);
            $useruploaded = mksize($row["uploaded"]);
        }

        if ($row["downloaded"] > 0)
            $userratio = number_format($row["uploaded"] / $row["downloaded"], 2);
        else
            $userratio = "---";

        if (!$avatar)
            $avatar = $site_config["SITEURL"]."/images/default_avatar.png";

        $commenttext = format_comment($row["text"]);

        $edit = null;
        if ($type == "torrent" && $CURUSER["edit_torrents"] == "yes" || $type == "news" && $CURUSER["edit_news"] == "yes" || $CURUSER['id'] == $row['user'])
            $edit = '[<a href="comments.php?id='.$row["id"].'&amp;type='.$type.'&amp;edit=1">Edit</a>]&nbsp;';

        $delete = null;
        if ($type == "torrent" && $CURUSER["delete_torrents"] == "yes" || $type == "news" && $CURUSER["delete_news"] == "yes")
            $delete = '[<a href="comments.php?id='.$row["id"].'&amp;type='.$type.'&amp;delete=1">Delete</a>]&nbsp;';

        print('<div class="f-post f-border"><table cellspacing="0" width="100%">');
        print('<tr class="p-title">');
        print('<th align="center" width="150"></th>');
        print('<th align="right">' . $edit . $delete . '[<a href="report.php?comment='.$row["id"].'">Report</a>] Posted: '.date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"])).'<a id="comment'.$row["id"].'"></a></th>');
        print('</tr>');
        print('<tr valign="top">');
        if ($CURUSER['edit_users'] == 'no' && $privacylevel == 'strong')
            print('<td class="f-border comment-details" align="left" width="150"><center><b>'.$postername.'</b><br /><i>'.$title.'</i><br /><img width="80" height="80" src="'.$avatar.'" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ---<br /><br /><a href="account-details.php?id='.$row["user"].'"><img src="themes/'.$THEME.'/forums/icon_profile.png" border="" alt="" /></a> <a href="mailbox.php?compose&amp;id='.$row["user"].'"><img src="themes/'.$THEME.'/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        else
            print('<td class="f-border comment-details" align="left" width="150"><center><b>'.$postername.'</b><br /><i>'.$title.'</i><br /><img width="80" height="80" src="'.$avatar.'" alt="" /><br /><br />Uploaded: '.$useruploaded.'<br />Downloaded: '.$userdownloaded.'<br />Ratio: '.$userratio.'<br /><br /><a href="account-details.php?id='.$row["user"].'"><img src="themes/'.$THEME.'/forums/icon_profile.png" border="0" alt="" /></a> <a href="mailbox.php?compose&amp;id='.$row["user"].'"><img src="themes/'.$THEME.'/forums/icon_pm.png" border="0" alt="" /></a></center></td>');
        print('<td class="f-border comment">'.$commenttext.'<hr />'.$usersignature.'</td>');
        print('</tr>');
        print('</table></div>');
        print('<br />');
    }
}

function where ($scriptname = "index", $userid, $update=1){
    if (!is_valid_id($userid))
        die;
    if (preg_match("/torrents-details/i", $scriptname))
        $where = "Browsing Torrents Details (ID: $_GET[id])...";
    elseif (preg_match("/torrents.php/i", $scriptname))
        $where = "Browsing Torrents...";
    elseif (preg_match("/account-details/i", $scriptname))
        $where = "Browsing Account Details (ID: $_GET[id])...";
    elseif (preg_match("/torrents-upload/i", $scriptname))
        $where = "Uploading Torrent..";
    elseif (preg_match("/account/i", $scriptname))
        $where = "Browsing User Control Panel...";
    elseif (preg_match("/torrents-search/i", $scriptname))
        $where = "Searching Torrents...";
    elseif (preg_match("/forums/i", $scriptname))
        $where = "Browsing Forums...";
    elseif (preg_match("/index/i", $scriptname))
        $where = "Browsing Homepage...";
    else
        $where = "Unknown Location...";

    if ($update) {
        $query = sprintf("UPDATE users SET page=".sqlesc($where)." WHERE id ='%s'", ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $userid) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")));
        $result = SQL_Query_exec($query);
    }
        return $where;
}

function get_user_class_name($i){
    GLOBAL $CURUSER;
    if ($i == $CURUSER["class"])
        return $CURUSER["level"];

    $res=SQL_Query_exec("SELECT level FROM groups WHERE group_id=".$i."");
    $row=mysqli_fetch_row($res);
    return $row[0];
}

function get_user_class(){
    return $GLOBALS["CURUSER"]["class"];
}

function get_ratio_color($ratio) {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    return "#000000";
}

function ratingpic($num) {
    GLOBAL $site_config;
    $r = round($num * 2) / 2;
    if ($r != $num) {
        $n = $num-$r;
        if ($n < .25)
            $n = 0;
        elseif ($n >= .25 && $n < .75)
            $n = .5;
        $r += $n;
    }
    if ($r < 1 || $r > 5)
        return;

    return "<img src=\"".$site_config["SITEURL"]."/images/rating/$r.png\" border=\"0\" alt=\"rating: $num/5\" title=\"rating: $num/5\" />";
}

function DateDiff ($start, $end) {
    if (!is_numeric($start))
        $start = sql_timestamp_to_unix_timestamp($start);
    if (!is_numeric($end))
        $end = sql_timestamp_to_unix_timestamp($end);
    return ($end - $start);
}

function classlist() {
    $ret = array();
    $res = SQL_Query_exec("SELECT * FROM groups ORDER BY group_id ASC");
    while ($row = mysqli_fetch_assoc($res))
        $ret[] = $row;
    return $ret;
}

function array_map_recursive ($callback, $array) {
    $ret = array();

    if (!is_array($array))
        return $callback($array);

    foreach ($array as $key => $val) {
        $ret[$key] = array_map_recursive($callback, $val);
    }
    return $ret;
}

function cleanstr ($s) {
    if (function_exists("filter_var")) {
        return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    } else {
        return preg_replace('/[\x00-\x1F]/', "", $s);
    }
}

function is_ipv6 ($s) {
    return is_int(strpos($s, ":"));
}

// Taken from php.net comments
function ip2long6($ipv6) {
  $ip_n = inet_pton($ipv6);
  $bits = 15; // 16 x 8 bit = 128bit
  while ($bits >= 0) {
    $bin = sprintf("%08b",(ord($ip_n[$bits])));
    $ipv6long = $bin.$ipv6long;
    $bits--;
  }
  return gmp_strval(gmp_init($ipv6long,2),10);
}

function long2ip6($ipv6long) {

  $bin = gmp_strval(gmp_init($ipv6long,10),2);
  if (strlen($bin) < 128) {
    $pad = 128 - strlen($bin);
    for ($i = 1; $i <= $pad; $i++) {
    $bin = "0".$bin;
    }
  }
  $bits = 0;
  while ($bits <= 7) {
    $bin_part = substr($bin,($bits*16),16);
    $ipv6 .= dechex(bindec($bin_part)).":";
    $bits++;
  }
  // compress

  return inet_ntop(inet_pton(substr($ipv6,0,-1)));
}

function passhash ($text) {
    GLOBAL $site_config;

    switch (strtolower($site_config["passhash_method"])) {
        case "sha1":
            return sha1($text);
        break;
        case "md5":
            return md5($text);
        break;
        case "hmac":
            $ret = hash_hmac($site_config["passhash_algorithm"], $text, $site_config["passhash_salt"]);
            return $ret ? $ret : die("Config Error: Unknown algorithm '$site_config[passhash_algorithm]'");
        break;
        default:
            die("Unrecognised passhash_method. Check your config.");
    }
}

  function autolink($al_url, $al_msg) {
      stdhead();
      begin_frame("");
      echo "\n<meta http-equiv=\"refresh\" content=\"3; url=$al_url\">\n";
      echo "<b>$al_msg</b>\n";
      echo "\n<b>Redirecting ...</b>\n";
      echo "\n[ <a href='$al_url'>link</a> ]\n";
      end_frame();
      stdfoot();
      exit;
  }

function class_user($name)
{
  global $site_config;
  $a = "SELECT u.class, u.donated, u.warned, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE username ='" . $name . "'";
  //  echo "Query a".$a."<br />";
  $res = SQL_Query_exec($a);
  $classy = @mysqli_fetch_array($res);
  $gcolor = $classy['Color'];
  if ($classy['donated'] > 0) {
  $star = "<img src='" . $site_config[SITEURL] . "/images/icons/star.gif' alt='donated' border='0'>";
  } else {
  $star = "";
  }
  if ($classy['warned'] == "yes") {
  $warn = "<img src='" . $site_config[SITEURL] . "/images/icons/warn.gif' alt='warned' border='0'>";
  } else {
  $warn = "";
  }
  $rang = auto_rank_icon($classy['uploaded'], $classy['downloaded'], $classy['level']);
  return unesc("<font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $rang . "</font>");
}

// http://stackoverflow.com/questions/2089590/mysqli-equivalent-of-mysql-result
// MySQLi RESULT
function mysqli_result($result,$row,$field=0) {
    if ($result===false) return false;
    if ($row>=mysqli_num_rows($result)) return false;
    if (is_string($field) && !(strpos($field,".")===false)) {
        $t_field=explode(".",$field);
        $field=-1;
        $t_fields=mysqli_fetch_fields($result);
        for ($id=0;$id<mysqli_num_fields($result);$id++) {
            if ($t_fields[$id]->table==$t_field[0] && $t_fields[$id]->name==$t_field[1]) {
                $field=$id;
                break;
            }
        }
        if ($field==-1) return false;
    }
    mysqli_data_seek($result,$row);
    $line=mysqli_fetch_array($result);
    return isset($line[$field])?$line[$field]:false;
}
?>
