<?php
/*
 * 파일명: footer.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리 페이지 공통 푸터
 * 작성일: 2025-01-23
 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
        
        <!-- 각 페이지 콘텐츠 끝 -->
    </main>
</div>

<!-- 공통 스크립트 -->
<script>
// 사이드바 토글
function saSidebarToggle() {
    const sidebar = document.getElementById('saSidebar');
    const overlay = document.getElementById('saSidebarOverlay');
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // 모바일에서 body 스크롤 방지
    if (sidebar.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// 모바일에서 메뉴 클릭 시 사이드바 닫기
if (window.innerWidth <= 768) {
    document.querySelectorAll('.sa-menu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // 외부 링크가 아닌 경우에만
            if (!this.href.includes('#') && !this.href.includes('logout')) {
                setTimeout(() => {
                    saSidebarToggle();
                }, 100);
            }
        });
    });
}

// 윈도우 리사이즈 시 처리
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (window.innerWidth > 768) {
            // 데스크톱 모드에서는 사이드바 초기화
            document.getElementById('saSidebar').classList.remove('active');
            document.getElementById('saSidebarOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }
    }, 250);
});

// 페이지 로드 시 스크롤 위치 복원 방지 (모바일)
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}
</script>

</body>
</html>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>