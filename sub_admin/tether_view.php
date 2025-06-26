<?php
/*
 * 파일명: tether_view.php
 * 위치: /sub_admin/tether_view.php
 * 기능: 테더 구매 신청 상세보기 및 수정
 * 작성일: 2025-01-26
 */

include_once('./_common.php');
include_once('./header.php');

// 권한 체크
if($member['mb_grade'] < 2) {
    alert('접근 권한이 없습니다.', G5_URL);
}

$tp_id = isset($_GET['tp_id']) ? (int)$_GET['tp_id'] : 0;

if(!$tp_id) {
    alert('잘못된 접근입니다.', './tether_list.php');
}

// 수정 처리
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tp_status = isset($_POST['tp_status']) ? $_POST['tp_status'] : '';
    $tp_memo = isset($_POST['tp_memo']) ? trim($_POST['tp_memo']) : '';
    
    $sql = "UPDATE g5_tether_purchase SET 
            tp_status = '{$tp_status}',
            tp_memo = '".sql_real_escape_string($tp_memo)."'";
    
    // 상태 변경시 처리일시 기록
    if($tp_status == '1') {
        $sql .= ", tp_process_datetime = '".G5_TIME_YMDHIS."'";
    } else if($tp_status == '2') {
        $sql .= ", tp_complete_datetime = '".G5_TIME_YMDHIS."'";
    }
    
    $sql .= " WHERE tp_id = '{$tp_id}'";
    sql_query($sql);
    
    alert('수정되었습니다.', './tether_view.php?tp_id='.$tp_id);
}

// 신청 정보 조회
$sql = "SELECT tp.*, m.mb_name, m.mb_nick, m.mb_email, m.mb_hp as member_hp, m.mb_referral_code
        FROM g5_tether_purchase tp
        LEFT JOIN {$g5['member_table']} m ON tp.mb_id = m.mb_id
        WHERE tp.tp_id = '{$tp_id}'";
$purchase = sql_fetch($sql);

if(!$purchase) {
    alert('존재하지 않는 신청입니다.', './tether_list.php');
}

// 같은 회원의 다른 신청 내역
$sql = "SELECT * FROM g5_tether_purchase 
        WHERE mb_id = '{$purchase['mb_id']}' AND tp_id != '{$tp_id}'
        ORDER BY tp_id DESC LIMIT 5";
$history_result = sql_query($sql);
?>

<div class="sa-content">
    <div class="row">
        <!-- 좌측: 신청 정보 -->
        <div class="col-lg-8">
            <!-- 기본 정보 -->
            <div class="sa-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-text"></i> 신청 정보 #<?php echo $tp_id; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">상태</label>
                            <div class="col-sm-9">
                                <select name="tp_status" class="form-select" required>
                                    <option value="0" <?php echo $purchase['tp_status'] == '0' ? 'selected' : ''; ?>>신청완료</option>
                                    <option value="1" <?php echo $purchase['tp_status'] == '1' ? 'selected' : ''; ?>>진행중</option>
                                    <option value="2" <?php echo $purchase['tp_status'] == '2' ? 'selected' : ''; ?>>완료</option>
                                    <option value="9" <?php echo $purchase['tp_status'] == '9' ? 'selected' : ''; ?>>취소</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">신청자</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" value="<?php echo $purchase['tp_name']; ?> (<?php echo $purchase['mb_id']; ?>)" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">연락처</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" value="<?php echo $purchase['tp_hp']; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">송금업체</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" value="<?php echo $purchase['tp_transfer_company']; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">구매 수량</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" value="<?php echo number_format($purchase['tp_quantity'], 2); ?> USDT" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">단가</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" value="₩<?php echo number_format($purchase['tp_price_krw']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">총 금액</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext fw-bold text-primary" value="₩<?php echo number_format($purchase['tp_total_krw']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">지갑 주소</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?php echo $purchase['tp_wallet_address']; ?>" id="walletAddress" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyWallet()">
                                        <i class="bi bi-clipboard"></i> 복사
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">관리자 메모</label>
                            <div class="col-sm-9">
                                <textarea name="tp_memo" class="form-control" rows="3"><?php echo $purchase['tp_memo']; ?></textarea>
                                <small class="text-muted">고객에게는 표시되지 않습니다.</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> 수정 저장
                                </button>
                                <a href="./tether_list.php" class="btn btn-secondary">
                                    <i class="bi bi-list"></i> 목록
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 타임라인 -->
            <div class="sa-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history"></i> 처리 내역
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">신청 접수</h6>
                                <p class="text-muted mb-0"><?php echo $purchase['tp_datetime']; ?></p>
                            </div>
                        </div>
                        
                        <?php if($purchase['tp_process_datetime']) { ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">처리 시작</h6>
                                <p class="text-muted mb-0"><?php echo $purchase['tp_process_datetime']; ?></p>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if($purchase['tp_complete_datetime']) { ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">처리 완료</h6>
                                <p class="text-muted mb-0"><?php echo $purchase['tp_complete_datetime']; ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 우측: 회원 정보 -->
        <div class="col-lg-4">
            <!-- 회원 정보 -->
            <div class="sa-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person"></i> 회원 정보
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">회원ID</th>
                            <td><?php echo $purchase['mb_id']; ?></td>
                        </tr>
                        <tr>
                            <th>이름</th>
                            <td><?php echo $purchase['mb_name']; ?></td>
                        </tr>
                        <tr>
                            <th>닉네임</th>
                            <td><?php echo $purchase['mb_nick']; ?></td>
                        </tr>
                        <tr>
                            <th>이메일</th>
                            <td><?php echo $purchase['mb_email']; ?></td>
                        </tr>
                        <tr>
                            <th>휴대폰</th>
                            <td><?php echo $purchase['member_hp']; ?></td>
                        </tr>
                        <?php if($purchase['mb_referral_code']) { ?>
                        <tr>
                            <th>추천코드</th>
                            <td>
                                <span class="badge bg-primary"><?php echo $purchase['mb_referral_code']; ?></span>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
            
            <!-- 이전 신청 내역 -->
            <div class="sa-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock"></i> 이전 신청 내역
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if(sql_num_rows($history_result) > 0) { ?>
                    <div class="list-group list-group-flush">
                        <?php while($history = sql_fetch_array($history_result)) { 
                            $h_status_class = '';
                            $h_status_text = '';
                            switch($history['tp_status']) {
                                case '0': $h_status_class = 'warning'; $h_status_text = '신청'; break;
                                case '1': $h_status_class = 'info'; $h_status_text = '진행'; break;
                                case '2': $h_status_class = 'success'; $h_status_text = '완료'; break;
                                case '9': $h_status_class = 'danger'; $h_status_text = '취소'; break;
                            }
                        ?>
                        <a href="./tether_view.php?tp_id=<?php echo $history['tp_id']; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">#<?php echo $history['tp_id']; ?></h6>
                                    <small><?php echo date('Y-m-d', strtotime($history['tp_datetime'])); ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="mb-1"><?php echo number_format($history['tp_quantity'], 2); ?> USDT</div>
                                    <span class="badge bg-<?php echo $h_status_class; ?>"><?php echo $h_status_text; ?></span>
                                </div>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } else { ?>
                    <p class="text-muted text-center py-3 mb-0">이전 신청 내역이 없습니다.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* 타임라인 */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -21px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    font-size: 14px;
    font-weight: 600;
}

.timeline-content p {
    font-size: 13px;
}

/* 카드 스타일 */
.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
}
</style>

<script>
function copyWallet() {
    const walletInput = document.getElementById('walletAddress');
    walletInput.select();
    walletInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    alert('지갑 주소가 복사되었습니다.');
}
</script>

<?php
include_once('./footer.php');
?>