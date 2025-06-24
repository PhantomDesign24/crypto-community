<?php
/*
 * 파일명: otc_process.php
 * 위치: /otc_process.php
 * 기능: OTC 거래 상태 변경 처리 (AJAX)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// JSON 응답 헤더
header('Content-Type: application/json; charset=utf-8');

// ===================================
// 초기 설정
// ===================================

$response = array('success' => false, 'message' => '');

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['message'] = '잘못된 접근입니다.';
    echo json_encode($response);
    exit;
}

$ot_id = isset($_POST['ot_id']) ? (int)$_POST['ot_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if(!$ot_id) {
    $response['message'] = '게시글 번호가 올바르지 않습니다.';
    echo json_encode($response);
    exit;
}

// ===================================
// 게시글 정보 확인
// ===================================

$sql = "SELECT * FROM g5_otc WHERE ot_id = '$ot_id'";
$post = sql_fetch($sql);

if(!$post['ot_id']) {
    $response['message'] = '존재하지 않는 게시글입니다.';
    echo json_encode($response);
    exit;
}

// ===================================
// 액션별 처리
// ===================================

switch($action) {
    case 'complete':
        // 거래 완료 처리
        
        // 권한 체크 (작성자 본인 또는 관리자)
        if(!$is_admin && !($is_member && $post['mb_id'] == $member['mb_id'])) {
            $response['message'] = '거래 완료 처리 권한이 없습니다.';
            echo json_encode($response);
            exit;
        }
        
        // 이미 완료된 거래인지 체크
        if($post['ot_status'] == 1) {
            $response['message'] = '이미 완료된 거래입니다.';
            echo json_encode($response);
            exit;
        }
        
        // 거래 완료 처리
        sql_query("UPDATE g5_otc SET ot_status = 1 WHERE ot_id = '$ot_id'");
        
        $response['success'] = true;
        $response['message'] = '거래가 완료 처리되었습니다.';
        break;
        
    case 'cancel':
        // 거래 취소 처리 (추후 구현 가능)
        $response['message'] = '거래 취소 기능은 준비중입니다.';
        break;
        
    default:
        $response['message'] = '올바른 액션이 아닙니다.';
        break;
}

echo json_encode($response);
?>