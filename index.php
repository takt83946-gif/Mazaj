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
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لفة Mazaj | المنيو العصري</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-card: rgba(17, 24, 39, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f3f4f6;
            --text-muted: #d1d5db;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        
        body { 
            /* خلفية الموقع الأساسية باستخدام Ali.jpg بحيث تظهر كاملة وواضحة */
            background-color: #0b0f19;
            background-image: linear-gradient(rgba(11, 15, 25, 0.88), rgba(17, 24, 39, 0.88)), url('uploads/Ali.jpg');
            background-size: contain; /* تظهر الصورة كاملة */
            background-repeat: repeat; /* تتكرر بشكل متناسق لملء الخلفية */
            background-attachment: fixed;
            color: var(--text-main); 
            padding-bottom: 160px; 
            -webkit-tap-highlight-color: transparent;
            min-height: 100vh;
        }

        header { 
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            text-align: center; 
            padding: 35px 20px 25px 20px; 
            border-bottom: 1px solid var(--border-color);
            position: relative;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* تصميم شريط اللمبات المضيئة في الأعلى */
        .fairy-lights {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 12px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 10px;
            pointer-events: none;
        }

        .light-bulb {
            width: 8px;
            height: 10px;
            background-color: #fde047;
            border-radius: 50% 50% 40% 40%;
            box-shadow: 0 0 8px 3px #f59e0b, 0 0 15px 6px rgba(245, 158, 11, 0.6);
            animation: flashLights 1.5s infinite alternate ease-in-out;
        }

        .light-bulb:nth-child(even) {
            background-color: #fca5a5;
            box-shadow: 0 0 8px 3px #ef4444, 0 0 15px 6px rgba(239, 68, 68, 0.6);
            animation-delay: 0.5s;
        }

        .light-bulb:nth-child(3n) {
            background-color: #86efac;
            box-shadow: 0 0 8px 3px #22c55e, 0 0 15px 6px rgba(34, 197, 94, 0.6);
            animation-delay: 1s;
        }

        @keyframes flashLights {
            0% { opacity: 0.4; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1.2); box-shadow: 0 0 12px 5px currentColor, 0 0 25px 10px rgba(255, 255, 255, 0.8); }
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            padding: 5px 18px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 12px;
            margin-top: 5px;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        header h1 { font-size: 2.4rem; color: #fff; margin-bottom: 6px; font-weight: 900; letter-spacing: -0.5px; }
        header h1 span { color: var(--accent); }
        header p { color: var(--text-muted); font-size: 0.92rem; }

        .container { max-width: 900px; margin: 0 auto; padding: 0 16px; }

        .categories-nav {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            gap: 12px;
            overflow-x: auto;
            padding: 22px 0 10px 0;
            scrollbar-width: none;
        }
        .categories-nav::-webkit-scrollbar { display: none; }
        
        .cat-chip {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-muted);
            padding: 10px 18px;
            border-radius: 20px;
            white-space: nowrap;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cat-chip .cat-icon { font-size: 1.1rem; }
        .cat-chip .cat-count {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 800;
        }
        .cat-chip.active, .cat-chip:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
            transform: translateY(-1px);
        }

        .section-title { 
            color: #fff; 
            font-size: 1.3rem; 
            margin: 35px 0 18px 0; 
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--accent);
            border-radius: 4px;
        }

        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 20px; 
        }
        
        .card { 
            background: var(--bg-card); 
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color); 
            border-radius: 20px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between;
            transition: all 0.25s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        /* تم تصغير حاوية وصورة المنتج هنا لتصبح أصغر وأنيقة */
        .card-img-container {
            width: 100%;
            height: 110px;
            overflow: hidden;
            background: #0f172a;
            position: relative;
        }
        
        .card-img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.4s ease;
        }
        .card:hover .card-img { transform: scale(1.05); }
        
        .card-body { 
            padding: 16px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
        }
        .card h3 { font-size: 1.15rem; margin-bottom: 6px; color: #fff; font-weight: 800; }
        .card p { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 14px; line-height: 1.5; }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }
        
        .price { color: var(--accent); font-weight: 900; font-size: 1.2rem; }

        .action-btn { 
            background: var(--accent);
            color: #fff; 
            border: none; 
            padding: 8px 18px; 
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 800; 
            font-size: 0.85rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
        }
        .action-btn:hover { background: var(--accent-hover); }
        
        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(11, 15, 25, 0.8);
            border-radius: 12px;
            padding: 4px 12px;
            border: 1px solid var(--border-color);
        }
        .qty-btn {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: 900;
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-num { font-weight: 800; font-size: 1rem; color: #fff; min-width: 16px; text-align: center; }

        .checkout-section { 
            background: var(--bg-card); 
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color); 
            padding: 24px; 
            border-radius: 20px; 
            margin-top: 45px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .checkout-section h3 { color: #fff; margin-bottom: 15px; font-size: 1.2rem; display: flex; align-items: center; gap: 8px; font-weight: 800; }
        
        .input-group { display: flex; gap: 12px; flex-wrap: wrap; }
        .input-group input { 
            flex: 1; 
            min-width: 240px; 
            padding: 14px 16px; 
            background: rgba(11, 15, 25, 0.7); 
            border: 1px solid var(--border-color); 
            border-radius: 12px; 
            color: #fff; 
            outline: none; 
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .input-group input::placeholder { color: var(--text-muted); }
        .input-group input:focus { border-color: var(--accent); box-shadow: 0 0 10px rgba(249, 115, 22, 0.15); }

        .developer-footer {
            text-align: center;
            margin-top: 50px;
            padding: 25px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.85rem;
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border-radius: 20px 20px 0 0;
        }
        .developer-footer .dev-name { color: #fff; font-weight: 800; font-size: 1rem; }
        .developer-footer a { color: var(--accent); text-decoration: none; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; margin-top: 6px; }

        .cart-bar { 
            position: fixed; 
            bottom: 20px; 
            left: 20px; 
            right: 20px; 
            max-width: 860px;
            margin: 0 auto;
            background: rgba(17, 24, 39, 0.95); 
            backdrop-filter: blur(20px);
            border: 2px solid var(--accent); 
            border-radius: 20px;
            padding: 14px 22px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 10px 40px rgba(249, 115, 22, 0.35); 
            z-index: 100; 
            transform: translateY(150%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cart-bar.show { transform: translateY(0); }
        
        .cart-info { display: flex; align-items: center; gap: 14px; cursor: pointer; flex-grow: 1; }
        .cart-icon-box {
            background: var(--accent);
            color: #fff;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        
        .cart-details-text { display: flex; flex-direction: column; }
        .cart-title { font-size: 0.85rem; color: var(--text-muted); font-weight: 700; }
        .cart-total-val { color: #fff; font-weight: 900; font-size: 1.35rem; }
        .cart-total-val span { color: var(--accent); }
        
        .send-btn { 
            background: linear-gradient(135deg, #22c55e, #16a34a); 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 14px; 
            font-weight: 900; 
            cursor: pointer; 
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.45);
        }

        .cart-modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(6px);
            z-index: 200;
            display: flex;
            align-items: flex-end;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .cart-modal.open { opacity: 1; pointer-events: auto; }
        
        .cart-modal-content {
            background: #111827;
            width: 100%;
            max-height: 80vh;
            border-radius: 24px 24px 0 0;
            border-top: 1px solid var(--border-color);
            padding: 24px;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cart-modal.open .cart-modal-content { transform: translateY(0); }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }
        .modal-header h2 { font-size: 1.2rem; color: #fff; font-weight: 800; }
        .close-modal { background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; }
        
        .modal-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .modal-item-info h4 { font-size: 1rem; color: #fff; margin-bottom: 2px; font-weight: 700; }
        .modal-item-info span { color: var(--accent); font-weight: 800; font-size: 0.9rem; }
    </style>
</head>
<body>

    <header>
        <!-- شريط اللمبات المضيئة في أعلى الهيدر -->
        <div class="fairy-lights">
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
            <div class="light-bulb"></div><div class="light-bulb"></div><div class="light-bulb"></div>
        </div>
        
        <div class="logo-badge">🔥 نكهات استثنائية وعصرية</div>
        <h1>لفة <span>Mazaj</span> 🌯</h1>
        <p>تصفح المنيو الأنيق واطلب وجبتك المفضلة بكل سهولة</p>
    </header>

    <div class="container">
        <?php
        if (empty($products)) {
            echo '<p style="text-align:center; padding:70px; color:var(--text-muted); font-weight:700;">لا توجد منتجات مضافة حالياً. أضف منتجاتك عبر لوحة التحكم admin.php</p>';
        } else {
            $categories = array_unique(array_column($products, 'category'));
            $total_products_count = count($products);
            
            echo '<div class="categories-nav">';
            echo '<div class="cat-chip active" onclick="filterCategory(\'all\', this)">';
            echo '<span class="cat-icon">⚡</span><span>الكل</span>';
            echo '<span class="cat-count">' . $total_products_count . '</span>';
            echo '</div>';

            foreach ($categories as $cat) {
                $cat_count = count(array_filter($products, function($p) use ($cat) {
                    return isset($p['category']) && $p['category'] === $cat;
                }));
                echo '<div class="cat-chip" onclick="filterCategory(\'' . htmlspecialchars($cat) . '\', this)">';
                echo '<span class="cat-icon">🌯</span><span>' . htmlspecialchars($cat) . '</span>';
                echo '<span class="cat-count">' . $cat_count . '</span>';
                echo '</div>';
            }
            echo '</div>';

            foreach ($categories as $cat) {
                echo '<div class="category-section" data-category="' . htmlspecialchars($cat) . '">';
                echo '<h2 class="section-title">' . htmlspecialchars($cat) . '</h2>';
                echo '<div class="menu-grid">';
                foreach ($products as $p) {
                    if (isset($p['category']) && $p['category'] === $cat) {
                        $img_src = !empty($p['image']) ? htmlspecialchars($p['image']) : 'https://via.placeholder.com/300x190/1e293b/f97316?text=Mazaj';
                        $safe_name = htmlspecialchars($p['name'], ENT_QUOTES);
                        $hash_id = md5($p['name']);
                        $item_price = $p['price'] ?? 0;
                        
                        echo '<div class="card">';
                        echo '  <div class="card-img-container"><img src="' . $img_src . '" class="card-img" alt="' . $safe_name . '"></div>';
                        echo '  <div class="card-body">';
                        echo '      <div><h3>' . htmlspecialchars($p['name']) . '</h3><p>' . htmlspecialchars($p['desc_text'] ?? '') . '</p></div>';
                        echo '      <div class="card-footer">';
                        echo '          <span class="price" id="price-' . $hash_id . '">$' . number_format($item_price, 2) . '</span>';
                        echo '          <div id="btn-container-' . $hash_id . '"><button class="action-btn" onclick="changeQty(\'' . $safe_name . '\', ' . $item_price . ', 1, \'' . $hash_id . '\')">إضافة +</button></div>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                }
                echo '</div></div>';
            }
        }
        ?>

        <!-- قسم إدخال بيانات الزبون -->
        <div class="checkout-section">
            <h3><span>📍</span> تفاصيل الاستلام والتوصيل</h3>
            <div class="input-group">
                <input type="text" id="cust-name" placeholder="اسمك الكريم">
                <input type="tel" id="cust-phone" placeholder="رقم الهاتف (الواتساب)">
                <input type="text" id="cust-address" placeholder="عنوان التوصيل (المنطقة، الشارع، البناية)">
            </div>
        </div>

        <div class="developer-footer">
            <p>تم التصميم وبرمجة النظام خصيصاً بواسطة المبرمج:</p>
            <p class="dev-name">علي حسين ناصر الدين</p>
            <p style="margin-top: 6px;">للتواصل وحجز التطبيقات والبرمجيات: <a href="https://wa.me/96181058043" target="_blank">💬 96181058043+</a></p>
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
        <button class="send-btn" onclick="sendOrder()"><span>إرسال الطلب</span> 💬</button>
    </div>

    <div class="cart-modal" id="cart-modal" onclick="if(event.target === this) toggleCartModal()">
        <div class="cart-modal-content">
            <div class="modal-header">
                <h2>مراجعة سلة طلباتك 🛍️</h2>
                <button class="close-modal" onclick="toggleCartModal()">&times;</button>
            </div>
            <div id="modal-items-list"></div>
        </div>
    </div>

    <script>
        let cart = {};

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
            let totalCount = 0, totalPrice = 0, modalListHtml = '';
            for (let item in cart) {
                let qty = cart[item].qty, price = cart[item].price, itemTotal = price * qty;
                totalCount += qty;
                totalPrice += itemTotal;
                modalListHtml += `
                    <div class="modal-item">
                        <div class="modal-item-info"><h4>${item}</h4><span>$${itemTotal.toFixed(2)} (العدد: ${qty})</span></div>
                        <div class="qty-control" style="padding: 2px 8px;">
                            <button class="qty-btn" onclick="changeQty('${item.replace(/'/g, "\\'")}', ${price}, -1, '${cart[item].hashId}')">-</button>
                            <span class="qty-num" style="font-size:0.9rem;">${qty}</span>
                            <button class="qty-btn" onclick="changeQty('${item.replace(/'/g, "\\'")}', ${price}, 1, '${cart[item].hashId}')">+</button>
                        </div>
                    </div>`;
            }

            document.getElementById('cart-count').innerText = totalCount;
            document.getElementById('total-price').innerText = totalPrice.toFixed(2);
            document.getElementById('modal-items-list').innerHTML = modalListHtml || '<p style="text-align:center; color:#9ca3af; padding:20px;">السلة فارغة حالياً</p>';
            
            let cartBar = document.getElementById('cart-bar');
            if (totalCount > 0) cartBar.classList.add('show');
            else { cartBar.classList.remove('show'); document.getElementById('cart-modal').classList.remove('open'); }
        }

        function toggleCartModal() { document.getElementById('cart-modal').classList.toggle('open'); }

        function filterCategory(category, element) {
            document.querySelectorAll('.cat-chip').forEach(chip => chip.classList.remove('active'));
            element.classList.add('active');
            document.querySelectorAll('.category-section').forEach(sec => {
                sec.style.display = (category === 'all' || sec.getAttribute('data-category') === category) ? 'block' : 'none';
            });
        }

        function sendOrder() {
            let totalCount = 0;
            for (let item in cart) totalCount += cart[item].qty;
            if (totalCount === 0) return alert('السلة فارغة!');

            let name = document.getElementById('cust-name').value.trim();
            let phone = document.getElementById('cust-phone').value.trim();
            let address = document.getElementById('cust-address').value.trim();
            
            if (!name || !phone || !address) {
                alert('الرجاء إدخال الاسم، رقم الهاتف، وعنوان التوصيل!');
                return;
            }

            let orderData = {
                customer_name: name,
                customer_phone: phone,
                customer_address: address,
                items: cart,
                total: '$' + document.getElementById('total-price').innerText,
                status: 'قيد التحضير 🔥',
                time: new Date().toLocaleString()
            };

            fetch('save_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            }).then(response => response.json()).then(data => {
                let adminPhone = "96181058043"; 
                let message = "مرحباً *لفة Mazaj* 🌯، أريد طلب الآتي:\n\n";
                let index = 1, totalPrice = 0;

                for (let item in cart) {
                    let qty = cart[item].qty, itemTotal = cart[item].price * qty;
                    totalPrice += itemTotal;
                    message += `${index}. *${item}* (العدد: ${qty}) - $${itemTotal.toFixed(2)}\n`;
                    index++;
                }
                message += `\n*الإجمالي النهائي:* $${totalPrice.toFixed(2)}\n`;
                message += `\n👤 *الاسم:* ${name}`;
                message += `\n📞 *الهاتف:* ${phone}`;
                message += `\n📍 *العنوان:* ${address}`;

                window.open(`https://wa.me/${adminPhone}?text=${encodeURIComponent(message)}`, '_blank');
            }).catch(err => {
                console.error(err);
                window.open(`https://wa.me/96181058043?text=` + encodeURIComponent("طلب جديد من الموقع"), '_blank');
            });
        }
    </script>
</body>
</html>
