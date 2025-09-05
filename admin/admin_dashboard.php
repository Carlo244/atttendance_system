<?php
session_start();
require_once __DIR__ . '/classes/admin.php';

$admin = new Admin();
$admin->startSession();

// Handle course form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add_course') {
        $admin->addCourse($_POST['course_name'], $_POST['year_level']);
        header("Location: admin_dashboard.php");
        exit;
    }

    if ($_POST['action'] === 'edit_course') {
        $admin->updateCourse($_POST['id'], $_POST['course_name'], $_POST['year_level']);
        header("Location: admin_dashboard.php");
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete_course') {
    $admin->deleteCourse($_GET['id']);
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'enroll_student') {
        $admin->enrollStudent($_POST['student_id'], $_POST['course_id']);
        header("Location: admin_dashboard.php");
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'unenroll_student') {
    $admin->unenrollStudent($_GET['student_id'], $_GET['course_id']);
    header("Location: admin_dashboard.php");
    exit;
}


// Load data
$students = $admin->getAllStudents();
$admins = $admin->getAllAdmins();
$courses = $admin->getAllCourses();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#4f46e5',
                        accent: '#a5b4fc',
                        darkBackground: '#0f172a',
                        darkSurface: '#1e293b',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-darkBackground text-white min-h-screen">
    <!-- NAVBAR -->
    <nav class="bg-darkSurface shadow-md px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <a href="../core/user_handle.php?action=logout"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Logout</a>
    </nav>

    <div class="p-6 space-y-8">
        <!-- COURSE MANAGEMENT -->
        <section class="bg-darkSurface p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Course Management</h2>

            <!-- Add course -->
            <form method="POST" class="flex space-x-4 mb-6">
                <input type="hidden" name="action" value="add_course">
                <input type="text" name="course_name" placeholder="Course Name" required
                    class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                <input type="text" name="year_level" placeholder="Year Level (e.g. 1st Year)" required
                    class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="bg-gradient-to-r from-primary to-secondary px-4 py-2 rounded-lg text-white font-medium">
                    Add Course
                </button>
            </form>

            <!-- Courses Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-700 rounded-lg">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Course Name</th>
                            <th class="px-4 py-2">Year Level</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr class="border-t border-gray-700">
                                <td class="px-4 py-2"><?= $c['id'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($c['course_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($c['year_level']) ?></td>
                                <td class="px-4 py-2 space-x-2">
                                    <!-- Edit Button triggers inline form -->
                                    <button
                                        onclick="openEditForm(<?= $c['id'] ?>, '<?= htmlspecialchars($c['course_name']) ?>', '<?= htmlspecialchars($c['year_level']) ?>')"
                                        class="bg-yellow-600 hover:bg-yellow-700 px-3 py-1 rounded-lg text-sm">Edit</button>
                                    <a href="?action=delete_course&id=<?= $c['id'] ?>"
                                        class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg text-sm"
                                        onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- ENROLLMENT MANAGEMENT -->
            <section class="bg-darkSurface p-6 rounded-2xl shadow-lg mt-8">
                <h2 class="text-xl font-semibold mb-4">Student Enrollment</h2>

                <!-- Enroll Form -->
                <form method="POST" class="flex space-x-4 mb-6">
                    <input type="hidden" name="action" value="enroll_student">

                    <!-- Student Dropdown -->
                    <select name="student_id" required
                        class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                        <option value="">Select Student</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= $s['email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Course Dropdown -->
                    <select name="course_id" required
                        class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']) ?>
                                (<?= $c['year_level'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit"
                        class="bg-gradient-to-r from-primary to-secondary px-4 py-2 rounded-lg text-white font-medium">
                        Enroll Student
                    </button>
                </form>

                <!-- Enrollments Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-gray-700 rounded-lg">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-2">Student</th>
                                <th class="px-4 py-2">Course</th>
                                <th class="px-4 py-2">Year Level</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admin->getEnrollments() as $e): ?>
                                <tr class="border-t border-gray-700">
                                    <td class="px-4 py-2"><?= htmlspecialchars($e['student_name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($e['course_name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($e['year_level']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?action=unenroll_student&student_id=<?= $e['student_id'] ?>&course_id=<?= $e['course_id'] ?>"
                                            class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg text-sm"
                                            onclick="return confirm('Remove this student from course?')">Unenroll</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Attendance Reports -->
            <div class="bg-darkSurface p-6 rounded-2xl shadow-lg mt-8">
                <h2 class="text-xl font-semibold text-primary mb-4">Attendance Reports</h2>

                <?php
                $summary = $admin->getAttendanceSummary();
                if ($summary): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border border-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-800 text-accent">
                                <tr>
                                    <th class="p-3">Course</th>
                                    <th class="p-3">Year Level</th>
                                    <th class="p-3">Total Records</th>
                                    <th class="p-3">Present</th>
                                    <th class="p-3">Absent</th>
                                    <th class="p-3">Late</th>
                                    <th class="p-3">Excused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary as $row): ?>
                                    <tr class="border-b border-gray-700">
                                        <td class="p-3"><?= htmlspecialchars($row['course_name']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($row['year_level']) ?></td>
                                        <td class="p-3"><?= $row['total_records'] ?></td>
                                        <td class="p-3 text-green-400"><?= $row['total_present'] ?></td>
                                        <td class="p-3 text-red-400"><?= $row['total_absent'] ?></td>
                                        <td class="p-3 text-yellow-400"><?= $row['total_late'] ?></td>
                                        <td class="p-3 text-blue-400"><?= $row['total_excused'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400">No attendance records available.</p>
                <?php endif; ?>
            </div>

        </section>
    </div>

    <!-- Hidden Edit Form (Modal style) -->
    <div id="editForm" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-darkSurface p-6 rounded-xl shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">Edit Course</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit_course">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="course_name" id="editCourseName" required
                    class="w-full mb-3 px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                <input type="text" name="year_level" id="editYearLevel" required
                    class="w-full mb-3 px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-primary">
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditForm()"
                        class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit"
                        class="bg-primary hover:bg-secondary px-4 py-2 rounded-lg text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditForm(id, name, year) {
            document.getElementById('editId').value = id;
            document.getElementById('editCourseName').value = name;
            document.getElementById('editYearLevel').value = year;
            document.getElementById('editForm').classList.remove('hidden');
            document.getElementById('editForm').classList.add('flex');
        }

        function closeEditForm() {
            document.getElementById('editForm').classList.remove('flex');
            document.getElementById('editForm').classList.add('hidden');
        }
    </script>
</body>

</html>