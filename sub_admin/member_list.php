<?php
/*
 * 파일명: member_list.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 하위 회원 목록
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 접근 권한 확인
// ===================================

/* 로그인 체크 */
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

/* 하부조직 권한 체크 */
if ($member['mb_grade'] < 2) {
    alert('접근 권한이 없습니다.', G5_URL);
}

// ===================================
// 페이지 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '하위 회원 관리';

/* 페이지네이션 설정 */
$page = (int)$_GET['page'];
if ($page < 1) $page = 1;
$rows = 20;
$from_record = ($page - 1) * $rows;

/* 검색 조건 */
$sfl = strip_tags($_GET['sfl']);
$stx = strip_tags($_GET['stx']);

$sql_search = "";
if ($stx) {
    $sql_search .= " AND ( ";
    switch ($sfl) {
        case 'mb_id':
            $sql_search .= " mb_id LIKE '%{$stx}%' ";
            break;
        case 'mb_name':
            $sql_search .= " mb_name LIKE '%{$stx}%' ";
            break;
        case 'mb_email':
            $sql_search .= " mb_email LIKE '%{$stx}%' ";
            break;
        case 'mb_hp':
            $sql_search .= " mb_hp LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " (mb_id LIKE '%{$stx}%' OR mb_name LIKE '%{$stx}%' OR mb_email LIKE '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

// ===================================
// 데이터 조회
// ===================================

/* 전체 하위 회원 수 조회 */
$sql = "SELECT COUNT(*) as cnt 
        FROM {$g5['member_table']} 
        WHERE mb_recommend = '{$member['mb_id']}' 
        {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

/* 회원 목록 조회 */
$sql = "SELECT mb_no, mb_id, mb_name, mb_nick, mb_email, mb_hp, mb_point, mb_datetime, mb_today_login, mb_login_ip,
               (SELECT COUNT(*) FROM g5_event_apply WHERE mb_id = m.mb_id) as event_count,
               (SELECT COUNT(*) FROM g5_event_apply WHERE mb_id = m.mb_id AND ea_status = 1) as event_complete_count
        FROM {$g5['member_table']} m
        WHERE mb_recommend = '{$member['mb_id']}' 
        {$sql_search}
        ORDER BY mb_datetime DESC 
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

/* 페이지네이션 */
$total_page = ceil($total_count / $rows);
$qstr = "sfl={$sfl}&stx={$stx}";
$paging = get_paging(10, $page, $total_page, "./member_list.php?{$qstr}&page=");

include_once(G5_PATH.'/head.sub.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $g5['title']; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
    /* ===================================
     * 회원 관리 전용 스타일
     * =================================== */
    
    /* 전역 리셋 */
    .cmk-member-admin * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-member-admin {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }
    
    /* 컨테이너 */
    .cmk-ma-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* ===================================
     * 헤더 영역
     * =================================== */
    
    .cmk-ma-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .cmk-ma-header-content h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .cmk-ma-header-content h1 i {
        color: #3b82f6;
    }
    
    .cmk-ma-header-content p {
        color: #6b7280;
        font-size: 15px;
    }
    
    /* 통계 박스 */
    .cmk-ma-stat-box {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 20px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .cmk-ma-stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 4px;
    }
    
    .cmk-ma-stat-value {
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
    }
    
    /* ===================================
     * 검색 영역
     * =================================== */
    
    .cmk-ma-search {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .cmk-ma-search-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .cmk-ma-select,
    .cmk-ma-input {
        padding: 10px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
        background: #f9fafb;
    }
    
    .cmk-ma-select:focus,
    .cmk-ma-input:focus {
        outline: none;
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .cmk-ma-select {
        min-width: 140px;
    }
    
    .cmk-ma-input {
        flex: 1;
        min-width: 200px;
    }
    
    .cmk-ma-btn {
        padding: 10px 24px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .cmk-ma-btn-primary {
        background: #3b82f6;
        color: white;
    }
    
    .cmk-ma-btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* ===================================
     * 테이블 영역
     * =================================== */
    
    .cmk-ma-table-wrap {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .cmk-ma-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .cmk-ma-table th {
        background: #f9fafb;
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
    }
    
    .cmk-ma-table td {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }
    
    .cmk-ma-table tr:last-child td {
        border-bottom: none;
    }
    
    .cmk-ma-table tr:hover td {
        background: #fafafa;
    }
    
    /* 번호 */
    .cmk-ma-num {
        font-weight: 600;
        color: #9ca3af;
    }
    
    /* 회원 정보 */
    .cmk-ma-member-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .cmk-ma-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .cmk-ma-member-details h4 {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }
    
    .cmk-ma-member-details span {
        font-size: 13px;
        color: #6b7280;
    }
    
    /* 연락처 */
    .cmk-ma-contact {
        line-height: 1.5;
    }
    
    .cmk-ma-contact-email {
        color: #374151;
        font-weight: 500;
    }
    
    .cmk-ma-contact-phone {
        font-size: 13px;
        color: #6b7280;
    }
    
    /* 포인트 */
    .cmk-ma-point {
        font-weight: 600;
        color: #059669;
    }
    
    /* 이벤트 통계 */
    .cmk-ma-event-stats {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .cmk-ma-event-stat {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .cmk-ma-event-stat.total {
        background: #e0e7ff;
        color: #3730a3;
    }
    
    .cmk-ma-event-stat.complete {
        background: #d1fae5;
        color: #065f46;
    }
    
    /* 날짜 */
    .cmk-ma-date {
        color: #6b7280;
        font-size: 13px;
    }
    
    /* 최근 접속 */
    .cmk-ma-login-status {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .cmk-ma-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .cmk-ma-status-dot.online {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
    }
    
    .cmk-ma-status-dot.offline {
        background: #bdbdbd;
    }
    
    /* 액션 버튼 */
    .cmk-ma-actions {
        display: flex;
        gap: 6px;
    }
    
    .cmk-ma-btn-sm {
        padding: 6px 12px;
        font-size: 13px;
        border: none;
        border-radius: 6px;
        background: #f3f4f6;
        color: #374151;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .cmk-ma-btn-sm:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }
    
    .cmk-ma-btn-sm.primary {
        background: #3b82f6;
        color: white;
    }
    
    .cmk-ma-btn-sm.primary:hover {
        background: #2563eb;
    }
    
    /* ===================================
     * 페이지네이션
     * =================================== */
    
    .cmk-ma-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        margin-top: 32px;
    }
    
    .cmk-ma-pagination a,
    .cmk-ma-pagination strong {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .cmk-ma-pagination a {
        color: #6b7280;
        background: white;
        border: 1px solid #e5e7eb;
    }
    
    .cmk-ma-pagination a:hover {
        color: #3b82f6;
        border-color: #3b82f6;
        background: #f3f4f6;
    }
    
    .cmk-ma-pagination strong {
        color: white;
        background: #3b82f6;
        border: 1px solid #3b82f6;
    }
    
    /* ===================================
     * 빈 데이터
     * =================================== */
    
    .cmk-ma-empty {
        text-align: center;
        padding: 80px 20px;
        color: #9ca3af;
    }
    
    .cmk-ma-empty i {
        font-size: 64px;
        margin-bottom: 16px;
        display: block;
        color: #e5e7eb;
    }
    
    .cmk-ma-empty p {
        font-size: 18px;
        margin-bottom: 8px;
    }
    
    .cmk-ma-empty span {
        font-size: 14px;
        color: #d1d5db;
    }
    
    /* ===================================
     * 반응형
     * =================================== */
    
    @media (max-width: 1024px) {
        .cmk-ma-table-wrap {
            overflow-x: auto;
        }
        
        .cmk-ma-table {
            min-width: 900px;
        }
    }
    
    @media (max-width: 768px) {
        .cmk-member-admin {
            padding: 10px;
        }
        
        .cmk-ma-header {
            flex-direction: column;
            padding: 24px 20px;
        }
        
        .cmk-ma-search {
            padding: 20px;
        }
        
        .cmk-ma-search-form {
            flex-direction: column;
        }
        
        .cmk-ma-select,
        .cmk-ma-input,
        .cmk-ma-btn {
            width: 100%;
        }
        
        .cmk-ma-table th,
        .cmk-ma-table td {
            padding: 12px;
            font-size: 13px;
        }
        
        .cmk-ma-event-stats {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>
</head>
<body>

<!-- ===================================
     회원 관리 컨텐츠
     =================================== -->
<div class="cmk-member-admin">
    <div class="cmk-ma-container">
        <!-- 헤더 -->
        <div class="cmk-ma-header">
            <div class="cmk-ma-header-content">
                <h1><i class="bi bi-people"></i> 하위 회원 관리</h1>
                <p>나의 추천으로 가입한 회원들을 관리할 수 있습니다.</p>
            </div>
            <div class="cmk-ma-stat-box">
                <div class="cmk-ma-stat-label">전체 하위 회원</div>
                <div class="cmk-ma-stat-value"><?php echo number_format($total_count); ?>명</div>
            </div>
        </div>
        
        <!-- 검색 영역 -->
        <div class="cmk-ma-search">
            <form method="get" action="" class="cmk-ma-search-form">
                <select name="sfl" class="cmk-ma-select">
                    <option value="">전체 검색</option>
                    <option value="mb_id" <?php echo $sfl == 'mb_id' ? 'selected' : ''; ?>>회원ID</option>
                    <option value="mb_name" <?php echo $sfl == 'mb_name' ? 'selected' : ''; ?>>이름</option>
                    <option value="mb_email" <?php echo $sfl == 'mb_email' ? 'selected' : ''; ?>>이메일</option>
                    <option value="mb_hp" <?php echo $sfl == 'mb_hp' ? 'selected' : ''; ?>>휴대폰</option>
                </select>
                
                <input type="text" name="stx" value="<?php echo $stx; ?>" placeholder="검색어를 입력하세요" class="cmk-ma-input">
                
                <button type="submit" class="cmk-ma-btn cmk-ma-btn-primary">
                    <i class="bi bi-search"></i> 검색
                </button>
            </form>
        </div>
        
        <!-- 테이블 영역 -->
        <div class="cmk-ma-table-wrap">
            <?php if ($total_count > 0) { ?>
            <table class="cmk-ma-table">
                <thead>
                    <tr>
                        <th width="60">번호</th>
                        <th>회원정보</th>
                        <th>연락처</th>
                        <th width="100">포인트</th>
                        <th width="140">이벤트 참여</th>
                        <th width="100">가입일</th>
                        <th width="120">최근접속</th>
                        <th width="120">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $num = $total_count - (($page - 1) * $rows);
                    while ($row = sql_fetch_array($result)) { 
                        $mb_nick = get_text($row['mb_nick']);
                        $mb_name = get_text($row['mb_name']);
                        
                        // 최근 접속 확인 (1시간 이내)
                        $is_online = (strtotime($row['mb_today_login']) > strtotime('-1 hour'));
                    ?>
                    <tr>
                        <td class="cmk-ma-num"><?php echo $num--; ?></td>
                        <td>
                            <div class="cmk-ma-member-info">
                                <div class="cmk-ma-avatar">
                                    <?php echo mb_substr($mb_name, 0, 1); ?>
                                </div>
                                <div class="cmk-ma-member-details">
                                    <h4><?php echo $mb_name; ?> <?php if($mb_nick && $mb_nick != $mb_name) echo "({$mb_nick})"; ?></h4>
                                    <span><?php echo $row['mb_id']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cmk-ma-contact">
                                <div class="cmk-ma-contact-email"><?php echo $row['mb_email']; ?></div>
                                <div class="cmk-ma-contact-phone"><?php echo $row['mb_hp']; ?></div>
                            </div>
                        </td>
                        <td><span class="cmk-ma-point"><?php echo number_format($row['mb_point']); ?>P</span></td>
                        <td>
                            <div class="cmk-ma-event-stats">
                                <span class="cmk-ma-event-stat total">신청 <?php echo $row['event_count']; ?>건</span>
                                <span class="cmk-ma-event-stat complete">완료 <?php echo $row['event_complete_count']; ?>건</span>
                            </div>
                        </td>
                        <td class="cmk-ma-date"><?php echo date('Y-m-d', strtotime($row['mb_datetime'])); ?></td>
                        <td>
                            <div class="cmk-ma-login-status">
                                <span class="cmk-ma-status-dot <?php echo $is_online ? 'online' : 'offline'; ?>"></span>
                                <span class="cmk-ma-date"><?php echo date('m-d H:i', strtotime($row['mb_today_login'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="cmk-ma-actions">
                                <a href="./member_edit.php?mb_id=<?php echo $row['mb_id']; ?>" class="cmk-ma-btn-sm primary">
                                    <i class="bi bi-pencil"></i> 수정
                                </a>
                                <a href="<?php echo G5_BBS_URL; ?>/member_view.php?mb_id=<?php echo $row['mb_id']; ?>" class="cmk-ma-btn-sm" target="_blank">
                                    <i class="bi bi-eye"></i> 보기
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="cmk-ma-empty">
                <i class="bi bi-people-fill"></i>
                <p>등록된 하위 회원이 없습니다</p>
                <span>추천 코드를 통해 가입한 회원이 여기에 표시됩니다.</span>
            </div>
            <?php } ?>
        </div>
        
        <!-- 페이지네이션 -->
        <?php if ($total_count > 0) { ?>
        <div class="cmk-ma-pagination">
            <?php echo $paging; ?>
        </div>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>