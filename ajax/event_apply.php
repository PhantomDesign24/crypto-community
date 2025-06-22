<?php
/*
 * 파일명: event_apply.php
 * 위치: /ajax/event_apply.php
 * 기능: 이벤트 신청 처리
 * 작성일: 2025-01-11
 */

include_once('../common.php');

header('Content-Type: application/json');

// 로그인 체크
if(!$member['mb_id']) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit;
}

$ev_id = isset($_POST['ev_id']) ? (int)$_POST['ev_id'] : 0;
$wallet_address = isset($_POST['wallet_address']) ? trim($_POST['wallet_address']) : '';

// 유효성 검사
if(!$ev_id) {
    echo json_encode(['success' => false, 'message' => '이벤트를 선택해주세요.']);
    exit;
}

if(!$wallet_address) {
    echo json_encode(['success' => false, 'message' => '지갑 주소를 입력해주세요.']);
    exit;
}

// 이벤트 정보 확인
$event = sql_fetch("SELECT * FROM g5_event WHERE ev_id = '{$ev_id}'");
if(!$event) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 이벤트입니다.']);
    exit;
}

// 진행중 확인
$now = time();
$start_time = strtotime($event['ev_start_date']);
$end_time = strtotime($event['ev_end_date']);
if($now < $start_time || $now > $end_time || $event['ev_status'] != 'ongoing') {
    echo json_encode(['success' => false, 'message' => '현재 참여할 수 없는 이벤트입니다.']);
    exit;
}

// 중복 신청 확인
$check = sql_fetch("SELECT * FROM g5_event_apply WHERE ev_id = '{$ev_id}' AND mb_id = '{$member['mb_id']}'");
if($check) {
    echo json_encode(['success' => false, 'message' => '이미 신청한 이벤트입니다.']);
    exit;
}

// 파일 업로드 처리
$upload_dir = G5_DATA_PATH.'/event_apply';
if(!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755);
    @chmod($upload_dir, 0755);
}

// 이벤트 신청 등록
$sql = "INSERT INTO g5_event_apply SET
        ev_id = '{$ev_id}',
        mb_id = '{$member['mb_id']}',
        ea_wallet_address = '{$wallet_address}',
        ea_status = 'applied',
        ea_datetime = NOW()";
sql_query($sql);
$ea_id = sql_insert_id();

// 파일 업로드
$file_count = 0;
if(isset($_FILES['bf_file'])) {
    for($i = 0; $i < count($_FILES['bf_file']['name']); $i++) {
        if($_FILES['bf_file']['error'][$i] == 0 && $file_count < 5) {
            $tmp_name = $_FILES['bf_file']['tmp_name'][$i];
            $filename = $_FILES['bf_file']['name'][$i];
            $filesize = $_FILES['bf_file']['size'][$i];
            
            // 파일명 생성
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $save_filename = $ea_id.'_'.($file_count+1).'_'.time().'.'.$ext;
            
            // 이미지 파일 체크
            if(!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                continue;
            }
            
            // 파일 이동
            if(move_uploaded_file($tmp_name, $upload_dir.'/'.$save_filename)) {
                $sql = "INSERT INTO g5_event_apply_file SET
                        ea_id = '{$ea_id}',
                        ef_no = '{$file_count}',
                        ef_source = '{$filename}',
                        ef_file = '{$save_filename}',
                        ef_filesize = '{$filesize}',
                        ef_datetime = NOW()";
                sql_query($sql);
                $file_count++;
            }
        }
    }
}

// 이벤트 신청수 증가
sql_query("UPDATE g5_event SET ev_apply_count = ev_apply_count + 1 WHERE ev_id = '{$ev_id}'");

// 이벤트 접수 게시판에 자동 글 작성
$board_table = 'event_apply'; // 이벤트 접수 게시판 테이블명
$bo_table_check = sql_fetch("SELECT * FROM {$g5['board_table']} WHERE bo_table = '{$board_table}'");

if($bo_table_check) {
    $wr_subject = '[신청완료] ' . $event['ev_subject'] . ' - ' . $member['mb_nick'];
    $wr_content = "이벤트명: {$event['ev_subject']}\n";
    $wr_content .= "코인: {$event['ev_coin_symbol']} ({$event['ev_coin_amount']})\n";
    $wr_content .= "지갑주소: {$wallet_address}\n";
    $wr_content .= "신청일시: " . date('Y-m-d H:i:s');
    
    // 게시글 작성
    $sql = "INSERT INTO {$g5['write_prefix']}{$board_table} SET
            wr_num = '-1',
            wr_reply = '',
            wr_parent = 0,
            wr_is_comment = 0,
            wr_comment = 0,
            wr_comment_reply = '',
            ca_name = '',
            wr_option = 'secret',
            wr_subject = '{$wr_subject}',
            wr_content = '{$wr_content}',
            wr_link1 = '',
            wr_link2 = '',
            wr_link1_hit = 0,
            wr_link2_hit = 0,
            wr_hit = 0,
            wr_good = 0,
            wr_nogood = 0,
            mb_id = '{$member['mb_id']}',
            wr_password = '',
            wr_name = '{$member['mb_nick']}',
            wr_email = '{$member['mb_email']}',
            wr_homepage = '',
            wr_datetime = NOW(),
            wr_last = NOW(),
            wr_ip = '{$_SERVER['REMOTE_ADDR']}',
            wr_1 = '{$ev_id}',
            wr_2 = '{$ea_id}',
            wr_3 = 'applied'";
    sql_query($sql);
    $wr_id = sql_insert_id();
    
    // 게시글 번호 업데이트
    sql_query("UPDATE {$g5['write_prefix']}{$board_table} SET wr_num = '-{$wr_id}', wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}'");
    
    // 신청 테이블에 게시글 ID 업데이트
    sql_query("UPDATE g5_event_apply SET wr_id = '{$wr_id}' WHERE ea_id = '{$ea_id}'");
    
    // 게시판 글 수 증가
    sql_query("UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$board_table}'");
}

echo json_encode(['success' => true, 'message' => '이벤트 신청이 완료되었습니다.']);
?>