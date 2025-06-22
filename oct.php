<?php
/*
 * 파일명: otc.php
 * 위치: /
 * 기능: 장외거래(OTC) 페이지
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '장외거래 (OTC)';

/* 로그인 체크 (필수) */
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

include_once('./_head.php');
?>

<!-- ===================================
     장외거래 페이지 스타일
     =================================== -->
<style>
/* 컨테이너 */
.otc-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 페이지 헤더 */
.otc-header {
    text-align: center;
    margin-bottom: 50px;
}

.otc-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
}

.otc-header p {
    font-size: 18px;
    color: #6b7280;
    line-height: 1.6;
}

/* OTC 정보 박스 */
.otc-info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 40px;
}

.otc-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.info-item {
    text-align: center;
}

.info-icon {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.info-icon i {
    font-size: 28px;
    color: #3b82f6;
}

.info-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.info-desc {
    font-size: 14px;
    color: #6b7280;
}

/* 거래 유형 선택 */
.trade-type-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #3b82f6;
}

.trade-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.trade-type-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.3s;
}

.trade-type-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.trade-type-card.active {
    border-color: #3b82f6;
    background: #eff6ff;
}

.trade-type-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.trade-type-icon {
    width: 48px;
    height: 48px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.trade-type-icon i {
    font-size: 24px;
}

.trade-type-icon.buy {
    background: #fee2e2;
    color: #dc2626;
}

.trade-type-icon.sell {
    background: #dcfce7;
    color: #16a34a;
}

.trade-type-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.trade-type-desc {
    color: #6b7280;
    font-size: 14px;
}

/* 거래 폼 */
.otc-form-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 40px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-label .required {
    color: #ef4444;
}

/* 입력 필드 */
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
    background: #f9fafb;
    border-right: 1px solid #e5e7eb;
    color: #6b7280;
}

.form-control {
    flex: 1;
    padding: 10px 12px;
    border: none;
    font-size: 14px;
    outline: none;
}

.form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    cursor: pointer;
}

.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 요약 박스 */
.summary-box {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-top: 30px;
}

.summary-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 2px solid #d1d5db;
    font-weight: 600;
    font-size: 16px;
}

.summary-label {
    color: #6b7280;
}

.summary-value {
    color: #1f2937;
    font-weight: 500;
}

/* 버튼 */
.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-submit {
    padding: 14px 32px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-submit:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-reset {
    padding: 14px 32px;
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-reset:hover {
    background: #e5e7eb;
}

/* 알림 메시지 */
.alert-info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-info i {
    color: #3b82f6;
    font-size: 20px;
}

.alert-info p {
    margin: 0;
    color: #1e40af;
    font-size: 14px;
}

/* 반응형 */
@media (max-width: 768px) {
    .otc-form-section {
        padding: 24px 16px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-submit,
    .btn-reset {
        width: 100%;
    }
}
</style>

<!-- ===================================
     장외거래 페이지 콘텐츠
     =================================== -->
<div class="otc-container">
    <!-- 페이지 헤더 -->
    <div class="otc-header">
        <h1>장외거래 (OTC)</h1>
        <p>대량 거래를 위한 전문 OTC 서비스<br>
        안전하고 빠른 거래를 보장합니다</p>
    </div>
    
    <!-- OTC 정보 박스 -->
    <div class="otc-info-box">
        <h3 style="text-align: center; margin-bottom: 20px;">OTC 거래의 장점</h3>
        <div class="otc-info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4 class="info-title">안전한 거래</h4>
                <p class="info-desc">에스크로 시스템으로<br>안전한 거래 보장</p>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-lightning-charge"></i>
                </div>
                <h4 class="info-title">신속한 처리</h4>
                <p class="info-desc">전담 매니저를 통한<br>빠른 거래 진행</p>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h4 class="info-title">최적의 가격</h4>
                <p class="info-desc">시장 상황에 맞는<br>최적의 가격 제공</p>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-headset"></i>
                </div>
                <h4 class="info-title">1:1 지원</h4>
                <p class="info-desc">전문 상담사의<br>맞춤형 지원</p>
            </div>
        </div>
    </div>
    
    <!-- 거래 유형 선택 -->
    <div class="trade-type-section">
        <h2 class="section-title">
            <i class="bi bi-arrow-left-right"></i> 거래 유형 선택
        </h2>
        
        <div class="trade-type-grid">
            <!-- 구매 -->
            <div class="trade-type-card" onclick="selectTradeType('buy')">
                <div class="trade-type-header">
                    <div class="trade-type-icon buy">
                        <i class="bi bi-bag-plus"></i>
                    </div>
                    <div>
                        <h3 class="trade-type-title">구매하기</h3>
                        <p class="trade-type-desc">암호화폐를 구매합니다</p>
                    </div>
                </div>
            </div>
            
            <!-- 판매 -->
            <div class="trade-type-card" onclick="selectTradeType('sell')">
                <div class="trade-type-header">
                    <div class="trade-type-icon sell">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <h3 class="trade-type-title">판매하기</h3>
                        <p class="trade-type-desc">암호화폐를 판매합니다</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 거래 폼 -->
    <div class="otc-form-section" id="tradeForm" style="display:none;">
        <h2 class="section-title">
            <i class="bi bi-pencil-square"></i> <span id="formTitle">거래 신청서</span>
        </h2>
        
        <div class="alert-info">
            <i class="bi bi-info-circle"></i>
            <p>최소 거래 금액은 1,000만원 이상입니다. 거래 신청 후 전문 상담사가 연락드립니다.</p>
        </div>
        
        <form method="post" action="./otc_process.php" onsubmit="return validateOTCForm(this);">
            <input type="hidden" name="trade_type" id="trade_type" value="">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">암호화폐 선택 <span class="required">*</span></label>
                    <select name="crypto_type" class="form-select" required onchange="updateSummary()">
                        <option value="">선택하세요</option>
                        <option value="BTC">비트코인 (BTC)</option>
                        <option value="ETH">이더리움 (ETH)</option>
                        <option value="USDT">테더 (USDT)</option>
                        <option value="XRP">리플 (XRP)</option>
                        <option value="other">기타</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">수량 <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" name="quantity" class="form-control" 
                               placeholder="0.00" required step="0.00000001"
                               onchange="updateSummary()">
                        <span class="input-group-text" id="cryptoUnit">개</span>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">희망 거래가 (KRW) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₩</span>
                        <input type="number" name="price_krw" class="form-control" 
                               placeholder="0" required
                               onchange="updateSummary()">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">결제 방법 <span class="required">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">선택하세요</option>
                        <option value="bank">은행 송금</option>
                        <option value="cash">현금 거래</option>
                        <option value="crypto">암호화폐 교환</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">추가 요청사항</label>
                <textarea name="memo" class="form-control" rows="4" 
                          placeholder="추가 요청사항이 있으시면 입력해주세요"
                          style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; resize: vertical;"></textarea>
            </div>
            
            <!-- 거래 요약 -->
            <div class="summary-box">
                <h3 class="summary-title">거래 요약</h3>
                <div class="summary-row">
                    <span class="summary-label">거래 유형</span>
                    <span class="summary-value" id="summaryType">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">암호화폐</span>
                    <span class="summary-value" id="summaryCrypto">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">수량</span>
                    <span class="summary-value" id="summaryQuantity">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">단가</span>
                    <span class="summary-value" id="summaryPrice">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">총 거래금액</span>
                    <span class="summary-value" id="summaryTotal">-</span>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send"></i> 거래 신청
                </button>
                <button type="button" class="btn-reset" onclick="resetForm()">
                    <i class="bi bi-arrow-clockwise"></i> 다시 작성
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// 거래 유형 선택
function selectTradeType(type) {
    // 카드 활성화
    document.querySelectorAll('.trade-type-card').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // 폼 표시
    document.getElementById('tradeForm').style.display = 'block';
    document.getElementById('trade_type').value = type;
    
    // 제목 변경
    const formTitle = document.getElementById('formTitle');
    if (type === 'buy') {
        formTitle.textContent = '암호화폐 구매 신청서';
    } else {
        formTitle.textContent = '암호화폐 판매 신청서';
    }
    
    // 요약 업데이트
    updateSummary();
    
    // 스크롤
    document.getElementById('tradeForm').scrollIntoView({ behavior: 'smooth' });
}

// 암호화폐 선택 시 단위 변경
document.querySelector('select[name="crypto_type"]').addEventListener('change', function() {
    const unit = document.getElementById('cryptoUnit');
    if (this.value) {
        unit.textContent = this.value === 'other' ? '개' : this.value;
    }
});

// 거래 요약 업데이트
function updateSummary() {
    const tradeType = document.getElementById('trade_type').value;
    const cryptoType = document.querySelector('select[name="crypto_type"]').value;
    const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
    const price = parseFloat(document.querySelector('input[name="price_krw"]').value) || 0;
    
    // 거래 유형
    document.getElementById('summaryType').textContent = tradeType === 'buy' ? '구매' : '판매';
    
    // 암호화폐
    const cryptoText = document.querySelector('select[name="crypto_type"] option:checked').textContent;
    document.getElementById('summaryCrypto').textContent = cryptoType ? cryptoText : '-';
    
    // 수량
    document.getElementById('summaryQuantity').textContent = quantity > 0 ? 
        quantity.toLocaleString() + ' ' + (cryptoType || '개') : '-';
    
    // 단가
    document.getElementById('summaryPrice').textContent = price > 0 ? 
        '₩' + price.toLocaleString() : '-';
    
    // 총 거래금액
    const total = quantity * price;
    document.getElementById('summaryTotal').textContent = total > 0 ? 
        '₩' + total.toLocaleString() : '-';
}

// 폼 유효성 검사
function validateOTCForm(f) {
    // 거래 유형 체크
    if (!f.trade_type.value) {
        alert('거래 유형을 선택해주세요.');
        return false;
    }
    
    // 최소 거래금액 체크 (1000만원)
    const quantity = parseFloat(f.quantity.value);
    const price = parseFloat(f.price_krw.value);
    const total = quantity * price;
    
    if (total < 10000000) {
        alert('최소 거래 금액은 1,000만원 이상입니다.\n현재 거래금액: ' + total.toLocaleString() + '원');
        return false;
    }
    
    return confirm('거래를 신청하시겠습니까?\n신청 후 전문 상담사가 연락드립니다.');
}

// 폼 초기화
function resetForm() {
    if (confirm('작성한 내용을 모두 지우시겠습니까?')) {
        document.getElementById('tradeForm').querySelector('form').reset();
        updateSummary();
    }
}

// 숫자 입력 시 콤마 추가
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        updateSummary();
    });
});
</script>

<?php
include_once('./_tail.php');
?>