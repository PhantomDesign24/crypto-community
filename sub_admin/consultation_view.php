<?php
/*
 * 파일명: consultation_view.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 상담신청 상세보기 및 처리
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// ===================================
// 접근 권한 확인
// ===================================

/* 상담신청 ID 확인 */
$cs_id = isset($_GET['cs_id']) ? (int)$_GET['cs_id'] : 0;
if (!$cs_id) {
    alert('상담신청 정보가 없습니다.', './consultation_list.php');
}

// 헤더 포함 (권한 체크 포함)
include_once('./header.php');

// ===================================
// 상담신청 정보 조회
// ===================================

$sql = "SELECT * FROM g5_consultation WHERE cs_id = '{$cs_id}'";
$cs = sql_fetch($sql);

if (!$cs['cs_id']) {
    alert('존재하지 않는 상담신청입니다.', './consultation_list.php');
}

// 최고관리자가 아닌 경우 권한 확인
if (!$is_admin) {
    // 신청자가 자신의 하위 회원인지 확인
    if ($cs['mb_id']) {
        $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
                WHERE mb_id = '{$cs['mb_id']}' AND mb_recommend = '{$member['mb_id']}'";
        $row = sql_fetch($sql);
        if ($row['cnt'] == 0) {
            alert('접근 권한이 없습니다.', './consultation_list.php');
        }
    } else {
        alert('접근 권한이 없습니다.', './consultation_list.php');
    }
}

// ===================================
// 상태 변경 처리
// ===================================

if ($_POST['act'] == 'update_status') {
    $new_status = isset($_POST['cs_status']) ? trim($_POST['cs_status']) : '';
    $cs_memo = isset($_POST['cs_memo']) ? trim($_POST['cs_memo']) : '';
    
    if (!in_array($new_status, array('접수', '진행중', '완료'))) {
        alert('올바른 상태값이 아닙니다.');
    }
    
    $sql = "UPDATE g5_consultation SET 
            cs_status = '{$new_status}',
            cs_memo = '".sql_real_escape_string($cs_memo)."'
            WHERE cs_id = '{$cs_id}'";
    
    if (sql_query($sql)) {
        alert('상담 상태가 변경되었습니다.', './consultation_view.php?cs_id='.$cs_id);
    } else {
        alert('상태 변경 중 오류가 발생했습니다.');
    }
}

/* 페이지 제목 */
$g5['title'] = '상담신청 상세보기';

// 신청자 정보
$mb_info = '';
if ($cs['mb_id']) {
    $mb = get_member($cs['mb_id']);
    $mb_info = $mb['mb_id'] . ' / ' . $mb['mb_name'];
}
?>

<style>
/* 상담 상세보기 스타일 */
.consultation-view-container {
    max-width: 100%;
    margin: 0 auto;
}

/* 정보 카드 */
.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    padding: 20px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-title i {
    color: #3b82f6;
}

.card-body {
    padding: 20px;
}

/* 정보 테이블 */
.info-table {
    width: 100%;
}

.info-table tr {
    border-bottom: 1px solid #f3f4f6;
}

.info-table tr:last-child {
    border-bottom: none;
}

.info-table th {
    padding: 12px 16px;
    background: #f9fafb;
    font-weight: 500;
    color: #374151;
    width: 150px;
    text-align: left;
}

.info-table td {
    padding: 12px 16px;
    color: #1f2937;
}

/* 상태 뱃지 */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
}

.status-badge.status-접수 {
    background: #fee2e2;
    color: #dc2626;
}

.status-badge.status-진행중 {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.status-완료 {
    background: #dbeafe;
    color: #2563eb;
}

/* 내용 영역 */
.content-area {
    padding: 20px;
    background: #f9fafb;
    border-radius: 8px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-break: break-word;
}

/* 관리자 메모 */
.admin-section {
    margin-top: 40px;
}

.memo-textarea {
    width: 100%;
    min-height: 120px;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
}

/* 상태 선택 */
.status-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    margin-bottom: 16px;
}

/* 버튼 영역 */
.button-area {
    display: flex;
    gap: 8px;
    justify-content: space-between;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

/* 반응형 */
@media (max-width: 768px) {
    .info-table th {
        width: 100px;
        font-size: 13px;
    }
    
    .info-table td {
        font-size: 13px;
    }
    
    .button-area {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="consultation-view-container">
    <!-- 기본 정보 -->
    <div class="info-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="bi bi-person-lines-fill"></i> 신청자 정보
            </h2>
        </div>
        <div class="card-body">
            <table class="info-table">
                <tr>
                    <th>상태</th>
                    <td>
                        <span class="status-badge status-<?php echo $cs['cs_status']; ?>">
                            <?php echo $cs['cs_status']; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>이름</th>
                    <td><?php echo get_text($cs['cs_name']); ?></td>
                </tr>
                <tr>
                    <th>연락처</th>
                    <td><?php echo get_text($cs['cs_hp']); ?></td>
                </tr>
                <tr>
                    <th>이메일</th>
                    <td><?php echo $cs['cs_email'] ? get_text($cs['cs_email']) : '-'; ?></td>
                </tr>
                <?php if ($cs['cs_category']) { ?>
                <tr>
                    <th>상담분야</th>
                    <td><?php echo get_text($cs['cs_category']); ?></td>
                </tr>
                <?php } ?>
                <?php if ($cs['cs_time']) { ?>
                <tr>
                    <th>희망시간</th>
                    <td><?php echo get_text($cs['cs_time']); ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th>신청일시</th>
                    <td><?php echo $cs['cs_datetime']; ?></td>
                </tr>
                <?php if ($mb_info) { ?>
                <tr>
                    <th>회원정보</th>
                    <td><?php echo $mb_info; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    
    <!-- 상담 내용 -->
    <div class="info-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="bi bi-chat-left-text"></i> 상담 내용
            </h2>
        </div>
        <div class="card-body">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                <?php echo get_text($cs['cs_subject']); ?>
            </h3>
            <div class="content-area">
                <?php echo nl2br(get_text($cs['cs_content'])); ?>
            </div>
        </div>
    </div>
    
    <!-- 관리자 처리 -->
    <div class="info-card admin-section">
        <div class="card-header">
            <h2 class="card-title">
                <i class="bi bi-gear"></i> 상담 처리
            </h2>
        </div>
        <div class="card-body">
            <form method="post" onsubmit="return confirm('상태를 변경하시겠습니까?');">
                <input type="hidden" name="act" value="update_status">
                
                <label style="display: block; font-weight: 500; margin-bottom: 8px;">상담 상태</label>
                <select name="cs_status" class="status-select">
                    <option value="접수" <?php echo $cs['cs_status'] == '접수' ? 'selected' : ''; ?>>접수</option>
                    <option value="진행중" <?php echo $cs['cs_status'] == '진행중' ? 'selected' : ''; ?>>진행중</option>
                    <option value="완료" <?php echo $cs['cs_status'] == '완료' ? 'selected' : ''; ?>>완료</option>
                </select>
                
                <label style="display: block; font-weight: 500; margin-bottom: 8px;">처리 메모</label>
                <textarea name="cs_memo" class="memo-textarea" placeholder="처리 내용이나 메모를 입력하세요."><?php echo get_text($cs['cs_memo']); ?></textarea>
                
                <div class="button-area">
                    <a href="./consultation_list.php" class="btn btn-secondary">
                        <i class="bi bi-list"></i> 목록으로
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> 상태 변경
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once('./footer.php');
?>