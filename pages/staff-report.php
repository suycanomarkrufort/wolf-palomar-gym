<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['staff_id'])) {
    redirect('../staff-login.php');
}

// Get staff user info
$staff_id = $_SESSION['staff_id'];
$is_staff_user = true;

$user_query = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$user_query->bind_param("i", $staff_id);
$user_query->execute();
$admin = $user_query->get_result()->fetch_assoc();

// Get today's date for goal tracking
$today = date('Y-m-d');

// Get today's revenue for goal tracking
$revenue_query = $conn->prepare("SELECT COALESCE(SUM(fee_charged), 0) as total FROM attendance WHERE date = ?");
$revenue_query->bind_param("s", $today);
$revenue_query->execute();
$revenue_result = $revenue_query->get_result()->fetch_assoc();
$today_revenue = $revenue_result['total'];

// Get daily goal
$daily_goal = get_setting('daily_goal');
$goal_percentage = ($daily_goal > 0) ? round(($today_revenue / $daily_goal) * 100) : 0;

$success = false;

// Get all members for tagging
$members_query = "SELECT * FROM member ORDER BY first_name ASC";
$members_result = $conn->query($members_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tagged_members = isset($_POST['tagged_members']) ? json_encode($_POST['tagged_members']) : '[]';
    $report_details = sanitize_input($_POST['report_details']);
    
    $insert_query = $conn->prepare("INSERT INTO staff_reports (staff_id, tagged_members, report_details) VALUES (?, ?, ?)");
    $insert_query->bind_param("iss", $staff_id, $tagged_members, $report_details);
    
    if ($insert_query->execute()) {
        $success = true;
    }
}

// Set page title
$page_title = "Staff Audit Report";

// Include header
include '../includes/header.php';
?>
    
    <style>
        /* =============================================
           WOLF PALOMAR FITNESS GYM — COLOR PALETTE
           --color-bg:        #0A0A0A  deep black
           --color-surface:   #141414  cards
           --color-surface-2: #1C1C1C  hover/header
           --color-border:    #2A2A2A  borders
           --color-primary:   #CC1C1C  shield red
           --color-primary-dk:#A01515  dark red
           --color-navy:      #1A3A8F  shield navy
           --color-silver:    #B0B0B0  wolf fur
           --color-white:     #FFFFFF  headings
           --color-text:      #CCCCCC  body text
           --color-muted:     #777777  labels
           ============================================= */
        :root {
            --color-bg:        #0A0A0A;
            --color-surface:   #141414;
            --color-surface-2: #1C1C1C;
            --color-border:    #2A2A2A;
            --color-primary:   #CC1C1C;
            --color-primary-dk:#A01515;
            --color-navy:      #1A3A8F;
            --color-silver:    #B0B0B0;
            --color-white:     #FFFFFF;
            --color-text:      #CCCCCC;
            --color-muted:     #777777;
        }

        body,
        .main-content {
            background: #0A0A0A !important;
            color: #CCCCCC !important;
        }

        .report-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        /* Back link — navy */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4A7AFF;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #FFFFFF;
            transform: translateX(-4px);
        }

        /* Main card — dark surface */
        .report-card {
            background: #141414 !important;
            border: 1px solid #2A2A2A;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        /* Report title */
        .report-title {
            font-size: 26px;
            font-weight: 800;
            font-style: italic;
            color: #FFFFFF;
            margin-bottom: 30px;
        }

        /* Section labels */
        .section-label {
            color: #777777;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        /* Search box */
        .search-box {
            position: relative;
            margin-bottom: 15px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            background: #1C1C1C !important;
            border: 1px solid #2A2A2A !important;
            border-radius: 10px;
            color: #FFFFFF !important;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .search-box input::placeholder { color: #555555; }

        .search-box input:focus {
            border-color: #CC1C1C !important;
            box-shadow: 0 0 0 3px rgba(204, 28, 28, 0.12);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777777;
        }

        /* Selected member chips */
        .selected-members {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            min-height: 40px;
        }

        .member-chip {
            background: rgba(204, 28, 28, 0.2);
            color: #FF5555;
            border: 1px solid rgba(204, 28, 28, 0.4);
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .member-chip .remove {
            cursor: pointer;
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: background 0.3s ease;
        }

        .member-chip .remove:hover { background: rgba(255, 255, 255, 0.25); }

        /* Report textarea */
        .report-textarea {
            width: 100%;
            min-height: 200px;
            padding: 15px;
            background: #1C1C1C !important;
            border: 1px solid #2A2A2A !important;
            border-radius: 10px;
            color: #CCCCCC !important;
            font-size: 14px;
            resize: vertical;
            margin-bottom: 20px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .report-textarea::placeholder { color: #555555; }

        .report-textarea:focus {
            border-color: #CC1C1C !important;
            box-shadow: 0 0 0 3px rgba(204, 28, 28, 0.12);
        }

        /* Submit button — red */
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #CC1C1C;
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            font-style: italic;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(204, 28, 28, 0.4);
        }

        .submit-btn:hover {
            background: #A01515;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(204, 28, 28, 0.5);
        }

        /* Member dropdown list */
        .member-list {
            max-height: 300px;
            overflow-y: auto;
            background: #1C1C1C !important;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #2A2A2A !important;
            display: none;
        }

        .member-list.active { display: block; }

        /* Scrollbar */
        .member-list::-webkit-scrollbar { width: 6px; }
        .member-list::-webkit-scrollbar-track { background: #1C1C1C; border-radius: 10px; }
        .member-list::-webkit-scrollbar-thumb { background: #3A3A3A; border-radius: 10px; }
        .member-list::-webkit-scrollbar-thumb:hover { background: #4A4A4A; }

        .member-item {
            padding: 12px 15px;
            border-bottom: 1px solid #2A2A2A;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #CCCCCC;
            font-size: 14px;
        }

        .member-item:last-child { border-bottom: none; }

        .member-item:hover {
            background: #252525;
            color: #FFFFFF;
        }

        /* Selected item — red highlight */
        .member-item.selected {
            background: rgba(204, 28, 28, 0.15);
            color: #FF5555;
            font-weight: 600;
        }

        .member-item i { margin-right: 8px; color: #777777; }
        .member-item.selected i { color: #FF5555; }

        /* Alert Modals */
        .alert-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 10001;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .alert-modal.active { display: flex; }

        .alert-modal-content {
            background: #141414 !important;
            border: 1px solid #2A2A2A !important;
            border-radius: 16px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
            animation: slideUp 0.3s ease;
            color: #CCCCCC !important;
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .alert-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        /* Success — navy */
        .alert-icon.success {
            background: rgba(26, 58, 143, 0.2);
            color: #4A7AFF;
            border: 1px solid rgba(26, 58, 143, 0.3);
        }

        .alert-title { flex: 1; }

        .alert-title h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF !important;
        }

        .alert-body {
            margin-bottom: 25px;
            color: #CCCCCC !important;
            line-height: 1.6;
            font-size: 15px;
        }

        .alert-footer { display: flex; gap: 10px; }

        .alert-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        /* OK — navy */
        .alert-btn.success {
            background: #1A3A8F;
            color: #FFFFFF;
        }

        .alert-btn.success:hover { background: #243FA0; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        @media (max-width: 768px) {
            .report-container { padding: 15px; }
            .report-card { padding: 20px; }
            .report-title { font-size: 22px; }
        }

        @media (max-width: 480px) {
            .report-title { font-size: 18px; }
            .submit-btn { padding: 12px; font-size: 14px; }
        }
    </style>
    
    <div class="report-container">
        
        <a href="staff-feedback.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Feedback Pool
        </a>
        
        <div class="report-card">
            
            <h1 class="report-title">STAFF AUDIT REPORT</h1>
            
            <form method="POST" id="reportForm">
                
                <div style="margin-bottom: 30px;">
                    <div class="section-label">TAG INVOLVED ASSETS</div>
                    
                    <div class="search-box">
                        <input type="text" id="searchMembers" placeholder="SEARCH ASSET DATABASE...">
                        <i class="fas fa-search"></i>
                    </div>
                    
                    <div class="selected-members" id="selectedMembers"></div>
                    
                    <div class="member-list" id="memberList">
                        <?php while($member = $members_result->fetch_assoc()): ?>
                            <div class="member-item" data-id="<?php echo $member['id']; ?>" data-name="<?php echo $member['first_name'] . ' ' . $member['last_name']; ?>" onclick="toggleMember(this)">
                                <i class="fas fa-user"></i> <?php echo $member['first_name'] . ' ' . $member['last_name']; ?> - <?php echo $member['member_id']; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <div class="section-label">REPORT DETAILS</div>
                    <textarea name="report_details" class="report-textarea" placeholder="Describe the issue or observation..." required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> SUBMIT OFFICIAL REPORT
                </button>
                
            </form>
            
        </div>
        
    </div>

    <!-- Success Modal -->
    <div class="alert-modal" id="successModal">
        <div class="alert-modal-content">
            <div class="alert-header">
                <div class="alert-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="alert-title">
                    <h3>Success!</h3>
                </div>
            </div>
            <div class="alert-body">
                <p>Report submitted successfully!</p>
            </div>
            <div class="alert-footer">
                <button class="alert-btn success" onclick="closeSuccessModal()">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>
    
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/bottom-nav.php'; ?>
    
    <?php 
    $extra_scripts = "
    <script>
        let selectedMembers = [];
        const searchInput = document.getElementById('searchMembers');
        const memberList = document.getElementById('memberList');

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
        }

        // Show success modal if form was submitted successfully
        " . ($success ? "document.getElementById('successModal').classList.add('active');" : "") . "

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('alert-modal')) {
                closeSuccessModal();
            }
        });
        
        function toggleMember(element) {
            const id = element.getAttribute('data-id');
            const name = element.getAttribute('data-name');
            
            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                selectedMembers = selectedMembers.filter(m => m.id !== id);
            } else {
                element.classList.add('selected');
                selectedMembers.push({ id, name });
            }
            
            updateSelectedMembers();
        }
        
        function removeThis(id) {
            selectedMembers = selectedMembers.filter(m => m.id !== id);
            const item = document.querySelector(\`[data-id=\"\${id}\"]\`);
            if (item) item.classList.remove('selected');
            updateSelectedMembers();
        }
        
        function updateSelectedMembers() {
            const container = document.getElementById('selectedMembers');
            container.innerHTML = '';
            
            selectedMembers.forEach(member => {
                const chip = document.createElement('div');
                chip.className = 'member-chip';
                chip.innerHTML = \`
                    \${member.name.toUpperCase()}
                    <div class=\"remove\" onclick=\"removeThis('\${member.id}')\">×</div>
                \`;
                container.appendChild(chip);
            });
        }
        
        // Search functionality
        searchInput.addEventListener('input', function(e) {
            const search = e.target.value.toLowerCase().trim();
            const items = document.querySelectorAll('.member-item');
            
            if (search.length > 0) {
                memberList.classList.add('active');
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(search) ? 'block' : 'none';
                });
            } else {
                memberList.classList.remove('active');
            }
        });
        
        // Hide list when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !memberList.contains(e.target)) {
                memberList.classList.remove('active');
            }
        });
        
        // Show list when clicking on search input
        searchInput.addEventListener('click', function() {
            if (this.value.trim().length > 0) {
                memberList.classList.add('active');
            }
        });
        
        // Form submission
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            selectedMembers.forEach(member => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tagged_members[]';
                input.value = member.name;
                this.appendChild(input);
            });
            
            this.submit();
        });
    </script>
    ";
    
    include '../includes/footer.php'; 
    ?>