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
require_once "include/html_functions.php";
require_once "include/user_functions.php";
dbconn();

    $lang = array_merge( load_language('global'), load_language('rules') );
    $HTMLOUT = '';
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_general_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_general_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_downloading_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_downloading_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_forum_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_forum_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_avatar_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_avatar_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_moderating_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_moderating_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_mod_rules_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_mod_rules_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
    $HTMLOUT .= "<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
    <div class='headline'>{$lang['rules_mod_options_header']}</div><div class='headbody'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='faq' class='text'>
    {$lang['rules_mod_options_body']}
    </td></tr></table>
    </td></tr></table>
    <br />
    <br />";
     print stdhead("{$lang['rules_rules']}") . $HTMLOUT . stdfoot();
?>