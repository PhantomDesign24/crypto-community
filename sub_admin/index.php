<?php
/*
 * 파일명: index.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 대시보드
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// ===================================
// 추천 코드 생성 (없는 경우)
// ===================================

/* 추천 코드가 없으면 생성 */
if (!$member['mb_referral_code'] && $member['mb_grade'] >= 2) {
    $referral_code = generate_referral_code();
    sql_query("UPDATE {$g5['member_table']} SET mb_referral_code = '{$referral_code}' WHERE mb_id = '{$member['mb_id']}'");
    $member['mb_referral_code'] = $referral_code;
}

// ===================================
// 추천 코드 변경 처리
// ===================================

/* 추천 코드 변경 요청 처리 */
if ($_POST['act'] == 'change_referral_code') {
    $new_code = strtoupper(trim($_POST['new_referral_code']));
    
    // 유효성 검사
    if (strlen($new_code) !== 8) {
        alert('추천 코드는 8자리여야 합니다.');
    }
    
    if (!preg_match("/^[A-Z0-9]+$/", $new_code)) {
        alert('추천 코드는 영문 대문자와 숫자만 가능합니다.');
    }
    
    // 중복 확인
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE mb_referral_code = '{$new_code}' AND mb_id != '{$member['mb_id']}'";
    $row = sql_fetch($sql);
    
    if ($row['cnt'] > 0) {
        alert('이미 사용 중인 추천 코드입니다.');
    }
    
    // 추천 코드 업데이트
    sql_query("UPDATE {$g5['member_table']} SET mb_referral_code = '{$new_code}' WHERE mb_id = '{$member['mb_id']}'");
    
    alert('추천 코드가 변경되었습니다.', './index.php');
}

// 추천 코드 생성 함수
function generate_referral_code() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code_length = 8;
    
    do {
        $code = '';
        for ($i = 0; $i < $code_length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // 중복 확인
        $sql = "SELECT COUNT(*) as cnt FROM {$GLOBALS['g5']['member_table']} WHERE mb_referral_code = '{$code}'";
        $row = sql_fetch($sql);
    } while ($row['cnt'] > 0);
    
    return $code;
}

// ===================================
// 페이지 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '대시보드';

// 헤더 포함 (권한 체크 포함)
include_once('./header.php');

// ===================================
// 통계 데이터 조회 (이미 헤더에서 조회함)
// ===================================

// ===================================
// 이벤트 신청 현황 부분 수정
// ===================================

// index.php의 이벤트 통계 부분을 다음과 같이 수정하세요:

// ===================================
// 이벤트 신청 현황 - 대기중
// ===================================
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        AND a.ea_status = 'applied'";
$row = sql_fetch($sql);
$event_wait = $row['cnt'];

// ===================================
// 이벤트 신청 현황 - 완료
// ===================================
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        AND a.ea_status = 'paid'";
$row = sql_fetch($sql);
$event_complete = $row['cnt'];

// ===================================
// 최근 이벤트 신청 목록 (중요: 여기가 수정되어야 함)
// ===================================
$sql = "SELECT a.*, m.mb_id, m.mb_name, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount 
        FROM g5_event_apply a 
        LEFT JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        LEFT JOIN g5_event e ON a.ev_id = e.ev_id 
        WHERE m.mb_recommend = '{$member['mb_id']}' 
        ORDER BY a.ea_datetime DESC 
        LIMIT 5";
$recent_events = sql_query($sql);

// ===================================
// 최근 가입 회원 목록
// ===================================

/* 최근 가입한 하위 회원 5명 */
$sql = "SELECT mb_id, mb_name, mb_email, mb_datetime, mb_grade 
        FROM {$g5['member_table']} 
        WHERE mb_recommend = '{$member['mb_id']}' 
        ORDER BY mb_datetime DESC 
        LIMIT 5";
$recent_members = sql_query($sql);

// ===================================
// 최근 이벤트 신청 목록
// ===================================

?>

<style>
/* ===================================
 * 대시보드 전용 스타일
 * =================================== */

/* 추천 코드 박스 */
.referral-code-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.referral-code-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.referral-info h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    opacity: 0.9;
}

.referral-code-display {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: 3px;
    margin-bottom: 12px;
}

.referral-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-referral {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-referral:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

/* 추천 코드 변경 폼 */
.code-change-form {
    margin-top: 20px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.code-input {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
    letter-spacing: 2px;
    margin-bottom: 12px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

/* 통계 카드 그리드 */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* 통계 카드 */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
}

.stat-card.members {
    --card-color: #4F46E5;
    --card-color-light: #818CF8;
}

.stat-card.today {
    --card-color: #059669;
    --card-color-light: #34D399;
}

.stat-card.wait {
    --card-color: #F59E0B;
    --card-color-light: #FCD34D;
}

.stat-card.complete {
    --card-color: #8B5CF6;
    --card-color-light: #C084FC;
}

/* 통계 카드 아이콘 */
.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 28px;
}

.stat-card.members .stat-icon {
    background: #EEF2FF;
    color: #4F46E5;
}

.stat-card.today .stat-icon {
    background: #D1FAE5;
    color: #059669;
}

.stat-card.wait .stat-icon {
    background: #FEF3C7;
    color: #F59E0B;
}

.stat-card.complete .stat-icon {
    background: #F3E8FF;
    color: #8B5CF6;
}

/* 통계 카드 내용 */
.stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #1F2937;
}

.stat-label {
    font-size: 15px;
    color: #6B7280;
    font-weight: 500;
}

/* 섹션 스타일 */
.dashboard-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #E5E7EB;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1F2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #6B7280;
}

.view-more {
    font-size: 14px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.3s;
}

.view-more:hover {
    color: #2563eb;
    gap: 8px;
}

/* 테이블 스타일 */
.dashboard-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.dashboard-table th {
    background: #F9FAFB;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #E5E7EB;
}

.dashboard-table td {
    padding: 16px;
    border-bottom: 1px solid #F3F4F6;
    color: #374151;
}

.dashboard-table tr:last-child td {
    border-bottom: none;
}

.dashboard-table tr:hover td {
    background: #F9FAFB;
}

/* 회원 정보 */
.member-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.member-avatar {
    width: 36px;
    height: 36px;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.member-name {
    font-weight: 600;
    color: #1f2937;
}

.member-id {
    font-size: 12px;
    color: #6b7280;
}

/* 상태 뱃지 */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.wait {
    background: #FEF3C7;
    color: #92400E;
}

.status-badge.complete {
    background: #D1FAE5;
    color: #065F46;
}

/* 빈 데이터 */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9CA3AF;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: #E5E7EB;
}

/* 반응형 */
@media (max-width: 768px) {
    .referral-code-content {
        flex-direction: column;
        text-align: center;
    }
    
    .referral-code-display {
        font-size: 24px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-table {
        font-size: 13px;
    }
    
    .dashboard-table th,
    .dashboard-table td {
        padding: 12px;
    }
}
</style>

<!-- 추천 코드 섹션 -->
<div class="referral-code-section">
    <div class="referral-code-content">
        <div class="referral-info">
            <h2>나의 추천 코드</h2>
            <div class="referral-code-display" id="referralCode"><?php echo $member['mb_referral_code']; ?></div>
            <div class="referral-actions">
                <button class="btn-referral" onclick="copyReferralCode()">
                    <i class="bi bi-clipboard"></i> 복사하기
                </button>
                <button class="btn-referral" onclick="toggleChangeForm()">
                    <i class="bi bi-pencil"></i> 변경하기
                </button>
            </div>
        </div>
    </div>
    
    <!-- 추천 코드 변경 폼 -->
    <div id="changeCodeForm" class="code-change-form" style="display:none;">
        <form method="post" action="" onsubmit="return validateChangeCode()">
            <input type="hidden" name="act" value="change_referral_code">
            <input type="text" name="new_referral_code" id="newReferralCode" 
                   class="code-input" placeholder="새 추천 코드 입력 (8자리)" 
                   maxlength="8" style="text-transform: uppercase;">
            <div class="form-actions">
                <button type="submit" class="btn-referral">
                    <i class="bi bi-check"></i> 확인
                </button>
                <button type="button" class="btn-referral" onclick="toggleChangeForm()">
                    <i class="bi bi-x"></i> 취소
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 통계 카드 -->
<div class="stats-grid">
    <div class="stat-card members">
        <div class="stat-icon">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-value"><?php echo number_format($total_members); ?></div>
        <div class="stat-label">전체 하위 회원</div>
    </div>
    
    <div class="stat-card today">
        <div class="stat-icon">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div class="stat-value"><?php echo number_format($today_members); ?></div>
        <div class="stat-label">오늘 가입 회원</div>
    </div>
    
    <div class="stat-card wait">
        <div class="stat-icon">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-value"><?php echo number_format($event_wait); ?></div>
        <div class="stat-label">이벤트 신청 대기</div>
    </div>
    
    <div class="stat-card complete">
        <div class="stat-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-value"><?php echo number_format($event_complete); ?></div>
        <div class="stat-label">이벤트 지급 완료</div>
    </div>
</div>

<!-- 최근 가입 회원 -->
<div class="dashboard-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="bi bi-person-lines-fill"></i>
            최근 가입 회원
        </h2>
        <a href="./member_list.php" class="view-more">
            전체 보기 <i class="bi bi-arrow-right-short"></i>
        </a>
    </div>
    
    <?php if (sql_num_rows($recent_members) > 0) { ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>회원정보</th>
                <th>이메일</th>
                <th>가입일</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = sql_fetch_array($recent_members)) { ?>
            <tr>
                <td>
                    <div class="member-info">
                        <div class="member-avatar">
                            <?php echo mb_substr($row['mb_name'], 0, 1); ?>
                        </div>
                        <div>
                            <div class="member-name"><?php echo get_text($row['mb_name']); ?></div>
                            <div class="member-id"><?php echo $row['mb_id']; ?></div>
                        </div>
                    </div>
                </td>
                <td><?php echo $row['mb_email']; ?></td>
                <td><?php echo date('Y-m-d', strtotime($row['mb_datetime'])); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
    <div class="empty-state">
        <i class="bi bi-person-x"></i>
        <p>아직 가입한 하위 회원이 없습니다.</p>
    </div>
    <?php } ?>
</div>

<!-- 최근 이벤트 신청 섹션을 다음과 같이 수정 -->
<div class="dashboard-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="bi bi-gift"></i>
            최근 이벤트 신청
        </h2>
        <a href="./event_list.php" class="view-more">
            전체 보기 <i class="bi bi-arrow-right-short"></i>
        </a>
    </div>
    
    <?php if (sql_num_rows($recent_events) > 0) { ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>신청자</th>
                <th>이벤트명</th>
                <th>코인</th>
                <th>신청일</th>
                <th>상태</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = sql_fetch_array($recent_events)) { ?>
            <tr>
                <td>
                    <div class="member-info">
                        <div class="member-avatar">
                            <?php echo mb_substr($row['mb_name'], 0, 1); ?>
                        </div>
                        <div>
                            <div class="member-name"><?php echo get_text($row['mb_name']); ?></div>
                            <div class="member-id"><?php echo $row['mb_id']; ?></div>
                        </div>
                    </div>
                </td>
                <td><?php echo get_text($row['ev_subject']); ?></td>
                <td>
                    <span style="background: #EFF6FF; color: #3B82F6; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 600;">
                        <?php echo $row['ev_coin_symbol']; ?> <?php echo $row['ev_coin_amount']; ?>
                    </span>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($row['ea_datetime'])); ?></td>
                <td>
                    <?php if ($row['ea_status'] == 'applied') { ?>
                        <span class="status-badge wait">대기중</span>
                    <?php } else { ?>
                        <span class="status-badge complete">지급완료</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <p>아직 이벤트 신청 내역이 없습니다.</p>
    </div>
    <?php } ?>
</div>

<script>
// 추천 코드 복사
function copyReferralCode() {
    const code = document.getElementById('referralCode').textContent;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(() => {
            showCopySuccess();
        });
    } else {
        // Fallback
        const textArea = document.createElement("textarea");
        textArea.value = code;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            alert('복사에 실패했습니다.');
        }
        
        document.body.removeChild(textArea);
    }
}

function showCopySuccess() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i> 복사완료!';
    btn.style.background = 'rgba(16, 185, 129, 0.3)';
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
    }, 2000);
}

// 추천 코드 변경 폼 토글
function toggleChangeForm() {
    const form = document.getElementById('changeCodeForm');
    const isVisible = form.style.display === 'block';
    
    form.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) {
        document.getElementById('newReferralCode').focus();
    }
}

// 추천 코드 입력 시 자동 대문자 변환
document.getElementById('newReferralCode').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// 추천 코드 변경 유효성 검사
function validateChangeCode() {
    const newCode = document.getElementById('newReferralCode').value;
    
    if (newCode.length !== 8) {
        alert('추천 코드는 8자리여야 합니다.');
        return false;
    }
    
    if (!/^[A-Z0-9]+$/.test(newCode)) {
        alert('추천 코드는 영문 대문자와 숫자만 가능합니다.');
        return false;
    }
    
    return confirm('추천 코드를 변경하시겠습니까?');
}
</script>

<?php
// 푸터 포함
include_once('./footer.php');
?>