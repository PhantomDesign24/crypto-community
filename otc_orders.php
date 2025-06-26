<?php
/*
 * 파일명: ajax/otc_orders.php
 * 위치: /ajax/otc_orders.php
 * 기능: 내 테더 구매 신청내역 AJAX 페이징
 * 작성일: 2025-01-26
 */

include_once('./_common.php');

// 로그인 체크
if (!$member['mb_id']) {
    die(json_encode(['error' => '로그인이 필요합니다.']));
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 1; // 한 페이지에 3개씩
$from_record = ($page - 1) * $rows;

// 전체 개수 조회
$sql = "SELECT COUNT(*) as cnt FROM g5_tether_purchase WHERE mb_id = '{$member['mb_id']}'";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);

// 내 신청 내역 조회
$sql = "SELECT * FROM g5_tether_purchase 
        WHERE mb_id = '{$member['mb_id']}' 
        ORDER BY tp_id DESC 
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

$orders = array();
while($order = sql_fetch_array($result)) {
    $status_class = '';
    $status_text = '';
    switch($order['tp_status']) {
        case 0:
            $status_class = 'pending';
            $status_text = '신청완료';
            break;
        case 1:
            $status_class = 'processing';
            $status_text = '진행중';
            break;
        case 2:
            $status_class = 'completed';
            $status_text = '완료';
            break;
        case 9:
            $status_class = 'cancelled';
            $status_text = '취소';
            break;
    }
    
    // 지갑주소 마스킹
    $masked_wallet = strlen($order['tp_wallet_address']) > 20 
        ? substr($order['tp_wallet_address'], 0, 10) . '...' . substr($order['tp_wallet_address'], -6)
        : $order['tp_wallet_address'];
    
    $orders[] = array(
        'tp_id' => $order['tp_id'],
        'status_class' => $status_class,
        'status_text' => $status_text,
        'quantity' => number_format($order['tp_quantity'], 2),
        'datetime' => date('m/d H:i', strtotime($order['tp_datetime'])),
        'transfer_company' => $order['tp_transfer_company'],
        'wallet_address' => $order['tp_wallet_address'],
        'masked_wallet' => $masked_wallet,
        'total_krw' => number_format($order['tp_total_krw']),
        'price_krw' => number_format($order['tp_price_krw']),
        'memo' => $order['tp_memo']
    );
}

// 페이징 HTML 생성
$paging_html = '';
if($total_page > 1) {
    $paging_html .= '<div class="order-pagination">';
    
    // 이전 페이지
    if($page > 1) {
        $paging_html .= '<button class="page-btn" onclick="loadOrders('.($page-1).')"><i class="bi bi-chevron-left"></i></button>';
    }
    
    // 페이지 번호
    $start_page = max(1, $page - 2);
    $end_page = min($total_page, $page + 2);
    
    for($i = $start_page; $i <= $end_page; $i++) {
        $active = ($i == $page) ? 'active' : '';
        $paging_html .= '<button class="page-btn '.$active.'" onclick="loadOrders('.$i.')">'.$i.'</button>';
    }
    
    // 다음 페이지
    if($page < $total_page) {
        $paging_html .= '<button class="page-btn" onclick="loadOrders('.($page+1).')"><i class="bi bi-chevron-right"></i></button>';
    }
    
    $paging_html .= '</div>';
}

// 결과 반환
echo json_encode(array(
    'orders' => $orders,
    'paging' => $paging_html,
    'total_count' => $total_count,
    'current_page' => $page,
    'total_page' => $total_page
));
?>