<?php
/*
 * 파일명: organization_tree.php
 * 위치: /sub_admin/organization_tree.php
 * 기능: 하부조직 트리 구조 보기
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

$g5['title'] = '하부조직 구조';
include_once('./header.php');

// 선택된 조직장 (기본값: 현재 로그인 사용자)
$root_mb_id = isset($_GET['root']) ? trim($_GET['root']) : $member['mb_id'];

// 최고관리자가 아닌 경우 자신의 조직만 볼 수 있음
if (!$is_admin && $root_mb_id != $member['mb_id']) {
    $root_mb_id = $member['mb_id'];
}

// 조직장 정보 조회
$root_member = null;
if ($root_mb_id) {
    $sql = "SELECT mb_id, mb_name, mb_nick, mb_grade, mb_datetime 
            FROM {$g5['member_table']} 
            WHERE mb_id = '{$root_mb_id}'";
    $root_member = sql_fetch($sql);
}

// 하부조직 회원 조회 함수
function get_sub_members($recommend_id, $depth = 0, $max_depth = 5) {
    global $g5;
    
    if ($depth >= $max_depth) {
        return array();
    }
    
    $sql = "SELECT mb_id, mb_name, mb_nick, mb_email, mb_hp, mb_grade, mb_point, mb_datetime, mb_today_login,
                   (SELECT COUNT(*) FROM {$g5['member_table']} WHERE mb_recommend = m.mb_id) as sub_count
            FROM {$g5['member_table']} m
            WHERE mb_recommend = '{$recommend_id}'
            ORDER BY mb_datetime DESC";
    
    $result = sql_query($sql);
    $members = array();
    
    while ($row = sql_fetch_array($result)) {
        $row['depth'] = $depth;
        $row['sub_members'] = get_sub_members($row['mb_id'], $depth + 1, $max_depth);
        $members[] = $row;
    }
    
    return $members;
}

// 조직 통계 계산 함수
function calculate_org_stats($recommend_id) {
    global $g5;
    
    $stats = array(
        'total' => 0,
        'grade_1' => 0,
        'grade_2' => 0,
        'grade_3' => 0,
        'today' => 0
    );
    
    // 직접 하위 회원
    $sql = "SELECT mb_grade, DATE(mb_datetime) as join_date 
            FROM {$g5['member_table']} 
            WHERE mb_recommend = '{$recommend_id}'";
    $result = sql_query($sql);
    
    while ($row = sql_fetch_array($result)) {
        $stats['total']++;
        $stats['grade_' . $row['mb_grade']]++;
        
        if ($row['join_date'] == G5_TIME_YMD) {
            $stats['today']++;
        }
    }
    
    // 하위의 하위 회원들도 포함 (재귀적으로)
    $sub_sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_recommend = '{$recommend_id}'";
    $sub_result = sql_query($sub_sql);
    
    while ($sub = sql_fetch_array($sub_result)) {
        $sub_stats = calculate_org_stats($sub['mb_id']);
        $stats['total'] += $sub_stats['total'];
        $stats['grade_1'] += $sub_stats['grade_1'];
        $stats['grade_2'] += $sub_stats['grade_2'];
        $stats['grade_3'] += $sub_stats['grade_3'];
        $stats['today'] += $sub_stats['today'];
    }
    
    return $stats;
}

// 하부조직 목록 (최고관리자용)
$org_leaders = array();
if ($is_admin) {
    $sql = "SELECT DISTINCT m1.mb_id, m1.mb_name, m1.mb_nick, m1.mb_grade,
                   COUNT(DISTINCT m2.mb_id) as direct_count
            FROM {$g5['member_table']} m1
            LEFT JOIN {$g5['member_table']} m2 ON m2.mb_recommend = m1.mb_id
            WHERE m1.mb_grade >= 2
            GROUP BY m1.mb_id
            HAVING direct_count > 0
            ORDER BY direct_count DESC, m1.mb_id";
    $result = sql_query($sql);
    
    while ($row = sql_fetch_array($result)) {
        $org_leaders[] = $row;
    }
}
?>

<style>
/* 조직도 스타일 */
.org-container {
    display: flex;
    gap: 24px;
}

.org-sidebar {
    width: 300px;
    flex-shrink: 0;
}

.org-main {
    flex: 1;
    min-width: 0;
}

/* 조직장 선택 */
.org-selector {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.org-selector h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.org-selector h3 i {
    color: #3b82f6;
}

.org-list {
    max-height: 400px;
    overflow-y: auto;
}

.org-item {
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.org-item:hover {
    background: #f9fafb;
    border-color: #3b82f6;
}

.org-item.active {
    background: #eff6ff;
    border-color: #3b82f6;
}

.org-item-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.org-item-info {
    font-size: 12px;
    color: #6b7280;
}

/* 조직 통계 */
.org-stats {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
}

.stat-item {
    text-align: center;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: #6b7280;
}

/* 조직도 헤더 */
.org-header {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.org-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.org-title h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.org-actions {
    display: flex;
    gap: 8px;
}

/* 트리 구조 */
.org-tree {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.tree-node {
    margin-bottom: 12px;
}

.tree-node-content {
    display: flex;
    align-items: center;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
}

.tree-node-content:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.tree-node-content.has-children {
    font-weight: 500;
}

.tree-indent {
    display: inline-block;
}

.tree-toggle {
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    color: #6b7280;
}

.tree-toggle i {
    font-size: 12px;
    transition: transform 0.2s;
}

.tree-toggle.expanded i {
    transform: rotate(90deg);
}

.tree-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.tree-avatar {
    width: 32px;
    height: 32px;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.tree-details {
    flex: 1;
}

.tree-name {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 2px;
}

.tree-meta {
    font-size: 12px;
    color: #6b7280;
}

.tree-stats {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #6b7280;
}

.tree-children {
    margin-left: 20px;
    margin-top: 8px;
    display: none;
}

.tree-children.expanded {
    display: block;
}

/* 등급 뱃지 */
.grade-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.grade-1 {
    background: #f3f4f6;
    color: #4b5563;
}

.grade-2 {
    background: #dbeafe;
    color: #1e40af;
}

.grade-3 {
    background: #ede9fe;
    color: #5b21b6;
}

/* 버튼 */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-outline {
    background: white;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}

/* 빈 상태 */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

/* 반응형 */
@media (max-width: 1024px) {
    .org-container {
        flex-direction: column;
    }
    
    .org-sidebar {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tree-stats {
        display: none;
    }
}
</style>

<div class="org-container">
    <?php if ($is_admin && count($org_leaders) > 0) { ?>
    <!-- 왼쪽: 조직장 선택 (최고관리자) -->
    <div class="org-sidebar">
        <div class="org-selector">
            <h3><i class="bi bi-diagram-3"></i> 조직 선택</h3>
            <div class="org-list">
                <?php foreach ($org_leaders as $leader) { 
                    $active = ($leader['mb_id'] == $root_mb_id) ? 'active' : '';
                ?>
                <div class="org-item <?php echo $active; ?>" onclick="location.href='?root=<?php echo $leader['mb_id']; ?>'">
                    <div class="org-item-name">
                        <?php echo $leader['mb_name']; ?> (<?php echo $leader['mb_id']; ?>)
                    </div>
                    <div class="org-item-info">
                        <span class="grade-badge grade-<?php echo $leader['mb_grade']; ?>">
                            등급 <?php echo $leader['mb_grade']; ?>
                        </span>
                        직속 <?php echo number_format($leader['direct_count']); ?>명
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- 조직 통계 -->
        <?php if ($root_member) { 
            $org_stats = calculate_org_stats($root_mb_id);
        ?>
        <div class="org-stats">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                <i class="bi bi-graph-up"></i> 조직 통계
            </h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($org_stats['total']); ?></div>
                    <div class="stat-label">전체 인원</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($org_stats['today']); ?></div>
                    <div class="stat-label">오늘 가입</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($org_stats['grade_2'] + $org_stats['grade_3']); ?></div>
                    <div class="stat-label">관리자</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($org_stats['grade_1']); ?></div>
                    <div class="stat-label">일반회원</div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
    
    <!-- 오른쪽: 조직도 -->
    <div class="org-main">
        <?php if ($root_member) { ?>
        <!-- 조직 헤더 -->
        <div class="org-header">
            <div class="org-title">
                <h2>
                    <i class="bi bi-diagram-3-fill"></i>
                    <?php echo $root_member['mb_name']; ?>님의 조직도
                </h2>
                <div class="org-actions">
                    <a href="./member_register.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> 회원 추가
                    </a>
                    <a href="./member_list.php?sfl=mb_recommend&stx=<?php echo $root_mb_id; ?>" class="btn btn-outline">
                        <i class="bi bi-list"></i> 목록 보기
                    </a>
                </div>
            </div>
        </div>
        
        <!-- 트리 구조 -->
        <div class="org-tree">
            <?php
            $sub_members = get_sub_members($root_mb_id);
            
            if (count($sub_members) > 0) {
                // 트리 렌더링 함수
                function render_tree($members) {
                    foreach ($members as $member) {
                        $has_children = count($member['sub_members']) > 0;
                        $grade_text = '';
                        switch($member['mb_grade']) {
                            case 1: $grade_text = '일반'; break;
                            case 2: $grade_text = '파트너'; break;
                            case 3: $grade_text = '매니저'; break;
                            default: $grade_text = '관리자';
                        }
                ?>
                <div class="tree-node">
                    <div class="tree-node-content <?php echo $has_children ? 'has-children' : ''; ?>">
                        <span class="tree-indent" style="width: <?php echo $member['depth'] * 20; ?>px;"></span>
                        <?php if ($has_children) { ?>
                        <span class="tree-toggle" onclick="toggleNode(this)">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                        <?php } else { ?>
                        <span class="tree-toggle"></span>
                        <?php } ?>
                        
                        <div class="tree-info">
                            <div class="tree-avatar">
                                <?php echo mb_substr($member['mb_name'], 0, 1); ?>
                            </div>
                            <div class="tree-details">
                                <div class="tree-name">
                                    <?php echo $member['mb_name']; ?> (<?php echo $member['mb_id']; ?>)
                                    <span class="grade-badge grade-<?php echo $member['mb_grade']; ?>">
                                        <?php echo $grade_text; ?>
                                    </span>
                                </div>
                                <div class="tree-meta">
                                    <?php echo $member['mb_email']; ?> | 가입: <?php echo date('Y.m.d', strtotime($member['mb_datetime'])); ?>
                                </div>
                            </div>
                            <div class="tree-stats">
                                <span><i class="bi bi-people"></i> 하위 <?php echo $member['sub_count']; ?>명</span>
                                <span><i class="bi bi-coin"></i> <?php echo number_format($member['mb_point']); ?>P</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($has_children) { ?>
                    <div class="tree-children">
                        <?php render_tree($member['sub_members']); ?>
                    </div>
                    <?php } ?>
                </div>
                <?php
                    }
                }
                
                render_tree($sub_members);
            } else {
            ?>
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <p>아직 하위 조직이 없습니다.</p>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="empty-state">
            <i class="bi bi-exclamation-circle"></i>
            <p>조직 정보를 찾을 수 없습니다.</p>
        </div>
        <?php } ?>
    </div>
</div>

<script>
// 트리 노드 토글
function toggleNode(element) {
    const node = element.closest('.tree-node');
    const children = node.querySelector('.tree-children');
    
    if (children) {
        element.classList.toggle('expanded');
        children.classList.toggle('expanded');
    }
}

// 초기 로드시 첫 번째 레벨 펼치기
document.addEventListener('DOMContentLoaded', function() {
    const firstLevelToggles = document.querySelectorAll('.tree-node > .tree-node-content .tree-toggle');
    firstLevelToggles.forEach(toggle => {
        if (toggle.querySelector('i')) {
            toggleNode(toggle);
        }
    });
});
</script>

<?php
include_once('./footer.php');
?>