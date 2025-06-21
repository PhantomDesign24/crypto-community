<?php
/*
 * 파일명: index.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 대시보드
 * 작성일: 2025-01-23
 */

include_once('../common.php');

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
// 추천 코드 생성 (없는 경우)
// ===================================

    /* 추천 코드가 없으면 생성 */
if (!$member['mb_referral_code']) {
    $referral_code = generate_referral_code();
    sql_query("UPDATE {$g5['member_table']} SET mb_referral_code = '{$referral_code}' WHERE mb_id = '{$member['mb_id']}'");
    $member['mb_referral_code'] = $referral_code;
}

// ===================================
// 추천 코드 변경 처리
// ===================================

/* 추천 코드 변경 요청 처리 */
if ($_POST['act'] == 'change_referral_code') {
    $new_code = strtoupper(trim($_POST['new_referral_code']));
    
    // 유효성 검사
    if (strlen($new_code) !== 8) {
        alert('추천 코드는 8자리여야 합니다.');
    }
    
    if (!preg_match("/^[A-Z0-9]+$/", $new_code)) {
        alert('추천 코드는 영문 대문자와 숫자만 가능합니다.');
    }
    
    // 중복 확인
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE mb_referral_code = '{$new_code}' AND mb_id != '{$member['mb_id']}'";
    $row = sql_fetch($sql);
    
    if ($row['cnt'] > 0) {
        alert('이미 사용 중인 추천 코드입니다.');
    }
    
    // 추천 코드 업데이트
    sql_query("UPDATE {$g5['member_table']} SET mb_referral_code = '{$new_code}' WHERE mb_id = '{$member['mb_id']}'");
    
    alert('추천 코드가 변경되었습니다.', './index.php');
}

// 추천 코드 생성 함수
function generate_referral_code() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code_length = 8;
    
    do {
        $code = '';
        for ($i = 0; $i < $code_length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // 중복 확인
        $sql = "SELECT COUNT(*) as cnt FROM {$GLOBALS['g5']['member_table']} WHERE mb_referral_code = '{$code}'";
        $row = sql_fetch($sql);
    } while ($row['cnt'] > 0);
    
    return $code;
}

// ===================================
// 페이지 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '하부조직 관리 대시보드';

// ===================================
// 통계 데이터 조회
// ===================================

/* 하위 회원 수 조회 */
$sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_recommend = '{$member['mb_id']}'";
$row = sql_fetch($sql);
$total_member = $row['cnt'];

/* 오늘 가입한 하위 회원 수 */
$sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
        WHERE mb_recommend = '{$member['mb_id']}' 
        AND DATE(mb_datetime) = CURDATE()";
$row = sql_fetch($sql);
$today_member = $row['cnt'];

/* 이벤트 신청 현황 - 대기중 */
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        AND a.ea_status = 0";
$row = sql_fetch($sql);
$event_wait = $row['cnt'];

/* 이벤트 신청 현황 - 완료 */
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        AND a.ea_status = 1";
$row = sql_fetch($sql);
$event_complete = $row['cnt'];

// ===================================
// 최근 가입 회원 목록
// ===================================

/* 최근 가입한 하위 회원 5명 */
$sql = "SELECT mb_id, mb_name, mb_email, mb_datetime 
        FROM {$g5['member_table']} 
        WHERE mb_recommend = '{$member['mb_id']}' 
        ORDER BY mb_datetime DESC 
        LIMIT 5";
$recent_members = sql_query($sql);

// ===================================
// 최근 이벤트 신청 목록
// ===================================

/* 최근 이벤트 신청 5건 */
$sql = "SELECT a.*, m.mb_id, m.mb_name, b.bo_subject, w.wr_subject 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        LEFT JOIN {$g5['write_prefix']}event w ON a.wr_id = w.wr_id 
        LEFT JOIN {$g5['board_table']} b ON w.bo_table = b.bo_table 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        ORDER BY a.ea_datetime DESC 
        LIMIT 5";
$recent_events = sql_query($sql);

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
     * 전역 스타일
     * =================================== */
    
    /* 리셋 및 기본 설정 */
    .cmk-sub-admin * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-sub-admin {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    
    /* 컨테이너 */
    .cmk-sa-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* ===================================
     * 헤더 영역
     * =================================== */
    
    /* 헤더 */
    .cmk-sa-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin: -20px -20px 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .cmk-sa-header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .cmk-sa-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .cmk-sa-header p {
        font-size: 16px;
        opacity: 0.9;
    }
    
    /* 추천 코드 박스 */
    .cmk-sa-referral-box {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    
    .cmk-sa-referral-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    
    .cmk-sa-referral-code {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }
    
    .cmk-sa-copy-btn,
    .cmk-sa-change-btn,
    .cmk-sa-submit-btn,
    .cmk-sa-cancel-btn {
        background: rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        margin: 0 4px;
    }
    
    .cmk-sa-copy-btn:hover,
    .cmk-sa-change-btn:hover,
    .cmk-sa-submit-btn:hover,
    .cmk-sa-cancel-btn:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: translateY(-1px);
    }
    
    .cmk-sa-code-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.9);
        color: #333;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
        letter-spacing: 2px;
    }
    
    /* ===================================
     * 통계 카드
     * =================================== */
    
    /* 통계 카드 그리드 */
    .cmk-sa-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    
    /* 통계 카드 */
    .cmk-sa-stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    
    .cmk-sa-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    .cmk-sa-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
    }
    
    .cmk-sa-stat-card.cmk-members {
        --card-color: #4F46E5;
        --card-color-light: #818CF8;
    }
    
    .cmk-sa-stat-card.cmk-today {
        --card-color: #059669;
        --card-color-light: #34D399;
    }
    
    .cmk-sa-stat-card.cmk-wait {
        --card-color: #F59E0B;
        --card-color-light: #FCD34D;
    }
    
    .cmk-sa-stat-card.cmk-complete {
        --card-color: #8B5CF6;
        --card-color-light: #C084FC;
    }
    
    /* 통계 카드 아이콘 */
    .cmk-sa-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 28px;
    }
    
    .cmk-sa-stat-card.cmk-members .cmk-sa-stat-icon {
        background: #EEF2FF;
        color: #4F46E5;
    }
    
    .cmk-sa-stat-card.cmk-today .cmk-sa-stat-icon {
        background: #D1FAE5;
        color: #059669;
    }
    
    .cmk-sa-stat-card.cmk-wait .cmk-sa-stat-icon {
        background: #FEF3C7;
        color: #F59E0B;
    }
    
    .cmk-sa-stat-card.cmk-complete .cmk-sa-stat-icon {
        background: #F3E8FF;
        color: #8B5CF6;
    }
    
    /* 통계 카드 내용 */
    .cmk-sa-stat-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #1F2937;
    }
    
    .cmk-sa-stat-label {
        font-size: 15px;
        color: #6B7280;
        font-weight: 500;
    }
    
    /* ===================================
     * 섹션 스타일
     * =================================== */
    
    /* 섹션 컨테이너 */
    .cmk-sa-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    /* 섹션 헤더 */
    .cmk-sa-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .cmk-sa-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1F2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-sa-section-title i {
        color: #6B7280;
    }
    
    /* 더보기 링크 */
    .cmk-sa-view-more {
        font-size: 14px;
        color: #4F46E5;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.3s;
    }
    
    .cmk-sa-view-more:hover {
        color: #4338CA;
        gap: 8px;
    }
    
    /* ===================================
     * 테이블 스타일
     * =================================== */
    
    /* 테이블 */
    .cmk-sa-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .cmk-sa-table th {
        background: #F9FAFB;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .cmk-sa-table td {
        padding: 16px;
        border-bottom: 1px solid #F3F4F6;
        color: #374151;
    }
    
    .cmk-sa-table tr:last-child td {
        border-bottom: none;
    }
    
    .cmk-sa-table tr:hover td {
        background: #F9FAFB;
    }
    
    /* 빈 데이터 */
    .cmk-sa-empty {
        text-align: center;
        padding: 60px 20px;
        color: #9CA3AF;
    }
    
    .cmk-sa-empty i {
        font-size: 48px;
        margin-bottom: 16px;
        display: block;
        color: #E5E7EB;
    }
    
    .cmk-sa-empty p {
        font-size: 16px;
    }
    
    /* 상태 뱃지 */
    .cmk-sa-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .cmk-sa-badge.cmk-wait {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .cmk-sa-badge.cmk-complete {
        background: #D1FAE5;
        color: #065F46;
    }
    
    /* ===================================
     * 반응형
     * =================================== */
    
    @media (max-width: 768px) {
        .cmk-sa-header-content {
            flex-direction: column;
            text-align: center;
        }
        
        .cmk-sa-referral-box {
            width: 100%;
            max-width: 300px;
        }
        
        .cmk-sa-stats-grid {
            grid-template-columns: 1fr;
        }
        
        .cmk-sa-table {
            font-size: 13px;
        }
        
        .cmk-sa-table th,
        .cmk-sa-table td {
            padding: 12px 8px;
        }
        
        .cmk-sa-section {
            padding: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .cmk-sa-container {
            padding: 10px;
        }
        
        .cmk-sa-header {
            margin: -10px -10px 20px;
            padding: 30px 0;
        }
        
        .cmk-sa-header h1 {
            font-size: 24px;
        }
        
        .cmk-sa-stat-value {
            font-size: 28px;
        }
        
        .cmk-sa-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
    </style>
</head>
<body>

<!-- ===================================
     대시보드 컨텐츠
     =================================== -->
<div class="cmk-sub-admin">
    <!-- 헤더 영역 -->
    <div class="cmk-sa-header">
        <div class="cmk-sa-header-content">
            <div>
                <h1>하부조직 관리 대시보드</h1>
                <p><?php echo $member['mb_name']; ?>님, 환영합니다!</p>
            </div>
            <div class="cmk-sa-referral-box">
                <div class="cmk-sa-referral-label">나의 추천 코드</div>
                <div class="cmk-sa-referral-code" id="referralCode"><?php echo $member['mb_referral_code']; ?></div>
                <button class="cmk-sa-copy-btn" onclick="copyReferralCode()">
                    <i class="bi bi-clipboard"></i> 복사하기
                </button>
                <button class="cmk-sa-change-btn" onclick="showChangeForm()">
                    <i class="bi bi-pencil"></i> 변경하기
                </button>
                
                <!-- 추천 코드 변경 폼 (숨김) -->
                <div id="changeCodeForm" style="display:none; margin-top: 15px;">
                    <form method="post" action="" onsubmit="return validateChangeCode()">
                        <input type="hidden" name="act" value="change_referral_code">
                        <input type="text" name="new_referral_code" id="newReferralCode" 
                               class="cmk-sa-code-input" placeholder="새 추천 코드 (8자리)" 
                               maxlength="8" style="text-transform: uppercase;">
                        <div style="margin-top: 10px;">
                            <button type="submit" class="cmk-sa-submit-btn">확인</button>
                            <button type="button" class="cmk-sa-cancel-btn" onclick="hideChangeForm()">취소</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cmk-sa-container">
        <!-- 통계 카드 -->
        <div class="cmk-sa-stats-grid">
            <div class="cmk-sa-stat-card cmk-members">
                <div class="cmk-sa-stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="cmk-sa-stat-value"><?php echo number_format($total_member); ?></div>
                <div class="cmk-sa-stat-label">전체 하위 회원</div>
            </div>
            
            <div class="cmk-sa-stat-card cmk-today">
                <div class="cmk-sa-stat-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="cmk-sa-stat-value"><?php echo number_format($today_member); ?></div>
                <div class="cmk-sa-stat-label">오늘 가입 회원</div>
            </div>
            
            <div class="cmk-sa-stat-card cmk-wait">
                <div class="cmk-sa-stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="cmk-sa-stat-value"><?php echo number_format($event_wait); ?></div>
                <div class="cmk-sa-stat-label">이벤트 신청 대기</div>
            </div>
            
            <div class="cmk-sa-stat-card cmk-complete">
                <div class="cmk-sa-stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="cmk-sa-stat-value"><?php echo number_format($event_complete); ?></div>
                <div class="cmk-sa-stat-label">이벤트 지급 완료</div>
            </div>
        </div>
        
        <!-- 최근 가입 회원 -->
        <div class="cmk-sa-section">
            <div class="cmk-sa-section-header">
                <h2 class="cmk-sa-section-title">
                    <i class="bi bi-person-lines-fill"></i>
                    최근 가입 회원
                </h2>
                <a href="./member_list.php" class="cmk-sa-view-more">
                    전체 보기 <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
            
            <?php if (sql_num_rows($recent_members) > 0) { ?>
            <table class="cmk-sa-table">
                <thead>
                    <tr>
                        <th>회원ID</th>
                        <th>이름</th>
                        <th>이메일</th>
                        <th>가입일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sql_fetch_array($recent_members)) { ?>
                    <tr>
                        <td><strong><?php echo $row['mb_id']; ?></strong></td>
                        <td><?php echo $row['mb_name']; ?></td>
                        <td><?php echo $row['mb_email']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['mb_datetime'])); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="cmk-sa-empty">
                <i class="bi bi-person-x"></i>
                <p>아직 가입한 하위 회원이 없습니다.</p>
            </div>
            <?php } ?>
        </div>
        
        <!-- 최근 이벤트 신청 -->
        <div class="cmk-sa-section">
            <div class="cmk-sa-section-header">
                <h2 class="cmk-sa-section-title">
                    <i class="bi bi-calendar-event"></i>
                    최근 이벤트 신청
                </h2>
                <a href="./event_apply_list.php" class="cmk-sa-view-more">
                    전체 보기 <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
            
            <?php if (sql_num_rows($recent_events) > 0) { ?>
            <table class="cmk-sa-table">
                <thead>
                    <tr>
                        <th>신청자</th>
                        <th>이벤트명</th>
                        <th>신청일</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sql_fetch_array($recent_events)) { ?>
                    <tr>
                        <td>
                            <strong><?php echo $row['mb_name']; ?></strong><br>
                            <small style="color: #9CA3AF;"><?php echo $row['mb_id']; ?></small>
                        </td>
                        <td><?php echo $row['wr_subject']; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['ea_datetime'])); ?></td>
                        <td>
                            <?php if ($row['ea_status'] == 0) { ?>
                                <span class="cmk-sa-badge cmk-wait">대기중</span>
                            <?php } else { ?>
                                <span class="cmk-sa-badge cmk-complete">지급완료</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="cmk-sa-empty">
                <i class="bi bi-calendar-x"></i>
                <p>아직 이벤트 신청 내역이 없습니다.</p>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
// 추천 코드 복사
function copyReferralCode() {
    const code = document.getElementById('referralCode').textContent;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(() => {
            showCopySuccess();
        });
    } else {
        // Fallback
        const textArea = document.createElement("textarea");
        textArea.value = code;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            alert('복사에 실패했습니다.');
        }
        
        document.body.removeChild(textArea);
    }
}

function showCopySuccess() {
    const btn = document.querySelector('.cmk-sa-copy-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i> 복사완료!';
    btn.style.background = 'rgba(16, 185, 129, 0.3)';
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
    }, 2000);
}

// 추천 코드 변경 폼 표시/숨기기
function showChangeForm() {
    document.getElementById('changeCodeForm').style.display = 'block';
    document.getElementById('newReferralCode').focus();
}

function hideChangeForm() {
    document.getElementById('changeCodeForm').style.display = 'none';
    document.getElementById('newReferralCode').value = '';
}

// 추천 코드 입력 시 자동 대문자 변환
document.getElementById('newReferralCode').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// 추천 코드 변경 유효성 검사
function validateChangeCode() {
    const newCode = document.getElementById('newReferralCode').value;
    
    if (newCode.length !== 8) {
        alert('추천 코드는 8자리여야 합니다.');
        return false;
    }
    
    if (!/^[A-Z0-9]+$/.test(newCode)) {
        alert('추천 코드는 영문 대문자와 숫자만 가능합니다.');
        return false;
    }
    
    return confirm('추천 코드를 변경하시겠습니까?');
}
</script>

</body>
</html>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>