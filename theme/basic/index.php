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
        margin-bottom: 20px;
    }
    
    .ticker-content {
        animation-duration: 40s;
    }
    
    .feature-item {
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
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
<!-- 실시간 코인 시세 섹션 -->
<!-- =================================== -->
<section class="section" style="background: #f8fafc;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">실시간 코인 시세</h2>
            <p class="section-subtitle">국내외 주요 거래소의 실시간 가격 정보를 한눈에</p>
        </div>
        
        <!-- 코인 시세 위젯 -->
        <div class="crypto-widget-wrapper">
            <?php include_once(G5_PATH.'/widget/crypto_widget.php'); ?>
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
        // 샘플 데이터 (실제로는 DB에서 가져옴)
        $payments = [
            ['name' => '김*수', 'amount' => '100 USDT', 'event' => '신규가입 이벤트'],
            ['name' => '이*희', 'amount' => '50 USDT', 'event' => '친구추천 이벤트'],
            ['name' => '박*철', 'amount' => '200 USDT', 'event' => '거래인증 이벤트'],
            ['name' => '최*영', 'amount' => '150 USDT', 'event' => '댓글 이벤트'],
            ['name' => '정*호', 'amount' => '80 USDT', 'event' => '출석체크 이벤트']
        ];
        
        // 2번 반복하여 끊김없는 스크롤 구현
        for($i = 0; $i < 2; $i++) {
            foreach($payments as $payment) {
        ?>
        <span class="ticker-item">
            <i class="bi bi-gift-fill"></i>
            <?php echo $payment['name']; ?>님 
            <strong><?php echo $payment['amount']; ?></strong> 
            <?php echo $payment['event']; ?> 지급완료
        </span>
        <?php 
            }
        }
        ?>
    </div>
</div>

<!-- =================================== -->
<!-- 이벤트 섹션 -->
<!-- =================================== -->
<section class="section section-bg">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">진행중인 이벤트</h2>
            <p class="section-subtitle">다양한 에어드랍과 보상 이벤트에 참여하세요</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="event-card card-hot">
                    <span class="event-badge">HOT</span>
                    <div class="event-decoration"></div>
                    <div class="event-content">
                        <div class="event-icon">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h4>신규 가입 이벤트</h4>
                        <p class="text-muted">지금 가입하고 즉시 받는 보상</p>
                        <div class="event-reward">100 USDT</div>
                        <p class="mb-4">회원가입 후 이메일 인증 완료시 즉시 지급</p>
                        <a href="<?php echo G5_URL ?>/event.php" class="btn-gradient w-100">
                            참여하기 <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="event-card card-new">
                    <span class="event-badge">NEW</span>
                    <div class="event-decoration"></div>
                    <div class="event-content">
                        <div class="event-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>친구 추천 이벤트</h4>
                        <p class="text-muted">친구와 함께 받는 더블 보상</p>
                        <div class="event-reward">50 + 50 USDT</div>
                        <p class="mb-4">추천인과 신규회원 모두에게 지급</p>
                        <a href="<?php echo G5_URL ?>/event.php" class="btn-gradient w-100">
                            참여하기 <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="event-card card-special">
                    <span class="event-badge">SPECIAL</span>
                    <div class="event-decoration"></div>
                    <div class="event-content">
                        <div class="event-icon">
                            <i class="bi bi-camera"></i>
                        </div>
                        <h4>거래 인증 이벤트</h4>
                        <p class="text-muted">거래 스크린샷 제출시</p>
                        <div class="event-reward">최대 500 USDT</div>
                        <p class="mb-4">월간 거래량에 따라 차등 지급</p>
                        <a href="<?php echo G5_URL ?>/event.php" class="btn-gradient w-100">
                            참여하기 <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =================================== -->
<!-- CTA 섹션 -->
<!-- =================================== -->
<section class="section">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title mb-4">지금 시작하세요</h2>
            <p class="lead text-muted mb-5">
                매일 업데이트되는 최신 코인 정보와<br>
                다양한 이벤트 혜택을 놓치지 마세요
            </p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?php echo G5_BBS_URL ?>/register.php" class="btn-gradient btn-lg">
                    무료 회원가입 <i class="bi bi-arrow-right"></i>
                </a>
                <a href="<?php echo G5_URL ?>/consult.php" class="btn-outline btn-lg">
                    상담 신청
                </a>
            </div>
        </div>
    </div>
</section>

<?php
include_once(G5_PATH.'/tail.php');
?>