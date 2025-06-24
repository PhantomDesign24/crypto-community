<?php
/*
 * 파일명: otc_direct_process.php
 * 위치: /otc_direct_process.php
 * 기능: OTC 사이트 직거래 신청 처리
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    alert('잘못된 접근입니다.', './otc.php');
}

$trade_type = isset($_POST['trade_type']) ? trim($_POST['trade_type']) : '';

if(!$trade_type || !in_array($trade_type, array('buy', 'sell'))) {
    alert('거래 타입을 선택해주세요.');
}

// ===================================
// OTC 시세 정보 가져오기
// ===================================

$otc_price = sql_fetch("SELECT * FROM g5_otc_price ORDER BY op_id DESC LIMIT 1");
if (!$otc_price) {
    $otc_price = array(
        'op_buy_price' => 1450,
        'op_sell_price' => 1430
    );
}

// ===================================
// 직거래 신청 테이블 생성
// ===================================

$sql = "CREATE TABLE IF NOT EXISTS g5_otc_direct (
    od_id INT NOT NULL AUTO_INCREMENT,
    od_type VARCHAR(10) NOT NULL DEFAULT '' COMMENT '거래타입: buy/sell',
    od_name VARCHAR(50) NOT NULL DEFAULT '',
    od_hp VARCHAR(20) NOT NULL DEFAULT '',
    od_quantity DECIMAL(20,8) NOT NULL DEFAULT '0' COMMENT '수량',
    od_price_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '단가(원)',
    od_total_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '총금액(원)',
    od_wallet_address VARCHAR(255) NOT NULL DEFAULT '' COMMENT '지갑주소',
    od_bank_name VARCHAR(50) NOT NULL DEFAULT '' COMMENT '은행명',
    od_bank_account VARCHAR(100) NOT NULL DEFAULT '' COMMENT '계좌번호',
    od_verification_file VARCHAR(255) NOT NULL DEFAULT '' COMMENT '인증파일',
    od_status TINYINT NOT NULL DEFAULT '0' COMMENT '상태: 0=대기, 1=진행중, 2=완료, 9=취소',
    od_datetime DATETIME NOT NULL,
    od_ip VARCHAR(50) NOT NULL DEFAULT '',
    od_memo TEXT COMMENT '관리자 메모',
    mb_id VARCHAR(50) NOT NULL DEFAULT '',
    PRIMARY KEY (od_id),
    KEY idx_type (od_type),
    KEY idx_status (od_status),
    KEY idx_datetime (od_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// ===================================
// 데이터 검증 및 수집
// ===================================

if($trade_type == 'buy') {
    // 매수 신청
    $name = isset($_POST['buy_name']) ? trim($_POST['buy_name']) : '';
    $quantity = isset($_POST['buy_quantity']) ? (float)$_POST['buy_quantity'] : 0;
    $wallet = isset($_POST['buy_wallet']) ? trim($_POST['buy_wallet']) : '';
    $hp = ''; // 매수 시 연락처는 받지 않음
    
    if(!$name) alert('신청자 이름을 입력해주세요.');
    if($quantity <= 0) alert('매수 수량을 입력해주세요.');
    if(!$wallet) alert('USDT 지갑 주소를 입력해주세요.');
    
    $price = $otc_price['op_buy_price'];
    $total = $quantity * $price;
    
    // 최소 거래 금액 체크
    if($total < 10000000) {
        alert('최소 거래 금액은 1,000만원입니다.\\n현재 거래금액: '.number_format($total).'원');
    }
    
    // 파일 업로드 확인
    if(!isset($_FILES['buy_verification']) || $_FILES['buy_verification']['error'] !== UPLOAD_ERR_OK) {
        alert('신분증 사진과 자필 확인서를 업로드해주세요.');
    }
    
    $bank_name = '';
    $bank_account = '';
    
} else {
    // 매도 신청
    $name = isset($_POST['sell_name']) ? trim($_POST['sell_name']) : '';
    $quantity = isset($_POST['sell_quantity']) ? (float)$_POST['sell_quantity'] : 0;
    $bank_name = isset($_POST['sell_bank']) ? trim($_POST['sell_bank']) : '';
    $bank_account = isset($_POST['sell_account']) ? trim($_POST['sell_account']) : '';
    $hp = isset($_POST['sell_phone']) ? trim($_POST['sell_phone']) : '';
    $wallet = ''; // 매도 시 지갑주소는 받지 않음
    
    if(!$name) alert('신청자 이름을 입력해주세요.');
    if($quantity <= 0) alert('매도 수량을 입력해주세요.');
    if(!$bank_name) alert('입금받을 은행을 선택해주세요.');
    if(!$bank_account) alert('계좌번호를 입력해주세요.');
    if(!$hp) alert('연락처를 입력해주세요.');
    
    $price = $otc_price['op_sell_price'];
    $total = $quantity * $price;
    
    // 최소 거래 금액 체크
    if($total < 10000000) {
        alert('최소 거래 금액은 1,000만원입니다.\\n현재 거래금액: '.number_format($total).'원');
    }
}

// ===================================
// 파일 업로드 처리
// ===================================

$verification_file = '';

if($trade_type == 'buy' && isset($_FILES['buy_verification'])) {
    $file = $_FILES['buy_verification'];
    
    // 파일이 실제로 업로드되었는지 확인
    if($file['error'] === UPLOAD_ERR_OK && $file['tmp_name']) {
        // 파일 타입 확인 (MIME 타입)
        $file_info = @getimagesize($file['tmp_name']);
        if($file_info === false) {
            alert('이미지 파일이 아닙니다.');
        }
        
        $allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
        if(!in_array($file_info[2], $allowed_types)) {
            alert('JPG, PNG, GIF 이미지 파일만 업로드 가능합니다.');
        }
        
        if($file['size'] > 5 * 1048576) { // 5MB
            alert('파일 크기는 5MB 이하여야 합니다.');
        }
        
        // 디렉토리 생성
        $upload_dir = G5_DATA_PATH.'/otc_direct';
        if(!is_dir($upload_dir)) {
            @mkdir($upload_dir, G5_DIR_PERMISSION);
            @chmod($upload_dir, G5_DIR_PERMISSION);
        }
        
        // 파일 확장자 추출
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            alert('허용되지 않은 파일 확장자입니다.');
        }
        
        // 파일명 생성
        $filename = date('YmdHis').'_'.substr(md5(uniqid(time())),0,8).'.'.$ext;
        $dest_path = $upload_dir.'/'.$filename;
        
        if(move_uploaded_file($file['tmp_name'], $dest_path)) {
            @chmod($dest_path, G5_FILE_PERMISSION);
            $verification_file = $filename;
        } else {
            alert('파일 업로드에 실패했습니다.');
        }
    }
}

// ===================================
// DB 저장
// ===================================

$mb_id = $is_member ? $member['mb_id'] : '';

$sql = "INSERT INTO g5_otc_direct SET
        od_type = '".sql_real_escape_string($trade_type)."',
        od_name = '".sql_real_escape_string($name)."',
        od_hp = '".sql_real_escape_string($hp)."',
        od_quantity = '$quantity',
        od_price_krw = '$price',
        od_total_krw = '$total',
        od_wallet_address = '".sql_real_escape_string($wallet)."',
        od_bank_name = '".sql_real_escape_string($bank_name)."',
        od_bank_account = '".sql_real_escape_string($bank_account)."',
        od_verification_file = '$verification_file',
        od_datetime = '".G5_TIME_YMDHIS."',
        od_ip = '".$_SERVER['REMOTE_ADDR']."',
        mb_id = '$mb_id'";

sql_query($sql);
$od_id = sql_insert_id();

// ===================================
// 관리자 알림 메일 발송
// ===================================

if($config['cf_admin_email']) {
    $subject = "[OTC 직거래 신청] ".($trade_type == 'buy' ? '매수' : '매도')." 신청 - ".$name;
    
    $content = "OTC 직거래 신청이 접수되었습니다.\n\n";
    $content .= "거래 타입: ".($trade_type == 'buy' ? '매수' : '매도')."\n";
    $content .= "신청자: ".$name."\n";
    $content .= "수량: ".number_format($quantity, 8)." USDT\n";
    $content .= "단가: ".number_format($price)."원\n";
    $content .= "총 금액: ".number_format($total)."원\n";
    $content .= "신청일시: ".G5_TIME_YMDHIS."\n\n";
    
    if($trade_type == 'buy') {
        $content .= "지갑 주소: ".$wallet."\n";
    } else {
        $content .= "입금 계좌: ".$bank_name." ".$bank_account."\n";
        $content .= "연락처: ".$hp."\n";
    }
    
    $content .= "\n관리자 페이지에서 처리해주세요.";
    
    // 그누보드5 mailer 함수: mailer($name, $email, $to_email, $subject, $content, $type=0, $file="", $cc="", $bcc="")
    mailer($name, $config['cf_admin_email'], $config['cf_admin_email'], $subject, $content, 1);
}

// ===================================
// 신청 완료 페이지로 이동
// ===================================

alert('직거래 신청이 완료되었습니다.\\n\\n담당자가 확인 후 연락드리겠습니다.\\n신청번호: '.$od_id, './otc.php');
?>