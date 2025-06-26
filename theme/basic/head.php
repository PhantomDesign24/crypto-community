<?php
/*
 * 파일명: head.php
 * 위치: /
 * 기능: 크립토 사이트 메인 헤더 (네비게이션 포함)
 * 작성일: 2025-01-11
 */

if (!defined('_GNUBOARD_')) exit;
// 캐시된 가격 정보 가져오기
$usdt_price_cache = G5_DATA_PATH.'/cache/usdt_price.txt';
$usdt_market_price = 1361; // 기본값

if(file_exists($usdt_price_cache)) {
    $cache_data = json_decode(file_get_contents($usdt_price_cache), true);
    if(isset($cache_data['price'])) {
        $usdt_market_price = $cache_data['price'];
    }
}

// 가격 조정값 가져오기
$sql = "SELECT * FROM g5_tether_price ORDER BY tp_id DESC LIMIT 1";
$price_adjustment = sql_fetch($sql);

if(!$price_adjustment) {
    $buy_adjustment = 20;  // 기본 +20원
    $sell_adjustment = -20; // 기본 -20원
} else {
    $buy_adjustment = $price_adjustment['tp_buy_adjustment'];
    $sell_adjustment = $price_adjustment['tp_sell_adjustment'];
}

// 최종 가격 계산
$usdt_buy_price = $usdt_market_price + $buy_adjustment;
$usdt_sell_price = $usdt_market_price + $sell_adjustment;
// 상단 파일 include
include_once(G5_PATH.'/head.sub.php');
?>

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
                    <a href="<?php echo G5_URL ?>/sub_admin">
                        <i class="bi bi-gear"></i> 하부 관리자
                    </a>
                    <span class="mx-2">|</span>
                    <a href="<?php echo G5_BBS_URL ?>/logout.php">
                        <i class="bi bi-box-arrow-right"></i> 로그아웃
                    </a>
                <?php } else { ?>
                    <a href="<?php echo G5_BBS_URL ?>/login.php">
                        <i class="bi bi-box-arrow-in-right"></i> 로그인
                    </a>
                    <span class="mx-2">|</span>
                    <a href="javascript:void(0);" onclick="showSimpleRegister()">
                        <i class="bi bi-person-plus"></i> 간단 회원가입
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- ===================================
     간단 회원가입 모달
     =================================== -->
<div id="simpleRegisterModal" class="simple-register-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill"></i> 빠른 회원가입
                </h5>
                <button type="button" class="btn-close" onclick="hideSimpleRegister()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form id="simpleRegisterForm" onsubmit="submitSimpleRegister(event)">
                <div class="modal-body">
                    <!-- 성공 메시지 -->
                    <div id="registerSuccess" class="register-success" style="display:none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <h4>회원가입 완료!</h4>
                        <p id="successMessage"></p>
                        <button type="button" class="btn btn-primary" onclick="location.reload();">
                            로그인하기
                        </button>
                    </div>
                    
                    <!-- 가입 폼 -->
                    <div id="registerFormContent">
                        <div class="form-group">
                            <label>아이디 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-person text-primary"></i>
                                </span>
                                <input type="text" name="mb_id" class="form-control border-start-0" 
                                       placeholder="영문 소문자, 숫자 3~20자" required
                                       pattern="[a-z0-9_]{3,20}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>비밀번호 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-lock text-primary"></i>
                                </span>
                                <input type="password" name="mb_password" class="form-control border-start-0" 
                                       placeholder="4자 이상" required minlength="4">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>이름 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-person-badge text-primary"></i>
                                </span>
                                <input type="text" name="mb_name" class="form-control border-start-0" 
                                       placeholder="실명 입력" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>이메일 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-envelope text-primary"></i>
                                </span>
                                <input type="email" name="mb_email" class="form-control border-start-0" 
                                       placeholder="example@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>휴대폰 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-phone text-primary"></i>
                                </span>
                                <input type="tel" name="mb_hp" class="form-control border-start-0" 
                                       placeholder="010-0000-0000" required
                                       pattern="[0-9]{3}-[0-9]{4}-[0-9]{4}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>추천 코드 <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-gift text-primary"></i>
                                </span>
                                <input type="text" name="mb_referral_code" id="mb_referral_code" 
                                       class="form-control border-start-0" 
                                       placeholder="8자리 추천 코드" required
                                       maxlength="8" style="text-transform: uppercase;">
                            </div>
                            <div id="referralMsg" class="form-text"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideSimpleRegister()">
                        취소
                    </button>
                    <button type="submit" class="btn btn-primary" id="registerSubmitBtn">
                        <i class="bi bi-check-lg"></i> 회원가입
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================
     간단 회원가입 스타일
     =================================== -->
<style>
/* 모달 기본 스타일 */
.simple-register-modal {
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

.simple-register-modal.show {
    display: flex;
}

.modal-dialog {
    width: 100%;
    max-width: 450px;
    margin: 20px;
}

.modal-content {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

/* 모달 헤더 */
.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.modal-title i {
    color: #3b82f6;
}

.btn-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
}

.btn-close:hover {
    color: #374151;
}

/* 모달 바디 */
.modal-body {
    padding: 24px;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-group .required {
    color: #ef4444;
}

/* 입력 필드 스타일 */
.input-group {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
}

.input-group:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-group-text {
    padding: 10px 12px;
    background: #fff !important;
    border: none !important;
}

.form-control {
    flex: 1;
    padding: 10px 12px;
    border: none !important;
    font-size: 14px;
    outline: none;
}

.form-control::placeholder {
    color: #9ca3af;
}

/* 메시지 */
.form-text {
    margin-top: 4px;
    font-size: 12px;
}

.form-text.text-success {
    color: #10b981;
}

.form-text.text-danger {
    color: #ef4444;
}

/* 성공 메시지 */
.register-success {
    text-align: center;
    padding: 40px 20px;
}

.register-success i {
    font-size: 48px;
    color: #10b981;
    margin-bottom: 16px;
}

.register-success h4 {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 12px;
}

.register-success p {
    color: #6b7280;
    margin-bottom: 24px;
}

/* 모달 푸터 */
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* 버튼 스타일 */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #3b82f6;
    color: #fff;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

/* 반응형 */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-body {
        padding: 20px;
    }
}
</style>

<!-- ===================================
     간단 회원가입 스크립트
     =================================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// 모달 표시
function showSimpleRegister() {
    document.getElementById('simpleRegisterModal').classList.add('show');
    document.getElementById('simpleRegisterForm').reset();
    document.getElementById('registerFormContent').style.display = 'block';
    document.getElementById('registerSuccess').style.display = 'none';
    document.querySelector('.modal-footer').style.display = 'flex';
    
    // 버튼 초기화
    const submitBtn = document.getElementById('registerSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> 회원가입';
    
    // 메시지 초기화
    document.getElementById('referralMsg').textContent = '';
    document.getElementById('referralMsg').className = 'form-text';
}

// 모달 숨기기
function hideSimpleRegister() {
    document.getElementById('simpleRegisterModal').classList.remove('show');
}

// 추천 코드 자동 대문자 변환
document.getElementById('mb_referral_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// 추천 코드 확인
document.getElementById('mb_referral_code').addEventListener('blur', function() {
    const code = this.value;
    const msgDiv = document.getElementById('referralMsg');
    
    if (code.length === 0) {
        msgDiv.textContent = '';
        msgDiv.className = 'form-text';
        return;
    }
    
    if (code.length !== 8) {
        msgDiv.textContent = '추천 코드는 8자리여야 합니다.';
        msgDiv.className = 'form-text text-danger';
        return;
    }
    
    // AJAX로 추천 코드 확인
    $.post('<?php echo G5_BBS_URL; ?>/ajax.mb_referral_code.php', {
        referral_code: code
    }, function(response) {
        // span 태그가 포함된 경우와 텍스트만 있는 경우 모두 처리
        if (response.indexOf('유효한') > -1 || response.indexOf('cmk-success') > -1) {
            msgDiv.textContent = response.replace(/<[^>]*>/g, ''); // HTML 태그 제거
            msgDiv.className = 'form-text text-success';
        } else {
            msgDiv.textContent = response.replace(/<[^>]*>/g, ''); // HTML 태그 제거
            msgDiv.className = 'form-text text-danger';
        }
    });
});

// 연락처 자동 하이픈
document.querySelector('input[name="mb_hp"]').addEventListener('input', function(e) {
    var value = e.target.value.replace(/[^0-9]/g, '');
    var formatted = '';
    
    if (value.length <= 3) {
        formatted = value;
    } else if (value.length <= 7) {
        formatted = value.slice(0, 3) + '-' + value.slice(3);
    } else {
        formatted = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
    }
    
    e.target.value = formatted;
});

// 회원가입 폼 제출
function submitSimpleRegister(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('registerSubmitBtn');
    
    // 버튼 비활성화
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 처리중...';
    
    // AJAX 요청
    $.ajax({
        url: '<?php echo G5_BBS_URL; ?>/simple_register.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // 성공 메시지 표시
                document.getElementById('registerFormContent').style.display = 'none';
                document.getElementById('registerSuccess').style.display = 'block';
                document.getElementById('successMessage').innerHTML = 
                    response.data.mb_name + '님, 회원가입이 완료되었습니다.<br>' +
                    '추천인: ' + response.data.recommender_name;
                
                // 모달 푸터도 숨기기
                document.querySelector('.modal-footer').style.display = 'none';
            } else {
                alert(response.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> 회원가입';
            }
        },
        error: function() {
            alert('회원가입 처리 중 오류가 발생했습니다.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> 회원가입';
        }
    });
}

// 모달 외부 클릭 시 닫기
document.getElementById('simpleRegisterModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideSimpleRegister();
    }
});
</script>

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
<?php
// head.php에 추가할 코드
// 진행중인 이벤트 가져오기
$event_sql = "SELECT ev_id, ev_subject FROM g5_event WHERE ev_status = 'ongoing' ORDER BY ev_id DESC LIMIT 5";
$event_result = sql_query($event_sql);
$header_events = array();
while($row = sql_fetch_array($event_result)) {
    $header_events[] = $row;
}
?>

<!-- 비트코인 시세 대신 이벤트 롤링 -->
<?php if(count($header_events) > 0) { ?>
<div class="market-item">
    <div class="event-rolling">
        <div class="event-content">
            <?php foreach($header_events as $event) { ?>
            <a href="<?php echo G5_URL; ?>/event.php" class="event-text">
                <i class="bi bi-gift"></i> <?php echo $event['ev_subject']; ?>
            </a>
            <?php } ?>
            <!-- 복사본 -->
            <?php foreach($header_events as $event) { ?>
            <a href="<?php echo G5_URL; ?>/event.php" class="event-text">
                <i class="bi bi-gift"></i> <?php echo $event['ev_subject']; ?>
            </a>
            <?php } ?>
        </div>
    </div>
</div>

<style>
.event-rolling {
    width: 300px;
    height: 40px;
    background: #6366f1;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: center;
}

.event-content {
    display: flex;
    animation: scroll 20s linear infinite;
}

.event-text {
    color: white;
    padding: 0 30px;
    white-space: nowrap;
    text-decoration: none;
    font-size: 14px;
}

.event-text:hover {
    color: #fbbf24;
}

@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>
<?php } ?>

                        <!-- USDT 거래 정보 -->
						<div class="market-item">
							<div class="usdt-trade-info">
								<div class="trade-item">
									<div class="trade-label">USDT 매수</div>
									<div class="trade-price buy">₩<?php echo number_format($usdt_buy_price); ?></div>
								</div>
								<div class="trade-item">
									<div class="trade-label">USDT 매도</div>
									<div class="trade-price sell">₩<?php echo number_format($usdt_sell_price); ?></div>
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
                <li><a href="<?php echo G5_URL ?>/listing_news.php">
                    <i class="bi bi-megaphone"></i> 신규상장소식
                </a></li>
                <li><a href="<?php echo G5_URL ?>/otc.php">
                    <i class="bi bi-currency-exchange"></i> 해외테더구매
                </a></li>
                <li><a href="<?php echo G5_URL ?>/event.php">
                    <i class="bi bi-gift"></i> 이벤트
                </a></li>
                <li><a href="<?php echo G5_URL ?>/community.php">
                    <i class="bi bi-people"></i> 커뮤니티
                </a></li>
                <li><a href="<?php echo G5_URL ?>/consultation.php">
                    <i class="bi bi-headset"></i> 광고 상담 신청
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
    
    <div class="mobile-market-widget">
        <h4>실시간 시세</h4>
        <div class="mobile-market-items">
<?php
// head.php에 추가할 코드
// 진행중인 이벤트 가져오기
$event_sql = "SELECT ev_id, ev_subject FROM g5_event WHERE ev_status = 'ongoing' ORDER BY ev_id DESC LIMIT 5";
$event_result = sql_query($event_sql);
$header_events = array();
while($row = sql_fetch_array($event_result)) {
    $header_events[] = $row;
}
?>

<!-- 비트코인 시세 대신 이벤트 롤링 -->
<?php if(count($header_events) > 0) { ?>
<div class="market-item">
    <div class="event-rolling">
        <div class="event-content">
            <?php foreach($header_events as $event) { ?>
            <a href="<?php echo G5_URL; ?>/event.php" class="event-text">
                <i class="bi bi-gift"></i> <?php echo $event['ev_subject']; ?>
            </a>
            <?php } ?>
            <!-- 복사본 -->
            <?php foreach($header_events as $event) { ?>
            <a href="<?php echo G5_URL; ?>/event.php" class="event-text">
                <i class="bi bi-gift"></i> <?php echo $event['ev_subject']; ?>
            </a>
            <?php } ?>
        </div>
    </div>
</div>

<style>
.event-rolling {
    width: 300px;
    height: 40px;
    background: #6366f1;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: center;
}

.event-content {
    display: flex;
    animation: scroll 20s linear infinite;
}

.event-text {
    color: white;
    padding: 0 30px;
    white-space: nowrap;
    text-decoration: none;
    font-size: 14px;
}

.event-text:hover {
    color: #fbbf24;
}

@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* 모바일 */
@media (max-width: 768px) {
    .event-rolling {
        width: 100%;
        max-width: 250px;
    }
    
    .event-text {
        font-size: 12px;
        padding: 0 20px;
    }
}
</style>
<?php } ?>

<!-- 모바일 메뉴 - 비트코인 대신 이벤트 -->
<?php if(count($header_events) > 0) { ?>
<div class="mobile-coin-item">
    <div class="coin-icon"><i class="bi bi-gift-fill"></i></div>
    <div class="coin-details">
        <span class="coin-name">진행중 이벤트</span>
        <span class="coin-price"><?php echo $header_events[0]['ev_subject']; ?></span>
        <span class="coin-change positive">
            <a href="<?php echo G5_URL; ?>/event.php" style="color: inherit; text-decoration: none;">
                <i class="bi bi-arrow-right-circle"></i> 참여하기
            </a>
        </span>
    </div>
</div>
<?php } ?>
			<div class="mobile-usdt-item">
				<div class="price-item">
					<span class="label">USDT 매수</span>
					<span class="price buy">₩<?php echo number_format($usdt_buy_price); ?></span>
				</div>
				<div class="price-item">
					<span class="label">USDT 매도</span>
					<span class="price sell">₩<?php echo number_format($usdt_sell_price); ?></span>
				</div>
			</div>        </div>
    </div>
    
    <nav class="mobile-nav-menu">
        <ul>
            <li><a href="<?php echo G5_URL ?>">
                <i class="bi bi-house-door"></i> 홈
            </a></li>
            <li><a href="<?php echo G5_URL ?>/listing_news.php">
                <i class="bi bi-megaphone"></i> 신규상장소식
            </a></li>
            <li><a href="<?php echo G5_URL ?>/otc.php">
                <i class="bi bi-currency-exchange"></i> 해외테더구매
				
            </a></li>
            <li><a href="<?php echo G5_URL ?>/event.php">
                <i class="bi bi-gift"></i> 이벤트
            </a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=community">
                <i class="bi bi-people"></i> 커뮤니티
            </a></li>
            <li><a href="<?php echo G5_URL ?>/consultation.php">
                <i class="bi bi-headset"></i> 광고 상담 신청
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