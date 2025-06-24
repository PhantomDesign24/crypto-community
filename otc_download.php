<?php
/*
 * 파일명: otc_download.php
 * 위치: /otc_download.php
 * 기능: OTC 첨부파일 다운로드
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 파라미터 체크
// ===================================

$ot_id = isset($_GET['ot_id']) ? (int)$_GET['ot_id'] : 0;
$bf_no = isset($_GET['bf_no']) ? (int)$_GET['bf_no'] : 0;

if(!$ot_id || !$bf_no) {
    alert('잘못된 접근입니다.');
}

// ===================================
// 게시글 정보 확인
// ===================================

$sql = "SELECT * FROM g5_otc WHERE ot_id = '$ot_id'";
$post = sql_fetch($sql);

if(!$post['ot_id']) {
    alert('존재하지 않는 게시글입니다.');
}

// ===================================
// 파일 정보 확인
// ===================================

$sql = "SELECT * FROM g5_otc_file WHERE ot_id = '$ot_id' AND bf_no = '$bf_no'";
$file = sql_fetch($sql);

if(!$file['bf_file']) {
    alert('파일이 존재하지 않습니다.');
}

$filepath = G5_DATA_PATH.'/otc/'.$file['bf_file'];

if(!is_file($filepath) || !file_exists($filepath)) {
    alert('파일이 존재하지 않습니다.');
}

// ===================================
// 다운로드 권한 체크
// ===================================

// 로그인한 회원만 다운로드 가능
if(!$is_member) {
    alert('파일 다운로드는 로그인 후 이용 가능합니다.', G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// ===================================
// 다운로드 카운트 증가
// ===================================

sql_query("UPDATE g5_otc_file SET bf_download = bf_download + 1 WHERE ot_id = '$ot_id' AND bf_no = '$bf_no'");

// ===================================
// 파일 다운로드 처리
// ===================================

$original = $file['bf_source'];
$original = urlencode($original);

// IE 대응
if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
    header("content-type: doesn/matter");
    header("content-length: ".filesize($filepath));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-transfer-encoding: binary");
} else if (preg_match("/Firefox/i", $_SERVER['HTTP_USER_AGENT'])){
    header("content-type: file/unknown");
    header("content-length: ".filesize($filepath));
    header("content-disposition: attachment; filename=\"".$original."\"");
    header("content-description: php generated data");
} else {
    header("content-type: file/unknown");
    header("content-length: ".filesize($filepath));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-description: php generated data");
}

header("pragma: no-cache");
header("expires: 0");
flush();

$fp = fopen($filepath, 'rb');

// 4096 바이트씩 읽어서 출력
$download_rate = 4096;

while(!feof($fp)) {
    print fread($fp, $download_rate);
    flush();
}

fclose($fp);
?>