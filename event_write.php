<?php
/*
 * 파일명: event_write.php
 * 위치: /event_write.php
 * 기능: 이벤트 작성/수정 (관리자)
 * 작성일: 2025-01-11
 * 수정일: 2025-01-26
 */

include_once('./_common.php');

// 관리자만 접근 가능
if(!$is_admin) {
    alert('관리자만 접근 가능합니다.', G5_URL);
}

$g5['title'] = '이벤트 작성';

// 모드 구분 (w=u 수정, 없으면 신규)
$w = isset($_GET['w']) ? $_GET['w'] : '';
$ev_id = isset($_GET['ev_id']) ? (int)$_GET['ev_id'] : 0;
$event_2 = null;

if($w == 'u' && $ev_id) {
    $event_2 = sql_fetch("SELECT * FROM g5_event WHERE ev_id = '{$ev_id}'");
    if(!$event_2) {
        alert('존재하지 않는 이벤트입니다.', G5_URL.'/event.php');
    }
    $g5['title'] = '이벤트 수정';
    
    // 디버깅: 데이터 확인
    if($is_admin) {
        echo "<!-- Debug: ";
        echo "ev_subject: " . $event_2['ev_subject'] . "\n";
        echo "ev_coin_name: " . $event_2['ev_coin_name'] . "\n";
        echo "ev_start_date: " . $event_2['ev_start_date'] . "\n";
        echo " -->";
    }
}

// 폼 처리
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ev_subject = trim($_POST['ev_subject']);
    $ev_content = trim($_POST['ev_content']);
    $ev_summary = trim($_POST['ev_summary']);
    $ev_coin_name = trim($_POST['ev_coin_name']);
    $ev_coin_symbol = trim($_POST['ev_coin_symbol']);
    $ev_coin_amount = trim($_POST['ev_coin_amount']);
    $ev_start_date = $_POST['ev_start_date'] . ' ' . $_POST['ev_start_time'];
    $ev_end_date = $_POST['ev_end_date'] . ' ' . $_POST['ev_end_time'];
    $ev_recommend = isset($_POST['ev_recommend']) ? 1 : 0;
    
    // POST로 전달된 ev_id 확인
    if(isset($_POST['ev_id'])) {
        $ev_id = (int)$_POST['ev_id'];
    }
    
    // 날짜 기반으로 상태 자동 결정
    $now = time();
    $start_time = strtotime($ev_start_date);
    $end_time = strtotime($ev_end_date);
    
    if($now < $start_time) {
        $ev_status = 'scheduled';
    } else if($now >= $start_time && $now <= $end_time) {
        $ev_status = 'ongoing';
    } else {
        $ev_status = 'ended';
    }
    
    // 이미지 업로드 처리
    $ev_image = $event_2 ? $event_2['ev_image'] : '';
    if(isset($_FILES['ev_image']) && $_FILES['ev_image']['error'] == 0) {
        $upload_dir = G5_DATA_PATH.'/event';
        if(!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755);
            @chmod($upload_dir, 0755);
        }
        
        $ext = strtolower(pathinfo($_FILES['ev_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = time().'_'.uniqid().'.'.$ext;
            if(move_uploaded_file($_FILES['ev_image']['tmp_name'], $upload_dir.'/'.$filename)) {
                // 기존 이미지 삭제
                if($ev_image && file_exists($upload_dir.'/'.$ev_image)) {
                    @unlink($upload_dir.'/'.$ev_image);
                }
                $ev_image = $filename;
            }
        }
    }
    
    if($ev_id && $w == 'u') {
        // 수정
        $sql = "UPDATE g5_event SET
                ev_subject = '{$ev_subject}',
                ev_content = '{$ev_content}',
                ev_summary = '{$ev_summary}',
                ev_status = '{$ev_status}',
                ev_coin_name = '{$ev_coin_name}',
                ev_coin_symbol = '{$ev_coin_symbol}',
                ev_coin_amount = '{$ev_coin_amount}',
                ev_image = '{$ev_image}',
                ev_start_date = '{$ev_start_date}',
                ev_end_date = '{$ev_end_date}',
                ev_recommend = '{$ev_recommend}',
                ev_update_datetime = NOW()
                WHERE ev_id = '{$ev_id}'";
        sql_query($sql);
        
        alert('이벤트가 수정되었습니다.', G5_URL.'/event_view.php?ev_id='.$ev_id);
    } else {
        // 등록
        $sql = "INSERT INTO g5_event SET
                ev_subject = '{$ev_subject}',
                ev_content = '{$ev_content}',
                ev_summary = '{$ev_summary}',
                ev_type = 'airdrop',
                ev_status = '{$ev_status}',
                ev_coin_name = '{$ev_coin_name}',
                ev_coin_symbol = '{$ev_coin_symbol}',
                ev_coin_amount = '{$ev_coin_amount}',
                ev_image = '{$ev_image}',
                ev_start_date = '{$ev_start_date}',
                ev_end_date = '{$ev_end_date}',
                ev_recommend = '{$ev_recommend}',
                mb_id = '{$member['mb_id']}',
                ev_datetime = NOW()";
        sql_query($sql);
        $new_ev_id = sql_insert_id();
        
        alert('이벤트가 등록되었습니다.', G5_URL.'/event_view.php?ev_id='.$new_ev_id);
    }
}

include_once(G5_PATH.'/head.php');
?>

<!-- ===================================
     이벤트 작성 페이지
     =================================== -->
<div class="event-write-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="write-card">
                    <div class="write-header">
                        <h2><?php echo $g5['title']; ?></h2>
                        <p class="text-muted">에어드랍 이벤트를 등록하고 관리하세요</p>
                    </div>
                    
                    <form method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
                        <?php if($w == 'u' && $ev_id) { ?>
                        <input type="hidden" name="w" value="u">
                        <input type="hidden" name="ev_id" value="<?php echo $ev_id; ?>">
                        <?php } ?>
                        
                        <!-- 기본 정보 -->
                        <div class="section-title">
                            <i class="bi bi-info-circle"></i> 기본 정보
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">이벤트 제목 <span class="text-danger">*</span></label>
                            <input type="text" name="ev_subject" class="form-control" 
                                   value="<?php echo $event_2 ? $event_2['ev_subject'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">짧은 설명 (1-2줄) <span class="text-danger">*</span></label>
                            <input type="text" name="ev_summary" class="form-control" 
                                   value="<?php echo $event_2 ? $event_2['ev_summary'] : ''; ?>" 
                                   placeholder="메인 페이지에 표시될 짧은 설명을 입력하세요" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">상세 내용 <span class="text-danger">*</span></label>
                            <textarea name="ev_content" id="ev_content" class="form-control" rows="10" required><?php echo $event_2 ? $event_2['ev_content'] : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">대표 이미지</label>
                            <?php if($event_2 && $event_2['ev_image']) { ?>
                            <div class="current-image mb-2">
                                <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $event_2['ev_image']; ?>" 
                                     style="max-width: 200px; max-height: 200px;">
                                <p class="text-muted small mt-1">현재 이미지</p>
                            </div>
                            <?php } ?>
                            <input type="file" name="ev_image" class="form-control" accept="image/*">
                            <small class="text-muted">권장 크기: 600x400px</small>
                        </div>
                        
                        <!-- 코인 정보 -->
                        <div class="section-title mt-4">
                            <i class="bi bi-currency-bitcoin"></i> 코인 정보
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">코인명 <span class="text-danger">*</span></label>
                                    <input type="text" name="ev_coin_name" class="form-control" 
                                           value="<?php echo $event_2 ? $event_2['ev_coin_name'] : ''; ?>" 
                                           placeholder="예: 비트코인" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">심볼 <span class="text-danger">*</span></label>
                                    <input type="text" name="ev_coin_symbol" class="form-control" 
                                           value="<?php echo $event_2 ? $event_2['ev_coin_symbol'] : ''; ?>" 
                                           placeholder="예: BTC" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">지급 수량 <span class="text-danger">*</span></label>
                                    <input type="text" name="ev_coin_amount" class="form-control" 
                                           value="<?php echo $event_2 ? $event_2['ev_coin_amount'] : ''; ?>" 
                                           placeholder="예: 100" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 이벤트 설정 -->
                        <div class="section-title mt-4">
                            <i class="bi bi-gear"></i> 이벤트 설정
                        </div>
                        
                        <?php if($event_2) { 
                            // 현재 상태 표시 (수정 모드에서만)
                            $now = time();
                            $start_time = strtotime($event_2['ev_start_date']);
                            $end_time = strtotime($event_2['ev_end_date']);
                            
                            if($now < $start_time) {
                                $current_status = '진행예정';
                                $status_class = 'info';
                            } else if($now >= $start_time && $now <= $end_time) {
                                $current_status = '진행중';
                                $status_class = 'success';
                            } else {
                                $current_status = '종료';
                                $status_class = 'secondary';
                            }
                        ?>
                        <div class="alert alert-<?php echo $status_class; ?> mb-3">
                            <i class="bi bi-info-circle"></i> 현재 상태: <strong><?php echo $current_status; ?></strong>
                            <br><small class="text-muted">이벤트 상태는 설정된 날짜에 따라 자동으로 결정됩니다.</small>
                        </div>
                        <?php } ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">시작일시 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="date" name="ev_start_date" class="form-control" 
                                               value="<?php echo $event_2 ? date('Y-m-d', strtotime($event_2['ev_start_date'])) : date('Y-m-d'); ?>" required>
                                        <input type="time" name="ev_start_time" class="form-control" 
                                               value="<?php echo $event_2 ? date('H:i', strtotime($event_2['ev_start_date'])) : '00:00'; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">종료일시 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="date" name="ev_end_date" class="form-control" 
                                               value="<?php echo $event_2 ? date('Y-m-d', strtotime($event_2['ev_end_date'])) : date('Y-m-d', strtotime('+7 days')); ?>" required>
                                        <input type="time" name="ev_end_time" class="form-control" 
                                               value="<?php echo $event_2 ? date('H:i', strtotime($event_2['ev_end_date'])) : '23:59'; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="status-info alert alert-light">
                            <i class="bi bi-lightbulb"></i> <strong>상태 안내</strong>
                            <ul class="mb-0 mt-2">
                                <li>현재 시간이 시작일시 이전: <span class="badge bg-info">진행예정</span></li>
                                <li>현재 시간이 시작일시와 종료일시 사이: <span class="badge bg-success">진행중</span></li>
                                <li>현재 시간이 종료일시 이후: <span class="badge bg-secondary">종료</span></li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="ev_recommend" class="form-check-input" id="ev_recommend" 
                                       value="1" <?php echo ($event_2 && $event_2['ev_recommend']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ev_recommend">
                                    <i class="bi bi-star-fill text-warning"></i> 메인 페이지에 추천 표시
                                </label>
                            </div>
                        </div>
                        
                        <!-- 버튼 -->
                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> <?php echo $w == 'u' ? '수정' : '등록'; ?>
                            </button>
                            <a href="<?php echo G5_URL; ?>/event.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> 취소
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* 작성 페이지 */
.event-write-page {
    padding: 40px 0;
    background: #f9fafb;
    min-height: calc(100vh - 200px);
}

.write-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.write-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f3f4f6;
}

.write-header h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
}

/* 섹션 제목 */
.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #3b82f6;
}

/* 현재 이미지 */
.current-image {
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    text-align: center;
}

.current-image img {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 상태 정보 */
.status-info {
    border: 1px solid #e5e7eb;
    font-size: 14px;
}

.status-info ul {
    list-style: none;
    padding-left: 0;
}

.status-info li {
    padding: 4px 0;
}

/* 버튼 그룹 */
.button-group {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f3f4f6;
    text-align: center;
}

.button-group .btn {
    margin: 0 5px;
    padding: 10px 30px;
    font-size: 16px;
}

/* 반응형 */
@media (max-width: 768px) {
    .write-card {
        padding: 24px;
    }
    
    .button-group .btn {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
}
</style>

<script>
// CKEditor 적용 (있다면)
if(typeof CKEDITOR !== 'undefined') {
    CKEDITOR.replace('ev_content');
}

// 폼 검증
function validateForm() {
    const startDate = document.querySelector('input[name="ev_start_date"]').value + ' ' + 
                     document.querySelector('input[name="ev_start_time"]').value;
    const endDate = document.querySelector('input[name="ev_end_date"]').value + ' ' + 
                   document.querySelector('input[name="ev_end_time"]').value;
    
    if(new Date(startDate) >= new Date(endDate)) {
        alert('종료일시는 시작일시보다 이후여야 합니다.');
        return false;
    }
    
    return true;
}

// 폼 데이터 자동 저장 및 복원
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="text"], input[type="date"], input[type="time"], textarea, input[type="checkbox"]');
    const storageKey = 'event_form_<?php echo $ev_id ? $ev_id : "new"; ?>';
    
    // 저장된 데이터 복원
    const savedData = sessionStorage.getItem(storageKey);
    if(savedData) {
        try {
            const data = JSON.parse(savedData);
            inputs.forEach(input => {
                if(data[input.name] !== undefined) {
                    if(input.type === 'checkbox') {
                        input.checked = data[input.name] === true || data[input.name] === 'true';
                    } else {
                        input.value = data[input.name];
                    }
                }
            });
            
            // CKEditor 내용 복원
            if(typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.ev_content && data.ev_content) {
                CKEDITOR.instances.ev_content.setData(data.ev_content);
            }
        } catch(e) {
            console.error('Failed to restore form data:', e);
        }
    }
    
    // 입력 값 변경 시 자동 저장
    function saveFormData() {
        const data = {};
        inputs.forEach(input => {
            if(input.name) {
                if(input.type === 'checkbox') {
                    data[input.name] = input.checked;
                } else {
                    data[input.name] = input.value;
                }
            }
        });
        
        // CKEditor 내용도 저장
        if(typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.ev_content) {
            data.ev_content = CKEDITOR.instances.ev_content.getData();
        }
        
        sessionStorage.setItem(storageKey, JSON.stringify(data));
    }
    
    // 각 입력 필드에 이벤트 리스너 추가
    inputs.forEach(input => {
        input.addEventListener('input', saveFormData);
        input.addEventListener('change', saveFormData);
    });
    
    // CKEditor 변경 감지
    if(typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.ev_content) {
        CKEDITOR.instances.ev_content.on('change', saveFormData);
    }
    
    // 폼 제출 성공 시 저장된 데이터 삭제
    form.addEventListener('submit', function() {
        if(validateForm()) {
            sessionStorage.removeItem(storageKey);
        }
    });
});

// 페이지 떠날 때 확인
let formChanged = false;
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if(formChanged) {
            e.preventDefault();
            e.returnValue = '작성 중인 내용이 있습니다. 페이지를 떠나시겠습니까?';
            return e.returnValue;
        }
    });
    
    form.addEventListener('submit', function() {
        formChanged = false;
    });
});
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>