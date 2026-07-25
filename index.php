<?php
/**
 * index.php
 * الصفحة الشغالة فعلياً: تتصل بقاعدة البيانات، تحفظ الأفلام، وتعرضها في جدول.
 */

$DB_HOST = "sql111.infinityfree.com";
$DB_USER = "if0_42498480";
$DB_PASS = "Pq4UQBpme0FruO";
$DB_NAME = "if0_42498480_amani";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// ينشئ الجدول تلقائياً أول مرة إذا ما كان موجود
$conn->query("
    CREATE TABLE IF NOT EXISTS movies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        category VARCHAR(50) NOT NULL,
        rating TINYINT NOT NULL,
        watched TINYINT(1) NOT NULL DEFAULT 0,
        note VARCHAR(500) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// استقبال الفورم وحفظ الفيلم
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title    = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $rating   = (int)($_POST["rating"] ?? 0);
    $watched  = isset($_POST["watched"]) ? 1 : 0;
    $note     = trim($_POST["note"] ?? "");

    if ($title !== "" && $category !== "" && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare(
            "INSERT INTO movies (title, category, rating, watched, note) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssiis", $title, $category, $rating, $watched, $note);
        $stmt->execute();
        $stmt->close();
    }

    // إعادة توجيه لنفس الصفحة عشان نمنع إعادة إرسال الفورم عند تحديث الصفحة
    header("Location: index.php");
    exit;
}

// جلب كل الأفلام لعرضها في الجدول
$result = $conn->query("SELECT * FROM movies ORDER BY id DESC");
$movies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$categories = ["Action", "Comedy", "Drama", "Horror", "Romance", "Sci-Fi", "Animation", "Documentary"];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Movie Night Planner 🎬</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #12121a;
    --surface: #1c1c28;
    --surface-2: #23232f;
    --gold: #e8b84b;
    --gold-dim: #b3903c;
    --crimson: #8c2f39;
    --crimson-bright: #b8414c;
    --text: #f1ede4;
    --text-muted: #a8a5b0;
    --border: #34343f;
    --radius: 10px;
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font-family: 'Work Sans', sans-serif; line-height: 1.5; }
.filmstrip { height: 22px; background: repeating-linear-gradient(90deg, #000 0px, #000 14px, transparent 14px, transparent 28px); position: relative; }
.filmstrip::before { content: ""; position: absolute; inset: 0; background: repeating-linear-gradient(90deg, var(--gold) 0 8px, transparent 8px 28px); opacity: 0.15; }
.marquee { text-align: center; padding: 36px 20px 24px; background: linear-gradient(180deg, var(--surface) 0%, var(--bg) 100%); border-bottom: 2px solid var(--gold-dim); }
.marquee h1 { font-family: 'Bebas Neue', sans-serif; letter-spacing: 4px; font-size: 2.8rem; margin: 0; color: var(--gold); text-shadow: 0 0 18px rgba(232, 184, 75, 0.35); }
.tagline { color: var(--text-muted); margin-top: 8px; font-size: 0.95rem; }
.wrap { max-width: 960px; margin: 0 auto; padding: 32px 20px 60px; display: grid; gap: 28px; }
.card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
.card h2 { font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; font-size: 1.5rem; color: var(--gold); margin-top: 0; border-bottom: 1px dashed var(--border); padding-bottom: 10px; }
.movie-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; }
.field-wide { grid-column: 1 / -1; }
.field { display: flex; flex-direction: column; gap: 6px; }
label { font-size: 0.85rem; color: var(--text-muted); }
input[type="text"], select, textarea { background: var(--surface-2); border: 1px solid var(--border); border-radius: 6px; padding: 10px 12px; color: var(--text); font-family: inherit; font-size: 0.95rem; }
input[type="text"]:focus, select:focus, textarea:focus { outline: 2px solid var(--gold); outline-offset: 1px; }
textarea { resize: vertical; }
.checkbox-field { justify-content: center; }
.checkbox-label { display: flex; align-items: center; gap: 8px; color: var(--text); font-size: 0.9rem; cursor: pointer; }
.checkbox-label input { width: 18px; height: 18px; accent-color: var(--gold); }
.btn-submit { grid-column: 1 / -1; background: var(--gold); color: #1a1508; border: none; border-radius: 6px; padding: 12px 16px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: transform 0.15s ease, background 0.15s ease; }
.btn-submit:hover { background: #f2c766; transform: translateY(-1px); }
.table-scroll { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; min-width: 640px; }
thead th { text-align: right; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); padding: 10px 12px; border-bottom: 2px solid var(--border); }
tbody td { padding: 12px; border-bottom: 1px dashed var(--border); font-size: 0.9rem; vertical-align: middle; }
.title-cell { font-weight: 600; }
.stars { color: var(--gold); letter-spacing: 1px; }
.note-cell { color: var(--text-muted); max-width: 220px; }
.badge { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 0.78rem; white-space: nowrap; }
.badge-cat { background: var(--surface-2); color: var(--gold); border: 1px solid var(--gold-dim); }
.badge-watched { background: rgba(76, 175, 100, 0.15); color: #7fd996; border: 1px solid #4c8f5e; }
.badge-pending { background: rgba(140, 47, 57, 0.2); color: #e88b93; border: 1px solid var(--crimson); }
.btn-toggle { background: var(--crimson); color: var(--text); border: none; border-radius: 6px; padding: 8px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.15s ease, transform 0.15s ease; }
.btn-toggle:hover { background: var(--crimson-bright); transform: translateY(-1px); }
.empty-state { color: var(--text-muted); text-align: center; padding: 20px 0; }
@media (max-width: 600px) { .movie-form { grid-template-columns: 1fr; } .marquee h1 { font-size: 2rem; } }
</style>
</head>
<body>

<div class="filmstrip"></div>

<header class="marquee">
    <h1>🎬 MOVIE NIGHT PLANNER</h1>
    <p class="tagline">خطط لليلة أفلامك القادمة… أضف، قيّم، وتابع اللي شفته</p>
</header>

<main class="wrap">

    <section class="card form-card">
        <h2>إضافة فيلم جديد</h2>
        <form action="index.php" method="POST" class="movie-form">
            <div class="field">
                <label for="title">اسم الفيلم</label>
                <input type="text" id="title" name="title" placeholder="مثال: Inception" required maxlength="150">
            </div>
            <div class="field">
                <label for="category">التصنيف</label>
                <select id="category" name="category" required>
                    <option value="" disabled selected>اختر تصنيف</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="rating">التقييم</label>
                <select id="rating" name="rating" required>
                    <option value="" disabled selected>من 1 إلى 5</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= str_repeat("★", $i) . str_repeat("☆", 5 - $i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="field checkbox-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="watched" value="1">
                    <span>سبق وشاهدته</span>
                </label>
            </div>
            <div class="field field-wide">
                <label for="note">ملاحظة</label>
                <textarea id="note" name="note" rows="2" placeholder="أي تعليق أو ملاحظة عن الفيلم..." maxlength="500"></textarea>
            </div>
            <button type="submit" class="btn-submit">إضافة إلى القائمة 🍿</button>
        </form>
    </section>

    <section class="card table-card">
        <h2>قائمة أفلامي</h2>
        <?php if (empty($movies)): ?>
            <p class="empty-state">لسا ما أضفت أي فيلم. عبّي الفورم فوق وابدأ قائمتك 🎥</p>
        <?php else: ?>
        <div class="table-scroll">
        <table id="moviesTable">
            <thead>
                <tr>
                    <th>الفيلم</th><th>التصنيف</th><th>التقييم</th><th>الحالة</th><th>ملاحظة</th><th>تبديل</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $m): ?>
                <tr id="row-<?= $m['id'] ?>">
                    <td class="title-cell"><?= htmlspecialchars($m['title']) ?></td>
                    <td><span class="badge badge-cat"><?= htmlspecialchars($m['category']) ?></span></td>
                    <td class="stars"><?= str_repeat("★", (int)$m['rating']) . str_repeat("☆", 5 - (int)$m['rating']) ?></td>
                    <td>
                        <span id="status-<?= $m['id'] ?>" class="badge <?= $m['watched'] ? 'badge-watched' : 'badge-pending' ?>">
                            <?= $m['watched'] ? '✅ تمت المشاهدة' : '🎟️ لم يشاهد بعد' ?>
                        </span>
                    </td>
                    <td class="note-cell"><?= htmlspecialchars($m['note'] ?: '—') ?></td>
                    <td>
                        <button class="btn-toggle" onclick="toggleWatched(<?= $m['id'] ?>)">Toggle</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </section>

</main>

<div class="filmstrip"></div>

<script>
// يرسل طلب AJAX لملف toggle.php ويحدث شكل الخانة مباشرة بدون إعادة تحميل الصفحة
function toggleWatched(id) {
    const statusBadge = document.getElementById("status-" + id);

    fetch("toggle.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                alert(data.message || "صار خطأ، حاول مرة ثانية.");
                return;
            }
            if (data.watched == 1) {
                statusBadge.textContent = "✅ تمت المشاهدة";
                statusBadge.classList.remove("badge-pending");
                statusBadge.classList.add("badge-watched");
            } else {
                statusBadge.textContent = "🎟️ لم يشاهد بعد";
                statusBadge.classList.remove("badge-watched");
                statusBadge.classList.add("badge-pending");
            }
        })
        .catch((err) => {
            console.error(err);
            alert("تعذر الاتصال بالسيرفر.");
        });
}
</script>
</body>
</html>
