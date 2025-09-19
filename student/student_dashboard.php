<?php
require_once __DIR__ . '/classes/user.php';
require_once __DIR__ . '/classes/student.php';
require_once __DIR__ . '/classes/attendance.php';

$user = new User();
$user->startSession();


if (!$user->isLoggedIn() || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

/** Instantiate dependencies first */
$student = new Student();
$attendance = new Attendance();

$studentId = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? 'Student';
$excuses = $student->getExcuseLetters($studentId);



/** Load data for view */
$courses = $student->getEnrolledCourses($studentId);           // id, course_name, year_level
$history = $attendance->getAttendanceHistory($studentId);       // date, status, course_name, year_level, etc.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
    <div class="max-w-5xl mx-auto py-10 px-6 space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-accent">Welcome, <?= htmlspecialchars($name) ?> 👋</h1>
            <a href="../core/user_handle.php?action=logout"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-xl shadow">Logout</a>
        </div>

        <!-- Flash Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="bg-green-600/20 border border-green-600 text-green-300 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="bg-red-600/20 border border-red-600 text-red-300 px-4 py-3 rounded-xl">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Enrolled Courses -->
        <div class="bg-darkSurface p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold text-primary mb-4">Your Courses</h2>
            <?php if ($courses): ?>
                <ul class="space-y-3">
                    <?php foreach ($courses as $course): ?>
                        <li
                            class="p-4 bg-gray-800 rounded-xl flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="font-semibold"><?= htmlspecialchars($course['course_name']) ?></div>
                                <div class="text-accent text-sm"><?= htmlspecialchars($course['year_level']) ?></div>
                            </div>
                            <div class="flex gap-2">
                                <!-- Check In -->
                                <form method="POST" action="../core/user_handle.php">
                                    <input type="hidden" name="action" value="check_in">
                                    <input type="hidden" name="course_id" value="<?= (int) $course['id'] ?>">
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg">
                                        Check In
                                    </button>
                                </form>
                                <!-- Check Out -->
                                <form method="POST" action="../core/user_handle.php">
                                    <input type="hidden" name="action" value="check_out">
                                    <input type="hidden" name="course_id" value="<?= (int) $course['id'] ?>">
                                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg">
                                        Check Out
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-400">You are not enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>

        <!-- Attendance History -->
        <div class="bg-darkSurface p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold text-primary mb-4">Attendance History</h2>
            <?php if ($history): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-gray-700 rounded-lg overflow-hidden">
                        <thead class="bg-gray-800 text-accent">
                            <tr>
                                <th class="p-3">Date</th>
                                <th class="p-3">Course</th>
                                <th class="p-3">Year</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Check-in</th>
                                <th class="p-3">Check-out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $row): ?>
                                <tr class="border-b border-gray-700">
                                    <td class="p-3"><?= htmlspecialchars($row['attendance_date']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['course_name']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['year_level'] ?? '') ?></td>
                                    <td class="p-3 font-semibold">
                                        <span
                                            class="<?= ($row['status'] === 'Present') ? 'text-green-400' : (($row['status'] === 'Late') ? 'text-yellow-400' : 'text-red-400') ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($row['check_in'] ?? '-') ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['check_out'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-400">No attendance records yet.</p>
            <?php endif; ?>
        </div>

        <!-- Excuse Letters -->
        <div class="bg-darkSurface p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold text-primary mb-4">Excuse Letters</h2>

            <!-- Submit Form -->
            <form method="POST" action="../core/user_handle.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submit_excuse">
                <div>
                    <label class="block mb-1 text-accent">Course</label>
                    <select name="course_id" class="w-full p-2 rounded bg-gray-800 text-white" required>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= (int) $course['id'] ?>">
                                <?= htmlspecialchars($course['course_name'] . " (" . $course['year_level'] . ")") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-accent">Reason</label>
                    <textarea name="reason" class="w-full p-2 rounded bg-gray-800 text-white" rows="3"
                        required></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-accent">Attach File (optional)</label>
                    <input type="file" name="file" class="w-full text-white">
                </div>
                <button type="submit" name="submit_excuse" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg">
                    Submit Excuse Letter
                </button>
            </form>

            <!-- History -->
            <?php if ($excuses): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-gray-700 rounded-lg overflow-hidden">
                        <thead class="bg-gray-800 text-accent">
                            <tr>
                                <th class="p-3">Date</th>
                                <th class="p-3">Course</th>
                                <th class="p-3">Reason</th>
                                <th class="p-3">File</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($excuses as $row): ?>
                                <tr class="border-b border-gray-700">
                                    <td class="p-3"><?= htmlspecialchars($row['submitted_at']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['course_name']) ?>
                                        (<?= htmlspecialchars($row['year_level']) ?>)</td>
                                    <td class="p-3"><?= htmlspecialchars($row['reason']) ?></td>
                                    <td class="p-3">
                                        <?php if ($row['file_path']): ?>
                                            <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank"
                                                class="text-blue-400 underline">View</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 font-semibold">
                                        <?php if ($row['status'] === 'Approved'): ?>
                                            <span class="text-green-400">Approved</span>
                                        <?php elseif ($row['status'] === 'Rejected'): ?>
                                            <span class="text-red-400">Rejected</span>
                                        <?php else: ?>
                                            <span class="text-yellow-400">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-400">No excuse letters submitted yet.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>