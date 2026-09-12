<?php
// index.php - الواجهة الرئيسية لعرض منتجات ملف products.json بدون أخطاء
$json_file = 'products.json';
$products = [];

if (file_exists($json_file)) {
    $json_data = file_get_contents($json_file);
    $products = json_decode($json_data, true);
}

// تجميع المنتجات حسب الأقسام
$grouped_menu = [];
if (is_array($products)) {
    foreach ($products as $item) {
        $cat = $item['category'] ?? 'أخرى';
        $grouped_menu[$cat][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لفة مزاج - المنيو</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        body { background: #0f172a; color: #f8fafc; min-height: 100vh; }
        
        header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 20px 24px; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            position: sticky;
            top: 0;
            background: #0f172a;
            z-index: 100;
        }
        
        .menu-icon, .cart-icon { 
            background: none; 
            border: none; 
            cursor: pointer; 
            font-size: 1.4rem; 
            color: #fff; 
        }

        .logo-container { text-align: center; }
        .logo-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #f59e0b;
        }

        /* Sidebar Drawer */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
            z-index: 998;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }

        .sidebar {
            position: fixed;
            top: 0;
            right: -320px;
            width: 300px;
            height: 100%;
            background: #1e293b;
            box-shadow: -5px 0 25px rgba(0,0,0,0.5);
            transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
            display: flex;
            flex-direction: column;
            padding: 24px;
        }
        .sidebar.active { right: 0; }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .close-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #fff;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 18px;
            overflow-y: auto;
        }

        .sidebar-menu li a {
            text-decoration: none;
            color: #f8fafc;
            font-size: 1.05rem;
            font-weight: 600;
            transition: color 0.2s;
            display: block;
        }
        .sidebar-menu li a:hover { color: #f59e0b; }
        .sidebar-divider { margin: 20px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1); }

        /* المحتوى والمنيو */
        .container { max-width: 900px; margin: 0 auto; padding: 30px 20px; }
        .category-section { margin-bottom: 40px; }
        .category-title { font-size: 1.4rem; font-weight: 700; border-bottom: 2px solid #f59e0b; padding-bottom: 8px; margin-bottom: 20px; color: #f59e0b; }
        
        .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px; }
        .item-card { 
            background: #1e293b; 
            border: 1px solid rgba(255,255,255,0.08); 
            padding: 18px; 
            border-radius: 12px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .item-name { font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .item-price { font-size: 0.88rem; color: #34d399; font-weight: 600; background: rgba(52, 211, 153, 0.1); padding: 6px 10px; border-radius: 6px; width: fit-content; }
    </style>
</head>
<body>

    <header>
        <button class="menu-icon" onclick="toggleSidebar()">☰</button>
        
        <div class="logo-container">
            <div class="logo-text">🌯 لفة مزاج</div>
        </div>

        <button class="cart-icon" onclick="window.location.href='cart.php'">🛒</button>
    </header>

    <!-- Sidebar Drawer -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-text">🌯 لفة مزاج</div>
            <button class="close-sidebar" onclick="toggleSidebar()">✕</button>
        </div>
        
        <ul class="sidebar-menu">
            <?php foreach (array_keys($grouped_menu) as $cat): ?>
                <li><a href="#cat-<?= md5($cat) ?>" onclick="toggleSidebar()"><?= htmlspecialchars($cat) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <hr class="sidebar-divider">

        <ul class="sidebar-menu">
            <li><a href="admin.php">لوحة التحكم (Admin)</a></li>
            <li><a href="index.php">الرئيسية</a></li>
        </ul>
    </div>

    <div class="container">
        <?php if (empty($grouped_menu)): ?>
            <p style="text-align: center; color: #94a3b8; padding: 40px;">لا توجد منتجات مضافة حالياً في ملف products.json.</p>
        <?php else: ?>
            <?php foreach ($grouped_menu as $category => $items): ?>
                <div class="category-section" id="cat-<?= md5($category) ?>">
                    <div class="category-title"><?= htmlspecialchars($category) ?></div>
                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card">
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-price"><?= htmlspecialchars($item['price']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
    </script>
</body>
</html>
