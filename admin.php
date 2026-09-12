<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

$products_file = 'products.json';
$orders_file = 'orders.json';

function get_data($file) {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function save_data($file, $data) {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$products = get_data($products_file);
$orders = get_data($orders_file);

// 1. إضافة أو تعديل منتج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_product']) || isset($_POST['update_product']))) {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $desc = $_POST['desc_text'] ?? '';
    $category = trim($_POST['category'] ?? '');
    
    $image_path = $_POST['old_image'] ?? '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $destination = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            if (!empty($_POST['old_image']) && file_exists($_POST['old_image'])) {
                @unlink($_POST['old_image']);
            }
            $image_path = $destination;
        }
    }

    $product_data = [
        'name' => $name,
        'price' => $price,
        'desc_text' => $desc,
        'category' => $category,
        'image' => $image_path
    ];

    if (isset($_POST['update_product']) && isset($_POST['edit_index'])) {
        $edit_index = $_POST['edit_index'];
        if (isset($products[$edit_index])) {
            $products[$edit_index] = $product_data;
        }
    } else {
        $products[] = $product_data;
    }

    save_data($products_file, $products);
    header("Location: admin.php");
    exit;
}

// 2. حذف منتج
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($products[$index])) {
        if (!empty($products[$index]['image']) && file_exists($products[$index]['image'])) {
            @unlink($products[$index]['image']);
        }
        unset($products[$index]);
        $products = array_values($products);
        save_data($products_file, $products);
    }
    header("Location: admin.php");
    exit;
}

// 3. تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_index = $_POST['order_index'] ?? null;
    $new_status = $_POST['new_status'] ?? '';
    if (isset($orders[$order_index])) {
        $orders[$order_index]['status'] = $new_status;
        save_data($orders_file, $orders);
    }
    header("Location: admin.php");
    exit;
}

// 4. مسح الطلبات
if (isset($_GET['clear_all_orders'])) {
    save_data($orders_file, []);
    header("Location: admin.php");
    exit;
}

// حساب المبيعات اليومية والشهرية
$daily_sales = 0;
$monthly_sales = 0;
$current_date = date('d/m/Y'); // تنسيق التاريخ المفترض في الطلبات (مثال: 09/09/2026 أو حسب تنسيقك)
$current_month = date('m/Y');   // الشهر والسنة

foreach ($orders as $order) {
    $order_time = isset($order['time']) ? $order['time'] : '';
    // استخراج التاريخ من حقل الوقت (بافتراض أن الوقت يحتوي على التاريخ مثل 09/09/2026 16:56:35)
    $raw_total = isset($order['total']) ? $order['total'] : '$0';
    $amount = floatval(preg_replace('/[^\d.]/', '', $raw_total));
    
    // مطابقة اليوم والشهر بناءً على نص التاريخ
    if (!empty($order_time)) {
        if (strpos($order_time, date('d/m/Y')) !== false) {
            $daily_sales += $amount;
        }
        if (strpos($order_time, date('/m/Y')) !== false || strpos($order_time, date('m/Y')) !== false) {
            $monthly_sales += $amount;
        }
    }
}

// وضع التعديل
$edit_mode = false;
$edit_data = [];
$edit_index = null;
if (isset($_GET['edit'])) {
    $edit_index = $_GET['edit'];
    if (isset($products[$edit_index])) {
        $edit_mode = true;
        $edit_data = $products[$edit_index];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | Mazaj Cafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #d97706;
            --bg-card: rgba(28, 20, 15, 0.85);
            --border-color: rgba(217, 119, 6, 0.2);
            --text-main: #f3f4f6;
            --text-muted: #d1d5db;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        body { 
            background: linear-gradient(rgba(15, 10, 5, 0.85), rgba(15, 10, 5, 0.85)), url('Ali.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main); 
            padding: 12px; 
            min-height: 100vh; 
        }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2 { color: #fef3c7; font-weight: 800; font-size: 1.25rem; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
        .card { background: var(--bg-card); backdrop-filter: blur(8px); border: 1px solid var(--border-color); border-radius: 16px; padding: 16px; margin-bottom: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.5); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-box { background: rgba(20, 14, 10, 0.9); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; text-align: center; }
        .stat-box h3 { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px; }
        .stat-box .amount { font-size: 1.4rem; font-weight: 900; color: var(--accent); }

        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 10px; background: rgba(20, 14, 10, 0.9); border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem; outline: none; }
        input[type="file"] { padding: 8px; cursor: pointer; }
        button, .btn { background: var(--accent); color: #fff; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-block; font-size: 0.88rem; transition: 0.2s; }
        button:hover { opacity: 0.9; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 0.85rem; min-width: 550px; }
        th, td { padding: 10px 8px; text-align: right; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        th { color: var(--accent); font-weight: 800; white-space: nowrap; }
        
        .wa-btn { background: #22c55e; color: #fff; padding: 6px 10px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .del-btn { background: #ef4444; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; }
        .edit-btn { background: #3b82f6; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; margin-left: 5px; }
        .clear-all-btn { background: transparent; border: 1px solid var(--accent); color: var(--accent); padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; }
        .clear-all-btn:hover { background: var(--accent); color: #fff; }
        .status-form { display: flex; gap: 4px; align-items: center; }
        .status-form select { margin-bottom: 0; padding: 5px; font-size: 0.78rem; min-width: 115px; }
        .status-form button { padding: 5px 10px; font-size: 0.78rem; white-space: nowrap; }
        .item-list { padding-right: 12px; font-size: 0.8rem; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom: 20px; font-size: 1.5rem; text-align: center; color: #fef3c7;">☕ إدارة المنيو والطلبات - Mazaj Cafe</h1>

        <!-- إحصائيات المبيعات اليومية والشهرية -->
        <div class="stats-grid">
            <div class="stat-box">
                <h3>📈 مبيعات اليوم</h3>
                <div class="amount">$<?= number_format($daily_sales, 2) ?></div>
            </div>
            <div class="stat-box">
                <h3>📊 مبيعات الشهر</h3>
                <div class="amount">$<?= number_format($monthly_sales, 2) ?></div>
            </div>
        </div>

        <div class="card">
            <div class="header-flex">
                <h2>📦 الطلبات الحالية</h2>
                <?php if (!empty($orders)): ?>
                    <a href="admin.php?clear_all_orders=1" class="btn clear-all-btn" onclick="return confirm('⚠️ هل أنت متأكد من مسح جميع الطلبات؟')">🗑️ مسح الكل</a>
                <?php endif; ?>
            </div>
            <?php if (empty($orders)): ?>
                <p style="color:var(--text-muted); font-size:0.9rem;">لا توجد طلبات حتى الآن.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>الوقت</th>
                                <th>الزبون والهاتف</th>
                                <th>العنوان</th>
                                <th>الأصناف</th>
                                <th>الإجمالي</th>
                                <th>الحالة</th>
                                <th>إشعار واتساب</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $index => $order): 
                                $c_name = isset($order['customer_name']) ? $order['customer_name'] : 'بدون اسم';
                                $c_phone = isset($order['customer_phone']) ? $order['customer_phone'] : '';
                                $c_address = isset($order['customer_address']) ? $order['customer_address'] : '';
                                $c_status = isset($order['status']) ? $order['status'] : 'قيد التحضير 🔥';
                                $order_time = isset($order['time']) ? $order['time'] : '';
                                $order_total = isset($order['total']) ? $order['total'] : '$0.00';
                                
                                $clean_phone = preg_replace('/[^0-9]/', '', $c_phone);
                                $wa_text = "مرحباً $c_name، طلبك من *Mazaj Cafe* أصبح: *$c_status*. شكراً لاختيارك لنا! ☕";
                            ?>
                            <tr>
                                <td><small style="color:var(--text-muted);"><?= htmlspecialchars($order_time) ?></small></td>
                                <td><strong><?= htmlspecialchars($c_name) ?></strong><br><small style="color:var(--accent);"><?= htmlspecialchars($c_phone) ?></small></td>
                                <td><?= htmlspecialchars($c_address) ?></td>
                                <td>
                                    <ul class="item-list">
                                        <?php if (isset($order['items']) && is_array($order['items'])): ?>
                                            <?php foreach ($order['items'] as $item_name => $item_data): 
                                                $qty = isset($item_data['qty']) ? $item_data['qty'] : 1;
                                            ?>
                                                <li>☕ <?= htmlspecialchars($item_name) ?> (<?= $qty ?>)</li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                                <td style="color:var(--accent); font-weight:900;"><?= htmlspecialchars($order_total) ?></td>
                                <td>
                                    <form action="admin.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_index" value="<?= $index ?>">
                                        <select name="new_status">
                                            <option value="قيد التحضير 🔥" <?= $c_status == 'قيد التحضير 🔥' ? 'selected' : '' ?>>قيد التحضير 🔥</option>
                                            <option value="في الطريق 🛵" <?= $c_status == 'في الطريق 🛵' ? 'selected' : '' ?>>في الطريق 🛵</option>
                                            <option value="تم التوصيل ✅" <?= $c_status == 'تم التوصيل ✅' ? 'selected' : '' ?>>تم التوصيل ✅</option>
                                        </select>
                                        <button type="submit" name="update_status">حفظ</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if (!empty($clean_phone)): ?>
                                        <a href="https://wa.me/<?= $clean_phone ?>?text=<?= urlencode($wa_text) ?>" target="_blank" class="wa-btn">💬 إرسال</a>
                                    <?php else: ?>
                                        <small style="color:#ef4444;">لا رقم</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" id="product-form-card">
            <h2><?= $edit_mode ? '✏️ تعديل المنتج: ' . htmlspecialchars($edit_data['name'] ?? '') : '✨ إضافة منتج جديد' ?></h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data" style="margin-top: 12px;">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="edit_index" value="<?= $edit_index ?>">
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>">
                <?php endif; ?>
                
                <input type="text" name="name" placeholder="اسم المنتج (مثال: كريب نوتيلا)" value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>
                <input type="text" name="category" placeholder="التصنيف / القسم" value="<?= htmlspecialchars($edit_data['category'] ?? '') ?>">
                <input type="number" step="0.01" name="price" placeholder="السعر ($)" value="<?= htmlspecialchars($edit_data['price'] ?? '') ?>" required>
                <textarea name="desc_text" placeholder="وصف المنتج..."><?= htmlspecialchars($edit_data['desc_text'] ?? '') ?></textarea>
                
                <?php if ($edit_mode && !empty($edit_data['image'])): ?>
                    <div style="margin-bottom: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        الصورة الحالية: <img src="<?= htmlspecialchars($edit_data['image']) ?>" style="width: 30px; height: 30px; object-fit: cover; vertical-align: middle; border-radius: 4px;">
                    </div>
                <?php endif; ?>
                
                <input type="file" name="product_image" accept="image/*">
                
                <?php if ($edit_mode): ?>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" name="update_product" style="flex: 1; background: #3b82f6; margin-top:8px;">💾 حفظ التعديلات</button>
                        <a href="admin.php" class="btn" style="background: #6b7280; text-align: center; margin-top:8px; text-decoration:none;">إلغاء</a>
                    </div>
                <?php else: ?>
                    <button type="submit" name="add_product" style="width:100%; margin-top:8px;">☕ حفظ وإضافة المنتج</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>📋 المنتجات الحالية في المنيو</h2>
            <?php if (empty($products)): ?>
                <p style="color:var(--text-muted); font-size:0.9rem; margin-top:10px;">لا توجد منتجات مضافة حالياً.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>الاسم والتصنيف</th>
                                <th>السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $index => $prod): 
                                $p_name = isset($prod['name']) ? $prod['name'] : '';
                                $p_cat = isset($prod['category']) ? $prod['category'] : 'عام';
                                $p_img = isset($prod['image']) ? $prod['image'] : '';
                                $raw_price = isset($prod['price']) ? $prod['price'] : 0;
                                $p_price = floatval(preg_replace('/[^\d.]/', '', $raw_price));
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p_img)): ?>
                                        <img src="<?= htmlspecialchars($p_img) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                    <?php else: ?>
                                        <span style="font-size:0.75rem; color:var(--text-muted);">بدون</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p_name) ?></strong><br>
                                    <small style="color:var(--accent);"><?= htmlspecialchars($p_cat) ?></small>
                                </td>
                                <td style="color:var(--accent); font-weight:800;">$<?= number_format($p_price, 2) ?></td>
                                <td>
                                    <a href="admin.php?edit=<?= $index ?>#product-form-card" class="btn edit-btn">تعديل</a>
                                    <a href="admin.php?delete=<?= $index ?>" class="btn del-btn" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
