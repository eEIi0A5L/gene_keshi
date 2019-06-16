<?php

// gene_keshi.php
// ジェネ消し (Generic Filters 消し)

header("Content-Type: text/plain");

// https://ss1.xrea.com/z4s85ttt.s1007.xrea.com/adblock/gene_keshi.php?url=

/*
https://ss1.xrea.com/z4s85ttt.s1007.xrea.com/adblock/gene_keshi.php?url=https://raw.githubusercontent.com/eEIi0A5L/adblock_filter/master/mochi_filter.txt
https://ss1.xrea.com/z4s85ttt.s1007.xrea.com/adblock/gene_keshi.php?url=http://tofukko.r.ribbon.to/Adblock_Plus_list.txt
https://ss1.xrea.com/z4s85ttt.s1007.xrea.com/adblock/gene_keshi.php?url=https://easylist.to/easylist/easylist.txt
https://ss1.xrea.com/z4s85ttt.s1007.xrea.com/adblock/gene_keshi.php?url=https://raw.githubusercontent.com/k2jp/abp-japanese-filters/master/abpjf.txt

*/

// Genericフィルタならtrueを返す。

function is_gene($line)
{
    $ret = preg_match('/^[ \t]*#@?#/', $line, $matches);
    if ($ret) return true;
    $ret = preg_match('/.#@?#/', $line, $matches);
    if ($ret) return false;
    $ret = preg_match('/^[ \t]*\|\|/', $line, $matches);
    if ($ret) return false;
    $ret = preg_match('/^[ \t]*@@\|\|/', $line, $matches);
    if ($ret) return false;

    // ドメイン指定有り
    $ret = preg_match('/\$domain=[^~]/', $line, $matches);
    if ($ret) return false;
    $ret = preg_match('/\$.+,domain=[^~]/', $line, $matches);
    if ($ret) return false;

    // コメントとヘッダ
    $ret = preg_match('/^[ \t]*!/', $line, $matches);
    if ($ret) return false;
    $ret = preg_match('/^\[/', $line, $matches);
    if ($ret) return false;

    // 空行
    $ret = preg_match('/^$/', $line, $matches);
    if ($ret) return false;
    return true;
}

function sub($url)
{
    try {
        $contents = file_get_contents($url);
    }

    catch(Exception $e) {
        header("HTTP/1.1 408 Request Timeout");
        exit;
    }

    // print "contents=[$contents]";

    if ($contents == null) {
        print "NULL<br/>\n";
        header("HTTP/1.1 408 Request Timeout");
        exit;
    }

    $list = explode("\n", $contents);
    $title = 0; // タイトル処理済なら1になる
    foreach($list as $line) {
        $line = preg_replace("/\r|\n/", "", $line);
        if (is_gene($line)) {

            // print "GENE: $line\n";
            // continue;

            $line = "!GENEKESHI: " . $line;
        }

        // タイトル変更
        if ( $title == 0 ) {
            if (preg_match('/^! Title:(.*)/', $line, $matches)) {
                $line = $line . " （ジェネ消し化）";
                $title = 1;
            }
        }

        print "$line\n";
    }
}

function main()
{
    $url = "";
    if (isset($_GET['url'])) {
        $url = $_GET['url'];

        // echo $url;

    }

    sub($url);
}

main();
?>