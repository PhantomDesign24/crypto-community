<?php
/*
 * 파일명: member_register.php
 * 위치: /sub_admin/member_register.php
 * 기능: 하부조직 관리자 - 직접 회원가입
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// 권한 체크
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

if ($member['mb_grade'] < 2 && !$is_admin) {
    alert('접근 권한이 없습니다.', G5_URL);
}

// 가입 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['act'] == 'register') {
    // 입력값 정리
    $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
    $mb_password = isset($_POST['mb_password']) ? trim($_POST['mb_password']) : '';
    $mb_name = isset($_POST['mb_name']) ? trim($_POST['mb_name']) : '';
    $mb_nick = isset($_POST['mb_nick']) ? trim($_POST['mb_nick']) : '';
    $mb_email = isset($_POST['mb_email']) ? trim($_POST['mb_email']) : '';
    $mb_hp = isset($_POST['mb_hp']) ? trim($_POST['mb_hp']) : '';
    $mb_grade = isset($_POST['mb_grade']) ? (int)$_POST['mb_grade'] : 1;
    
    // 추천인 설정 (기본값: 현재 로그인한 관리자)
    $mb_recommend = $member['mb_id'];
    
    // 최고관리자는 추천인 직접 지정 가능
    if ($is_admin && isset($_POST['mb_recommend'])) {
        $mb_recommend = trim($_POST['mb_recommend']);
    }
    
    // 유효성 검사
    if (!$mb_id) {
        alert('아이디를 입력해주세요.');
    }
    
    if (!preg_match("/^[a-z0-9_]+$/", $mb_id)) {
        alert('아이디는 영문 소문자, 숫자, _ 만 사용 가능합니다.');
    }
    
    if (strlen($mb_id) < 3 || strlen($mb_id) > 20) {
        alert('아이디는 3자 이상 20자 이하로 입력해주세요.');
    }
    
    if (!$mb_password || strlen($mb_password) < 4) {
        alert('비밀번호는 4자 이상 입력해주세요.');
    }
    
    if (!$mb_name) {
        alert('이름을 입력해주세요.');
    }
    
    if (!$mb_email) {
        alert('이메일을 입력해주세요.');
    }
    
    if (!filter_var($mb_email, FILTER_VALIDATE_EMAIL)) {
        alert('올바른 이메일 형식이 아닙니다.');
    }
    
    // 아이디 중복 체크
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}'";
    $row = sql_fetch($sql);
    if ($row['cnt'] > 0) {
        alert('이미 사용 중인 아이디입니다.');
    }
    
    // 닉네임 중복 체크
    if ($mb_nick) {
        $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_nick = '{$mb_nick}'";
        $row = sql_fetch($sql);
        if ($row['cnt'] > 0) {
            alert('이미 사용 중인 닉네임입니다.');
        }
    } else {
        $mb_nick = $mb_name; // 닉네임 미입력시 이름으로 설정
    }
    
    // 이메일 중복 체크
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_email = '{$mb_email}'";
    $row = sql_fetch($sql);
    if ($row['cnt'] > 0) {
        alert('이미 사용 중인 이메일입니다.');
    }
    
    // 추천 코드 생성
    function generate_referral_code() {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code_length = 8;
        
        do {
            $code = '';
            for ($i = 0; $i < $code_length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $sql = "SELECT COUNT(*) as cnt FROM {$GLOBALS['g5']['member_table']} WHERE mb_referral_code = '{$code}'";
            $row = sql_fetch($sql);
        } while ($row['cnt'] > 0);
        
        return $code;
    }
    
    $mb_referral_code = '';
    if ($mb_grade >= 2) {
        $mb_referral_code = generate_referral_code();
    }
    
    // 회원 등록
    $sql = "INSERT INTO {$g5['member_table']} SET
            mb_id = '{$mb_id}',
            mb_password = '".get_encrypt_string($mb_password)."',
            mb_name = '{$mb_name}',
            mb_nick = '{$mb_nick}',
            mb_email = '{$mb_email}',
            mb_hp = '{$mb_hp}',
            mb_grade = '{$mb_grade}',
            mb_recommend = '{$mb_recommend}',
            mb_referral_code = '{$mb_referral_code}',
            mb_datetime = '".G5_TIME_YMDHIS."',
            mb_ip = '{$_SERVER['REMOTE_ADDR']}',
            mb_email_certify = '".G5_TIME_YMDHIS."',
            mb_mailling = 1,
            mb_sms = 1,
            mb_open = 1,
            mb_level = 2";
    
    sql_query($sql);
    
    // 포인트 지급 (회원가입)
    insert_point($mb_id, $config['cf_register_point'], '회원가입 축하', '@member', $mb_id, '회원가입');
    
    alert('회원가입이 완료되었습니다.', './member_list.php');
}

$g5['title'] = '하부조직 회원 등록';
include_once('./header.php');
?>

<style>
/* 회원 등록 폼 */
.register-container {
    max-width: 600px;
    margin: 0 auto;
}

.register-form {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-section {
    margin-bottom: 32px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 18px;
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

.form-group {
    margin-bottom: 16px;
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

.form-control {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-select {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.form-help {
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
}

.info-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    color: #1e40af;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box i {
    flex-shrink: 0;
}

.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 32px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

/* 반응형 */
@media (max-width: 768px) {
    .register-container {
        padding: 0 16px;
    }
    
    .register-form {
        padding: 24px 20px;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="register-container">
    <div class="register-form">
        <h1 class="page-title">
            <i class="bi bi-person-plus"></i> 하부조직 회원 등록
        </h1>
        
        <form method="post" action="" onsubmit="return fregister_submit(this);">
        <input type="hidden" name="act" value="register">
        
        <!-- 기본 정보 -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-person"></i> 기본 정보
            </h3>
            
            <div class="info-box">
                <i class="bi bi-info-circle"></i>
                <div>
                    하부조직 회원을 직접 등록합니다. 
                    등록된 회원의 추천인은 <strong><?php echo $member['mb_id']; ?></strong>님으로 자동 설정됩니다.
                </div>
            </div>
            
            <?php if ($is_admin) { ?>
            <div class="form-group">
                <label class="form-label">추천인 <span style="color: #6b7280; font-weight: normal;">(최고관리자 전용)</span></label>
                <input type="text" name="mb_recommend" class="form-control" value="<?php echo $member['mb_id']; ?>" placeholder="추천인 아이디">
                <p class="form-help">비워두면 현재 로그인한 관리자가 추천인이 됩니다.</p>
            </div>
            <?php } ?>
            
            <div class="form-group">
                <label class="form-label">아이디 <span class="required">*</span></label>
                <input type="text" name="mb_id" id="mb_id" class="form-control" required placeholder="영문 소문자, 숫자, _ 조합 3~20자">
                <p class="form-help">영문 소문자, 숫자, _ 만 사용 가능합니다. (3자 이상 20자 이하)</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">비밀번호 <span class="required">*</span></label>
                <input type="password" name="mb_password" id="mb_password" class="form-control" required placeholder="4자 이상">
                <p class="form-help">최소 4자 이상 입력해주세요.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">이름 <span class="required">*</span></label>
                <input type="text" name="mb_name" id="mb_name" class="form-control" required placeholder="실명 입력">
            </div>
            
            <div class="form-group">
                <label class="form-label">닉네임</label>
                <input type="text" name="mb_nick" id="mb_nick" class="form-control" placeholder="미입력시 이름으로 자동 설정">
            </div>
            
            <div class="form-group">
                <label class="form-label">회원 등급</label>
                <select name="mb_grade" class="form-select">
                    <option value="1">일반회원 (등급 1)</option>
                    <option value="2">파트너 (등급 2 - 하부조직 관리 가능)</option>
                    <?php if ($is_admin || $member['mb_grade'] >= 3) { ?>
                    <option value="3">매니저 (등급 3)</option>
                    <?php } ?>
                </select>
                <p class="form-help">2등급 이상은 추천 코드가 자동 발급되어 하부조직을 구성할 수 있습니다.</p>
            </div>
        </div>
        
        <!-- 연락처 정보 -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-telephone"></i> 연락처 정보
            </h3>
            
            <div class="form-group">
                <label class="form-label">이메일 <span class="required">*</span></label>
                <input type="email" name="mb_email" id="mb_email" class="form-control" required placeholder="example@email.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">휴대폰</label>
                <input type="tel" name="mb_hp" id="mb_hp" class="form-control" placeholder="010-0000-0000">
            </div>
        </div>
        
        <!-- 버튼 -->
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> 회원 등록
            </button>
            <a href="./member_list.php" class="btn btn-secondary">
                <i class="bi bi-list"></i> 목록으로
            </a>
        </div>
        
        </form>
    </div>
</div>

<script>
// 폼 제출 전 유효성 검사
function fregister_submit(f) {
    // 아이디 체크
    if (f.mb_id.value.length < 3) {
        alert("아이디는 3자 이상 입력하세요.");
        f.mb_id.focus();
        return false;
    }
    
    if (!/^[a-z0-9_]+$/.test(f.mb_id.value)) {
        alert("아이디는 영문 소문자, 숫자, _ 만 사용 가능합니다.");
        f.mb_id.focus();
        return false;
    }
    
    // 비밀번호 체크
    if (f.mb_password.value.length < 4) {
        alert("비밀번호는 4자 이상 입력하세요.");
        f.mb_password.focus();
        return false;
    }
    
    // 이름 체크
    if (!f.mb_name.value) {
        alert("이름을 입력하세요.");
        f.mb_name.focus();
        return false;
    }
    
    // 이메일 체크
    if (!f.mb_email.value) {
        alert("이메일을 입력하세요.");
        f.mb_email.focus();
        return false;
    }
    
    return confirm("회원을 등록하시겠습니까?");
}
</script>

<?php
include_once('./footer.php');
?>