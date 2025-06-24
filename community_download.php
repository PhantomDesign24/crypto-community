<?php
/*
 * 파일명: community_download.php
 * 위치: /
 * 기능: 커뮤니티 파일 다운로드
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 파라미터 확인
// ===================================

$cm_id = isset($_GET['cm_id']) ? (int)$_GET['cm_id'] : 0;
$bf_no = isset($_GET['bf_no']) ? (int)$_GET['bf_no'] : 0;

if(!$cm_id || !$bf_no) {
    alert('잘못된 접근입니다.');
}

// ===================================
// 파일 정보 조회
// ===================================

$sql = "SELECT * FROM g5_community_file WHERE cm_id = '$cm_id' AND bf_no = '$bf_no'";
$file = sql_fetch($sql);

if(!$file['bf_file']) {
    alert('파일이 존재하지 않습니다.');
}

$filepath = G5_DATA_PATH.'/community/'.$file['bf_file'];

if(!is_file($filepath) || !file_exists($filepath)) {
    alert('파일이 존재하지 않습니다.');
}

// ===================================
// 다운로드 권한 확인 (필요시 추가)
// ===================================

// 예: 회원만 다운로드 가능
// if(!$is_member) {
//     alert('회원만 다운로드 가능합니다.');
// }

// ===================================
// 다운로드 수 증가
// ===================================

sql_query("UPDATE g5_community_file SET bf_download = bf_download + 1 WHERE cm_id = '$cm_id' AND bf_no = '$bf_no'");

// ===================================
// 파일 다운로드
// ===================================

$original = urlencode($file['bf_source']);

// IE 대응
if(preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filepath));
    header("Content-Disposition: attachment; filename=$original");
    header("Content-Transfer-Encoding: binary");
} else if (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])){
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filepath));
    header("Content-Disposition: attachment; filename=\"".$file['bf_source']."\"");
    header("Content-Description: PHP Generated Data");
    header("Content-Transfer-Encoding: binary");
} else {
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filepath));
    header("Content-Disposition: attachment; filename=$original");
    header("Content-Description: PHP Generated Data");
    header("Content-Transfer-Encoding: binary");
}

header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
header("Pragma: public");

$fp = fopen($filepath, "rb");
fpassthru($fp);
fclose($fp);
?>