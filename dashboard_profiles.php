<?php
// db connection
include("connection.php");

// Get filter/search values
$branch = isset($_GET['branch']) ? $_GET['branch'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM reg_tab WHERE 1";

// Branch filter
if ($branch != '') {
    $sql .= " AND dept = '" . $conn->real_escape_string($branch) . "'";
}

// Name search
if ($search != '') {
    $sql .= " AND faculty_name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        th { position: sticky; top: 0; z-index: 0; }
        .scroll-box::-webkit-scrollbar { height: 8px; }
        .scroll-box::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .scroll-box::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        .scroll-box::-webkit-scrollbar-thumb:hover { background: #374151; }
    </style>
    <script>
        function applyFilters() {
            const branch = document.querySelector('select[name="branch"]').value;
            const search = document.querySelector('input[name="search"]').value;
            const params = new URLSearchParams();
            if (branch) params.append("branch", branch);
            if (search) params.append("search", search);
            window.location = "?" + params.toString();
        }

        function openModal(details) {
            const modal = document.getElementById("detailsModal");
            const content = document.getElementById("modalContent");
            content.innerHTML = "";

            for (const key in details) {
                if (["id","photo_path","created_at","password"].includes(key)) continue;
                const div = document.createElement("div");
                div.classList.add("mb-2");
                div.innerHTML = `<span class="font-semibold">${key.replace(/_/g," ").toUpperCase()}:</span> ${details[key] || ""}`;
                content.appendChild(div);
            }

            document.getElementById("modalPhoto").src = details.photo_path || "Uploads/default_pic.png";
            modal.classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("detailsModal").classList.add("hidden");
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Faculty Dashboard</h2>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-8 bg-white p-6 rounded-lg shadow-md">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Department</label>
                <select 
                    name="branch" 
                    onchange="applyFilters()"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                >
                    <option value="">All Departments</option>
                    <option value="CSE" <?= $branch=='CSE'?'selected':'' ?>>CSE</option>
                    <option value="ECE" <?= $branch=='ECE'?'selected':'' ?>>ECE</option>
                    <option value="EEE" <?= $branch=='EEE'?'selected':'' ?>>EEE</option>
                    <option value="MECH" <?= $branch=='MECH'?'selected':'' ?>>MECH</option>
                    <option value="CIVIL" <?= $branch=='CIVIL'?'selected':'' ?>>CIVIL</option>
                    <option value="IT" <?= $branch=='IT'?'selected':'' ?>>IT</option>
                    <option value="AIDS" <?= $branch=='AIDS'?'selected':'' ?>>AIDS</option>
                    <option value="AIML" <?= $branch=='AIML'?'selected':'' ?>>AIML</option>
                    <option value="BSH" <?= $branch=='BSH'?'selected':'' ?>>BSH</option>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search by Name</label>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by Name" 
                    value="<?= htmlspecialchars($search) ?>" 
                    onkeyup="applyFilters()"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                >
            </div>
        </div>

        <!-- Table -->
        <div class="scroll-box overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="w-full min-w-[800px]">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Photo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Faculty Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Designation</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Qualification</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Dept</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td class="px-4 py-3">
                                    <img 
                                        src="<?= $row['photo_path'] ?: 'Uploads/default_pic.png' ?>" 
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200"
                                        alt="Faculty Photo"
                                    >
                                </td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($row['faculty_name']) ?></td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($row['designation']) ?></td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($row['qualification']) ?></td>
                                <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($row['dept']) ?></td>
                                <td class="px-4 py-3">
                                    <button 
                                        onclick='openModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition"
                                    >
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative">
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl">&times;</button>
            <div class="flex items-center gap-4 mb-6">
                <img id="modalPhoto" src="Uploads/default_pic.png" class="w-20 h-20 rounded-full border object-cover">
                <h3 class="text-xl font-bold text-gray-800">Faculty Details</h3>
            </div>
            <div id="modalContent" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-sm"></div>
        </div>
    </div>
</body>
</html>
