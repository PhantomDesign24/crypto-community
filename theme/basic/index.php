<?php
/*
 * 파일명: index.php
 * 위치: /
 * 기능: 크립토 사이트 메인 페이지 콘텐츠
 * 작성일: 2025-01-11
 */

include_once('./_common.php');

// 페이지 제목
$g5['title'] = '국내 최고의 암호화폐 커뮤니티';

include_once(G5_PATH.'/head.php');
?>

<!-- =================================== -->
<!-- 페이지 전용 스타일 -->
<!-- =================================== -->
<style>
/* =================================== -->
<!-- 마케팅 소개 섹션 스타일 -->
<!-- =================================== -->
/* 마케팅 섹션 */
.marketing-intro {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    position: relative;
    overflow: hidden;
}


/* 텍스트 그라디언트 */
.text-gradient {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* 기능 리스트 */
.feature-list {
    margin-top: 30px;
}

.feature-item {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.feature-item:hover {
    transform: translateX(10px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.feature-item i {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.feature-item h5 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--primary-color);
}

.feature-item p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

/* 통계 그리드 */
.marketing-stats {
    position: relative;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
}

.stat-box {
    background: white;
    padding: 40px 30px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.stat-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.stat-box::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59,130,246,0.05) 0%, transparent 70%);
    animation: pulse-slow 4s ease-in-out infinite;
}

@keyframes pulse-slow {
    0%, 100% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1); opacity: 1; }
}

.stat-icon-wrap {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(139,92,246,0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--accent-color);
}

.stat-box h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.stat-box p {
    color: var(--text-secondary);
    font-size: 1rem;
    margin: 0;
}

/* =================================== -->
<!-- 히어로 섹션 스타일 -->
<!-- =================================== -->
/* 히어로 컨테이너 */
.hero-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
    color: white;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
    animation: pulse 20s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.3; }
    50% { transform: scale(1.1); opacity: 0.5; }
}

/* 히어로 콘텐츠 */
.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    animation: fadeInUp 0.8s ease-out;
}

.hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 30px;
    animation: fadeInUp 0.8s ease-out 0.2s;
    animation-fill-mode: both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =================================== -->
<!-- 통계 카드 스타일 -->
<!-- =================================== -->
/* 통계 카드 컨테이너 */
.stats-cards {
    margin-top: -50px;
    position: relative;
    z-index: 10;
}

/* 통계 카드 */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
}

.stat-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
.stat-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }
.stat-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
.stat-icon.orange { background: rgba(245,158,11,0.1); color: #f59e0b; }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

/* =================================== -->
<!-- 상장 소식 스타일 -->
<!-- =================================== -->
/* 상장 카드 */
.listing-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    height: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.listing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.listing-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    position: relative;
    overflow: hidden;
}

.listing-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.listing-card:hover .listing-image img {
    transform: scale(1.05);
}

.exchange-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.badge-upbit { background: #093687; }
.badge-bithumb { background: #f89e1b; }
.badge-coinone { background: #0066cc; }
.badge-korbit { background: #4b79d8; }

.listing-content {
    padding: 20px;
}

.listing-content h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 10px;
    line-height: 1.4;
}

.listing-content .text-muted {
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* 모바일 최적화 */
@media (max-width: 768px) {
    .listing-card {
        margin-bottom: 15px;
    }
    
    .listing-image {
        height: 150px;
    }
    
    .listing-content {
        padding: 15px;
    }
    
    .listing-content h5 {
        font-size: 0.95rem;
    }
    
    .exchange-badge {
        padding: 3px 10px;
        font-size: 0.75rem;
        top: 10px;
        left: 10px;
    }
}

/* =================================== -->
<!-- 이벤트 섹션 스타일 -->
<!-- =================================== -->
/* 이벤트 카드 */
.event-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
}

.event-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* 이벤트 카드 타입별 스타일 */
.event-card.card-hot {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.event-card.card-new {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.event-card.card-special {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.event-card.card-hot .event-content h4,
.event-card.card-new .event-content h4,
.event-card.card-special .event-content h4 {
    color: white;
}

.event-card.card-hot .text-muted,
.event-card.card-new .text-muted,
.event-card.card-special .text-muted {
    color: rgba(255,255,255,0.8) !important;
}


@keyframes badge-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* 이벤트 아이콘 애니메이션 */
.event-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    display: inline-block;
    animation: icon-float 3s ease-in-out infinite;
}

@keyframes icon-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.event-card:nth-child(2) .event-icon {
    animation-delay: 0.5s;
}

.event-card:nth-child(3) .event-icon {
    animation-delay: 1s;
}

/* 이벤트 콘텐츠 */
.event-content {
    padding: 30px;
    position: relative;
    z-index: 1;
}

.event-reward {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    margin: 15px 0;
    position: relative;
}

.event-card.card-hot .event-reward,
.event-card.card-new .event-reward,
.event-card.card-special .event-reward {
    color: white;
}

/* 그라디언트 버튼 (이벤트 카드용) */
.event-card .btn-gradient {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
}

.event-card .btn-gradient:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* 기본 이벤트 카드 버튼 */
.event-card:not(.card-hot):not(.card-new):not(.card-special) .btn-gradient {
    background: linear-gradient(135deg, var(--accent-color) 0%, #2563eb 100%);
    color: white;
}

/* 이벤트 카드 장식 요소 */
.event-decoration {
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    top: -100px;
    right: -100px;
    animation: decoration-rotate 20s linear infinite;
}

@keyframes decoration-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}


/* =================================== -->
<!-- 섹션 공통 스타일 -->
<!-- =================================== -->
/* 섹션 컨테이너 */
.section {
    padding: 80px 0;
}

.section-bg {
    background: #f8fafc;
}

/* 섹션 헤더 */
.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.section-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
}

/* =================================== -->
<!-- 버튼 스타일 -->
<!-- =================================== -->
/* 그라디언트 버튼 */
.btn-gradient {
    background: linear-gradient(135deg, var(--accent-color) 0%, #2563eb 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-block;
    text-decoration: none;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    color: white;
}

/* 아웃라인 버튼 */
.btn-outline {
    background: transparent;
    color: var(--accent-color);
    border: 2px solid var(--accent-color);
    padding: 10px 28px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-block;
    text-decoration: none;
}

.btn-outline:hover {
    background: var(--accent-color);
    color: white;
}
/* =================================== -->
<!-- 코인 시세 위젯 스타일 -->
<!-- =================================== -->
/* 위젯 래퍼 */
.crypto-widget-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0;
}

/* 위젯 컨테이너 스타일 오버라이드 */
.crypto-widget-wrapper .crypto-widget-container {
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.crypto-widget-wrapper .crypto-widget-container:hover {
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}

/* 위젯 헤더 커스터마이징 */
.crypto-widget-wrapper .crypto-widget-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 20px;
}

.crypto-widget-wrapper .crypto-widget-header h1 {
    font-size: 1.25rem;
}

/* 위젯 탭 스타일 통합 */
.crypto-widget-wrapper .crypto-widget-tab.crypto-widget-active {
    color: var(--accent-color);
    border-bottom-color: var(--accent-color);
}

/* 위젯 버튼 스타일 통합 */
.crypto-widget-wrapper .crypto-widget-settings-toggle:hover,
.crypto-widget-wrapper .crypto-widget-collapse-toggle:hover {
    background: var(--accent-color);
    color: white;
}

/* 반응형 조정 */
@media (max-width: 768px) {
    .crypto-widget-wrapper {
        margin: 0 -10px;
    }
    
    .crypto-widget-wrapper .crypto-widget-container {
        border-radius: 0;
        box-shadow: none;
        border-left: none;
        border-right: none;
    }
}/* =================================== -->
<!-- 코인 시세 위젯 통합 스타일 -->
<!-- =================================== -->
/* 위젯 래퍼 */
.coin-widget-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0;
}

/* 위젯 컨테이너 커스터마이징 */
.coin-widget-wrapper .crypto-widget-container {
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.coin-widget-wrapper .crypto-widget-container:hover {
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}

/* 위젯 헤더 스타일 통합 */
.coin-widget-wrapper .crypto-widget-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 20px;
}

.coin-widget-wrapper .crypto-widget-header h1 {
    font-size: 1.25rem;
}

/* 위젯 탭 색상 통합 */
.coin-widget-wrapper .crypto-widget-tab.crypto-widget-active {
    color: var(--accent-color);
    border-bottom-color: var(--accent-color);
}

/* 위젯 버튼 스타일 통합 */
.coin-widget-wrapper .crypto-widget-settings-toggle:hover,
.coin-widget-wrapper .crypto-widget-collapse-toggle:hover {
    background: var(--accent-color);
    color: white;
}

/* 위젯 테이블 스타일 조정 */
.coin-widget-wrapper .crypto-widget-plus {
    color: #ef4444;
}

.coin-widget-wrapper .crypto-widget-minus {
    color: #3b82f6;
}

/* 반응형 조정 */
@media (max-width: 768px) {
    .coin-widget-wrapper {
        margin: 0 -10px;
    }
    
    .coin-widget-wrapper .crypto-widget-container {
        border-radius: 0;
        box-shadow: none;
        border-left: none;
        border-right: none;
    }
}

@media (max-width: 991px) {
    .marketing-content {
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .feature-item {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .section {
        padding: 60px 0;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .stat-box {
        padding: 30px 20px;
    }
    
    .stat-box h3 {
        font-size: 2rem;
    }
    
    .marketing-content h2 {
        font-size: 1.75rem;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
        gap: 15px !important;
    }
    
    .btn-gradient, .btn-outline {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 60px 0;
    }
    
    .stats-cards {
        margin-top: -30px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .listing-card, .event-card {
    }
    .listing-content h5 { font-size:1rem; }
	.text-muted { font-size:0.75em; }
	.listing-image {
    width: 100%;
    height: auto;
    object-fit: cover;
    position: relative;
    overflow: hidden;
}
.lead { font-size:1rem; }
.feature-item p { font-size:min(14px, 3vw); }
    .ticker-content {
        animation-duration: 40s;
    }
    
    .feature-item {
        padding: 15px;
    }
    
    .stats-grid {
        gap: 15px;
    }
}
</style>

<!-- =================================== -->
<!-- 히어로 섹션 -->
<!-- =================================== -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title">암호화폐 투자의 새로운 기준</h1>
            <p class="hero-subtitle">최신 상장 정보와 독점 이벤트를 한눈에</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?php echo G5_BBS_URL ?>/register.php" class="btn-gradient">
                    지금 시작하기 <i class="bi bi-arrow-right"></i>
                </a>
                <a href="#features" class="btn-outline">
                    더 알아보기
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =================================== -->
<!-- 통계 카드 섹션 -->
<!-- =================================== -->
<div class="container stats-cards">
    <div class="row g-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">
                    <?php
                    // 전체 회원 수 + 3472명 추가
                    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_leave_date = ''";
                    $row = sql_fetch($sql);
                    $total_members = $row['cnt'] + 3472;
                    echo number_format($total_members);
                    ?>
                </div>
                <div class="text-muted">활성 회원</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-currency-bitcoin"></i>
                </div>
                <div class="stat-number">
                    <?php
                    // 상장 정보 수
                    $sql = "SELECT COUNT(*) as cnt FROM g5_listing_news";
                    $row = sql_fetch($sql);
                    echo number_format($row['cnt']);
                    ?>
                </div>
                <div class="text-muted">상장 정보</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="bi bi-gift-fill"></i>
                </div>
                <div class="stat-number">
                    <?php
                    // 진행중 이벤트 수
                    $sql = "SELECT COUNT(*) as cnt FROM g5_event WHERE ev_status = 'ongoing'";
                    $row = sql_fetch($sql);
                    echo number_format($row['cnt']);
                    ?>
                </div>
                <div class="text-muted">진행중 이벤트</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-number">
                    <?php
                    // 누적 에어드랍 금액 (예시: 포인트 총합을 원화로 환산)
                    // 실제로는 에어드랍 관리 테이블에서 계산해야 함
                    $sql = "SELECT SUM(po_point) as total FROM {$g5['point_table']} WHERE po_point > 0 AND po_rel_table = 'airdrop'";
                    $row = sql_fetch($sql);
                    $total_airdrop = $row['total'] ? $row['total'] : 0;
                    
                    // 금액 포맷팅
                    if($total_airdrop >= 100000000) { // 1억 이상
                        echo '₩' . number_format($total_airdrop / 100000000, 1) . '억';
                    } else if($total_airdrop >= 10000000) { // 천만원 이상
                        echo '₩' . number_format($total_airdrop / 10000000) . '천만';
                    } else if($total_airdrop >= 10000) { // 만원 이상
                        echo '₩' . number_format($total_airdrop / 10000) . '만';
                    } else {
                        echo '₩' . number_format($total_airdrop);
                    }
                    ?>
                </div>
                <div class="text-muted">누적 에어드랍</div>
            </div>
        </div>
    </div>
</div><!-- =================================== -->
<!-- 실시간 코인 시세 섹션 -->
<!-- =================================== -->
<section class="section" style="background: #f8fafc;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">실시간 코인 시세</h2>
            <p class="section-subtitle">국내외 주요 거래소의 실시간 가격 정보를 한눈에</p>
        </div>
        
        <!-- 코인 시세 위젯 -->
        <div class="coin-widget-wrapper">
            <?php include_once(G5_PATH.'/coin.php'); ?>
        </div>
    </div>
</section>


<!-- =================================== -->
<!-- 신규 상장 소식 섹션 -->
<!-- =================================== -->
<section class="ln-section">
    <div class="container">
        <div class="ln-header">
            <div class="ln-header-content">
                <h2 class="ln-title">
                    <span class="ln-title-icon">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                    </span>
                    최신 상장 소식
                </h2>
                <p class="ln-subtitle">실시간 업데이트되는 거래소 상장 정보</p>
            </div>
            <a href="<?php echo G5_URL ?>/listing_news.php" class="ln-view-all">
                전체보기 <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="ln-grid">
            <?php
            // g5_listing_news 테이블에서 최신 상장 정보 가져오기
            $sql = "SELECT * FROM g5_listing_news 
                    ORDER BY ln_date DESC, ln_id DESC 
                    LIMIT 6";
            $result = sql_query($sql);
            
            // 거래소 정보
            $exchanges = array(
                'upbit' => array('name' => '업비트', 'color' => '#093687', 'bg' => 'rgba(9,54,135,0.1)'),
                'bithumb' => array('name' => '빗썸', 'color' => '#f89e1b', 'bg' => 'rgba(248,158,27,0.1)'),
                'coinone' => array('name' => '코인원', 'color' => '#0066cc', 'bg' => 'rgba(0,102,204,0.1)'),
                'korbit' => array('name' => '코빗', 'color' => '#4b79d8', 'bg' => 'rgba(75,121,216,0.1)'),
                'bybit' => array('name' => '바이비트', 'color' => '#FFD748', 'bg' => 'rgba(255,215,72,0.1)'),
                'okx' => array('name' => 'OKX', 'color' => '#000000', 'bg' => 'rgba(0,0,0,0.1)')
            );
            
            $count = 0;
            while($row = sql_fetch_array($result)) {
                $exchange_info = $exchanges[$row['ln_exchange']] ?? array(
                    'name' => $row['ln_exchange'], 
                    'color' => '#6b7280', 
                    'bg' => 'rgba(107,114,128,0.1)'
                );
                
                // 썸네일 이미지 처리
                $thumb_url = '';
                if($row['ln_logo'] && file_exists(G5_DATA_PATH.'/listing/'.$row['ln_logo'])) {
                    $thumb_url = G5_DATA_URL.'/listing/'.$row['ln_logo'];
                } else {
                    // 심볼 첫 글자로 아이콘 생성
                    $symbol_initial = substr($row['ln_symbol'], 0, 1);
                }
                
                // 날짜 처리
                $date = new DateTime($row['ln_date']);
                $today = new DateTime();
                $diff = $today->diff($date)->days;
                
                if($diff == 0) {
                    $date_text = '오늘';
                    $date_detail = $date->format('H:i');
                } elseif($diff == 1) {
                    $date_text = '어제';
                    $date_detail = $date->format('H:i');
                } elseif($diff < 7) {
                    $date_text = $diff . '일 전';
                    $date_detail = $date->format('m.d');
                } else {
                    $date_text = $date->format('m.d');
                    $date_detail = $date->format('Y');
                }
                
                $count++;
            ?>
            <div class="ln-item" onclick="window.open('<?php echo $row['ln_notice_url'] ? $row['ln_notice_url'] : '#'; ?>', '_blank');">
                <div class="ln-item-header">
                    <div class="ln-exchange" style="background: <?php echo $exchange_info['bg']; ?>; color: <?php echo $exchange_info['color']; ?>">
                        <?php echo $exchange_info['name']; ?>
                    </div>
                    <div class="ln-date-wrap">
                        <span class="ln-date"><?php echo $date_text; ?></span>
                        <span class="ln-date-detail"><?php echo $date_detail; ?></span>
                    </div>
                </div>
                
                <div class="ln-item-body">
                    <div class="ln-coin-icon" style="background: <?php echo $exchange_info['color']; ?>">
                        <?php if($thumb_url) { ?>
                            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $row['ln_symbol']; ?>">
                        <?php } else { ?>
                            <span><?php echo $symbol_initial ?? 'C'; ?></span>
                        <?php } ?>
                    </div>
                    
                    <div class="ln-coin-info">
                        <h3 class="ln-coin-name"><?php echo $row['ln_name_kr']; ?></h3>
                        <div class="ln-coin-meta">
                            <span class="ln-symbol"><?php echo $row['ln_symbol']; ?></span>
                            <?php if($row['ln_type']) { ?>
                            <span class="ln-market"><?php echo $row['ln_type']; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <?php if($diff <= 1) { ?>
                <div class="ln-new-badge">
                    <span class="ln-new-text">NEW</span>
                    <span class="ln-new-dot"></span>
                </div>
                <?php } ?>
            </div>
            <?php 
            }
            
            // 데이터가 부족한 경우 플레이스홀더
            if($count < 6) {
                // 랜덤 아이콘 배열 (Bootstrap Icons)
                $random_icons = [
                    'bi-currency-bitcoin',
                    'bi-currency-exchange', 
                    'bi-graph-up-arrow',
                    'bi-lightning-charge-fill',
                    'bi-star-fill',
                    'bi-gem',
                    'bi-trophy-fill',
                    'bi-fire'
                ];
                $placeholder_exchanges = ['upbit', 'bithumb', 'coinone', 'korbit'];
                
                for($i = $count; $i < 6; $i++) {
                    $random_exchange = $placeholder_exchanges[array_rand($placeholder_exchanges)];
                    $exchange_info = $exchanges[$random_exchange];
                    $random_icon = $random_icons[array_rand($random_icons)];
            ?>
            <div class="ln-item ln-placeholder">
                <div class="ln-item-header">
                    <div class="ln-exchange" style="background: <?php echo $exchange_info['bg']; ?>; color: <?php echo $exchange_info['color']; ?>">
                        <?php echo $exchange_info['name']; ?>
                    </div>
                    <div class="ln-date-wrap">
                        <span class="ln-date">곧 공개</span>
                        <span class="ln-date-detail">-</span>
                    </div>
                </div>
                <div class="ln-item-body">
                    <div class="ln-coin-icon ln-placeholder-icon">
                        <i class="<?php echo $random_icon; ?>"></i>
                        <div class="ln-placeholder-pulse"></div>
                    </div>
                    <div class="ln-coin-info">
                        <h3 class="ln-coin-name">상장 예정</h3>
                        <div class="ln-coin-meta">
                            <span class="ln-symbol">SOON</span>
                            <span class="ln-market">준비중</span>
                        </div>
                    </div>
                </div>
                <div class="ln-coming-badge">
                    <i class="bi bi-clock"></i>
                    <span>COMING</span>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<style>
/* ===================================
   섹션 기본 스타일
   =================================== */
.ln-section {
    padding: 40px 0;
    background: #f9fafb;
}

.ln-section .container {
    max-width: 1200px;
}

/* ===================================
   헤더 스타일
   =================================== */
.ln-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.ln-header-content {
    flex: 1;
}

.ln-title {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 800;
    color: #111827;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ln-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 12px;
    color: white;
    font-size: 1.2rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.ln-subtitle {
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    color: #6b7280;
    margin: 0;
}

.ln-view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    background: #1f2937;
    color: white;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s;
}

.ln-view-all:hover {
    background: #111827;
    transform: translateX(-2px);
    color: white;
}

.ln-view-all i {
    transition: transform 0.3s;
}

.ln-view-all:hover i {
    transform: translateX(3px);
}

/* ===================================
   그리드 레이아웃
   =================================== */
.ln-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

@media (min-width: 992px) {
    .ln-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .ln-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

@media (max-width: 767px) {
    .ln-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

/* ===================================
   아이템 카드 스타일
   =================================== */
.ln-item {
    background: white;
    border-radius: 16px;
    padding: 20px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.ln-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    border-color: transparent;
}

.ln-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transform: scaleX(0);
    transition: transform 0.3s;
}

.ln-item:hover::before {
    transform: scaleX(1);
}

/* 플레이스홀더 */
.ln-placeholder {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 1px dashed #e5e7eb;
}

.ln-placeholder:hover {
    transform: none;
    box-shadow: none;
    border-color: #e5e7eb;
}

.ln-placeholder::before {
    display: none;
}

/* ===================================
   아이템 헤더
   =================================== */
.ln-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.ln-exchange {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 12px;
}

.ln-date-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: clamp(0.75rem, 2vw, 0.875rem);
}

.ln-date {
    color: #374151;
    font-weight: 600;
}

.ln-date-detail {
    color: #9ca3af;
    font-weight: 400;
}

/* ===================================
   아이템 바디
   =================================== */
.ln-item-body {
    display: flex;
    align-items: center;
    gap: 16px;
}

.ln-coin-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: white;
    font-weight: 800;
    font-size: 1.25rem;
    position: relative;
    overflow: hidden;
}

.ln-coin-icon img {
    width: 60%;
    height: 60%;
    object-fit: contain;
}

.ln-coin-icon span {
    position: relative;
    z-index: 1;
}

.ln-coin-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0));
}

.ln-coin-info {
    flex: 1;
    min-width: 0;
}

.ln-coin-name {
    font-size: clamp(1rem, 3vw, 1.125rem);
    font-weight: 700;
    color: #111827;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ln-coin-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.ln-symbol {
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    font-weight: 600;
    color: #4b5563;
}

.ln-market {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    color: #6b7280;
    padding: 2px 8px;
    background: #f3f4f6;
    border-radius: 8px;
}

/* ===================================
   NEW 배지
   =================================== */
.ln-new-badge {
    position: absolute;
    bottom: 16px;
    right: 16px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.ln-new-text {
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 10px;
    letter-spacing: 0.05em;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

.ln-new-dot {
    width: 6px;
    height: 6px;
    background: #ef4444;
    border-radius: 50%;
    animation: blink 1.5s infinite;
    box-shadow: 0 0 4px rgba(239, 68, 68, 0.5);
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* ===================================
   COMING 배지
   =================================== */
.ln-coming-badge {
    position: absolute;
    bottom: 16px;
    right: 16px;
    display: flex;
    align-items: center;
    gap: 4px;
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    font-size: 0.625rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 10px;
    letter-spacing: 0.05em;
    backdrop-filter: blur(4px);
}

.ln-coming-badge i {
    font-size: 0.75rem;
}

/* ===================================
   플레이스홀더 스타일
   =================================== */
.ln-placeholder-icon {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%) !important;
    position: relative;
    overflow: visible;
}

.ln-placeholder-icon i {
    font-size: 1.5rem;
    color: #9ca3af;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

.ln-placeholder-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.5);
    animation: pulse-ring 2s infinite;
}

@keyframes pulse-ring {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.5;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.3);
        opacity: 0;
    }
}

/* ===================================
   모바일 반응형
   =================================== */
@media (max-width: 768px) {
    .ln-section {
        padding: 30px 0;
    }
    
    .ln-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .ln-view-all {
        align-self: flex-end;
        padding: 6px 16px;
        font-size: 0.8125rem;
    }
    
    .ln-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .ln-item {
        padding: 16px;
    }
    
    .ln-item-header {
        margin-bottom: 12px;
    }
    
    .ln-coin-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .ln-new-badge {
        bottom: 12px;
        right: 12px;
		display:none;

    }
    
    .ln-new-text {
        padding: 2px 6px;
        font-size: 0.5625rem;
		display:none;
    }
    
    .ln-new-dot {
        width: 4px;
        height: 4px;
		display:none;
    }
    
    .ln-coming-badge {
        bottom: 12px;
        right: 12px;
        padding: 3px 8px;
        font-size: 0.5625rem;
		display:none;
    }
    
    .ln-date-wrap {
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
		line-height:1;
    }
    
    .ln-date-detail {
        font-size: 0.625rem;
    }
}

@media (max-width: 480px) {
    .ln-section {
        padding: 20px 0;
    }
    
    .ln-grid {
        gap: 10px;
    }
    
    .ln-item {
        padding: 14px;
    }
    
    .ln-item-body {
        gap: 12px;
    }
    
    .ln-coin-meta {
        gap: 6px;
    }
    
    .ln-market {
        padding: 1px 6px;
    }
}

/* 작은 화면에서 1열 */
@media (max-width: 360px) {
    .ln-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<!-- =================================== -->
<!-- 코인 지급 현황 전광판 -->
<!-- =================================== -->
<div class="ticker-section">
    <div class="ticker-title">
        <i class="bi bi-megaphone"></i> 실시간 지급
    </div>
    <div class="ticker-wrapper">
        <div class="ticker-content" id="tickerContent">
            <?php
            // DB에서 최근 지급 내역 가져오기
            $ticker_sql = "SELECT a.*, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount, m.mb_nick 
                          FROM g5_event_apply a 
                          JOIN g5_event e ON a.ev_id = e.ev_id 
                          JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
                          WHERE a.ea_status = 'paid' 
                          ORDER BY a.ea_pay_datetime DESC 
                          LIMIT 20";
            $ticker_result = sql_query($ticker_sql);
            
            $ticker_items = array();
            while($row = sql_fetch_array($ticker_result)) {
                // 닉네임 마스킹
                $masked_nick = mb_substr($row['mb_nick'], 0, 1) . str_repeat('*', mb_strlen($row['mb_nick']) - 2) . mb_substr($row['mb_nick'], -1);
                
                $ticker_items[] = array(
                    'name' => $masked_nick,
                    'amount' => $row['ev_coin_amount'] . ' ' . $row['ev_coin_symbol'],
                    'event' => $row['ev_subject'],
                    'time' => $row['ea_pay_datetime']
                );
            }
            
            // 데이터가 없으면 샘플 표시
            if(empty($ticker_items)) {
                $ticker_items = [
                    ['name' => '홍*동', 'amount' => '100 USDT', 'event' => '신규가입 이벤트', 'time' => date('Y-m-d H:i:s')],
                    ['name' => '김*수', 'amount' => '50 BTC', 'event' => '친구추천 이벤트', 'time' => date('Y-m-d H:i:s')],
                    ['name' => '이*희', 'amount' => '200 ETH', 'event' => '거래인증 이벤트', 'time' => date('Y-m-d H:i:s')]
                ];
            }
            
            // 항목 출력 (중복 제거)
            foreach($ticker_items as $item) {
            ?>
            <span class="ticker-item" data-time="<?php echo $item['time']; ?>">
                <i class="bi bi-check-circle-fill"></i>
                <span class="ticker-name"><?php echo $item['name']; ?>님</span>
                <span class="ticker-amount"><?php echo $item['amount']; ?></span>
                <span class="ticker-event"><?php echo $item['event']; ?></span>
                <span class="ticker-status">지급완료</span>
            </span>
            <?php 
            }
            ?>
        </div>
    </div>
</div>

<!-- AJAX 업데이트 스크립트 제거 - 페이지 새로고침 시에만 업데이트 -->
<script>
// 전광판 애니메이션만 유지
document.addEventListener('DOMContentLoaded', function() {
    // 새 항목 하이라이트 효과 (선택사항)
    const firstItems = document.querySelectorAll('.ticker-item');
    if(firstItems.length > 0) {
        firstItems[0].classList.add('new-item');
    }
});
</script>

<style>
/* 전광판 스타일 */
.ticker-section {
    background: #1a1a2e;
    color: white;
    overflow: hidden;
    position: relative;
    height: 50px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.ticker-title {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    background: #0f1729;
    padding: 0 25px;
    z-index: 10;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.ticker-title i {
    color: #fbbf24;
    font-size: 18px;
}

.ticker-wrapper {
    overflow: hidden;
    position: relative;
    flex: 1;
    margin-left: 150px; /* ticker-title 너비 */
}

.ticker-content {
    display: flex;
    white-space: nowrap;
    animation: scroll-left 30s linear infinite;
}

/* 무한 스크롤을 위한 복제 */
.ticker-content::after {
    content: attr(data-content);
    position: absolute;
    left: 100%;
}

@keyframes scroll-left {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

.ticker-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0 30px;
    font-size: 14px;
    height: 50px;
}

.ticker-item i {
    color: #10b981;
    font-size: 16px;
}

.ticker-name {
    font-weight: 600;
    color: #fbbf24;
}

.ticker-amount {
    font-weight: 700;
    color: #10b981;
    font-size: 16px;
}

.ticker-event {
    color: #94a3b8;
}

.ticker-status {
    background: rgba(16, 185, 129, 0.1);
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}


/* 반응형 */
@media (max-width: 768px) {
    .ticker-section {
        height: 40px;
    }
    
    .ticker-title {
        padding: 0 15px;
        font-size: 14px;
    }
    
    .ticker-title i {
        font-size: 16px;
    }
    
    .ticker-wrapper {
        margin-left: 120px;
    }
    
    .ticker-item {
        padding: 0 20px;
        font-size: 12px;
        height: 40px;
    }
    
    .ticker-amount {
        font-size: 14px;
    }
}
</style>
<!-- ===================================
     이벤트 섹션
     =================================== -->
<section class="ev-section">
    <div class="container">
        <div class="ev-header">
            <div class="ev-header-content">
                <h2 class="ev-title">
                    <span class="ev-title-icon">
                        <i class="bi bi-gift-fill"></i>
                    </span>
                    에어드랍 이벤트
                </h2>
                <p class="ev-subtitle">참여만 해도 코인을 받을 수 있는 다양한 이벤트</p>
            </div>
            <a href="<?php echo G5_URL; ?>/event.php" class="ev-view-all">
                전체 이벤트 <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <!-- 이벤트 탭 -->
        <div class="ev-tabs" id="eventTabs">
            <?php
            // 각 상태별 이벤트 개수 조회
            $count_ongoing = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event WHERE ev_status = 'ongoing'")['cnt'];
            $count_scheduled = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event WHERE ev_status = 'scheduled'")['cnt'];
            $count_ended = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event WHERE ev_status = 'ended'")['cnt'];
            ?>
            <button class="ev-tab active" data-status="ongoing">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>진행중</span>
                <span class="ev-tab-count"><?php echo $count_ongoing; ?></span>
            </button>
            <button class="ev-tab" data-status="scheduled">
                <i class="bi bi-hourglass-split"></i>
                <span>예정</span>
                <span class="ev-tab-count"><?php echo $count_scheduled; ?></span>
            </button>
            <button class="ev-tab" data-status="ended">
                <i class="bi bi-check-circle"></i>
                <span>종료</span>
                <span class="ev-tab-count"><?php echo $count_ended; ?></span>
            </button>
        </div>
        
        <!-- 이벤트 그리드 -->
        <div class="ev-grid" id="eventGrid">
            <?php
            // 모든 이벤트 가져오기 (진행중, 예정, 종료 모두)
            $sql = "SELECT * FROM g5_event 
                    WHERE ev_recommend = 1 
                    ORDER BY 
                        CASE 
                            WHEN ev_status = 'ongoing' THEN 1
                            WHEN ev_status = 'scheduled' THEN 2
                            ELSE 3
                        END,
                        ev_id DESC 
                    LIMIT 9";
            $result = sql_query($sql);
            
            $event_count = 0;
            while($row = sql_fetch_array($result)) {
                $remaining_days = floor((strtotime($row['ev_end_date']) - time()) / 86400);
                $event_count++;
                
                // 이벤트 타입별 색상
                $type_colors = [
                    'hot' => ['bg' => '#ef4444', 'light' => 'rgba(239,68,68,0.1)'],
                    'new' => ['bg' => '#3b82f6', 'light' => 'rgba(59,130,246,0.1)'],
                    'special' => ['bg' => '#8b5cf6', 'light' => 'rgba(139,92,246,0.1)']
                ];
                
                // 랜덤 타입 지정 (실제로는 DB에서 가져옴)
                $event_types = array_keys($type_colors);
                $event_type = $event_types[array_rand($event_types)];
                $colors = $type_colors[$event_type];
                
                // 진행중인 이벤트만 기본적으로 표시
                $display_style = ($row['ev_status'] == 'ongoing') ? 'block' : 'none';
            ?>
            <div class="ev-card" data-event-id="<?php echo $row['ev_id']; ?>" data-status="<?php echo $row['ev_status']; ?>" style="display: <?php echo $display_style; ?>">
                <!-- 이벤트 이미지 -->
                <?php if($row['ev_image']) { ?>
                <div class="ev-card-image">
                    <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $row['ev_image']; ?>" alt="<?php echo $row['ev_subject']; ?>">
                    <!-- 이벤트 헤더 오버레이 -->
                    <div class="ev-card-header-overlay">
                        <div class="ev-status <?php echo $row['ev_status']; ?>">
                            <?php if($row['ev_status'] == 'ongoing') { ?>
                                <i class="bi bi-circle-fill"></i> 진행중
                            <?php } else if($row['ev_status'] == 'scheduled') { ?>
                                <i class="bi bi-clock-fill"></i> 예정
                            <?php } else { ?>
                                <i class="bi bi-check-circle-fill"></i> 종료
                            <?php } ?>
                        </div>
                        <?php if($remaining_days <= 3 && $row['ev_status'] == 'ongoing') { ?>
                        <div class="ev-urgent">
                            <i class="bi bi-fire"></i> 곧 종료
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } else { ?>
                <!-- 이벤트 헤더 (이미지 없을 때) -->
                <div class="ev-card-header">
                    <div class="ev-status <?php echo $row['ev_status']; ?>">
                        <?php if($row['ev_status'] == 'ongoing') { ?>
                            <i class="bi bi-circle-fill"></i> 진행중
                        <?php } else if($row['ev_status'] == 'scheduled') { ?>
                            <i class="bi bi-clock-fill"></i> 예정
                        <?php } else { ?>
                            <i class="bi bi-check-circle-fill"></i> 종료
                        <?php } ?>
                    </div>
                    <?php if($remaining_days <= 3 && $row['ev_status'] == 'ongoing') { ?>
                    <div class="ev-urgent">
                        <i class="bi bi-fire"></i> 곧 종료
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                
                <!-- 코인 정보 -->
                <div class="ev-coin-info" style="background: <?php echo $colors['light']; ?>">
                    <div class="ev-coin-icon" style="background: <?php echo $colors['bg']; ?>">
                        <?php if($row['ev_coin_logo']) { ?>
                            <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $row['ev_coin_logo']; ?>" alt="">
                        <?php } else { ?>
                            <span><?php echo substr($row['ev_coin_symbol'], 0, 1); ?></span>
                        <?php } ?>
                    </div>
                    <div class="ev-coin-details">
                        <span class="ev-coin-symbol"><?php echo $row['ev_coin_symbol']; ?></span>
                        <span class="ev-coin-amount"><?php echo $row['ev_coin_amount']; ?></span>
                    </div>
                </div>
                
                <!-- 이벤트 내용 -->
                <div class="ev-card-body">
                    <h4 class="ev-card-title"><?php echo $row['ev_subject']; ?></h4>
                    <p class="ev-card-desc"><?php echo $row['ev_summary']; ?></p>
                    
                    <!-- 진행 정보 -->
                    <div class="ev-progress-info">
                        <div class="ev-progress-text">
                            <span class="ev-participants">
                                <i class="bi bi-people-fill"></i> 
                                <?php echo number_format($row['ev_apply_count'] ?: 0); ?>명 참여중
                            </span>
                            <span class="ev-deadline">
                                <?php if($remaining_days > 0) { ?>
                                    D-<?php echo $remaining_days; ?>
                                <?php } else if($remaining_days == 0) { ?>
                                    <span class="text-danger">오늘 마감</span>
                                <?php } else { ?>
                                    종료됨
                                <?php } ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- 참여 버튼 -->
                <button class="ev-card-action" onclick="viewEvent(<?php echo $row['ev_id']; ?>)">
                    <span>지금 참여하기</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
            <?php 
            }
            
            // 데이터가 부족한 경우 플레이스홀더
            if($event_count < 6) {
                for($i = $event_count; $i < 6; $i++) {
            ?>
            <div class="ev-card ev-placeholder" data-status="scheduled" style="display: none;">
                <!-- 플레이스홀더 이미지 영역 -->
                <div class="ev-card-image ev-placeholder-image">
                    <div class="ev-placeholder-icon-wrapper">
                        <i class="bi bi-image"></i>
                    </div>
                    <div class="ev-card-header-overlay">
                        <div class="ev-status scheduled">
                            <i class="bi bi-hourglass"></i> 준비중
                        </div>
                    </div>
                </div>
                
                <div class="ev-coin-info">
                    <div class="ev-coin-icon ev-placeholder-icon">
                        <i class="bi bi-question-lg"></i>
                    </div>
                    <div class="ev-coin-details">
                        <span class="ev-coin-symbol">SOON</span>
                        <span class="ev-coin-amount">???</span>
                    </div>
                </div>
                
                <div class="ev-card-body">
                    <h4 class="ev-card-title">새로운 이벤트 준비중</h4>
                    <p class="ev-card-desc">곧 만나보실 수 있습니다</p>
                    
                    <div class="ev-coming-soon">
                        <i class="bi bi-bell"></i>
                        <span>알림 신청하고 놓치지 마세요!</span>
                    </div>
                </div>
                
                <button class="ev-card-action" disabled>
                    <span>Coming Soon</span>
                </button>
            </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<style>
/* ===================================
   섹션 기본 스타일
   =================================== */
.ev-section {
    padding: 60px 0;
    background: #ffffff;
}

/* ===================================
   헤더 스타일
   =================================== */
.ev-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.ev-header-content {
    flex: 1;
}

.ev-title {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 800;
    color: #111827;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ev-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    border-radius: 12px;
    color: white;
    font-size: 1.2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.ev-subtitle {
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    color: #6b7280;
    margin: 0;
}

.ev-view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    background: #1f2937;
    color: white;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s;
}

.ev-view-all:hover {
    background: #111827;
    transform: translateX(-2px);
    color: white;
}

/* ===================================
   탭 스타일
   =================================== */
.ev-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    padding: 6px;
    background: #f3f4f6;
    border-radius: 12px;
    width: fit-content;
}

.ev-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.ev-tab:hover {
    color: #374151;
}

.ev-tab.active {
    background: white;
    color: #1f2937;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.ev-tab i {
    font-size: 1rem;
}

.ev-tab-count {
    background: rgba(0,0,0,0.1);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
}

.ev-tab.active .ev-tab-count {
    background: #3b82f6;
    color: white;
}

/* ===================================
   그리드 레이아웃
   =================================== */
.ev-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
}

@media (min-width: 992px) {
    .ev-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ===================================
   이벤트 카드
   =================================== */
.ev-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    cursor: pointer;
}

.ev-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    border-color: transparent;
}

/* 카드 이미지 */
.ev-card-image {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: #f3f4f6;
}

.ev-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.ev-card:hover .ev-card-image img {
    transform: scale(1.05);
}

/* 카드 헤더 오버레이 (이미지 위) */
.ev-card-header-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0) 100%);
}

.ev-card-header-overlay .ev-status,
.ev-card-header-overlay .ev-urgent {
    backdrop-filter: blur(8px);
    background: rgba(255,255,255,0.9);
}

/* 카드 헤더 (이미지 없을 때) */
.ev-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.ev-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 8px;
}

.ev-status i {
    font-size: 0.5rem;
}

.ev-status.ongoing {
    color: #16a34a;
}

.ev-status.scheduled {
    color: #3b82f6;
}

.ev-status.ended {
    color: #6b7280;
}

.ev-urgent {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #fef3c7;
    color: #d97706;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* 코인 정보 */
.ev-coin-info {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
}

.ev-coin-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 800;
    flex-shrink: 0;
}

.ev-coin-icon img {
    width: 60%;
    height: 60%;
    object-fit: contain;
}

.ev-coin-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.ev-coin-symbol {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
}

.ev-coin-amount {
    font-size: 1.5rem;
    font-weight: 800;
    color: #16a34a;
}

/* 카드 바디 */
.ev-card-body {
    padding: 20px;
    flex: 1;
}

.ev-card-title {
    font-size: clamp(1rem, 3vw, 1.125rem);
    font-weight: 700;
    color: #111827;
    margin: 0 0 8px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ev-card-desc {
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    color: #6b7280;
    margin: 0 0 20px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 진행 정보 */
.ev-progress-info {
    margin-top: auto;
}

.ev-progress-text {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.ev-participants {
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
}

.ev-participants i {
    font-size: 0.875rem;
}

.ev-deadline {
    font-weight: 700;
    color: #374151;
}

.ev-deadline .text-danger {
    color: #ef4444;
}

.ev-limit-status {
    font-weight: 600;
    color: #374151;
}

.ev-limit-status.text-danger {
    color: #ef4444;
}

/* 참여 버튼 */
.ev-card-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 16px;
    background: #1f2937;
    border: none;
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.ev-card-action:hover {
    background: #111827;
}

.ev-card-action:hover i {
    transform: translateX(3px);
}

.ev-card-action i {
    transition: transform 0.3s;
}

/* ===================================
   플레이스홀더 스타일
   =================================== */
.ev-placeholder {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 1px dashed #e5e7eb;
}

.ev-placeholder:hover {
    transform: none;
    box-shadow: none;
    border-color: #e5e7eb;
}

.ev-placeholder-image {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.ev-placeholder-icon-wrapper {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #9ca3af;
}

.ev-placeholder-icon {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%) !important;
}

.ev-placeholder-icon i {
    color: #9ca3af;
}

.ev-coming-soon {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #fef3c7;
    border-radius: 8px;
    color: #92400e;
    font-size: 0.875rem;
    font-weight: 500;
}

.ev-placeholder .ev-card-action {
    background: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

/* ===================================
   모바일 반응형
   =================================== */
@media (max-width: 768px) {
    .ev-section {
        padding: 40px 0;
    }
    
    .ev-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .ev-view-all {
        align-self: flex-end;
    }
    
    .ev-tabs {
        width: 100%;
        padding: 4px;
        gap: 4px;
    }
    
    .ev-tab {
        flex: 1;
        padding: 8px 12px;
        font-size: 0.8125rem;
    }
    
    .ev-tab span:first-child {
        display: none;
    }
    
    .ev-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .ev-card-image {
        height: 140px;
    }
    
    .ev-card-header,
    .ev-card-header-overlay {
        padding: 12px 16px;
    }
    
    .ev-coin-info {
        padding: 12px 16px;
    }
    
    .ev-coin-icon {
        width: 48px;
        height: 48px;
    }
    
    .ev-card-body {
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .ev-section {
        padding: 30px 0;
    }
    
    .ev-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .ev-tabs {
        justify-content: space-between;
    }
    
    .ev-tab-count {
        display: none;
    }
}
</style>

<script>
// 이벤트 탭 전환
document.querySelectorAll('.ev-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // 활성 탭 변경
        document.querySelectorAll('.ev-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // 이벤트 카드 필터링
        const status = this.getAttribute('data-status');
        filterEvents(status);
    });
});

// 이벤트 필터링 함수
function filterEvents(status) {
    const cards = document.querySelectorAll('.ev-card');
    
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        
        if (status === 'all' || cardStatus === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// 이벤트 상세보기
function viewEvent(eventId) {
    window.location.href = `<?php echo G5_URL; ?>/event.php?ev_id=${eventId}`;
}
</script>
<!-- =================================== -->
<!-- 크립토 마케팅 소개 섹션 -->
<!-- =================================== -->
<section class="section marketing-intro">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="marketing-content">
                    <h2 class="display-5 fw-bold mb-4">
                        <span class="text-gradient">크립토 마케팅의</span><br>
                        새로운 패러다임
                    </h2>
                    <p class="lead mb-4">
                        10년 이상의 블록체인 산업 경험과 국내 최대 규모의 커뮤니티를 바탕으로
                        귀사의 프로젝트 성공을 위한 최적의 마케팅 솔루션을 제공합니다.
                    </p>
                    <div class="feature-list mb-4">
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>검증된 전문성</h5>
                                <p>100개 이상의 프로젝트 성공 경험과 업계 최고 수준의 전문가 팀</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>종합 마케팅 솔루션</h5>
                                <p>상장 지원부터 커뮤니티 관리까지 원스톱 토탈 서비스</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>투명한 성과 보고</h5>
                                <p>실시간 대시보드를 통한 캠페인 성과 추적 및 ROI 분석</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="<?php echo G5_URL ?>/consult.php" class="btn-gradient">
                            무료 컨설팅 신청 <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#" class="btn-outline">
                            포트폴리오 보기
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="marketing-stats">
                    <div class="stats-grid">
                        <div class="stat-box">
                            <div class="stat-icon-wrap">
                                <i class="bi bi-building"></i>
                            </div>
                            <h3>127+</h3>
                            <p>성공 프로젝트</p>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon-wrap">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3>50K+</h3>
                            <p>활성 커뮤니티</p>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon-wrap">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h3>98%</h3>
                            <p>고객 만족도</p>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon-wrap">
                                <i class="bi bi-award"></i>
                            </div>
                            <h3>15+</h3>
                            <p>수상 경력</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ===================================
     프로젝트 진행 사례 섹션
     =================================== -->
<section class="cmk-projects-showcase" style="background: #f8f9fa; padding: 80px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">
                <i class="bi bi-rocket-takeoff text-primary"></i>
                프로젝트 진행 사례
            </h2>
            <p class="lead text-muted">성공적으로 진행한 코인 마케팅 프로젝트</p>
        </div>
        
        <!-- 프로젝트 그리드 -->
        <div class="cmk-ps-grid">
            <?php
            $projects = [
                ['name' => 'BONK', 'file' => 'BONK.png'],
                ['name' => 'ACS', 'file' => 'ACS(액세스플토콜.png', 'full' => '액세스플토콜'],
                ['name' => 'AHT', 'file' => 'AHT(아하토큰.png', 'full' => '아하토큰'],
                ['name' => 'ALT', 'file' => 'ALT알트레이어.png', 'full' => '알트레이어'],
                ['name' => 'ANIME', 'file' => 'ANIME애니메코인.png', 'full' => '애니메코인'],
                ['name' => 'ARPA', 'file' => 'ARPA(알파.png', 'full' => '알파'],
                ['name' => 'ASTR', 'file' => 'ASTR아스타.png', 'full' => '아스타'],
                ['name' => 'BEAM', 'file' => 'BEAM(빔.png', 'full' => '빔'],
                ['name' => 'BIGTIME', 'file' => 'BIGTIME빅타임.png', 'full' => '빅타임'],
                ['name' => 'BLAST', 'file' => 'BLAST(블라스트.png', 'full' => '블라스트'],
                ['name' => 'NCT', 'file' => 'NCT폴리스웜.png', 'full' => '폴리스웜'],
                ['name' => 'OAS', 'file' => 'OAS(오아시스.png', 'full' => '오아시스'],
                ['name' => 'BRETT', 'file' => 'BRETT브렛.png', 'full' => '브렛'],
                ['name' => 'CKB', 'file' => 'CKB(너보스.png', 'full' => '너보스'],
                ['name' => 'DGB', 'file' => 'DGB(디지바이트.png', 'full' => '디지바이트'],
                ['name' => 'EPT', 'file' => 'EPT(밸런스.png', 'full' => '밸런스'],
                ['name' => 'GO', 'file' => 'GO(고체인.png', 'full' => '고체인'],
                ['name' => 'JASMY', 'file' => 'JASMY재스미코인.png', 'full' => '재스미코인'],
                ['name' => 'LWA', 'file' => 'LWA(루미웨이브.png', 'full' => '루미웨이브'],
                ['name' => 'MEW', 'file' => 'MEW(캣인어독스월드.png', 'full' => '캣인어독스월드'],
                ['name' => 'RVN', 'file' => 'RVN(레이븐코인.png', 'full' => '레이븐코인'],
                ['name' => 'SC', 'file' => 'SC(시아코인.png', 'full' => '시아코인'],
                ['name' => 'SOPH', 'file' => 'SOPH소폰.png', 'full' => '소폰'],
                ['name' => 'OBSR', 'file' => 'OBSR(옵저버.png', 'full' => '옵저버'],
                ['name' => 'OXT', 'file' => 'OXT오키드.png', 'full' => '오키드'],
                ['name' => 'PENGU', 'file' => 'PENGU(펏지펭귄.png', 'full' => '펏지펭귄'],
                ['name' => 'POKT', 'file' => 'POKT포켓네트워크.png', 'full' => '포켓네트워크'],
                ['name' => 'QTCON', 'file' => 'QTCON(퀴즈톡.png', 'full' => '퀴즈톡'],
                ['name' => 'RLY', 'file' => 'RLY랠리.png', 'full' => '랠리'],
                ['name' => 'W', 'file' => 'W웜홀.png', 'full' => '웜홀'],
                ['name' => 'SWELL', 'file' => 'SWELL(스웰네트워크.png', 'full' => '스웰네트워크'],
                ['name' => 'VTHO', 'file' => 'VTHO(비토르토큰.png', 'full' => '비토르토큰']
            ];
            
            foreach ($projects as $project) {
                $full_name = isset($project['full']) ? $project['full'] : $project['name'];
            ?>
            <div class="cmk-ps-item">
                <div class="cmk-ps-item-inner bg-white rounded-3 p-3 text-center h-100 shadow-sm">
                    <div class="cmk-ps-logo mx-auto mb-3">
                        <img src="<?php echo G5_IMG_URL; ?>/<?php echo $project['file']; ?>" 
                             alt="<?php echo $full_name; ?>" 
                             class="img-fluid">
                    </div>
                    <h5 class="cmk-ps-name fw-bold mb-1"><?php echo $project['name']; ?></h5>
                    <p class="cmk-ps-fullname text-muted small mb-0"><?php echo $full_name; ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<style>
/* ===================================
 * 프로젝트 진행 사례 섹션 스타일
 * =================================== */

/* 프로젝트 그리드 */
.cmk-ps-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    padding: 20px 0;
}

/* 프로젝트 아이템 */
.cmk-ps-item-inner {
    transition: all 0.3s;
    cursor: pointer;
}

.cmk-ps-item-inner:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.cmk-ps-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #f8f9fa;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cmk-ps-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.cmk-ps-name {
    font-size: 16px;
    color: #212529;
}

.cmk-ps-fullname {
    font-size: 12px;
}

/* 태블릿 (768px - 1024px) */
@media (max-width: 1024px) {
    .cmk-ps-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* 모바일 (768px 이하) */
@media (max-width: 768px) {
    .cmk-ps-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .cmk-ps-logo {
        width: 60px;
        height: 60px;
        padding: 10px;
    }
    
    .cmk-ps-name {
        font-size: 14px;
    }
    
    .cmk-ps-fullname {
        font-size: 11px;
    }
    
    .cmk-ps-item-inner {
        padding: 0.75rem !important;
    }
}

/* 작은 모바일 (480px 이하) */
@media (max-width: 480px) {
    .cmk-ps-grid {
        gap: 10px;
    }
}
</style>
<?php
include_once(G5_PATH.'/tail.php');
?>