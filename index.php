<?php
/*
+------------------------------------------------
|   BitTorrent Tracker PHP
|   =============================================
|   by xblade
|   (c) 2017 - 2018
|   =============================================
|   Licence Info: GPL
+------------------------------------------------
*/
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
dbconn(true);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('index') );
    $HTMLOUT = '';
if ($CURUSER) {
    $cache_newuser = "./cache/newuser.txt";
    $cache_newuser_life = 2 * 60 ; //2 min
if (file_exists($cache_newuser) && is_array(unserialize(file_get_contents($cache_newuser))) && (time() - filemtime($cache_newuser)) < $cache_newuser_life)
    $arr = unserialize(@file_get_contents($cache_newuser));
    else {
    $r_new = mysqli_query($GLOBALS["___mysqli_ston"], "select id , username, class FROM users order by id desc limit 1 ") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($r_new);
    $handle = fopen($cache_newuser, "w+");
    fwrite($handle, serialize($arr));
    fclose($handle);
}
    $new_user = "&nbsp;<font color='#" . get_user_class_color($arr['class']) . "'> " . htmlspecialchars($arr['username']) . "</font>\n";
}
    $registered = number_format(get_row_count("users"));
    $torrents = number_format(get_row_count("torrents"));
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value_u FROM avps WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
    $a = mysqli_fetch_row($r);
    $seeders = 0 + $a[0];
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value_u FROM avps WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
    $a = mysqli_fetch_row($r);
    $leechers = 0 + $a[0];
if ($leechers == 0)
    $ratio = 0;
    else
    $ratio = round($seeders / $leechers * 100);
    $peers = number_format($seeders + $leechers);
    $seeders = number_format($seeders);
    $leechers = number_format($leechers);
    $male = number_format(get_row_count("users", "WHERE gender='Male'"));
    $female = number_format(get_row_count("users", "WHERE gender='Female'"));
    $nobody = number_format(get_row_count("users", "WHERE gender='N/A'"));
    $unverified = number_format(get_row_count("users", "WHERE status='pending'"));
    $warnedu = number_format(get_row_count("users", "WHERE warned='yes'"));
    $disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
    $NAT = number_format(get_row_count("peers", "WHERE connectable='no'"));
    $dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
    $donor = number_format(get_row_count("users", "WHERE donor='yes'"));
    $adminbutton = '';
if (get_user_class() >= UC_ADMINISTRATOR)
    $adminbutton = "&nbsp;<span style='float:right;'><a href='admin.php?action=news'>News page</a></span>\n";
    $HTMLOUT .= "<div class='headline'>&nbsp;-&nbsp;{$lang['news_title']}</span>{$adminbutton}</span></div><div class='headbody'>";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM news WHERE added + ( 3600 *24 *45 ) > ".time()." ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
require_once "include/bbcode_functions.php";
    $button = "";
    while($array = mysqli_fetch_assoc($res)){
if (get_user_class() >= UC_ADMINISTRATOR){
    $button = "<div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid={$array['id']}'>{$lang['news_edit']}</a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid={$array['id']}'>{$lang['news_delete']}</a></div>";
}
    $HTMLOUT .= "<div style='background:black;height:20px;'><span style='font-weight:bold;font-size:10pt;'>{$array['headline']}</span></div>\n";
    $HTMLOUT .= "<span style='color:grey;font-weight:bold;text-decoration:underline;'>".get_date( $array['added'],'DATE') . "</span>{$button}\n";
    $HTMLOUT .= "<div style='margin-top:10px;padding:5px;'>".format_comment($array['body'])."</div><hr />\n";
    }
}
    $HTMLOUT .= "</div><br />\n";
if ($CURUSER['show_shout'] === "yes") {
    $commandbutton = '';
    $refreshbutton = '';
    $smilebutton = '';
if ($CURUSER['class'] >= UC_ADMINISTRATOR){
    $commandbutton = "<span style='float:right;'><a href=\"javascript:popUp('shoutbox_commands.php')\">{$lang['index_shoutbox_commands']}</a></span>\n";}
    $refreshbutton = "<span style='float:right;'><a href='shoutbox.php' target='sbox'>{$lang['index_shoutbox_refresh']}</a></span>\n";
    $smilebutton = "<span style='float:right;'><a href=\"javascript:PopMoreSmiles('shbox','shbox_text')\">{$lang['index_shoutbox_smilies']}</a></span>\n";
    $HTMLOUT .= "<form action='shoutbox.php' method='get' target='sbox' name='shbox' onsubmit='mysubmit()'>
    <div class='headline'>&nbsp;-&nbsp;
    ShoutBox - General Chit-chat
    <span class='shouthis'>";
    $HTMLOUT.= "</span><span class='shoutextra'><a href='{$FMED['baseurl']}/shoutbox.php?show_shout=1&amp;show=no'>[&nbsp;{$lang['index_shoutbox_close']}&nbsp;]</a>
    {$smilebutton}{$refreshbutton}{$commandbutton}</font></span>
    </div>
    <div class='headbody'>
    <font size='2'><center><b><font color=red>Site Announcements</font><b>
    <br />
    <b><font color=#777777>::: Please seed to site rules to a Ratio of 1:1 or 72 hours seedtime which ever comes first :::</font></b>
    <br /></b>
    <font size='1'><b><font color=#777777>Shoutbox Rules: English Only, No Swearing, Personal Attacks, No Outside Links,  No Abuse, No Spamming</font><br><b>
    </center>
    <br />
    <div align='center'><br />
    <hr>
    <iframe src='shoutbox.php' width='100%' height='200' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe>
    <hr>
    <script type=\"text/javascript\" src=\"scripts/shout.js\"></script>
    <br />
    <div align='center'><br />
    <br /><b>{$lang['index_shoutbox_shout']}</b>
    <input type='text' maxlength='680' name='shbox_text' size='1' style='width:500px;' />
    <input class='btn' type='submit' value='{$lang['index_shoutbox_send']}' />
    <input type='hidden' name='sent' value='yes' />
    <br /><br />
    <a href=\"javascript:SmileIT(':-)','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/smile1.gif' alt='Smile' title='Smile' /></a>
    <a href=\"javascript:SmileIT(':smile:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/smile2.gif' alt='Smiling' title='Smiling' /></a>
    <a href=\"javascript:SmileIT(':-D','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/grin.gif' alt='Grin' title='Grin' /></a>
    <a href=\"javascript:SmileIT(':lol:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/laugh.gif' alt='Laughing' title='Laughing' /></a>
    <a href=\"javascript:SmileIT(':w00t:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='W00t' title='W00t' /></a>
    <a href=\"javascript:SmileIT(';-)','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/wink.gif' alt='Wink' title='Wink' /></a>
    <a href=\"javascript:SmileIT(':devil:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/devil.gif' alt='Devil' title='Devil' /></a>
    <a href=\"javascript:SmileIT(':yawn:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/yawn.gif' alt='Yawn' title='Yawn' /></a>
    <a href=\"javascript:SmileIT(':-/','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/confused.gif' alt='Confused' title='Confused' /></a>
    <a href=\"javascript:SmileIT(')','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/clown.gif' alt='Clown' title='Clown' /></a>
    <a href=\"javascript:SmileIT(':innocent:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/innocent.gif' alt='Innocent' title='innocent' /></a>
    <a href=\"javascript:SmileIT(':whistle:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/whistle.gif' alt='Whistle' title='Whistle' /></a>
    <a href=\"javascript:SmileIT(':unsure:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/unsure.gif' alt='Unsure' title='Unsure' /></a>
    <a href=\"javascript:SmileIT(':blush:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/blush.gif' alt='Blush' title='Blush' /></a>
    <a href=\"javascript:SmileIT(':hmm:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/hmm.gif' alt='Hmm' title='Hmm' /></a>
    <a href=\"javascript:SmileIT(':hmmm:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/hmmm.gif' alt='Hmmm' title='Hmmm' /></a>
    <a href=\"javascript:SmileIT(':huh:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/huh.gif' alt='Huh' title='Huh' /></a>
    <a href=\"javascript:SmileIT(':look:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/look.gif' alt='Look' title='Look' /></a>
    <a href=\"javascript:SmileIT(':rolleyes:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/rolleyes.gif' alt='Roll Eyes' title='Roll Eyes' /></a>
    <a href=\"javascript:SmileIT(':kiss:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/kiss.gif' alt='Kiss' title='Kiss' /></a>
    <a href=\"javascript:SmileIT(':blink:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/blink.gif' alt='Blink' title='Blink' /></a>
    <a href=\"javascript:SmileIT(':baby:','shbox','shbox_text')\"><img border='0' src='{$FMED['pic_base_url']}smilies/baby.gif' alt='Baby' title='Baby' /></a><br/>
    <br />
    </div>
    </div>
    </div>
    </form><br />\n";
}
if ($CURUSER['show_shout'] === "no") {
    $HTMLOUT.= "<div class='headline'><b>{$lang['index_shoutbox']}&nbsp;</b></div><div class='headbody'>&nbsp;<a href='{$FMED['baseurl']}/shoutbox.php?show_shout=1&amp;show=yes'>[&nbsp;{$lang['index_shoutbox_open']}&nbsp;]</a><!--</div>--></div><br />";
}
    $activeusers24 = "";
    $no24 = "";
    $dt24 = time() - 86400;
    $arr = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM avps WHERE arg='last24'")) or $no24 = true;
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, username, class, warned, donor, enabled FROM users WHERE last_access >= $dt24 ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);
    $totalonline24 = mysqli_num_rows($res);
    $_ss24 = ($totalonline24 != 1) ? '\'s':'';
    $last24record = get_date($arr["value_u"], 'DATE');
    $last24 = $arr["value_i"];
if ($no24 || $totalonline24 > $last24 ){
    $last24 = $totalonline24;
    $period = time();
    mysqli_query($GLOBALS["___mysqli_ston"], ($no24 ? 'INSERT':'UPDATE'). " avps SET value_s = '0', value_i = $last24 , value_u = $period ". ($no24 ? ", arg='last24'":"WHERE arg='last24'")) or sqlerr();
}
    $colors= array('8E35EF','f9a200','009F00','0000FF','FE2E2E','B000B0','FF0000');
    while ($arr = mysqli_fetch_assoc($res)){
if ($activeusers24) $activeusers24 .= ",\n";
    $activeusers24 .= "<span style=\"white-space: nowrap;\">";
    $arr["username"] = "<font color='#". $colors[$arr['class']] ."'>" . htmlspecialchars($arr['username'])."</font>";
    $donator = $arr["donor"] === "yes";
    $warned = $arr["warned"] === "yes";
    $enabled = $arr["enabled"] === "no";
if ($CURUSER)
    $activeusers24 .= "<a href='{$FMED['baseurl']}/userdetails.php?id={$arr["id"]}'><b>{$arr["username"]}</b></a>";
    else
    $activeusers24 .= "<b>{$arr["username"]}</b>";
if ($donator)
    $activeusers24 .= "<img src='{$FMED['pic_base_url']}star.gif' alt='Donated' />";
if ($warned)
    $activeusers24 .= "<img src='{$FMED['pic_base_url']}warned.gif' alt='Warned' />";
if ($enabled)
    $activeusers24 .= "<img src='{$FMED['pic_base_url']}disabled.gif' alt='Disabled' />";
    $activeusers24 .= "</span>\n";
}
if (!$activeusers24)
    $activeusers24 = "{$lang['index_noactive']}";
    $HTMLOUT.= "<div class='headline'><b>&nbsp;-&nbsp;{$lang['index_active24']}&nbsp;</b></div>
    <div class='headbody' >
    <font color='#00FFFF'>{$lang['index_omca']}&nbsp;</font>
    <font color='#FF0000'>&nbsp;{$lang['index_ss']}</font>
    <font color='#B000B0'><b>&nbsp;{$lang['index_sa']}</font>
    <font color='#FE2E2E'>&nbsp;{$lang['index_sm']}</font>
    <font color='#0000FF'>&nbsp;{$lang['index_sup']}</font>
    <font color='#009F00'>&nbsp;{$lang['index_vip']}</font>
    <font color='#f9a200'>&nbsp;{$lang['index_spur']}</font>
    <font color='#8E35EF'>&nbsp;{$lang['index_urs']}</font>
    </br></br><hr><p align='left'>{$activeusers24}<hr><p align='left'>{$lang['index_most24']}&nbsp;{$last24}&nbsp;{$lang['index_member24']}{$_ss24}&nbsp;:&nbsp;{$last24record}<hr /></div><br />\n";
    $active3 ="";
    $file = "./cache/active.txt";
    $expire = 30; // 30 seconds
if (file_exists($file) && filemtime($file) > (time() - $expire)) {
    $active3 = unserialize(file_get_contents($file));
    } else {
    $dt = sqlesc(time() - 180);
    $active1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, username, class, warned, chatpost,  enabled, downloadpos, donor, added FROM users WHERE last_access >= $dt ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);
    while ($active2 = mysqli_fetch_assoc($active1)) {
    $active3[] = $active2;
}
    $OUTPUT = serialize($active3);
    $fp = fopen($file, "w");
    fputs($fp, $OUTPUT);
    fclose($fp);
}
    $activeusers = '';
if (is_array($active3))
    foreach ($active3 as $arr) {
if ($activeusers) $activeusers .= ",\n";
    $activeusers .= format_username($arr);
}
if (!$activeusers)
    $activeusers = "{$lang['index_omca']}";
    $HTMLOUT .= "<div class='headline'><b>&nbsp;-&nbsp;{$lang['index_activeusers']}&nbsp;</b></div><div class='headbody'>
    <div></div>
    <font color='#00FFFF'>{$lang['index_amca']}&nbsp;</font>
    <font color='#FF0000'>&nbsp;{$lang['index_ss']}</font>
    <font color='#B000B0'><b>&nbsp;{$lang['index_sa']}</font>
    <font color='#FF0000'>&nbsp;{$lang['index_sm']}</font>
    <font color='#0000FF'>&nbsp;{$lang['index_sup']}</font>
    <font color='#009F00'>&nbsp;{$lang['index_vip']}</font>
    <font color='#f9a200'>&nbsp;{$lang['index_spur']}</font>
    <font color='#8E35EF'>&nbsp;{$lang['index_urs']}</font></br></br></center><hr><p align='left'>{$activeusers}&nbsp;<hr /></div><br />\n";
    $HTMLOUT .= "<div class='headline'>&nbsp;-&nbsp;{$lang['index_newusers']}</div>
    <div class='headbody'><hr />
    <br /><font class='small'><font color=#FFFFFF>Welcome to our newest member, <b>$new_user</b>!</font><hr /></div></div><br />\n";
    $active3 ="";
    $file = "./cache/active.txt";
    $expire = 30;
if (file_exists($file) && filemtime($file) > (time() - $expire)) {
    $active3 = unserialize(file_get_contents($file));
    } else {
    $dt = sqlesc(time() - 180);
    $active1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, username, class, warned, chatpost,  enabled, downloadpos, donor, added FROM users WHERE last_access >= $dt ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);
    while ($active2 = mysqli_fetch_assoc($active1)) {
    $active3[] = $active2;
}
    $OUTPUT = serialize($active3);
    $fp = fopen($file, "w");
    fputs($fp, $OUTPUT);
    fclose($fp);
}
    $HTMLOUT .= "</div>\n";
if (isset($peers)){
    $HTMLOUT.= "<div class='headline'>&nbsp;-&nbsp;{$lang['stats_title']}</div>
    <div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'>
    <tr>
    <td align='center'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'>
    <tr>
    <td align='center' colspan='8' class='colhead'>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <font color=red>Registered&nbsp;&nbsp;Users&nbsp;&nbsp;Stats</font></b></center> </td>
    </tr>
    <tr>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_regusers']}</td>
    <td class='col1' align='right'>
    {$registered}/{$FMED['maxusers']}</td>
	<td class='colhead'>
    &nbsp;&nbsp;
    Warned Users</td>
    <td class='col1' align='right'>{$warnedu}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Unconfirmed Users</td>
    <td class='col1' align='right'>{$unverified}</td>
	<td class='colhead'>
    &nbsp;&nbsp;
    Disabled Users</td>
    <td class='col1' align='right'>{$disabled}</td>
    </tr>
    <tr>
    <td align='center' colspan='8' class='colhead'>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <font color=red>General&nbsp;&nbsp;Stats&nbsp;&nbsp;Info</font></b></center></td>
    </tr>
    <tr>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_torrents']}</td>
    <td class='col1' align='right'>{$torrents}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Undecided Users</td>
    <td class='col1' align='right'>{$nobody}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Male Users</td>
    <td class='col1' align='right'>{$male}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Female Users</td>
    <td class='col1' align='right'>{$female}</td>
    </tr>
    <tr>
    <td align='center' colspan='8' class='colhead'>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <font color=red>Users&nbsp;&nbsp;Torrent&nbsp;&nbsp;Stats&nbsp;&nbsp;Info</font></b></center></td>
    </tr>
    <tr>
    <td class='colhead'>
    &nbsp;&nbsp;
    Inactive Torrents</td>
    <td class='col1' align='right'>{$dead}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_sl_ratio']}</td>
    <td class='col1' align='right'>{$ratio}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Members Donors</td>
    <td class='col1' align='right'>{$donor}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    Unconnectable Peers</td>
    <td class='col1' align='right'>{$NAT}</td>
    </tr>
    <tr>
    <td class='colhead'>
    &nbsp;&nbsp;
    Soon to be filled</td>
    <td class='col1' align='right'>Soon</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_peers']}</td>
    <td class='col1' align='right'>{$peers}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_seed']}</td>
    <td class='col1' align='right'>{$seeders}</td>
    <td class='colhead'>
    &nbsp;&nbsp;
    {$lang['stats_leech']}</td>
    <td class='col1' align='right'>{$leechers}</td>
    </td>
    </tr>
    </table>
    </div>";
}
    $HTMLOUT .= "</table></div><br />";
    $HTMLOUT.= "<div class='headline'>&nbsp;-&nbsp;Disclaimer</div><div class='headbody'>";
    $HTMLOUT.= sprintf("<p><font class='small'>{$lang['foot_disclaimer']}</font></p>", $FMED['site_name']);
    $HTMLOUT.= "</div>";
    $HTMLOUT .= "";
    print stdhead('Home') . $HTMLOUT . stdfoot();
?>