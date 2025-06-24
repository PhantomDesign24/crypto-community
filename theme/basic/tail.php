<?php
/*
 * 파일명: tail.php
 * 위치: /
 * 기능: 크립토 사이트 공통 푸터
 * 작성일: 2025-01-11
 */

if (!defined('_GNUBOARD_')) exit;
?>

</main>
<!-- 메인 컨텐츠 끝 -->

<!-- =================================== -->
<!-- 푸터 스타일 -->
<!-- =================================== -->
<style>
/* =================================== -->
<!-- 푸터 섹션 스타일 -->
<!-- =================================== -->
/* 메인 푸터 */
.main-footer {
    background: var(--dark-bg);
    color: #9ca3af;
    padding: 60px 0 30px;
    margin-top: 80px;
}

/* 푸터 로고 */
.footer-logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.footer-logo i {
    color: var(--accent-color);
}

/* 푸터 위젯 */
.footer-widget h5 {
    color: white;
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.footer-widget ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-widget ul li {
    margin-bottom: 10px;
}

.footer-widget a {
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-widget a:hover {
    color: var(--accent-color);
}

/* 연락처 정보 */
.contact-info {
    margin-bottom: 15px;
}

.contact-info i {
    width: 20px;
    color: var(--accent-color);
    margin-right: 10px;
}

/* 소셜 링크 */
.social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-links a {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.3s;
}

.social-links a:hover {
    background: var(--accent-color);
    transform: translateY(-3px);
}

/* 하단 카피라이트 */
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: 40px;
    padding-top: 30px;
    text-align: center;
}

/* 상단 이동 버튼 */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: var(--accent-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 999;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background: #2563eb;
    transform: translateY(-5px);
    color: white;
}
</style>

<!-- =================================== -->
<!-- 메인 푸터 -->
<!-- =================================== -->
<footer class="main-footer">
    <div class="container">
        <div class="row">
            <!-- 회사 정보 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-widget">
                    <a href="<?php echo G5_URL ?>" class="footer-logo">
                        <i class="bi bi-currency-bitcoin"></i>
                        <span>CRYPTO HUB</span>
                    </a>
                    <p>
                        국내 최고의 암호화폐 커뮤니티<br>
                        신뢰할 수 있는 코인 정보의 중심
                    </p>
                    <div class="social-links">
                        <a href="#" target="_blank">
                            <i class="bi bi-telegram"></i>
                        </a>
                        <a href="#" target="_blank">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" target="_blank">
                            <i class="bi bi-discord"></i>
                        </a>
                        <a href="#" target="_blank">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- 빠른 링크 -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-widget">
                    <h5>서비스</h5>
                    <ul>
                        <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=listing">신규상장소식</a></li>
                        <li><a href="<?php echo G5_URL ?>/otc.php">OTC장외거래</a></li>
                        <li><a href="<?php echo G5_URL ?>/event.php">이벤트</a></li>
                        <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=community">커뮤니티</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- 고객지원 -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-widget">
                    <h5>고객지원</h5>
                    <ul>
                        <li><a href="<?php echo G5_URL ?>/consult.php">상담신청</a></li>
                        <li><a href="<?php echo G5_BBS_URL ?>/faq.php">자주묻는질문</a></li>
                        <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=notice">공지사항</a></li>
                        <li><a href="<?php echo G5_URL ?>/company/terms.php">이용약관</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- 연락처 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-widget">
                    <h5>연락처</h5>
                    <div class="contact-info">
                        <i class="bi bi-geo-alt"></i>
                        서울특별시 강남구 테헤란로 123
                    </div>
                    <div class="contact-info">
                        <i class="bi bi-telephone"></i>
                        고객센터: 1588-0000
                    </div>
                    <div class="contact-info">
                        <i class="bi bi-envelope"></i>
                        support@cryptohub.co.kr
                    </div>
                    <div class="contact-info">
                        <i class="bi bi-clock"></i>
                        평일 09:00 - 18:00 (주말/공휴일 휴무)
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 하단 카피라이트 -->
        <div class="footer-bottom">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> CRYPTO HUB. All rights reserved. | 
                사업자등록번호: 123-45-67890 | 
                대표이사: 홍길동
            </p>
        </div>
    </div>
</footer>

<!-- =================================== -->
<!-- 상단 이동 버튼 -->
<!-- =================================== -->
<a href="#" class="back-to-top" id="backToTop">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- =================================== -->
<!-- 푸터 스크립트 -->
<!-- =================================== -->
<script>
// =================================== 
// 상단 이동 버튼
// =================================== 
window.addEventListener('scroll', function() {
    var backToTop = document.getElementById('backToTop');
    if (window.pageYOffset > 300) {
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});

document.getElementById('backToTop').addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// =================================== 
// AOS 초기화 (애니메이션 효과를 원하는 경우)
// =================================== 
// AOS 라이브러리 사용시 주석 해제
// AOS.init({
//     duration: 800,
//     easing: 'ease-in-out',
//     once: true
// });
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JSON-LD 구조화 데이터 -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?php echo $config['cf_title']; ?>",
    "url": "<?php echo G5_URL; ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo G5_BBS_URL; ?>/search.php?stx={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

<?php if ($seo_og_type == 'article' && isset($write['wr_datetime'])) { ?>
<!-- Article 구조화 데이터 -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo $seo_title; ?>",
    "description": "<?php echo $seo_description; ?>",
    "image": "<?php echo $seo_og_image; ?>",
    "datePublished": "<?php echo date('c', strtotime($write['wr_datetime'])); ?>",
    "dateModified": "<?php echo date('c', strtotime($write['wr_last'])); ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo isset($write['wr_name']) ? $write['wr_name'] : ''; ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo $config['cf_title']; ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo G5_THEME_IMG_URL; ?>/logo.png"
        }
    }
}
</script>
<?php } ?>
<?php
// 그누보드 필수 스크립트
include_once(G5_PATH.'/tail.sub.php');
?>