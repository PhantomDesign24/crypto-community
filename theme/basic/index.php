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

/* 이벤트 배지 애니메이션 */
.event-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.9);
    color: var(--primary-color);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 700;
    animation: badge-bounce 2s infinite;
    text-transform: uppercase;
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
<!-- 전광판 스타일 -->
<!-- =================================== -->
/* 전광판 컨테이너 */
.ticker-section {
    background: var(--dark-bg);
    padding: 20px 0;
    overflow: hidden;
    position: relative;
}

.ticker-title {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    background: var(--accent-color);
    padding: 10px 20px;
    border-radius: 0 25px 25px 0;
    font-weight: 600;
    color: white;
    z-index: 10;
}

/* 전광판 콘텐츠 */
.ticker-content {
    display: flex;
    animation: scroll-left 30s linear infinite;
    padding-left: 200px;
}

.ticker-item {
    color: white;
    padding: 0 50px;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ticker-item i {
    color: var(--warning-color);
}

@keyframes scroll-left {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
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
                <div class="stat-number">15,234</div>
                <div class="text-muted">활성 회원</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-currency-bitcoin"></i>
                </div>
                <div class="stat-number">456</div>
                <div class="text-muted">상장 정보</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="bi bi-gift-fill"></i>
                </div>
                <div class="stat-number">28</div>
                <div class="text-muted">진행중 이벤트</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-number">₩5.2억</div>
                <div class="text-muted">누적 에어드랍</div>
            </div>
        </div>
    </div>
</div>
<!-- =================================== -->
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
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">최신 상장 소식</h2>
            <p class="section-subtitle">국내 주요 거래소의 신규 상장 정보를 실시간으로</p>
        </div>
        
        <div class="row g-4">
            <?php
            // 샘플 데이터 (실제로는 게시판에서 가져옴)
            $listings = [
                [
                    'title' => 'Arbitrum (ARB) 원화마켓 상장',
                    'exchange' => 'upbit',
                    'date' => '2025-01-11',
                    'image' => 'https://placehold.co/400x200/093687/ffffff?text=ARB'
                ],
                [
                    'title' => 'Blur (BLUR) KRW 마켓 오픈',
                    'exchange' => 'bithumb',
                    'date' => '2025-01-10',
                    'image' => 'https://placehold.co/400x200/f89e1b/ffffff?text=BLUR'
                ],
                [
                    'title' => 'Optimism (OP) 거래 지원',
                    'exchange' => 'coinone',
                    'date' => '2025-01-09',
                    'image' => 'https://placehold.co/400x200/0066cc/ffffff?text=OP'
                ],
                [
                    'title' => 'Celestia (TIA) 신규 상장',
                    'exchange' => 'korbit',
                    'date' => '2025-01-08',
                    'image' => 'https://placehold.co/400x200/4b79d8/ffffff?text=TIA'
                ]
            ];
            
            foreach($listings as $item) {
                $badge_class = 'badge-' . $item['exchange'];
                $exchange_name = [
                    'upbit' => '업비트',
                    'bithumb' => '빗썸',
                    'coinone' => '코인원',
                    'korbit' => '코빗'
                ][$item['exchange']];
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="listing-card">
                    <div class="listing-image">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                        <span class="exchange-badge <?php echo $badge_class; ?>"><?php echo $exchange_name; ?></span>
                    </div>
                    <div class="listing-content">
                        <h5 class="mb-2"><?php echo $item['title']; ?></h5>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3"></i> <?php echo $item['date']; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=listing" class="btn-gradient">
                더 많은 상장 소식 보기 <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<!-- =================================== -->
<!-- 코인 지급 현황 전광판 -->
<!-- =================================== -->
<div class="ticker-section">
    <div class="ticker-title">
        <i class="bi bi-megaphone"></i> 실시간 지급
    </div>
	<div class="ticker-content">
		<?php
		// 전광판 데이터 읽기
		$ticker_file = G5_DATA_PATH.'/cache/ticker_data.json';
		$ticker_items = array();
		
		if(file_exists($ticker_file)) {
			$json_data = file_get_contents($ticker_file);
			$ticker_items = json_decode($json_data, true);
		}
		
		// 데이터가 없으면 샘플 데이터 표시
		if(empty($ticker_items)) {
			$ticker_items = [
				['name' => '김*수', 'amount' => '100 USDT', 'event' => '신규가입 이벤트'],
				['name' => '이*희', 'amount' => '50 USDT', 'event' => '친구추천 이벤트'],
				['name' => '박*철', 'amount' => '200 USDT', 'event' => '거래인증 이벤트'],
				['name' => '최*영', 'amount' => '150 USDT', 'event' => '댓글 이벤트'],
				['name' => '정*호', 'amount' => '80 USDT', 'event' => '출석체크 이벤트']
			];
		}
		
		// 2번 반복하여 끊김없는 스크롤 구현
		for($i = 0; $i < 2; $i++) {
			foreach($ticker_items as $item) {
		?>
		<span class="ticker-item">
			<i class="bi bi-gift-fill"></i>
			<?php echo $item['name']; ?>님 
			<strong><?php echo $item['amount']; ?></strong> 
			<?php echo $item['event']; ?> 지급완료
		</span>
		<?php 
			}
		}
		?>
	</div>
</div>

<!-- ===================================
     이벤트 섹션
     =================================== -->
<section class="events-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">
                <i class="bi bi-gift-fill text-primary"></i>
                에어드랍 이벤트
            </h2>
            <p class="lead text-muted">다양한 코인 에어드랍 이벤트에 참여하세요</p>
        </div>
        
        <!-- 이벤트 탭 -->
        <ul class="nav nav-pills justify-content-center mb-5" id="eventTabs">
            <li class="nav-item">
                <a class="nav-link active" data-status="ongoing" href="#ongoing">
                    <i class="bi bi-play-circle"></i> 진행중
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="scheduled" href="#scheduled">
                    <i class="bi bi-clock"></i> 진행예정
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="ended" href="#ended">
                    <i class="bi bi-check-circle"></i> 진행종료
                </a>
            </li>
        </ul>
        
        <!-- 이벤트 그리드 -->
        <div class="event-grid" id="eventGrid">
            <?php
            // 추천 이벤트 가져오기
            $sql = "SELECT * FROM g5_event 
                    WHERE ev_recommend = 1 
                    AND ev_status = 'ongoing'
                    ORDER BY ev_id DESC 
                    LIMIT 6";
            $result = sql_query($sql);
            
            while($row = sql_fetch_array($result)) {
                $remaining_days = floor((strtotime($row['ev_end_date']) - time()) / 86400);
            ?>
            <div class="event-card" data-event-id="<?php echo $row['ev_id']; ?>">
                <div class="event-card-inner">
                    <!-- 상태 배지 -->
                    <div class="event-badge">
                        <?php if($row['ev_status'] == 'ongoing') { ?>
                            <span class="badge bg-success">진행중</span>
                        <?php } else if($row['ev_status'] == 'scheduled') { ?>
                            <span class="badge bg-info">진행예정</span>
                        <?php } else { ?>
                            <span class="badge bg-secondary">종료</span>
                        <?php } ?>
                    </div>
                    
                    <!-- 이벤트 이미지 -->
                    <div class="event-image">
                        <?php if($row['ev_image']) { ?>
                            <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $row['ev_image']; ?>" alt="<?php echo $row['ev_subject']; ?>">
                        <?php } else { ?>
                            <div class="event-no-image">
                                <i class="bi bi-gift"></i>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <!-- 이벤트 내용 -->
                    <div class="event-content">
                        <div class="event-coin-info">
                            <span class="coin-symbol"><?php echo $row['ev_coin_symbol']; ?></span>
                            <span class="coin-amount"><?php echo $row['ev_coin_amount']; ?></span>
                        </div>
                        <h4 class="event-title"><?php echo $row['ev_subject']; ?></h4>
                        <p class="event-summary"><?php echo $row['ev_summary']; ?></p>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <span>D-<?php echo $remaining_days; ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <span><?php echo number_format($row['ev_apply_count']); ?>명 참여</span>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-sm w-100 mt-3" onclick="viewEvent(<?php echo $row['ev_id']; ?>)">
                            <i class="bi bi-arrow-right-circle"></i> 참여하기
                        </button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <!-- 더보기 버튼 -->
        <div class="text-center mt-5">
            <a href="<?php echo G5_URL; ?>/event.php" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-grid-3x3-gap"></i> 모든 이벤트 보기
            </a>
        </div>
    </div>
</section>

<style>
/* 이벤트 섹션 스타일 */
.events-section {
    position: relative;
    overflow: hidden;
}

/* 이벤트 탭 */
.nav-pills .nav-link {
    color: #6b7280;
    background: white;
    border: 1px solid #e5e7eb;
    margin: 0 5px;
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s;
}

.nav-pills .nav-link:hover {
    background: #f3f4f6;
}

.nav-pills .nav-link.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

/* 이벤트 그리드 */
.event-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

/* 이벤트 카드 */
.event-card {
    position: relative;
    cursor: pointer;
}

.event-card-inner {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    height: 100%;
}

.event-card:hover .event-card-inner {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* 상태 배지 */
.event-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 10;
}

/* 이벤트 이미지 */
.event-image {
    width: 100%;
    height: 200px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-no-image {
    font-size: 60px;
    color: #d1d5db;
}

/* 이벤트 내용 */
.event-content {
    padding: 24px;
}

.event-coin-info {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.coin-symbol {
    background: #eff6ff;
    color: #3b82f6;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.coin-amount {
    color: #16a34a;
    font-weight: 600;
    font-size: 14px;
}

.event-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1f2937;
    line-height: 1.4;
}

.event-summary {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 16px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-meta {
    display: flex;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
}

.meta-item i {
    color: #9ca3af;
}

/* 반응형 */
@media (max-width: 992px) {
    .event-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .event-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .nav-pills .nav-link {
        padding: 8px 16px;
        font-size: 14px;
    }
}
</style>

<script>
// 이벤트 탭 전환
document.querySelectorAll('#eventTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        
        // 활성 탭 변경
        document.querySelectorAll('#eventTabs .nav-link').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // 이벤트 로드
        const status = this.getAttribute('data-status');
        loadEvents(status);
    });
});

// 이벤트 로드 함수
function loadEvents(status) {
    fetch(`<?php echo G5_URL; ?>/ajax/get_events.php?status=${status}`)
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('eventGrid');
            grid.innerHTML = data.html;
        });
}

// 이벤트 상세보기
function viewEvent(eventId) {
    // 모달로 이벤트 상세 표시
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