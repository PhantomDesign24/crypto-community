<?php
/*
 * 파일명: header.php
 * 위치: /sub_admin/
 * 기능: 조직 관리 페이지 공통 헤더
 * 작성일: 2025-01-23
 * 수정일: 2025-01-23 (최고관리자 기능 추가)
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// ===================================
// 공통 권한 체크
// ===================================
/* 로그인 체크 */
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

/* 하부조직 권한 체크 (2등급 이상) */
if ($member['mb_grade'] < 2 && !$is_admin) {
    alert('접근 권한이 없습니다.', G5_URL);
}

// ===================================
// 하부조직 통계
// ===================================

/* 최고관리자는 전체 통계, 일반 관리자는 하위 회원 통계 */
if ($is_admin) {
    /* 전체 회원 수 */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']}";
    $row = sql_fetch($sql);
    $total_members = $row['cnt'];
    
    /* 오늘 가입한 전체 회원 수 */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE DATE(mb_datetime) = CURDATE()";
    $row = sql_fetch($sql);
    $today_members = $row['cnt'];
    
    /* 전체 하부조직 관리자 수 (2등급 이상) */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_grade >= 2";
    $row = sql_fetch($sql);
    $total_managers = $row['cnt'];
} else {
    /* 하위 회원 수 */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_recommend = '{$member['mb_id']}'";
    $row = sql_fetch($sql);
    $total_members = $row['cnt'];
    
    /* 오늘 가입한 회원 수 */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE mb_recommend = '{$member['mb_id']}' 
            AND DATE(mb_datetime) = CURDATE()";
    $row = sql_fetch($sql);
    $today_members = $row['cnt'];
    
    /* 내 하위 회원 중 관리자 수 */
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE mb_recommend = '{$member['mb_id']}' AND mb_grade >= 2";
    $row = sql_fetch($sql);
    $total_managers = $row['cnt'];
}

/* 현재 페이지 확인 */
$current_page = basename($_SERVER['PHP_SELF']);

include_once(G5_PATH.'/head.sub.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $g5['title']; ?> - 조직 관리</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
    /* ===================================
     * 조직 관리 공통 스타일
     * =================================== */
    
    /* 레이아웃 */
    .sa-layout-wrapper {
        display: flex;
        min-height: 100vh;
        background: #f3f4f6;
    }
    
    /* 사이드바 */
    .sa-sidebar {
        width: 260px;
        background: #1f2937;
        color: white;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1050;
    }
    
    .sa-sidebar-header {
        padding: 24px 20px;
        background: #111827;
        border-bottom: 1px solid #374151;
    }
    
    .sa-sidebar-title {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .sa-sidebar-title i {
        color: #3b82f6;
    }
    
    .sa-user-info {
        padding: 20px;
        border-bottom: 1px solid #374151;
    }
    
    .sa-user-name {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .sa-user-grade {
        font-size: 14px;
        color: #9ca3af;
    }
    
    .sa-user-grade i {
        color: #3b82f6;
    }
    
    /* 통계 박스 */
    .sa-sidebar-stats {
        padding: 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        border-bottom: 1px solid #374151;
    }
    
    .sa-stat-item {
        text-align: center;
        padding: 12px;
        background: #374151;
        border-radius: 8px;
    }
    
    .sa-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #3b82f6;
    }
    
    .sa-stat-label {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
    }
    
    /* 메뉴 */
    .sa-sidebar-menu {
        padding: 20px 0;
    }
    
    .sa-menu-item {
        display: block;
        padding: 12px 20px;
        color: #d1d5db;
        text-decoration: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .sa-menu-item:hover {
        background: #374151;
        color: white;
    }
    
    .sa-menu-item.active {
        background: #3b82f6;
        color: white;
        position: relative;
    }
    
    .sa-menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #60a5fa;
    }
    
    .sa-menu-item i {
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    /* 메인 콘텐츠 */
    .sa-main-content {
        flex: 1;
        margin-left: 260px;
        padding: 20px;
        transition: all 0.3s;
        min-width: 0; /* flexbox overflow 방지 */
    }
    
    /* 페이지 헤더 */
    .sa-page-header {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .sa-page-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .sa-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #6b7280;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    
    .sa-breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
    }
    
    .sa-breadcrumb a:hover {
        text-decoration: underline;
    }
    
    /* 모바일 메뉴 토글 */
    .sa-mobile-toggle {
        display: none;
        position: fixed;
        top: 16px;
        left: 16px;
        z-index: 1100;
        background: #1f2937;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .sa-mobile-toggle i {
        font-size: 20px;
    }
    
    /* 오버레이 */
    .sa-sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .sa-sidebar-overlay.active {
        display: block;
        opacity: 1;
    }
    
    /* 반응형 - 태블릿 */
    @media (max-width: 1024px) {
        .sa-sidebar {
            width: 240px;
        }
        
        .sa-main-content {
            margin-left: 240px;
        }
        
        .sa-page-title {
            font-size: 20px;
        }
    }
    
    /* 반응형 - 모바일 */
    @media (max-width: 768px) {
        .sa-sidebar {
            transform: translateX(-100%);
            width: 280px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .sa-sidebar.active {
            transform: translateX(0);
        }
        
        .sa-main-content {
            margin-left: 0;
            padding: 16px;
            padding-top: 70px; /* 모바일 토글 버튼 공간 */
        }
        
        .sa-mobile-toggle {
            display: block;
        }
        
        .sa-page-header {
            padding: 20px;
            border-radius: 8px;
        }
        
        .sa-page-title {
            font-size: 18px;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .sa-sidebar-stats {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        
        /* 추천 코드 뱃지 모바일 */
        .sa-page-title span {
            font-size: 12px;
            padding: 4px 10px;
        }
    }
    
    /* 반응형 - 작은 모바일 */
    @media (max-width: 480px) {
        .sa-main-content {
            padding: 12px;
            padding-top: 70px;
        }
        
        .sa-page-header {
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .sa-breadcrumb {
            font-size: 12px;
        }
        
        .sa-sidebar-header {
            padding: 20px 16px;
        }
        
        .sa-sidebar-title {
            font-size: 18px;
        }
        
        .sa-menu-item {
            padding: 10px 16px;
            font-size: 14px;
        }
        
        .sa-menu-item i {
            font-size: 16px;
        }
    }
    </style>
</head>
<body>

<div class="sa-layout-wrapper">
    <!-- 모바일 메뉴 토글 -->
    <button class="sa-mobile-toggle" onclick="saSidebarToggle()">
        <i class="bi bi-list"></i>
    </button>
    
    <!-- 사이드바 오버레이 -->
    <div class="sa-sidebar-overlay" id="saSidebarOverlay" onclick="saSidebarToggle()"></div>
    
    <!-- 사이드바 -->
    <aside class="sa-sidebar" id="saSidebar">
        <div class="sa-sidebar-header">
            <h1 class="sa-sidebar-title">
                <i class="bi bi-diagram-3"></i> 조직 관리
            </h1>
        </div>
        
        <div class="sa-user-info">
            <div class="sa-user-name"><?php echo $member['mb_name']; ?></div>
            <div class="sa-user-grade">
                <i class="bi bi-award"></i> 
                <?php 
                switch($member['mb_grade']) {
                    case 2: echo '파트너'; break;
                    case 3: echo '매니저'; break;
                    case 4: echo '관리자'; break;
                    default: echo '미지정';
                }
                ?>
            </div>
        </div>
        
        <div class="sa-sidebar-stats">
            <div class="sa-stat-item">
                <div class="sa-stat-value"><?php echo number_format($total_members); ?></div>
                <div class="sa-stat-label">전체 회원</div>
            </div>
            <div class="sa-stat-item">
                <div class="sa-stat-value"><?php echo number_format($today_members); ?></div>
                <div class="sa-stat-label">오늘 가입</div>
            </div>
        </div>
        
		<nav class="sa-sidebar-menu">
			<a href="./index.php" class="sa-menu-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
				<i class="bi bi-speedometer2"></i> 대시보드
			</a>
			<a href="./member_list.php" class="sa-menu-item <?php echo ($current_page == 'member_list.php') ? 'active' : ''; ?>">
				<i class="bi bi-people"></i> 회원 관리
			</a>
			<a href="./member_register.php" class="sa-menu-item <?php echo ($current_page == 'member_register.php') ? 'active' : ''; ?>">
				<i class="bi bi-person-plus"></i> 회원 등록
			</a>
			<a href="./organization_tree.php" class="sa-menu-item <?php echo ($current_page == 'organization_tree.php') ? 'active' : ''; ?>">
				<i class="bi bi-diagram-3"></i> 조직도
			</a>
			<a href="./event_list.php" class="sa-menu-item <?php echo ($current_page == 'event_list.php') ? 'active' : ''; ?>">
				<i class="bi bi-gift"></i> 이벤트 관리
			</a>
			<a href="./sub_admin_notice.php" class="sa-menu-item <?php echo ($current_page == 'sub_admin_notice.php') ? 'active' : ''; ?>">
				<i class="bi bi-megaphone"></i> 공지사항
			</a>
			<div style="border-top: 1px solid #374151; margin: 20px 0;"></div>
            <a href="<?php echo G5_URL; ?>" class="sa-menu-item">
                <i class="bi bi-house"></i> 메인으로
            </a>
            <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="sa-menu-item">
                <i class="bi bi-box-arrow-right"></i> 로그아웃
            </a>
		</nav>
    </aside>
    
    <!-- 메인 콘텐츠 -->
    <main class="sa-main-content">
        <div class="sa-page-header">
            <h1 class="sa-page-title">
                <?php echo $g5['title']; ?>
                <?php if ($member['mb_referral_code']) { ?>
                <span style="font-size: 14px; background: #eff6ff; color: #3b82f6; padding: 6px 12px; border-radius: 6px;">
                    <i class="bi bi-gift"></i> 추천코드: <?php echo $member['mb_referral_code']; ?>
                </span>
                <?php } ?>
            </h1>
            <div class="sa-breadcrumb">
                <a href="<?php echo G5_URL; ?>">홈</a>
                <i class="bi bi-chevron-right"></i>
                <a href="./index.php">조직 관리</a>
                <i class="bi bi-chevron-right"></i>
                <span><?php echo $g5['title']; ?></span>
            </div>
        </div>
        
        <!-- 여기부터 각 페이지 콘텐츠 시작 -->