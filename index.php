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
    <title>لفة Mazaj | المنيو العصري المتطور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        [data-theme="dark"] {
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-body: #0b0f19;
            --bg-header: rgba(17, 24, 39, 0.95);
            --bg-card: rgba(17, 24, 39, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(249, 115, 22, 0.4);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --input-bg: rgba(11, 15, 25, 0.7);
            --chip-bg: rgba(30, 41, 59, 0.85);
            --cart-bg: rgba(17, 24, 39, 0.95);
        }

        [data-theme="light"] {
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-body: #f8fafc;
            --bg-header: rgba(255, 255, 255, 0.95);
            --bg-card: rgba(255, 255, 255, 0.9);
            --border-color: rgba(0, 0, 0, 0.08);
            --border-hover: rgba(249, 115, 22, 0.4);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --input-bg: rgba(241, 245, 249, 0.9);
            --chip-bg: rgba(226, 232, 240, 0.9);
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
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            text-align: center; 
            padding: 20px 20px 12px 20px; 
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 90;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .theme-toggle-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--chip-bg);
            border: 1px solid var(--border-hover);
            color: var(--text-main);
            padding: 5px 10px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 800;
            z-index: 10;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            padding: 2px 10px;
            border-radius: 30px;
            font-size: 0.65rem;
            font-weight: 700;
            margin-bottom: 3px;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        header h1 { font-size: 1.4rem; color: var(--text-main); margin-bottom: 2px; font-weight: 900; }
        header h1 span { color: var(--accent); }
        header p { color: var(--text-muted); font-size: 0.7rem; }

        .container { max-width: 800px; margin: 0 auto; padding: 0 12px; }

        .top-tools {
            display: flex;
            gap: 8px;
            margin: 12px 0 10px 0;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-box-container { position: relative; flex-grow: 1; min-width: 160px; }
        .search-input {
            width: 100%;
            padding: 8px 34px 8px 12px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.8rem;
            outline: none;
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus { border-color: var(--accent); }
        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .mood-btn {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: #fff;
            border: none;
            padding: 0 12px;
            height: 36px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.75rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .reorder-banner {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid #22c55e;
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            display: none;
        }
        .reorder-info h4 { color: #22c55e; font-size: 0.8rem; font-weight: 800; }
        .reorder-info p { color: var(--text-muted); font-size: 0.65rem; }
        .reorder-action-btn {
            background: #22c55e;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.7rem;
            cursor: pointer;
        }

        /* ------------------------------------------------ */
        /* تصميم الدوائر الأفقية (Categories Circles) */
        /* ------------------------------------------------ */
        .categories-circles-wrapper {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 16px;
            scrollbar-width: none;
        }
        .categories-circles-wrapper::-webkit-scrollbar { display: none; }

        .category-circle-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            flex-shrink: 0;
            text-align: center;
            width: 75px;
        }

        .category-circle-img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            padding: 2px;
            transition: 0.2s ease;
        }

        .category-circle-item.active .category-circle-img {
            border-color: var(--accent);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
            transform: scale(1.05);
        }

        .category-circle-name {
            font-size: 0.68rem;
            font-weight: 800;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .category-circle-item.active .category-circle-name {
            color: var(--accent);
        }

        /* حاوية الأقسام */
        .category-section-block {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .category-header-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .category-header-title h2 {
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .category-badge-count {
            background: rgba(249, 115, 22, 0.15);
            color: var(--accent);
            font-size: 0.7rem;
            font-weight: 900;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
        }

        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        .card { 
            background: var(--bg-body); 
            border: 1px solid var(--border-color); 
            border-radius: 10px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between;
        }

        .card-img-container {
            width: 100%;
            height: 90px;
            overflow: hidden;
            background: #000;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .card-body { 
            padding: 6px 8px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
        }
        .card h3 { font-size: 0.75rem; margin-bottom: 2px; color: var(--text-main); font-weight: 800; }
        .card p { color: var(--text-muted); font-size: 0.65rem; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border-color);
            padding-top: 4px;
        }
        
        .price { color: #fb923c; font-weight: 900; font-size: 0.8rem; }

        .action-btn { 
            background: var(--accent);
            color: #fff; 
            border: none; 
            padding: 3px 8px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: 800; 
            font-size: 0.65rem;
        }
        
        .qty-control {
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--input-bg);
            border-radius: 5px;
            padding: 1px 4px;
            border: 1px solid var(--border-hover);
        }
        .qty-btn {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 0.8rem;
            font-weight: 900;
            cursor: pointer;
            width: 14px; height: 14px;
            display: flex; align-items: center; justify-content: center;
        }
        .qty-num { font-weight: 900; font-size: 0.7rem; color: var(--text-main); min-width: 10px; text-align: center; }

        .checkout-section { 
            background: var(--bg-card); 
            border: 1px solid var(--border-color); 
            padding: 12px; 
            border-radius: 12px; 
            margin-top: 20px; 
        }
        .checkout-section h3 { color: var(--text-main); margin-bottom: 8px; font-size: 0.9rem; font-weight: 800; }
        .input-group { display: flex; gap: 6px; flex-wrap: wrap; }
        .input-group input { 
            flex: 1; 
            min-width: 150px; 
            padding: 8px 10px; 
            background: var(--input-bg); 
            border: 1px solid var(--border-color); 
            border-radius: 6px; 
            color: var(--text-main); 
            font-size: 0.8rem;
            outline: none;
        }
        .input-group input:focus { border-color: var(--accent); }

        .developer-footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.7rem;
            background: var(--bg-card);
            border-radius: 10px 10px 0 0;
        }
        .developer-footer .dev-name { color: var(--text-main); font-weight: 800; font-size: 0.75rem; }
        .developer-footer a { color: var(--accent); text-decoration: none; font-weight: 800; }

        .cart-bar { 
            position: fixed; 
            bottom: 10px; left: 12px; right: 12px; 
            max-width: 780px;
            margin: 0 auto;
            background: var(--cart-bg); 
            border: 2px solid var(--accent); 
            border-radius: 12px;
            padding: 8px 14px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 6px 25px rgba(249, 115, 22, 0.25); 
            z-index: 100; 
            transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cart-bar.show { transform: translateY(0); }
        
        .cart-info { display: flex; align-items: center; gap: 8px; cursor: pointer; flex-grow: 1; }
        .cart-icon-box { background: var(--accent); color: #fff; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .cart-details-text { display: flex; flex-direction: column; }
        .cart-title { font-size: 0.7rem; color: var(--text-muted); font-weight: 700; }
        .cart-total-val { color: var(--text-main); font-weight: 900; font-size: 1rem; }
        .cart-total-val span { color: var(--accent); }
        
        .send-btn { 
            background: #22c55e; 
            color: white; 
            border: none; 
            padding: 7px 14px; 
            border-radius: 8px; 
            font-weight: 900; 
            cursor: pointer; 
            font-size: 0.8rem;
            display: flex; align-items: center; gap: 4px;
        }

        .cart-modal {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 200;
            display: flex; align-items: flex-end;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .cart-modal.open { opacity: 1; pointer-events: auto; }
        .cart-modal-content {
            background: var(--bg-card);
            width: 100%; max-height: 75vh;
            border-radius: 16px 16px 0 0;
            padding: 14px;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        .cart-modal.open .cart-modal-content { transform: translateY(0); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid var(--border-color); padding-bottom: 6px; }
        .modal-header h2 { font-size: 0.9rem; font-weight: 800; }
        .close-modal { background: none; border: none; color: var(--text-muted); font-size: 1.1rem; cursor: pointer; }
        .modal-item { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid var(--border-color); }
        .modal-item-info h4 { font-size: 0.8rem; font-weight: 700; }
        .modal-item-info span { color: var(--accent); font-size: 0.75rem; font-weight: 800; }
        .clear-cart-btn { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 2px 8px; border-radius: 5px; font-size: 0.65rem; cursor: pointer; }

        .order-tracker-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 15, 25, 0.96);
            z-index: 500;
            display: flex; justify-content: center; align-items: center;
            padding: 12px; display: none;
        }
        .order-tracker-card {
            background: var(--bg-card); border: 2px solid #22c55e;
            border-radius: 16px; width: 100%; max-width: 400px; padding: 16px; text-align: center;
        }
        .tracker-icon { font-size: 2.2rem; margin-bottom: 4px; }
        .tracker-title { color: #fff; font-size: 1.1rem; font-weight: 900; }
        .tracker-subtitle { color: #4ade80; font-size: 0.75rem; font-weight: 700; margin-bottom: 8px; }
        .tracker-status-box { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80; padding: 6px; border-radius: 8px; font-weight: 900; font-size: 0.8rem; margin-bottom: 8px; }
        .tracker-details { text-align: right; background: rgba(0,0,0,0.4); padding: 8px; border-radius: 8px; margin-bottom: 8px; font-size: 0.75rem; max-height: 100px; overflow-y: auto; }
        .whatsapp-redirect-btn { background: #22c55e; color: #fff; border: none; width: 100%; padding: 10px; border-radius: 8px; font-weight: 900; font-size: 0.9rem; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px; margin-bottom: 6px; }
        .new-order-btn { background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); width: 100%; padding: 6px; border-radius: 8px; font-size: 0.7rem; cursor: pointer; }
    </style>
</head>
<body>

    <header>
        <button class="theme-toggle-btn" id="theme-toggle" onclick="toggleTheme()">
            <span id="theme-icon">🌙</span> <span id="theme-text">ليلي</span>
        </button>
        <div class="logo-badge">🔥 نكهات استثنائية وعصرية</div>
        <h1>لفة <span>Mazaj</span> 🌯</h1>
        <p>تصفح المنيو المفتوح بكل سهولة وراحة</p>
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
                <input type="text" id="search-input" class="search-input" placeholder="ابحث عن وجبتك..." oninput="filterProducts()">
            </div>
            <button class="mood-btn" onclick="suggestRandomProduct()">🎲 عشوائي</button>
        </div>

        <?php
        if (empty($products)) {
            echo '<p style="text-align:center; padding:40px; color:var(--text-muted);">لا توجد منتجات مضافة حالياً.</p>';
        } else {
            $categories = array_unique(array_column($products, 'category'));
            
            // شريط الدوائر الأفقية للأقسام (Categories Circles) مع جلب صورة من أول منتج في كل قسم
            echo '<div class="categories-circles-wrapper" id="categories-circles">';
            echo '  <div class="category-circle-item active" onclick="filterByCategory(\'all\', this)">';
            echo '      <img src="uploads/Ali.jpg" class="category-circle-img" onerror="this.src=\'uploads/default.jpg\'">';
            echo '      <span class="category-circle-name">الكل 🔥</span>';
            echo '  </div>';

            foreach ($categories as $cat) {
                // البحث عن أول صورة متوفرة لهذا القسم
                $cat_first_img = 'uploads/default.jpg';
                foreach ($products as $p) {
                    if (isset($p['category']) && $p['category'] === $cat) {
                        $img = !empty($p['image']) ? $p['image'] : (!empty($p['img']) ? $p['img'] : (!empty($p['photo']) ? $p['photo'] : ''));
                        if (!empty($img)) {
                            $cat_first_img = $img;
                            break;
                        }
                    }
                }

                echo '  <div class="category-circle-item" onclick="filterByCategory(\'' . htmlspecialchars($cat) . '\', this)">';
                echo '      <img src="' . htmlspecialchars($cat_first_img) . '" class="category-circle-img" onerror="this.src=\'uploads/default.jpg\'">';
                echo '      <span class="category-circle-name">' . htmlspecialchars($cat) . '</span>';
                echo '  </div>';
            }
            echo '</div>';

            echo '<div class="categories-container" id="categories-wrapper">';

            foreach ($categories as $cat) {
                $cat_products = array_filter($products, function($p) use ($cat) {
                    return isset($p['category']) && $p['category'] === $cat;
                });
                $cat_count = count($cat_products);

                echo '<div class="category-section-block" data-category-name="' . htmlspecialchars($cat) . '">';
                echo '  <div class="category-header-title">';
                echo '      <h2>🌯 ' . htmlspecialchars($cat) . '</h2>';
                echo '      <span class="category-badge-count">' . $cat_count . ' أصناف</span>';
                echo '  </div>';
                
                echo '  <div class="menu-grid">';
                
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
            <p style="margin-top:2px;">للتواصل: <a href="https://wa.me/96181058043" target="_blank">96181058043+</a></p>
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

        window.addEventListener('DOMContentLoaded', () => {
            let savedTheme = localStorage.getItem('mazaj_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeUI(savedTheme);

            if(localStorage.getItem('mazaj_name')) document.getElementById('cust-name').value = localStorage.getItem('mazaj_name');
            if(localStorage.getItem('mazaj_phone')) document.getElementById('cust-phone').value = localStorage.getItem('mazaj_phone');
            if(localStorage.getItem('mazaj_address')) document.getElementById('cust-address').value = localStorage.getItem('mazaj_address');
            
            checkLastOrderBanner();
        });

        function toggleTheme() {
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

        function saveCustomerData() {
            localStorage.setItem('mazaj_name', document.getElementById('cust-name').value);
            localStorage.setItem('mazaj_phone', document.getElementById('cust-phone').value);
            localStorage.setItem('mazaj_address', document.getElementById('cust-address').value);
        }

        function suggestRandomProduct() {
            let cards = document.querySelectorAll('.product-card');
            let visibleCards = Array.from(cards).filter(c => c.style.display !== 'none');
            if (visibleCards.length === 0) return alert('لا توجد منتجات متاحة!');
            let randomIndex = Math.floor(Math.random() * visibleCards.length);
            let selectedCard = visibleCards[randomIndex];

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
            let lastOrder = localStorage.getItem('mazaj_last_order');
            if (!lastOrder) return;
            try {
                cart = JSON.parse(lastOrder);
                for (let itemName in cart) updateUI(itemName, cart[itemName].hashId);
                updateCartBar();
                alert('تمت إضافة طلبك السابق للسلة! 🚀');
            } catch(e) {}
        }

        function filterByCategory(categoryName, element) {
            document.querySelectorAll('.category-circle-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            let catSections = document.querySelectorAll('.category-section-block');
            catSections.forEach(section => {
                let sectionCat = section.getAttribute('data-category-name');
                if (categoryName === 'all' || sectionCat === categoryName) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
            document.getElementById('search-input').value = '';
        }

        function filterProducts() {
            let query = document.getElementById('search-input').value.trim().toLowerCase();
            let catSections = document.querySelectorAll('.category-section-block');

            if (query !== '') {
                document.querySelectorAll('.category-circle-item').forEach(el => el.classList.remove('active'));
            }

            catSections.forEach(section => {
                let cards = section.querySelectorAll('.product-card');
                let hasMatch = false;

                cards.items = cards; // standard node list
                cards.forEach(card => {
                    let name = card.getAttribute('data-name');
                    let desc = card.getAttribute('data-desc');
                    let match = name.includes(query) || desc.includes(query);
                    card.style.display = match ? 'flex' : 'none';
                    if (match) hasMatch = true;
                });

                section.style.display = (query === '' || hasMatch) ? 'block' : 'none';
            });
        }

        function changeQty(name, price, change, hashId) {
            price = parseFloat(price);
            if (!cart[name]) {
                if (change > 0) cart[name] = { price: price, qty: 1, hashId: hashId };
            } else {
                cart[name].qty += change;
                if (cart[name].qty <= 0) delete cart[name];
            }
            updateUI(name, hashId);
            updateCartBar();
        }

        function clearCart() {
            if (confirm('تفريغ السلة؟')) { cart = {}; location.reload(); }
        }

        function updateUI(name, hashId) {
            let container = document.getElementById('btn-container-' + hashId);
            let priceElement = document.getElementById('price-' + hashId);
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
                        <div class="qty-control" style="padding: 1px 4px;">
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

        function toggleCartModal() { document.getElementById('cart-modal').classList.toggle('open'); }

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

                summaryHtml += `<p style="margin-top:4px; border-top:1px solid rgba(255,255,255,0.1)">الإجمالي: <span>$${subtotal.toFixed(2)}</span></p>`;

                document.getElementById('cart-bar').classList.remove('show');
                document.getElementById('cart-modal').classList.remove('open');
                
                let finalWaUrl = `https://wa.me/${adminPhone}?text=${encodeURIComponent(message)}`;
                document.getElementById('tracker-order-summary').innerHTML = summaryHtml;
                document.getElementById('tracker-wa-link').href = finalWaUrl;
                document.getElementById('order-tracker').style.display = 'flex';
            });
        }
    </script>
</body>
</html>
