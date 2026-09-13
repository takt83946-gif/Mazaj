<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$file = 'products.json';
$products = [];
if (file_exists($file)) {
    $json_data = file_get_contents($file);
    $products = json_decode($json_data, true);
    if (!is_array($products)) {
        $products = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لفة Mazaj | المنيو الفاخر المتطور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        [data-theme="dark"] {
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-body: #0b0f19;
            --bg-header: rgba(17, 24, 39, 0.9);
            --bg-card: rgba(17, 24, 39, 0.75);
            --border-color: rgba(255, 255, 255, 0.06);
            --border-hover: rgba(249, 115, 22, 0.3);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --input-bg: rgba(11, 15, 25, 0.8);
            --chip-bg: rgba(30, 41, 59, 0.7);
            --cart-bg: rgba(17, 24, 39, 0.95);
        }

        [data-theme="light"] {
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-body: #f8fafc;
            --bg-header: rgba(255, 255, 255, 0.9);
            --bg-card: rgba(255, 255, 255, 0.85);
            --border-color: rgba(0, 0, 0, 0.06);
            --border-hover: rgba(249, 115, 22, 0.3);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --input-bg: rgba(241, 245, 249, 0.9);
            --chip-bg: rgba(226, 232, 240, 0.8);
            --cart-bg: rgba(255, 255, 255, 0.95);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        
        body { 
            background-color: var(--bg-body);
            background-image: linear-gradient(var(--bg-body), var(--bg-body)), url('uploads/Ali.jpg');
            background-size: contain;
            background-repeat: repeat;
            background-attachment: fixed;
            color: var(--text-main); 
            padding-bottom: 160px; 
            -webkit-tap-highlight-color: transparent;
            min-height: 100vh;
        }

        header { 
            background: var(--bg-header);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            text-align: center; 
            padding: 24px 20px 16px 20px; 
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 90;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }

        .header-controls {
            position: absolute;
            top: 18px;
            left: 18px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .control-btn {
            background: var(--chip-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 6px 12px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 800;
        }
        .control-btn:hover { border-color: var(--accent); }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            padding: 3px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
            margin-bottom: 6px;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        header h1 { font-size: 1.5rem; color: var(--text-main); margin-bottom: 2px; font-weight: 900; letter-spacing: -0.5px; }
        header h1 span { color: var(--accent); }
        header p { color: var(--text-muted); font-size: 0.75rem; }

        .container { max-width: 800px; margin: 0 auto; padding: 0 16px; }

        .top-tools {
            display: flex;
            gap: 10px;
            margin: 16px 0 12px 0;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-box-container { position: relative; flex-grow: 1; min-width: 180px; }
        .search-input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: var(--text-main);
            font-size: 0.85rem;
            outline: none;
            backdrop-filter: blur(10px);
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus { border-color: var(--accent); box-shadow: 0 0 15px rgba(249, 115, 22, 0.15); }
        .search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .view-switch-selector {
            display: flex;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 4px;
            gap: 3px;
        }
        .view-switch-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 8px 14px;
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 10px;
            cursor: pointer;
            white-space: nowrap;
        }
        .view-switch-btn.active {
            background: var(--accent);
            color: #fff;
        }

        .mood-btn {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: #fff;
            border: none;
            padding: 0 16px;
            height: 42px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 0.8rem;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        .mood-btn:hover { opacity: 0.9; }

        .reorder-banner {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 10px 14px;
            border-radius: 14px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            display: none;
        }
        .reorder-info h4 { color: #22c55e; font-size: 0.85rem; font-weight: 800; }
        .reorder-info p { color: var(--text-muted); font-size: 0.7rem; }
        .reorder-action-btn {
            background: #22c55e;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.75rem;
            cursor: pointer;
        }

        .categories-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 12px;
        }

        .category-accordion-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            backdrop-filter: blur(10px);
        }

        .category-header {
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }
        .category-header:hover { background: rgba(249, 115, 22, 0.04); }

        .category-title-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .category-title-area h2 {
            font-size: 1rem;
            font-weight: 900;
            color: var(--text-main);
        }
        .category-badge-count {
            background: var(--accent);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 8px;
        }

        .category-arrow {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: transform 0.3s ease;
        }
        .category-accordion-card.open .category-arrow {
            transform: rotate(180deg);
            color: var(--accent);
        }

        .category-content-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0, 1, 0, 1);
            padding: 0 14px;
        }
        .category-accordion-card.open .category-content-body {
            max-height: 2500px;
            padding: 4px 14px 16px 14px;
            transition: max-height 0.6s ease-in-out;
        }

        .unified-grid-container {
            display: none;
            margin-top: 12px;
        }
        .unified-grid-container.active {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }

        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
            gap: 10px; 
        }
        
        .card { 
            background: var(--bg-body); 
            border: 1px solid var(--border-color); 
            border-radius: 12px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .card:hover { border-color: var(--border-hover); }

        .card-img-container {
            width: 100%;
            height: 105px;
            overflow: hidden;
            background: #000;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .card:hover .card-img { transform: scale(1.05); }
        
        .card-body { 
            padding: 8px 10px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
        }
        .card h3 { font-size: 0.8rem; margin-bottom: 3px; color: var(--text-main); font-weight: 800; }
        .card p { color: var(--text-muted); font-size: 0.7rem; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border-color);
            padding-top: 6px;
        }
        
        .price { color: #fb923c; font-weight: 900; font-size: 0.85rem; }

        .action-btn { 
            background: var(--accent);
            color: #fff; 
            border: none; 
            padding: 4px 10px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 800; 
            font-size: 0.7rem;
        }
        .action-btn:hover { background: var(--accent-hover); }
        
        .qty-control {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--input-bg);
            border-radius: 6px;
            padding: 2px 6px;
            border: 1px solid var(--border-hover);
        }
        .qty-btn {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 900;
            cursor: pointer;
            width: 16px; height: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .qty-num { font-weight: 900; font-size: 0.75rem; color: var(--text-main); min-width: 12px; text-align: center; }

        .checkout-section { 
            background: var(--bg-card); 
            border: 1px solid var(--border-color); 
            padding: 16px; 
            border-radius: 16px; 
            margin-top: 24px; 
            backdrop-filter: blur(10px);
        }
        .checkout-section h3 { color: var(--text-main); margin-bottom: 10px; font-size: 0.95rem; font-weight: 800; }
        .input-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .input-group input { 
            flex: 1; 
            min-width: 160px; 
            padding: 10px 12px; 
            background: var(--input-bg); 
            border: 1px solid var(--border-color); 
            border-radius: 10px; 
            color: var(--text-main); 
            font-size: 0.85rem;
            outline: none;
        }
        .input-group input:focus { border-color: var(--accent); }

        .developer-footer {
            text-align: center;
            margin-top: 24px;
            padding: 14px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.75rem;
            background: var(--bg-card);
            border-radius: 14px 14px 0 0;
        }
        .developer-footer .dev-name { color: var(--text-main); font-weight: 800; }
        .developer-footer a { color: var(--accent); text-decoration: none; font-weight: 800; }

        .cart-bar { 
            position: fixed; 
            bottom: 12px; left: 16px; right: 16px; 
            max-width: 768px;
            margin: 0 auto;
            background: var(--cart-bg); 
            border: 2px solid var(--accent); 
            border-radius: 16px;
            padding: 10px 18px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 8px 30px rgba(249, 115, 22, 0.25); 
            z-index: 100; 
            transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(15px);
        }
        .cart-bar.show { transform: translateY(0); }
        
        .cart-info { display: flex; align-items: center; gap: 10px; cursor: pointer; flex-grow: 1; }
        .cart-icon-box { background: var(--accent); color: #fff; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .cart-details-text { display: flex; flex-direction: column; }
        .cart-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; }
        .cart-total-val { color: var(--text-main); font-weight: 900; font-size: 1.05rem; }
        .cart-total-val span { color: var(--accent); }
        
        .send-btn { 
            background: #22c55e; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 10px; 
            font-weight: 900; 
            cursor: pointer; 
            font-size: 0.85rem;
            display: flex; align-items: center; gap: 6px;
        }
        .send-btn:hover { background: #16a34a; }

        .cart-modal {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 200;
            display: flex; align-items: flex-end;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
            backdrop-filter: blur(5px);
        }
        .cart-modal.open { opacity: 1; pointer-events: auto; }
        .cart-modal-content {
            background: var(--bg-card);
            width: 100%; max-height: 75vh;
            border-radius: 20px 20px 0 0;
            padding: 18px;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            border-top: 1px solid var(--border-color);
        }
        .cart-modal.open .cart-modal-content { transform: translateY(0); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; }
        .modal-header h2 { font-size: 1rem; font-weight: 800; }
        .close-modal { background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; }
        .modal-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-color); }
        .modal-item-info h4 { font-size: 0.85rem; font-weight: 700; }
        .modal-item-info span { color: var(--accent); font-size: 0.8rem; font-weight: 800; }
        .clear-cart-btn { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 3px 10px; border-radius: 6px; font-size: 0.7rem; cursor: pointer; }

        .order-tracker-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 15, 25, 0.96);
            z-index: 500;
            display: flex; justify-content: center; align-items: center;
            padding: 16px; display: none;
        }
        .order-tracker-card {
            background: var(--bg-card); border: 2px solid #22c55e;
            border-radius: 18px; width: 100%; max-width: 400px; padding: 20px; text-align: center;
        }
        .tracker-icon { font-size: 2.5rem; margin-bottom: 6px; }
        .tracker-title { color: #fff; font-size: 1.2rem; font-weight: 900; }
        .tracker-subtitle { color: #4ade80; font-size: 0.8rem; font-weight: 700; margin-bottom: 10px; }
        .tracker-status-box { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80; padding: 8px; border-radius: 10px; font-weight: 900; font-size: 0.85rem; margin-bottom: 10px; }
        .tracker-details { text-align: right; background: rgba(0,0,0,0.4); padding: 10px; border-radius: 10px; margin-bottom: 12px; font-size: 0.8rem; max-height: 120px; overflow-y: auto; }
        .whatsapp-redirect-btn { background: #22c55e; color: #fff; border: none; width: 100%; padding: 12px; border-radius: 10px; font-weight: 900; font-size: 0.95rem; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 8px; }
        .new-order-btn { background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); width: 100%; padding: 8px; border-radius: 10px; font-size: 0.75rem; cursor: pointer; }
    </style>
</head>
<body>

    <header>
        <div class="header-controls">
            <button class="control-btn" id="sound-toggle-btn" onclick="toggleSound()">
                <span id="sound-icon">🔊</span>
            </button>
            <button class="control-btn" id="theme-toggle" onclick="toggleTheme()">
                <span id="theme-icon">🌙</span> <span id="theme-text">ليلي</span>
            </button>
        </div>
        <div class="logo-badge">🔥 نكهات استثنائية وعصرية</div>
        <h1>لفة <span>Mazaj</span> 🌯</h1>
        <p>تصفح أشهى الوجبات بتصميم راقٍ ونقي</p>
    </header>

    <div class="container">

        <div class="reorder-banner" id="reorder-banner">
            <div class="reorder-info">
                <h4>🔄 طلبت مسبقاً؟</h4>
                <p id="reorder-desc">اضغط لتكرار آخر طلب سريعاً</p>
            </div>
            <button class="reorder-action-btn" onclick="repeatLastOrder()">اطلبها ⚡</button>
        </div>

        <div class="top-tools">
            <div class="search-box-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="search-input" class="search-input" placeholder="ابحث عن وجبتك المفضلة..." oninput="filterProducts()">
            </div>
            <div class="view-switch-selector">
                <button class="view-switch-btn active" id="btn-mode-accordion" onclick="switchViewMode('accordion')">أقسام 📂</button>
                <button class="view-switch-btn" id="btn-mode-grid" onclick="switchViewMode('grid')">الكل ⚡</button>
            </div>
            <button class="mood-btn" onclick="suggestRandomProduct()">🎲 عشوائي</button>
        </div>

        <?php
        if (empty($products)) {
            echo '<p style="text-align:center; padding:40px; color:var(--text-muted);">لا توجد منتجات مضافة حالياً.</p>';
        } else {
            echo '<div class="categories-container" id="categories-wrapper">';
            $categories = array_unique(array_column($products, 'category'));
            $index_cat = 0;
            foreach ($categories as $cat) {
                $cat_products = array_filter($products, function($p) use ($cat) {
                    return isset($p['category']) && $p['category'] === $cat;
                });
                $cat_count = count($cat_products);
                $is_open_class = ($index_cat === 0) ? 'open' : '';

                echo '<div class="category-accordion-card ' . $is_open_class . '" data-category-name="' . htmlspecialchars($cat) . '">';
                echo '  <div class="category-header" onclick="toggleCategory(this)">';
                echo '      <div class="category-title-area">';
                echo '          <h2>🌯 ' . htmlspecialchars($cat) . '</h2>';
                echo '          <span class="category-badge-count">' . $cat_count . '</span>';
                echo '      </div>';
                echo '      <span class="category-arrow">▼</span>';
                echo '  </div>';
                
                echo '  <div class="category-content-body">';
                echo '      <div class="menu-grid">';
                
                foreach ($cat_products as $p) {
                    $safe_name = htmlspecialchars($p['name'], ENT_QUOTES);
                    $hash_id = md5($p['name']);
                    $item_price = $p['price'] ?? 0;
                    $item_image = !empty($p['image']) ? $p['image'] : (!empty($p['img']) ? $p['img'] : (!empty($p['photo']) ? $p['photo'] : 'uploads/default.jpg'));
                    
                    echo '<div class="card product-card" data-category="' . htmlspecialchars($cat) . '" data-name="' . mb_strtolower($p['name']) . '" data-desc="' . mb_strtolower($p['desc_text'] ?? '') . '">';
                    echo '  <div class="card-img-container">';
                    echo '      <img src="' . htmlspecialchars($item_image) . '" alt="' . $safe_name . '" class="card-img" onerror="this.src=\'uploads/default.jpg\'">';
                    echo '  </div>';
                    echo '  <div class="card-body">';
                    echo '      <div><h3>' . htmlspecialchars($p['name']) . '</h3><p>' . htmlspecialchars($p['desc_text'] ?? '') . '</p></div>';
                    echo '      <div class="card-footer">';
                    echo '          <span class="price" id="price-' . $hash_id . '">$' . number_format($item_price, 2) . '</span>';
                    echo '          <div id="btn-container-' . $hash_id . '"><button class="action-btn" onclick="changeQty(\'' . $safe_name . '\', ' . $item_price . ', 1, \'' . $hash_id . '\')">إضافة +</button></div>';
                    echo '      </div>';
                    echo '  </div>';
                    echo '</div>';
                }
                
                echo '      </div>';
                echo '  </div>';
                echo '</div>';

                $index_cat++;
            }
            echo '</div>';

            echo '<div class="unified-grid-container" id="unified-grid-wrapper">';
            foreach ($products as $p) {
                $safe_name = htmlspecialchars($p['name'], ENT_QUOTES);
                $hash_id = md5($p['name']);
                $item_price = $p['price'] ?? 0;
                $item_image = !empty($p['image']) ? $p['image'] : (!empty($p['img']) ? $p['img'] : (!empty($p['photo']) ? $p['photo'] : 'uploads/default.jpg'));
                $cat_name = $p['category'] ?? 'عام';
                
                echo '<div class="card product-card-grid" data-category="' . htmlspecialchars($cat_name) . '" data-name="' . mb_strtolower($p['name']) . '" data-desc="' . mb_strtolower($p['desc_text'] ?? '') . '">';
                echo '  <div class="card-img-container">';
                echo '      <img src="' . htmlspecialchars($item_image) . '" alt="' . $safe_name . '" class="card-img" onerror="this.src=\'uploads/default.jpg\'">';
                echo '  </div>';
                echo '  <div class="card-body">';
                echo '      <div><h3>' . htmlspecialchars($p['name']) . '</h3><p>' . htmlspecialchars($p['desc_text'] ?? '') . '</p></div>';
                echo '      <div class="card-footer">';
                echo '          <span class="price" id="grid-price-' . $hash_id . '">$' . number_format($item_price, 2) . '</span>';
                echo '          <div id="grid-btn-container-' . $hash_id . '"><button class="action-btn" onclick="changeQty(\'' . $safe_name . '\', ' . $item_price . ', 1, \'' . $hash_id . '\')">إضافة +</button></div>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <div class="checkout-section">
            <h3>📍 بيانات الاستلام والتوصيل</h3>
            <div class="input-group">
                <input type="text" id="cust-name" placeholder="اسمك الكريم" oninput="saveCustomerData()">
                <input type="tel" id="cust-phone" placeholder="رقم الهاتف (واتساب)" oninput="saveCustomerData()">
                <input type="text" id="cust-address" placeholder="عنوان التوصيل" oninput="saveCustomerData()">
            </div>
        </div>

        <div class="developer-footer">
            <p>تصميم وبرمجة: <span class="dev-name">علي حسين ناصر الدين</span></p>
            <p style="margin-top:3px;">للتواصل: <a href="https://wa.me/96181058043" target="_blank">96181058043+</a></p>
        </div>
    </div>

    <div class="cart-bar" id="cart-bar">
        <div class="cart-info" onclick="toggleCartModal()">
            <div class="cart-icon-box">🛒</div>
            <div class="cart-details-text">
                <span class="cart-title">إجمالي السلة (<span id="cart-count">0</span> أصناف)</span>
                <span class="cart-total-val">$<span id="total-price">0.00</span></span>
            </div>
        </div>
        <button class="send-btn" id="send-order-btn" onclick="sendOrder()"><span>إرسال</span> 💬</button>
    </div>

    <div class="cart-modal" id="cart-modal" onclick="if(event.target === this) toggleCartModal()">
        <div class="cart-modal-content">
            <div class="modal-header">
                <h2>مراجعة سلة طلباتك 🛍️</h2>
                <button class="clear-cart-btn" onclick="clearCart()">🗑️ تفريغ</button>
                <button class="close-modal" onclick="toggleCartModal()">&times;</button>
            </div>
            <div id="modal-items-list"></div>
        </div>
    </div>

    <div class="order-tracker-overlay" id="order-tracker">
        <div class="order-tracker-card">
            <div class="tracker-icon">🚀</div>
            <div class="tracker-title">تم حفظ طلبك بنجاح!</div>
            <div class="tracker-subtitle">الخطوة الأخيرة لاعتماد وجبتك</div>
            <div class="tracker-status-box">بانتظار تأكيد الواتساب 🔥</div>
            <div class="tracker-details" id="tracker-order-summary"></div>
            <a href="#" id="tracker-wa-link" target="_blank" class="whatsapp-redirect-btn"><span>تأكيد الطلب عبر الواتساب</span> 💬</a>
            <button class="new-order-btn" onclick="location.reload()">طلب وجبة أخرى 🔄</button>
        </div>
    </div>

    <script>
        let cart = {};
        let soundEnabled = localStorage.getItem('mazaj_sound') !== 'off';

        // محرك الألوان والأصوات التفاعلي (Web Audio API) لتجنب مشاكل روابط الملفات الخارجية
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        
        function playSound(type) {
            if (!soundEnabled) return;
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            let now = audioCtx.currentTime;

            if (type === 'click') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(400, now);
                osc.frequency.exponentialRampToValueAtTime(800, now + 0.05);
                gainNode.gain.setValueAtTime(0.05, now);
                gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
                osc.start(now);
                osc.stop(now + 0.05);
            } else if (type === 'add') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, now); // C5
                osc.frequency.setValueAtTime(659.25, now + 0.08); // E5
                gainNode.gain.setValueAtTime(0.08, now);
                gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.2);
                osc.start(now);
                osc.stop(now + 0.2);
            } else if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, now);
                osc.frequency.setValueAtTime(554.37, now + 0.1);
                osc.frequency.setValueAtTime(659.25, now + 0.2);
                gainNode.gain.setValueAtTime(0.1, now);
                gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc.start(now);
                osc.stop(now + 0.4);
            }
        }

        function toggleSound() {
            soundEnabled = !soundEnabled;
            localStorage.setItem('mazaj_sound', soundEnabled ? 'on' : 'off');
            updateSoundUI();
            if (soundEnabled) playSound('click');
        }

        function updateSoundUI() {
            let soundIcon = document.getElementById('sound-icon');
            soundIcon.innerText = soundEnabled ? '🔊' : '🔇';
        }

        window.addEventListener('DOMContentLoaded', () => {
            let savedTheme = localStorage.getItem('mazaj_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeUI(savedTheme);
            updateSoundUI();

            if(localStorage.getItem('mazaj_name')) document.getElementById('cust-name').value = localStorage.getItem('mazaj_name');
            if(localStorage.getItem('mazaj_phone')) document.getElementById('cust-phone').value = localStorage.getItem('mazaj_phone');
            if(localStorage.getItem('mazaj_address')) document.getElementById('cust-address').value = localStorage.getItem('mazaj_address');
            
            checkLastOrderBanner();
        });

        function toggleTheme() {
            playSound('click');
            let currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('mazaj_theme', newTheme);
            updateThemeUI(newTheme);
        }

        function updateThemeUI(theme) {
            let iconSpan = document.getElementById('theme-icon');
            let textSpan = document.getElementById('theme-text');
            if (theme === 'dark') { iconSpan.innerText = '🌙'; textSpan.innerText = 'ليلي'; }
            else { iconSpan.innerText = '☀️'; textSpan.innerText = 'مضيء'; }
        }

        function switchViewMode(mode) {
            playSound('click');
            document.getElementById('btn-mode-accordion').classList.toggle('active', mode === 'accordion');
            document.getElementById('btn-mode-grid').classList.toggle('active', mode === 'grid');

            let accordionWrapper = document.getElementById('categories-wrapper');
            let gridWrapper = document.getElementById('unified-grid-wrapper');

            if (mode === 'accordion') {
                accordionWrapper.style.display = 'flex';
                gridWrapper.classList.remove('active');
            } else {
                accordionWrapper.style.display = 'none';
                gridWrapper.classList.add('active');
            }
        }

        function toggleCategory(headerElement) {
            playSound('click');
            let card = headerElement.parentElement;
            card.classList.toggle('open');
        }

        function saveCustomerData() {
            localStorage.setItem('mazaj_name', document.getElementById('cust-name').value);
            localStorage.setItem('mazaj_phone', document.getElementById('cust-phone').value);
            localStorage.setItem('mazaj_address', document.getElementById('cust-address').value);
        }

        function suggestRandomProduct() {
            playSound('click');
            let cards = document.querySelectorAll('.product-card, .product-card-grid');
            let visibleCards = Array.from(cards).filter(c => c.style.display !== 'none');
            if (visibleCards.length === 0) return alert('لا توجد منتجات متاحة!');
            let randomIndex = Math.floor(Math.random() * visibleCards.length);
            let selectedCard = visibleCards[randomIndex];
            
            let parentCard = selectedCard.closest('.category-accordion-card');
            if(parentCard) {
                parentCard.style.display = 'block';
                parentCard.classList.add('open');
            }

            selectedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            selectedCard.style.transition = '0.3s';
            selectedCard.style.borderColor = '#8b5cf6';
            setTimeout(() => selectedCard.style.borderColor = '', 1500);
        }

        function checkLastOrderBanner() {
            let lastOrder = localStorage.getItem('mazaj_last_order');
            if (lastOrder) {
                try {
                    let parsed = JSON.parse(lastOrder);
                    let names = Object.keys(parsed);
                    if (names.length > 0) {
                        document.getElementById('reorder-desc').innerText = `تضمن: ${names.join(', ')}`;
                        document.getElementById('reorder-banner').style.display = 'flex';
                    }
                } catch(e) {}
            }
        }

        function repeatLastOrder() {
            playSound('add');
            let lastOrder = localStorage.getItem('mazaj_last_order');
            if (!lastOrder) return;
            try {
                cart = JSON.parse(lastOrder);
                for (let itemName in cart) updateUI(itemName, cart[itemName].hashId);
                updateCartBar();
                alert('تمت إضافة طلبك السابق للسلة! 🚀');
            } catch(e) {}
        }

        function filterProducts() {
            let query = document.getElementById('search-input').value.trim().toLowerCase();
            
            let catCards = document.querySelectorAll('.category-accordion-card');
            catCards.forEach(catCard => {
                let cards = catCard.querySelectorAll('.product-card');
                let hasMatch = false;
                cards.forEach(card => {
                    let name = card.getAttribute('data-name');
                    let desc = card.getAttribute('data-desc');
                    let match = name.includes(query) || desc.includes(query);
                    card.style.display = match ? 'flex' : 'none';
                    if (match) hasMatch = true;
                });
                if (query !== '' && hasMatch) {
                    catCard.classList.add('open');
                    catCard.style.display = 'block';
                } else if (query !== '' && !hasMatch) {
                    catCard.style.display = 'none';
                }
            });

            let gridCards = document.querySelectorAll('.product-card-grid');
            gridCards.forEach(card => {
                let name = card.getAttribute('data-name');
                let desc = card.getAttribute('data-desc');
                let match = name.includes(query) || desc.includes(query);
                card.style.display = match ? 'flex' : 'none';
            });
        }

        function changeQty(name, price, change, hashId) {
            price = parseFloat(price);
            if (!cart[name]) {
                if (change > 0) {
                    cart[name] = { price: price, qty: 1, hashId: hashId };
                    playSound('add');
                }
            } else {
                cart[name].qty += change;
                if (change > 0) playSound('add');
                else playSound('click');
                
                if (cart[name].qty <= 0) delete cart[name];
            }
            updateUI(name, hashId);
            updateCartBar();
        }

        function clearCart() {
            playSound('click');
            if (confirm('تفريغ السلة؟')) { cart = {}; location.reload(); }
        }

        function updateUI(name, hashId) {
            let containers = [
                document.getElementById('btn-container-' + hashId),
                document.getElementById('grid-btn-container-' + hashId)
            ];
            let priceElements = [
                document.getElementById('price-' + hashId),
                document.getElementById('grid-price-' + hashId)
            ];

            containers.forEach((container, idx) => {
                let priceElement = priceElements[idx];
                if (!container || !priceElement) return;

                let unitPrice = cart[name] ? cart[name].price : parseFloat(priceElement.getAttribute('data-base-price') || priceElement.innerText.replace('$', ''));
                if (!priceElement.hasAttribute('data-base-price')) priceElement.setAttribute('data-base-price', unitPrice);

                if (cart[name] && cart[name].qty > 0) {
                    let qty = cart[name].qty;
                    priceElement.innerText = '$' + (unitPrice * qty).toFixed(2);
                    container.innerHTML = `
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeQty('${name.replace(/'/g, "\\'")}', ${unitPrice}, -1, '${hashId}')">-</button>
                            <span class="qty-num">${qty}</span>
                            <button class="qty-btn" onclick="changeQty('${name.replace(/'/g, "\\'")}', ${unitPrice}, 1, '${hashId}')">+</button>
                        </div>`;
                } else {
                    let basePrice = parseFloat(priceElement.getAttribute('data-base-price'));
                    priceElement.innerText = '$' + basePrice.toFixed(2);
                    container.innerHTML = `<button class="action-btn" onclick="changeQty('${name.replace(/'/g, "\\'")}', ${basePrice}, 1, '${hashId}')">إضافة +</button>`;
                }
            });
        }

        function updateCartBar() {
            let totalCount = 0, subtotal = 0, modalListHtml = '';
            for (let item in cart) {
                let qty = cart[item].qty, price = cart[item].price, itemTotal = price * qty;
                totalCount += qty;
                subtotal += itemTotal;
                modalListHtml += `
                    <div class="modal-item">
                        <div class="modal-item-info"><h4>${item}</h4><span>$${itemTotal.toFixed(2)} (×${qty})</span></div>
                        <div class="qty-control" style="padding: 2px 6px;">
                            <button class="qty-btn" onclick="changeQty('${item.replace(/'/g, "\\'")}', ${price}, -1, '${cart[item].hashId}')">-</button>
                            <span class="qty-num">${qty}</span>
                            <button class="qty-btn" onclick="changeQty('${item.replace(/'/g, "\\'")}', ${price}, 1, '${cart[item].hashId}')">+</button>
                        </div>
                    </div>`;
            }

            document.getElementById('cart-count').innerText = totalCount;
            document.getElementById('total-price').innerText = subtotal.toFixed(2);
            document.getElementById('modal-items-list').innerHTML = modalListHtml || '<p style="text-align:center; color:var(--text-muted); padding:10px;">السلة فارغة</p>';
            
            let cartBar = document.getElementById('cart-bar');
            if (totalCount > 0) cartBar.classList.add('show');
            else { cartBar.classList.remove('show'); document.getElementById('cart-modal').classList.remove('open'); }
        }

        function toggleCartModal() {
            playSound('click');
            document.getElementById('cart-modal').classList.toggle('open');
        }

        function sendOrder() {
            let totalCount = 0;
            for (let item in cart) totalCount += cart[item].qty;
            if (totalCount === 0) return alert('السلة فارغة!');

            let name = document.getElementById('cust-name').value.trim();
            let phone = document.getElementById('cust-phone').value.trim();
            let address = document.getElementById('cust-address').value.trim();
            
            if (!name) return alert('الرجاء إدخال اسمك!');
            if (!phone) return alert('الرجاء إدخال رقم الهاتف!');
            if (!address) return alert('الرجاء إدخال العنوان!');

            playSound('success');
            localStorage.setItem('mazaj_last_order', JSON.stringify(cart));
            let subtotal = 0;
            for (let item in cart) subtotal += cart[item].price * cart[item].qty;

            let orderData = { customer_name: name, customer_phone: phone, customer_address: address, items: cart, total: '$' + subtotal.toFixed(2), status: 'قيد التحضير', time: new Date().toLocaleString() };

            fetch('save_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            }).then(res => res.json()).then(data => {
                let adminPhone = "96181079589"; 
                let message = "مرحباً *لفة Mazaj* 🌯، أريد طلب الآتي:\n\n";
                let index = 1, summaryHtml = '';

                for (let item in cart) {
                    let qty = cart[item].qty, itemTotal = cart[item].price * qty;
                    message += `${index}. *${item}* (×${qty}) - $${itemTotal.toFixed(2)}\n`;
                    summaryHtml += `<p>• ${item} (×${qty}) - <span>$${itemTotal.toFixed(2)}</span></p>`;
                    index++;
                }
                message += `\n*الإجمالي:* $${subtotal.toFixed(2)}\n👤 الاسم: ${name}\n📞 الهاتف: ${phone}\n📍 العنوان: ${address}`;

                summaryHtml += `<p style="margin-top:6px; border-top:1px solid rgba(255,255,255,0.1)">الإجمالي: <span>$${subtotal.toFixed(2)}</span></p>`;

                document.getElementById('cart-bar').classList.remove('show');
                document.getElementById('cart-modal').classList.remove('open');
                
                let finalWaUrl = `https://wa.me/${adminPhone}?text=${encodeURIComponent(message)}`;
                document.getElementById('tracker-order-summary').innerHTML, summaryHtml;
                document.getElementById('tracker-wa-link').href = finalWaUrl;
                document.getElementById('order-tracker').style.display = 'flex';
            });
        }
    </script>
</body>
</html>
