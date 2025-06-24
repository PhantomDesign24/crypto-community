<?php
/*
 * 파일명: head.sub.php
 * 위치: /theme/basic/
 * 기능: 크립토 사이트 공통 헤더 (HTML 시작부분) - SEO 최적화 추가
 * 작성일: 2025-01-11
 * 수정일: 2025-01-23 (SEO 최적화 코드 추가)
 */

// 필수 보안 체크
if (!defined('_GNUBOARD_')) exit;

// 모바일 체크
$is_mobile = false;
if (G5_IS_MOBILE) {
    $is_mobile = true;
}

// ===================================
// SEO 변수 초기화
// ===================================

/* SEO 메타 태그 기본값 설정 */
$seo_title = isset($g5_head_title) ? $g5_head_title : $config['cf_title'];
$seo_description = isset($config['cf_title']) ? $config['cf_title'] . ' - 암호화폐 거래소, 비트코인, 이더리움, 알트코인 거래' : '';
$seo_keywords = '암호화폐, 비트코인, 이더리움, 알트코인, 가상화폐, 코인거래소, 디지털자산';
$seo_author = isset($config['cf_admin_email_name']) ? strip_tags($config['cf_admin_email_name']) : '';
$seo_og_image = G5_THEME_IMG_URL.'/og_image.jpg'; // 기본 OG 이미지
$seo_og_type = 'website';
$seo_canonical = '';

// ===================================
// 페이지별 SEO 최적화
// ===================================

/* 게시판 페이지 SEO */
if (isset($bo_table) && $bo_table) {
    if (isset($board['bo_subject'])) {
        $seo_title = $board['bo_subject'] . ' | ' . $config['cf_title'];
        $seo_description = strip_tags($board['bo_subject']) . ' - ' . $config['cf_title'] . ' 커뮤니티';
        
        // 카테고리가 있으면 키워드에 추가
        if (isset($board['bo_category_list']) && $board['bo_category_list']) {
            $categories = str_replace('|', ', ', $board['bo_category_list']);
            $seo_keywords = $categories . ', ' . $seo_keywords;
        }
    }
    
    // 게시글 보기 페이지
    if (isset($wr_id) && $wr_id && isset($write['wr_subject'])) {
        $seo_title = strip_tags($write['wr_subject']) . ' | ' . $board['bo_subject'];
        $seo_description = cut_str(strip_tags($write['wr_content']), 160, '...');
        $seo_og_type = 'article';
        
        // 게시글 이미지 추출
        if (preg_match('/<img[^>]+src=[\'"]?([^>\'"]+)[\'"]?[^>]*>/i', $write['wr_content'], $match)) {
            $seo_og_image = $match[1];
            if (!preg_match('/^http/i', $seo_og_image)) {
                $seo_og_image = G5_URL . $seo_og_image;
            }
        }
        
        // Canonical URL 설정 (SEO 타이틀 활용)
        if (isset($write['wr_seo_title']) && $write['wr_seo_title']) {
            $seo_canonical = G5_BBS_URL . '/board.php?bo_table=' . $bo_table . '&wr_id=' . $wr_id;
        } else {
            $seo_canonical = get_pretty_url($bo_table, $wr_id);
        }
    }
}

/* 컨텐츠 페이지 SEO */
if (isset($co_id) && $co_id && isset($co['co_subject'])) {
    $seo_title = strip_tags($co['co_subject']) . ' | ' . $config['cf_title'];
    $seo_description = cut_str(strip_tags($co['co_content']), 160, '...');
    
    // Canonical URL 설정
    if (isset($co['co_seo_title']) && $co['co_seo_title']) {
        $seo_canonical = G5_BBS_URL . '/content.php?co_id=' . $co_id;
    } else {
        $seo_canonical = get_pretty_url('content', $co_id);
    }
}

/* 회원 페이지 SEO */
if (strpos($_SERVER['PHP_SELF'], '/bbs/register') !== false) {
    $seo_title = '회원가입 | ' . $config['cf_title'];
    $seo_description = $config['cf_title'] . ' 회원가입 - 암호화폐 거래를 시작하세요';
    $seo_keywords = '회원가입, 가입하기, ' . $seo_keywords;
} elseif (strpos($_SERVER['PHP_SELF'], '/bbs/login') !== false) {
    $seo_title = '로그인 | ' . $config['cf_title'];
    $seo_description = $config['cf_title'] . ' 로그인 - 안전한 암호화폐 거래';
    $seo_keywords = '로그인, 회원로그인, ' . $seo_keywords;
}

/* FAQ 페이지 SEO */
if (strpos($_SERVER['PHP_SELF'], '/bbs/faq') !== false) {
    $seo_title = '자주묻는질문 | ' . $config['cf_title'];
    $seo_description = $config['cf_title'] . ' FAQ - 암호화폐 거래 관련 자주묻는질문';
    $seo_og_type = 'article';
}

/* 1:1문의 페이지 SEO */
if (strpos($_SERVER['PHP_SELF'], '/bbs/qalist') !== false) {
    $seo_title = '1:1문의 | ' . $config['cf_title'];
    $seo_description = $config['cf_title'] . ' 1:1 문의하기 - 고객지원센터';
}

// ===================================
// SEO 메타 태그 정리
// ===================================

/* 설명 길이 제한 (160자) */
$seo_description = cut_str($seo_description, 160, '...');

/* 특수문자 이스케이프 */
$seo_title = htmlspecialchars($seo_title, ENT_QUOTES, 'UTF-8');
$seo_description = htmlspecialchars($seo_description, ENT_QUOTES, 'UTF-8');
$seo_keywords = htmlspecialchars($seo_keywords, ENT_QUOTES, 'UTF-8');
$seo_author = htmlspecialchars($seo_author, ENT_QUOTES, 'UTF-8');

/* Canonical URL 생성 */
if (!$seo_canonical) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $seo_canonical = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // 파라미터 정리
    $seo_canonical = strtok($seo_canonical, '?');
}

/* 현재 URL (OG URL용) */
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
}
?>

<!-- ===================================
     SEO 메타 태그
     =================================== -->
<title><?php echo $seo_title; ?></title>
<meta name="description" content="<?php echo $seo_description; ?>">
<meta name="keywords" content="<?php echo $seo_keywords; ?>">
<meta name="author" content="<?php echo $seo_author; ?>">
<meta name="robots" content="index,follow">
<link rel="canonical" href="<?php echo $seo_canonical; ?>">

<!-- Open Graph 메타 태그 -->
<meta property="og:type" content="<?php echo $seo_og_type; ?>">
<meta property="og:title" content="<?php echo $seo_title; ?>">
<meta property="og:description" content="<?php echo $seo_description; ?>">
<meta property="og:url" content="<?php echo $current_url; ?>">
<meta property="og:image" content="<?php echo $seo_og_image; ?>">
<meta property="og:site_name" content="<?php echo $config['cf_title']; ?>">
<meta property="og:locale" content="ko_KR">

<!-- Twitter Card 메타 태그 -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $seo_title; ?>">
<meta name="twitter:description" content="<?php echo $seo_description; ?>">
<meta name="twitter:image" content="<?php echo $seo_og_image; ?>">

<!-- 추가 SEO 메타 태그 -->
<meta name="naver-site-verification" content=""><!-- 네이버 서치어드바이저 -->
<meta name="google-site-verification" content=""><!-- 구글 서치콘솔 -->

<!-- 파비콘 -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo G5_THEME_IMG_URL; ?>/apple-touch-icon.png">



<!-- =================================== -->
<!-- 필수 스타일시트 -->
<!-- =================================== -->
<!-- Bootstrap CSS 비동기 로드 -->
<link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></noscript>

<!-- Bootstrap Icons 비동기 로드 -->
<link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"></noscript>

<!-- =================================== -->
<!-- 중요 인라인 스타일 -->
<!-- =================================== -->
<style>
/* 전역 스타일 */
:root {
    --primary-color: #1a1a2e;
    --secondary-color: #0f3460;
    --accent-color: #3b82f6;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --dark-bg: #111827;
    --light-bg: #f9fafb;
    --border-color: #e5e7eb;
    --text-primary: #111827;
    --text-secondary: #6b7280;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--light-bg);
    color: var(--text-primary);
    line-height: 1.6;
}

/* =================================== -->
<!-- 헤더 스타일 -->
<!-- =================================== -->
/* 상단 정보바 */
.top-info-bar {
    background-color: var(--dark-bg);
    color: #9ca3af;
    font-size: 0.875rem;
    padding: 8px 0;
}

.top-info-bar a {
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.3s;
}

.top-info-bar a:hover {
    color: white;
}

/* 메인 헤더 */
.main-header {
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* 메인 헤더 */
.main-header {
    background: white;
    position: relative;
    z-index: 1000;
}

/* 로고 영역 */
.logo-section {
    padding: 15px 0;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: relative;
    z-index: 1001;
}

.logo {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary-color);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}

.logo i {
    color: var(--accent-color);
}

.logo:hover {
    color: var(--accent-color);
}

/* 헤더 마켓 정보 */
.header-market-info {
    display: flex;
    align-items: center;
    gap: 25px;
}

.market-item {
    display: flex;
    align-items: center;
}

.market-coin {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    background: #f8fafc;
    border-radius: 10px;
    transition: all 0.3s;
}

.market-coin:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

.coin-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.coin-info {
    text-align: left;
}

.coin-info .coin-name {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 2px;
}

.coin-info .coin-price {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}

.coin-info .coin-change {
    font-size: 0.75rem;
    font-weight: 500;
}

.coin-change.positive {
    color: var(--success-color);
}

.coin-change.negative {
    color: var(--danger-color);
}

/* USDT 거래 정보 */
.usdt-trade-info {
    display: flex;
    gap: 1px;
    background: #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.trade-item {
    padding: 10px 20px;
    background: white;
    text-align: center;
    flex: 1;
}

.trade-item:first-child {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.trade-item:last-child {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
}

.trade-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 4px;
    font-weight: 500;
}

.trade-price {
    font-size: 1rem;
    font-weight: 700;
}

.trade-price.buy {
    color: #dc2626;
}

.trade-price.sell {
    color: #16a34a;
}

/* 헤더 액션 버튼 */
.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-cta {
    padding: 8px 20px;
    background: linear-gradient(135deg, var(--accent-color) 0%, #2563eb 100%);
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.header-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    color: white;
}

/* 네비게이션 */
.main-nav {
    background: var(--primary-color);
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-menu li {
    position: relative;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: block;
    font-weight: 500;
    transition: all 0.3s;
    font-size: 0.95rem;
}

.nav-menu a:hover {
    background: rgba(255,255,255,0.1);
    color: var(--accent-color);
}

/* 모바일 메뉴 토글 */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    padding: 10px;
}

/* =================================== -->
<!-- 유틸리티 스타일 -->
<!-- =================================== -->
/* 컨테이너 */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* 입력 폼 공통 스타일 */
.input-group-text {
    border-right: none;
    background-color: #fff;
}

.form-control {
    border-left: none;
    padding-left: 0;
}

.form-control:focus {
    box-shadow: none;
    border-color: var(--accent-color);
}

/* 버튼 스타일 */
.btn-primary {
    background: var(--accent-color);
    border: none;
    padding: 10px 24px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

/* 카드 스타일 */
.card {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* =================================== -->
<!-- 모바일 스타일 -->
<!-- =================================== -->
/* 모바일 메뉴 토글 버튼 */
.mobile-menu-toggle {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--primary-color);
    font-size: 1rem;
    border-radius: 8px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 5px;
}

.mobile-menu-toggle:hover {
    background: var(--accent-color);
    color: white;
    border-color: var(--accent-color);
}

.mobile-menu-toggle .menu-text {
    font-size: 0.875rem;
    font-weight: 500;
}

/* 모바일 전체 메뉴 */
.mobile-fullmenu {
    position: fixed;
    top: 0;
    right: -100%;
    width: 85%;
    max-width: 360px;
    height: 100vh;
    background: white;
    z-index: 1001;
    transition: right 0.3s ease;
    overflow-y: auto;
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
}

.mobile-fullmenu.active {
    right: 0;
}

/* 모바일 메뉴 헤더 */
.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: var(--primary-color);
    color: white;
}

.mobile-menu-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.close-menu {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    padding: 5px;
}

/* 모바일 사용자 정보 */
.mobile-user-info {
    padding: 20px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border-color);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.user-profile i {
    font-size: 2.5rem;
    color: var(--accent-color);
}

.user-actions {
    display: flex;
    gap: 10px;
}

.user-actions a {
    flex: 1;
    padding: 8px;
    text-align: center;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.875rem;
}

.login-prompt {
    text-align: center;
}

.login-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-login, .btn-register {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
}

.btn-login {
    background: var(--accent-color);
    color: white;
}

.btn-register {
    background: white;
    border: 1px solid var(--accent-color);
    color: var(--accent-color);
}

/* 모바일 시세 위젯 */
.mobile-market-widget {
    padding: 15px;
    background: white;
    border-bottom: 1px solid var(--border-color);
}

.mobile-market-widget h4 {
    font-size: 0.875rem;
    margin-bottom: 12px;
    color: var(--text-primary);
    font-weight: 600;
}

.mobile-market-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mobile-coin-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f8fafc;
    border-radius: 8px;
}

.mobile-coin-item .coin-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.coin-details {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.coin-details .coin-name {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.coin-details .coin-price {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}

.coin-details .coin-change {
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

.mobile-usdt-item {
    display: flex;
    gap: 1px;
    background: #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.mobile-usdt-item .price-item {
    flex: 1;
    padding: 10px;
    background: white;
    text-align: center;
}

.mobile-usdt-item .price-item:first-child {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.mobile-usdt-item .price-item:last-child {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
}

.price-item .label {
    display: block;
    font-size: 0.7rem;
    color: var(--text-secondary);
    margin-bottom: 3px;
    font-weight: 500;
}

.price-item .price {
    font-size: 0.875rem;
    font-weight: 700;
}

.price-item .price.buy {
    color: #dc2626;
}

.price-item .price.sell {
    color: #16a34a;
}

/* 모바일 네비게이션 메뉴 */
.mobile-nav-menu {
    padding: 10px 0;
}

.mobile-nav-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-nav-menu li {
    border-bottom: 1px solid var(--border-color);
}

.mobile-nav-menu a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.mobile-nav-menu a:hover {
    background: #f8fafc;
    color: var(--accent-color);
}

.mobile-nav-menu i {
    font-size: 1.25rem;
    color: var(--accent-color);
}

/* 모바일 고객센터 정보 */
.mobile-support-info {
    padding: 20px;
    background: #f8fafc;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.mobile-support-info p {
    margin: 5px 0;
}

/* 모바일 오버레이 */
.mobile-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.mobile-overlay.active {
    display: block;
}

/* 데스크톱 메뉴 스타일 */
.desktop-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

/* =================================== -->
<!-- 반응형 스타일 -->
<!-- =================================== -->
*/
@media (max-width: 991px) {
    .main-nav {
        display: none !important;
    }
}

@media (max-width: 768px) {
    .top-info-bar {
        font-size: 0.75rem;
        padding: 6px 0;
    }
    
    .top-info-bar .d-flex {
        flex-wrap: nowrap;
    }
    
    .top-info-bar a {
        font-size: 0.75rem;
    }
    
    .logo {
        font-size: 1.25rem;
    }
    
    .logo i {
        font-size: 1.25rem;
    }
    
    .logo span {
        font-size: 1.125rem;
    }
    
    .logo-section {
        padding: 10px 0;
    }
    
    /* 모바일 폰트 크기 조정 */
    .mobile-menu-header h3 {
        font-size: 1.125rem;
    }
    
    .mobile-nav-menu a {
        font-size: 0.875rem;
        padding: 12px 20px;
    }
    
    .mobile-nav-menu i {
        font-size: 1.125rem;
    }
    
    .user-profile strong {
        font-size: 0.875rem;
    }
    
    .user-profile p {
        font-size: 0.75rem;
    }
    
    .login-prompt p {
        font-size: 0.813rem;
    }
    
    .mobile-support-info {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .container {
        padding: 0 10px;
    }
    
    .top-info-bar {
        font-size: 0.7rem;
        padding: 5px 0;
    }
    
    .top-info-bar i {
        font-size: 0.75rem;
    }
    
    .logo {
        font-size: 1.125rem;
    }
    
    .logo i {
        font-size: 1.125rem;
    }
    
    .logo span {
        font-size: 1rem;
    }
    
    .mobile-menu-toggle {
        width: 36px;
        height: 36px;
    }
    
    .mobile-menu-toggle i {
        font-size: 1.25rem;
    }
}

@media (max-width: 320px) {
    .top-info-bar {
        font-size: 0.65rem;
    }
    
    .top-info-bar .mx-2 {
        margin: 0 0.25rem !important;
    }
    
    .logo {
        font-size: 1rem;
        gap: 6px;
    }
    
    .logo i {
        font-size: 1rem;
    }
    
    .logo span {
        font-size: 0.875rem;
    }
    
    .mobile-menu-toggle {
		line-height:1;
    }
    
    .mobile-fullmenu {
        width: 90%;
        max-width: none;
    }
    
    .mobile-coin-item {
        padding: 8px;
    }
    
    .mobile-coin-item .coin-icon {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
    
    .coin-details .coin-name {
        font-size: 0.7rem;
    }
    
    .coin-details .coin-price {
        font-size: 0.75rem;
    }
    
    .coin-details .coin-change {
        font-size: 0.7rem;
    }
    
    .price-item .label {
        font-size: 0.65rem;
    }
    
    .price-item .price {
        font-size: 0.75rem;
    }
}
</style>

<!-- jQuery (그누보드 필수) -->
<script src="<?php echo G5_JS_URL ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER; ?>"></script>

<!-- 추가 메타 정보 -->
<?php
if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
</head>
<body>
<?php
if ($is_member) { // 회원이라면 로그인 중이라는 메시지 출력
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";
}
?>