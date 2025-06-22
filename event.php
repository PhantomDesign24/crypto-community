<?php
/*
 * 파일명: event.php
 * 위치: /
 * 기능: 이벤트 페이지
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '이벤트';

include_once('./_head.php');
?>

<!-- ===================================
     이벤트 페이지 스타일
     =================================== -->
<style>
/* 컨테이너 */
.event-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 페이지 헤더 */
.event-header {
    text-align: center;
    margin-bottom: 60px;
}

.event-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
}

.event-header p {
    font-size: 18px;
    color: #6b7280;
}

/* 이벤트 탭 */
.event-tabs {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.tab-btn {
    padding: 12px 24px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 25px;
    color: #4b5563;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.tab-btn:hover {
    background: #e5e7eb;
}

.tab-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 이벤트 그리드 */
.event-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

/* 이벤트 카드 */
.event-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* 이벤트 이미지 */
.event-image {
    position: relative;
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 6px 12px;
    background: #ef4444;
    color: white;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.event-badge.ongoing {
    background: #10b981;
}

.event-badge.upcoming {
    background: #f59e0b;
}

/* 이벤트 내용 */
.event-content {
    padding: 24px;
}

.event-category {
    display: inline-block;
    padding: 4px 12px;
    background: #eff6ff;
    color: #3b82f6;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 12px;
}

.event-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 12px;
    line-height: 1.4;
}

.event-desc {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 16px;
}

.event-period {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4b5563;
    font-size: 14px;
    margin-bottom: 20px;
}

.event-period i {
    color: #3b82f6;
}

/* 이벤트 참여 버튼 */
.event-action {
    display: flex;
    gap: 12px;
}

.btn-participate {
    flex: 1;
    padding: 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-participate:hover {
    background: #2563eb;
}

.btn-detail {
    padding: 12px 20px;
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-detail:hover {
    background: #e5e7eb;
}

/* 이벤트 상세 모달 */
.event-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.event-modal.show {
    display: flex;
}

.modal-container {
    background: white;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 24px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6b7280;
    cursor: pointer;
}

/* 배너 섹션 */
.event-banner {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 60px 40px;
    border-radius: 20px;
    text-align: center;
    margin-bottom: 60px;
}

.event-banner h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 16px;
}

.event-banner p {
    font-size: 18px;
    opacity: 0.9;
    margin-bottom: 24px;
}

.btn-banner {
    padding: 14px 32px;
    background: white;
    color: #3b82f6;
    border: none;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* 반응형 */
@media (max-width: 768px) {
    .event-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .event-header h1 {
        font-size: 28px;
    }
    
    .event-banner {
        padding: 40px 20px;
    }
    
    .event-banner h2 {
        font-size: 24px;
    }
}
</style>

<!-- ===================================
     이벤트 페이지 콘텐츠
     =================================== -->
<div class="event-container">
    <!-- 페이지 헤더 -->
    <div class="event-header">
        <h1>이벤트</h1>
        <p>다양한 혜택과 특별한 이벤트를 만나보세요</p>
    </div>
    
    <!-- 이벤트 탭 -->
    <div class="event-tabs">
        <button class="tab-btn active" onclick="filterEvents('all')">전체</button>
        <button class="tab-btn" onclick="filterEvents('ongoing')">진행중</button>
        <button class="tab-btn" onclick="filterEvents('upcoming')">예정</button>
        <button class="tab-btn" onclick="filterEvents('ended')">종료</button>
    </div>
    
    <!-- 메인 배너 이벤트 -->
    <div class="event-banner">
        <h2>🎉 신규 회원 가입 이벤트</h2>
        <p>지금 가입하시면 특별한 혜택을 드립니다!</p>
        <button class="btn-banner" onclick="showEventDetail('new-member')">
            자세히 보기
        </button>
    </div>
    
    <!-- 이벤트 그리드 -->
    <div class="event-grid">
        <!-- 이벤트 카드 1 -->
        <div class="event-card" data-status="ongoing">
            <div class="event-image">
                <div class="event-badge ongoing">진행중</div>
                <i class="bi bi-gift" style="font-size: 80px; color: white;"></i>
            </div>
            <div class="event-content">
                <span class="event-category">신규가입</span>
                <h3 class="event-title">웰컴 보너스 이벤트</h3>
                <p class="event-desc">첫 거래 시 수수료 50% 할인 혜택을 제공합니다.</p>
                <div class="event-period">
                    <i class="bi bi-calendar-check"></i>
                    <span>2025.01.01 ~ 2025.02.28</span>
                </div>
                <div class="event-action">
                    <button class="btn-participate" onclick="participateEvent('welcome-bonus')">
                        참여하기
                    </button>
                    <button class="btn-detail" onclick="showEventDetail('welcome-bonus')">
                        상세보기
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 이벤트 카드 2 -->
        <div class="event-card" data-status="ongoing">
            <div class="event-image" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="event-badge ongoing">진행중</div>
                <i class="bi bi-people-fill" style="font-size: 80px; color: white;"></i>
            </div>
            <div class="event-content">
                <span class="event-category">추천</span>
                <h3 class="event-title">친구 추천 이벤트</h3>
                <p class="event-desc">친구를 추천하고 추천인과 함께 포인트를 받으세요!</p>
                <div class="event-period">
                    <i class="bi bi-calendar-check"></i>
                    <span>2025.01.15 ~ 2025.03.31</span>
                </div>
                <div class="event-action">
                    <button class="btn-participate" onclick="participateEvent('referral')">
                        참여하기
                    </button>
                    <button class="btn-detail" onclick="showEventDetail('referral')">
                        상세보기
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 이벤트 카드 3 -->
        <div class="event-card" data-status="upcoming">
            <div class="event-image" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="event-badge upcoming">예정</div>
                <i class="bi bi-trophy-fill" style="font-size: 80px; color: white;"></i>
            </div>
            <div class="event-content">
                <span class="event-category">경품</span>
                <h3 class="event-title">월간 트레이딩 대회</h3>
                <p class="event-desc">최고의 수익률을 달성한 회원에게 특별한 상품을 드립니다.</p>
                <div class="event-period">
                    <i class="bi bi-calendar-check"></i>
                    <span>2025.02.01 ~ 2025.02.28</span>
                </div>
                <div class="event-action">
                    <button class="btn-participate" disabled>
                        곧 시작됩니다
                    </button>
                    <button class="btn-detail" onclick="showEventDetail('trading-contest')">
                        상세보기
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 이벤트 카드 4 -->
        <div class="event-card" data-status="ended">
            <div class="event-image" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                <div class="event-badge" style="background: #6b7280;">종료</div>
                <i class="bi bi-calendar-x" style="font-size: 80px; color: white;"></i>
            </div>
            <div class="event-content">
                <span class="event-category">특별</span>
                <h3 class="event-title">연말 특별 이벤트</h3>
                <p class="event-desc">2024년을 마무리하는 특별한 이벤트였습니다.</p>
                <div class="event-period">
                    <i class="bi bi-calendar-check"></i>
                    <span>2024.12.01 ~ 2024.12.31</span>
                </div>
                <div class="event-action">
                    <button class="btn-participate" disabled>
                        종료됨
                    </button>
                    <button class="btn-detail" onclick="showEventDetail('year-end')">
                        결과보기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 이벤트 상세 모달 -->
<div id="eventModal" class="event-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modalTitle">이벤트 상세</h3>
            <button class="modal-close" onclick="hideEventModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body" id="modalContent">
            <!-- 동적으로 내용 추가 -->
        </div>
    </div>
</div>

<script>
// 이벤트 필터링
function filterEvents(status) {
    // 탭 활성화
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // 카드 필터링
    const cards = document.querySelectorAll('.event-card');
    cards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// 이벤트 참여
function participateEvent(eventId) {
    <?php if (!$is_member) { ?>
        alert('로그인 후 참여하실 수 있습니다.');
        location.href = '<?php echo G5_BBS_URL ?>/login.php';
        return;
    <?php } ?>
    
    if (confirm('이벤트에 참여하시겠습니까?')) {
        // 실제로는 AJAX로 처리
        alert('이벤트 참여가 완료되었습니다!');
    }
}

// 이벤트 상세 보기
function showEventDetail(eventId) {
    const modal = document.getElementById('eventModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');
    
    // 이벤트별 상세 내용 (실제로는 AJAX로 불러옴)
    const eventDetails = {
        'new-member': {
            title: '신규 회원 가입 이벤트',
            content: `
                <div style="text-align: center; margin-bottom: 30px;">
                    <i class="bi bi-gift" style="font-size: 80px; color: #3b82f6;"></i>
                </div>
                <h4>이벤트 내용</h4>
                <ul style="line-height: 2;">
                    <li>신규 회원가입 시 10,000 포인트 즉시 지급</li>
                    <li>첫 거래 수수료 50% 할인</li>
                    <li>VIP 등급 체험 기회 제공 (1개월)</li>
                </ul>
                <h4 style="margin-top: 30px;">참여 방법</h4>
                <ol style="line-height: 2;">
                    <li>회원가입을 완료합니다</li>
                    <li>이메일 인증을 완료합니다</li>
                    <li>첫 입금을 진행합니다</li>
                    <li>자동으로 혜택이 적용됩니다</li>
                </ol>
                <div style="background: #eff6ff; padding: 20px; border-radius: 8px; margin-top: 30px;">
                    <p style="margin: 0; color: #1e40af;">
                        <i class="bi bi-info-circle"></i> 
                        본 이벤트는 신규 회원에 한해 1회만 참여 가능합니다.
                    </p>
                </div>
            `
        },
        'welcome-bonus': {
            title: '웰컴 보너스 이벤트',
            content: `
                <h4>혜택 내용</h4>
                <p>첫 거래 시 수수료 50% 할인</p>
            `
        },
        'referral': {
            title: '친구 추천 이벤트',
            content: `
                <h4>추천 혜택</h4>
                <p>추천인과 피추천인 모두 5,000 포인트 지급</p>
            `
        }
    };
    
    const detail = eventDetails[eventId] || {
        title: '이벤트 상세',
        content: '<p>이벤트 상세 내용을 준비중입니다.</p>'
    };
    
    title.textContent = detail.title;
    content.innerHTML = detail.content;
    
    modal.classList.add('show');
}

// 모달 닫기
function hideEventModal() {
    document.getElementById('eventModal').classList.remove('show');
}

// 모달 외부 클릭 시 닫기
document.getElementById('eventModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideEventModal();
    }
});
</script>

<?php
include_once('./_tail.php');
?>