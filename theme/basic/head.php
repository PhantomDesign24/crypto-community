<?php
/*
 * 파일명: head.php
 * 위치: /
 * 기능: 크립토 사이트 메인 헤더 (네비게이션 포함)
 * 작성일: 2025-01-11
 */

if (!defined('_GNUBOARD_')) exit;

// 상단 파일 include
include_once(G5_PATH.'/head.sub.php');
?>

<!-- =================================== -->
<!-- 상단 정보바 -->
<!-- =================================== -->
<div class="top-info-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-clock"></i> 24시간 고객센터: 1588-0000
            </div>
            <div>
                <?php if($is_member) { ?>
                    <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">
                        <i class="bi bi-person"></i> <?php echo $member['mb_nick'] ?>님
                    </a>
                    <?php if($is_admin) { ?>
                    <span class="mx-2">|</span>
                    <a href="<?php echo G5_ADMIN_URL ?>">
                        <i class="bi bi-gear"></i> 관리자
                    </a>
                    <?php } ?>
                    <span class="mx-2">|</span>
                    <a href="<?php echo G5_BBS_URL ?>/logout.php">
                        <i class="bi bi-box-arrow-right"></i> 로그아웃
                    </a>
                <?php } else { ?>
                    <a href="<?php echo G5_BBS_URL ?>/login.php">
                        <i class="bi bi-box-arrow-in-right"></i> 로그인
                    </a>
                    <span class="mx-2">|</span>
                    <a href="<?php echo G5_BBS_URL ?>/register.php">
                        <i class="bi bi-person-plus"></i> 회원가입
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- =================================== -->
<!-- 메인 헤더 -->
<!-- =================================== -->
<header class="main-header">
    <!-- 로고 섹션 -->
    <div class="logo-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- 로고 -->
                <div class="col-8 col-lg-3">
                    <a href="<?php echo G5_URL ?>" class="logo">
                        <i class="bi bi-currency-bitcoin"></i>
                        <span>CRYPTO HUB</span>
                    </a>
                </div>
                
                <!-- 마켓 정보 (데스크톱) -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="header-market-info justify-content-center">
                        <!-- 비트코인 정보 -->
                        <div class="market-item">
                            <div class="market-coin">
                                <div class="coin-icon">
                                    <i class="bi bi-currency-bitcoin"></i>
                                </div>
                                <div class="coin-info">
                                    <div class="coin-name">BTC/KRW</div>
                                    <div class="coin-price">₩58,245,000</div>
                                </div>
                                <div class="coin-change positive">
                                    <i class="bi bi-caret-up-fill"></i> +2.45%
                                </div>
                            </div>
                        </div>
                        
                        <!-- USDT 거래 정보 -->
                        <div class="market-item">
                            <div class="usdt-trade-info">
                                <div class="trade-item">
                                    <div class="trade-label">USDT 매수</div>
                                    <div class="trade-price buy">₩1,450</div>
                                </div>
                                <div class="trade-item">
                                    <div class="trade-label">USDT 매도</div>
                                    <div class="trade-price sell">₩1,430</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 헤더 액션 (데스크톱) -->
                <div class="col-4 col-lg-3 text-end">
                    <div class="header-actions justify-content-end">
                        <a href="<?php echo G5_URL ?>/event.php" class="header-cta d-none d-lg-inline-block">
                            <i class="bi bi-gift"></i> 이벤트 참여
                        </a>
                        <button class="mobile-menu-toggle d-lg-none" onclick="toggleMobileMenu()">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- 네비게이션 (데스크톱 전용) -->
<nav class="main-nav sticky-nav d-none d-lg-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav-menu desktop-menu">
                <li><a href="<?php echo G5_URL ?>">
                    <i class="bi bi-house-door"></i> 홈
                </a></li>
                <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=listing">
                    <i class="bi bi-megaphone"></i> 신규상장소식
                </a></li>
                <li><a href="<?php echo G5_URL ?>/otc.php">
                    <i class="bi bi-currency-exchange"></i> OTC장외거래
                </a></li>
                <li><a href="<?php echo G5_URL ?>/event.php">
                    <i class="bi bi-gift"></i> 이벤트
                </a></li>
                <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=community">
                    <i class="bi bi-people"></i> 커뮤니티
                </a></li>
                <li><a href="<?php echo G5_URL ?>/consult.php">
                    <i class="bi bi-headset"></i> 상담신청
                </a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- 모바일 전체 메뉴 -->
<div class="mobile-fullmenu" id="mobileFullMenu">
    <div class="mobile-menu-header">
        <h3>전체메뉴</h3>
        <button class="close-menu" onclick="toggleMobileMenu()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <!-- 모바일 로그인 정보 -->
    <div class="mobile-user-info">
        <?php if($is_member) { ?>
            <div class="user-profile">
                <i class="bi bi-person-circle"></i>
                <div>
                    <strong><?php echo $member['mb_nick'] ?>님</strong>
                    <p>환영합니다!</p>
                </div>
            </div>
            <div class="user-actions">
                <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">
                    <i class="bi bi-gear"></i> 정보수정
                </a>
                <a href="<?php echo G5_BBS_URL ?>/logout.php">
                    <i class="bi bi-box-arrow-right"></i> 로그아웃
                </a>
            </div>
        <?php } else { ?>
            <div class="login-prompt">
                <p>로그인하고 더 많은 혜택을 받으세요!</p>
                <div class="login-buttons">
                    <a href="<?php echo G5_BBS_URL ?>/login.php" class="btn-login">로그인</a>
                    <a href="<?php echo G5_BBS_URL ?>/register.php" class="btn-register">회원가입</a>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <!-- 모바일 시세 정보 -->
    <div class="mobile-market-widget">
        <h4>실시간 시세</h4>
        <div class="mobile-market-items">
            <div class="mobile-coin-item">
                <div class="coin-icon"><i class="bi bi-currency-bitcoin"></i></div>
                <div class="coin-details">
                    <span class="coin-name">BTC/KRW</span>
                    <span class="coin-price">₩58,245,000</span>
                    <span class="coin-change positive"><i class="bi bi-caret-up-fill"></i> +2.45%</span>
                </div>
            </div>
            <div class="mobile-usdt-item">
                <div class="price-item">
                    <span class="label">USDT 매수</span>
                    <span class="price buy">₩1,450</span>
                </div>
                <div class="price-item">
                    <span class="label">USDT 매도</span>
                    <span class="price sell">₩1,430</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 모바일 메뉴 -->
    <nav class="mobile-nav-menu">
        <ul>
            <li><a href="<?php echo G5_URL ?>">
                <i class="bi bi-house-door"></i> 홈
            </a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=listing">
                <i class="bi bi-megaphone"></i> 신규상장소식
            </a></li>
            <li><a href="<?php echo G5_URL ?>/otc.php">
                <i class="bi bi-currency-exchange"></i> OTC장외거래
            </a></li>
            <li><a href="<?php echo G5_URL ?>/event.php">
                <i class="bi bi-gift"></i> 이벤트
            </a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=community">
                <i class="bi bi-people"></i> 커뮤니티
            </a></li>
            <li><a href="<?php echo G5_URL ?>/consult.php">
                <i class="bi bi-headset"></i> 상담신청
            </a></li>
        </ul>
    </nav>
    
    <!-- 고객센터 정보 -->
    <div class="mobile-support-info">
        <p><i class="bi bi-telephone"></i> 고객센터: 1588-0000</p>
        <p><i class="bi bi-clock"></i> 평일 09:00 - 18:00</p>
    </div>
</div>

<!-- 모바일 메뉴 오버레이 -->
<div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>

<!-- =================================== -->
<!-- 메인 컨텐츠 시작 -->
<!-- =================================== -->
<main class="main-content">

<script>
// =================================== 
// 모바일 메뉴 토글
// =================================== 
function toggleMobileMenu() {
    var fullMenu = document.getElementById('mobileFullMenu');
    var overlay = document.getElementById('mobileOverlay');
    var body = document.body;
    
    fullMenu.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // 스크롤 방지
    if (fullMenu.classList.contains('active')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
}

// ESC 키로 메뉴 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var fullMenu = document.getElementById('mobileFullMenu');
        if (fullMenu.classList.contains('active')) {
            toggleMobileMenu();
        }
    }
});
</script>