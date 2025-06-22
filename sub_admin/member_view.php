<?php
/*
 * 파일명: member_view.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 하위 회원 정보 보기
 * 작성일: 2025-01-23
 */

include_once('./_common.php');
include_once('./header.php');

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

/* 회원 ID 확인 */
$mb_id = isset($_GET['mb_id']) ? trim($_GET['mb_id']) : '';
if (!$mb_id) {
    alert('회원 정보가 없습니다.', './member_list.php');
}

// ===================================
// 회원 정보 조회 (기존 코드를 다음으로 교체)
// ===================================

/* 최고관리자는 모든 회원 조회 가능, 일반 관리자는 하위 회원만 */
if ($is_admin) {
    $sql = "SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}'";
} else {
    $sql = "SELECT * FROM {$g5['member_table']} 
            WHERE mb_id = '{$mb_id}' AND mb_recommend = '{$member['mb_id']}'";
}
$mb = sql_fetch($sql);

if (!$mb['mb_id']) {
    alert('권한이 없거나 존재하지 않는 회원입니다.', './member_list.php');
}

/* 회원 등급 텍스트 */
$grade_text = '';
switch($mb['mb_grade']) {
    case 1:
        $grade_text = '일반회원';
        $grade_color = '#6b7280';
        break;
    case 2:
        $grade_text = '파트너';
        $grade_color = '#3b82f6';
        break;
    case 3:
        $grade_text = '매니저';
        $grade_color = '#8b5cf6';
        break;
    case 4:
        $grade_text = '관리자';
        $grade_color = '#ef4444';
        break;
    default:
        $grade_text = '미지정';
        $grade_color = '#6b7280';
}

/* 페이지 제목 */
$g5['title'] = '회원 정보 보기';

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
     * 회원 정보 보기 전용 스타일
     * =================================== */
    
    .cmk-member-view * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-member-view {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }
    
    .cmk-mv-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* 헤더 */
    .cmk-mv-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .cmk-mv-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .cmk-mv-header h1 i {
        color: #3b82f6;
    }
    
    .cmk-mv-member-info {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 24px;
        padding: 20px;
        background: #f3f4f6;
        border-radius: 12px;
    }
    
    .cmk-mv-avatar {
        width: 80px;
        height: 80px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 600;
    }
    
    .cmk-mv-member-details h2 {
        font-size: 24px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .cmk-mv-member-details .info-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .cmk-mv-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .cmk-mv-badge.grade {
        background: <?php echo $grade_color; ?>20;
        color: <?php echo $grade_color; ?>;
    }
    
    /* 정보 섹션 */
    .cmk-mv-section {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .cmk-mv-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .cmk-mv-section-title i {
        color: #3b82f6;
    }
    
    /* 정보 항목 */
    .cmk-mv-info-item {
        display: flex;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .cmk-mv-info-item:last-child {
        border-bottom: none;
    }
    
    .cmk-mv-label {
        width: 140px;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        flex-shrink: 0;
    }
    
    .cmk-mv-value {
        flex: 1;
        font-size: 14px;
        color: #1f2937;
        word-break: break-all;
    }
    
    .cmk-mv-value.empty {
        color: #9ca3af;
        font-style: italic;
    }
    
    /* 추천 코드 */
    .referral-code-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        font-family: monospace;
        font-size: 16px;
        font-weight: 600;
        color: #1e40af;
    }
    
    /* 메모 박스 */
    .cmk-mv-memo-box {
        padding: 16px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        min-height: 100px;
        white-space: pre-wrap;
        line-height: 1.6;
    }
    
    /* 버튼 영역 */
    .cmk-mv-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 40px;
    }
    
    .cmk-mv-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-mv-btn-primary {
        background: #3b82f6;
        color: white;
    }
    
    .cmk-mv-btn-primary:hover {
        background: #2563eb;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .cmk-mv-btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    
    .cmk-mv-btn-secondary:hover {
        background: #d1d5db;
    }
    
    /* 통계 박스 */
    .cmk-mv-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-box {
        padding: 20px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* 반응형 */
    @media (max-width: 768px) {
        .cmk-member-view {
            padding: 10px;
        }
        
        .cmk-mv-header,
        .cmk-mv-section {
            padding: 24px 20px;
        }
        
        .cmk-mv-member-info {
            flex-direction: column;
            text-align: center;
        }
        
        .cmk-mv-info-item {
            flex-direction: column;
            gap: 8px;
        }
        
        .cmk-mv-actions {
            flex-direction: column;
        }
        
        .cmk-mv-btn {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
</head>
<body>

<div class="cmk-member-view">
    <div class="cmk-mv-container">
        <!-- 헤더 -->
        <div class="cmk-mv-header">
            <h1><i class="bi bi-person-lines-fill"></i> 회원 정보</h1>
            <div class="cmk-mv-member-info">
                <div class="cmk-mv-avatar">
                    <?php echo mb_substr($mb['mb_name'], 0, 1); ?>
                </div>
                <div class="cmk-mv-member-details">
                    <h2><?php echo get_text($mb['mb_name']); ?></h2>
                    <div class="info-row">
                        <span class="cmk-mv-badge">
                            <i class="bi bi-person"></i> <?php echo $mb['mb_id']; ?>
                        </span>
                        <span class="cmk-mv-badge grade">
                            <i class="bi bi-award"></i> <?php echo $grade_text; ?>
                        </span>
                        <?php if ($mb['mb_referral_code']) { ?>
                        <span class="cmk-mv-badge">
                            <i class="bi bi-gift"></i> 추천 코드 보유
                        </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 통계 정보 -->
        <div class="cmk-mv-stats">
            <div class="stat-box">
                <div class="stat-value"><?php echo number_format($mb['mb_point']); ?></div>
                <div class="stat-label">보유 포인트</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo $mb['mb_today_login'] ? date('m/d', strtotime($mb['mb_today_login'])) : '-'; ?></div>
                <div class="stat-label">최근 접속</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo date('Y.m.d', strtotime($mb['mb_datetime'])); ?></div>
                <div class="stat-label">가입일</div>
            </div>
        </div>
        
        <!-- 기본 정보 -->
        <div class="cmk-mv-section">
            <h3 class="cmk-mv-section-title">
                <i class="bi bi-person"></i> 기본 정보
            </h3>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">아이디</div>
                <div class="cmk-mv-value"><?php echo $mb['mb_id']; ?></div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">이름</div>
                <div class="cmk-mv-value"><?php echo get_text($mb['mb_name']); ?></div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">닉네임</div>
                <div class="cmk-mv-value"><?php echo get_text($mb['mb_nick']); ?></div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">추천인</div>
                <div class="cmk-mv-value"><?php echo $mb['mb_recommend'] ? get_text($mb['mb_recommend']) : '<span class="empty">없음</span>'; ?></div>
            </div>
            
            <?php if ($mb['mb_referral_code']) { ?>
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">추천 코드</div>
                <div class="cmk-mv-value">
                    <div class="referral-code-box">
                        <i class="bi bi-gift"></i>
                        <?php echo $mb['mb_referral_code']; ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <!-- 연락처 정보 -->
        <div class="cmk-mv-section">
            <h3 class="cmk-mv-section-title">
                <i class="bi bi-telephone"></i> 연락처 정보
            </h3>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">이메일</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_email'] ? get_text($mb['mb_email']) : '<span class="empty">미등록</span>'; ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">휴대폰</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_hp'] ? get_text($mb['mb_hp']) : '<span class="empty">미등록</span>'; ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">전화번호</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_tel'] ? get_text($mb['mb_tel']) : '<span class="empty">미등록</span>'; ?>
                </div>
            </div>
        </div>
        
        <!-- 주소 정보 -->
        <div class="cmk-mv-section">
            <h3 class="cmk-mv-section-title">
                <i class="bi bi-geo-alt"></i> 주소 정보
            </h3>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">우편번호</div>
                <div class="cmk-mv-value">
                    <?php 
                    if ($mb['mb_zip1'] && $mb['mb_zip2']) {
                        echo $mb['mb_zip1'] . '-' . $mb['mb_zip2'];
                    } else {
                        echo '<span class="empty">미등록</span>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">주소</div>
                <div class="cmk-mv-value">
                    <?php 
                    if ($mb['mb_addr1']) {
                        echo get_text($mb['mb_addr1']);
                        if ($mb['mb_addr2']) echo '<br>' . get_text($mb['mb_addr2']);
                        if ($mb['mb_addr3']) echo ' ' . get_text($mb['mb_addr3']);
                    } else {
                        echo '<span class="empty">미등록</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- 기타 정보 -->
        <div class="cmk-mv-section">
            <h3 class="cmk-mv-section-title">
                <i class="bi bi-info-circle"></i> 기타 정보
            </h3>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">가입일시</div>
                <div class="cmk-mv-value"><?php echo $mb['mb_datetime']; ?></div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">최근 접속</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_today_login'] ? $mb['mb_today_login'] : '<span class="empty">접속 기록 없음</span>'; ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">접속 IP</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_login_ip'] ? $mb['mb_login_ip'] : '<span class="empty">-</span>'; ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">메일 수신</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_mailling'] ? '<span style="color: #10b981;">수신</span>' : '<span style="color: #ef4444;">거부</span>'; ?>
                </div>
            </div>
            
            <div class="cmk-mv-info-item">
                <div class="cmk-mv-label">SMS 수신</div>
                <div class="cmk-mv-value">
                    <?php echo $mb['mb_sms'] ? '<span style="color: #10b981;">수신</span>' : '<span style="color: #ef4444;">거부</span>'; ?>
                </div>
            </div>
        </div>
        
        <!-- 관리자 메모 -->
        <?php if ($mb['mb_memo']) { ?>
        <div class="cmk-mv-section">
            <h3 class="cmk-mv-section-title">
                <i class="bi bi-pencil-square"></i> 관리자 메모
            </h3>
            
            <div class="cmk-mv-memo-box">
                <?php echo nl2br(get_text($mb['mb_memo'])); ?>
            </div>
        </div>
        <?php } ?>
        
        <!-- 버튼 영역 -->
        <div class="cmk-mv-actions">
            <a href="./member_edit.php?mb_id=<?php echo $mb_id; ?>" class="cmk-mv-btn cmk-mv-btn-primary">
                <i class="bi bi-pencil"></i> 정보 수정
            </a>
            <a href="./member_list.php" class="cmk-mv-btn cmk-mv-btn-secondary">
                <i class="bi bi-list"></i> 목록으로
            </a>
        </div>
    </div>
</div>

</body>
</html>

<?php
include_once('./footer.php');
?>