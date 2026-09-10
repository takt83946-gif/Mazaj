<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$products_file = 'products.json';
$orders_file = 'orders.json';

// 1. معالجة إضافة منتج جديد مع رفع الصورة من الهاتف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $desc_text = $_POST['desc_text'] ?? '';
    
    $image_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = 'uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $destination = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            $image_path = $destination;
        }
    }

    $products = file_exists($products_file) ? json_decode(file_get_contents($products_file), true) : [];
    if (!is_array($products)) $products = [];

    $products[] = [
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'desc_text' => $desc_text,
        'image' => $image_path
    ];
    file_put_contents($products_file, json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

// 2. معالجة تعديل منتج موجود
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $index = $_POST['product_index'] ?? null;
    $products = file_exists($products_file) ? json_decode(file_get_contents($products_file), true) : [];
    
    if ($index !== null && isset($products[$index])) {
        $products[$index]['name'] = $_POST['name'] ?? $products[$index]['name'];
        $products[$index]['category'] = $_POST['category'] ?? $products[$index]['category'];
        $products[$index]['price'] = $_POST['price'] ?? $products[$index]['price'];
        $products[$index]['desc_text'] = $_POST['desc_text'] ?? $products[$index]['desc_text'];
        
        // رفع صورة جديدة في حال تم اختيار صورة
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['product_image']['name']);
            $upload_dir = 'uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $destination = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                // حذف الصورة القديمة إن وجدت وليست رابط خارجي
                if (!empty($products[$index]['image']) && file_exists($products[$index]['image'])) {
                    @unlink($products[$index]['image']);
                }
                $products[$index]['image'] = $destination;
            }
        }
        
        file_put_contents($products_file, json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header("Location: admin.php");
    exit;
}

// 3. حذف منتج محدد
if (isset($_GET['delete_product'])) {
    $index = $_GET['delete_product'];
    $products = file_exists($products_file) ? json_decode(file_get_contents($products_file), true) : [];
    if (isset($products[$index])) {
        if (!empty($products[$index]['image']) && file_exists($products[$index]['image'])) {
            @unlink($products[$index]['image']);
        }
        unset($products[$index]);
        file_put_contents($products_file, json_encode(array_values($products), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header("Location: admin.php");
    exit;
}

// 4. تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_index = $_POST['order_index'] ?? null;
    $new_status = $_POST['new_status'] ?? '';
    
    $orders = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];
    if (is_array($orders) && isset($orders[$order_index])) {
        $orders[$order_index]['status'] = $new_status;
        file_put_contents($orders_file, json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header("Location: admin.php");
    exit;
}

// 5. مسح جميع الطلبات
if (isset($_GET['clear_all_orders'])) {
    file_put_contents($orders_file, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

// قراءة البيانات من الملفات
$products = file_exists($products_file) ? json_decode(file_get_contents($products_file), true) : [];
$orders = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];

if (!is_array($products)) $products = [];
if (!is_array($orders)) $orders = [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Menu Hub - Mazaj Cafe</title>
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
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 10px; background: rgba(20, 14, 10, 0.9); border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem; outline: none; }
        input[type="file"] { padding: 8px; cursor: pointer; }
        button, .btn { background: var(--accent); color: #fff; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-block; font-size: 0.88rem; transition: 0.2s; }
        button:hover { opacity: 0.9; }
        
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 0.85rem; min-width: 550px; }
        th, td { padding: 10px 8px; text-align: right; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        th { color: var(--accent); font-weight: 800; white-space: nowrap; }
        
        .wa-btn { background: #22c55e; color: #fff; padding: 6px 10px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .del-btn { background: #ef4444; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; }
        .edit-btn { background: #3b82f6; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; margin-left: 4px; }
        .clear-all-btn { background: transparent; border: 1px solid var(--accent); color: var(--accent); padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; }
        .clear-all-btn:hover { background: var(--accent); color: #fff; }
        .status-form { display: flex; gap: 4px; align-items: center; }
        .status-form select { margin-bottom: 0; padding: 5px; font-size: 0.78rem; min-width: 115px; }
        .status-form button { padding: 5px 10px; font-size: 0.78rem; white-space: nowrap; }
        .item-list { padding-right: 12px; font-size: 0.8rem; color: var(--text-muted); }

        /* نافذة التعديل المنبثقة Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); backdrop-filter: blur(5px); justify-content: center; align-items: center; padding: 15px; }
        .modal-content { background: #1c140f; border: 1px solid var(--border-color); padding: 20px; border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.8); }
        .close-modal { background: #ef4444; float: left; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; color: #fff; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom: 20px; font-size: 1.5rem; text-align: center; color: #fef3c7;">☕ Coffee Menu Hub - Mazaj Cafe</h1>

        <!-- قسم الطلبات الواردة -->
        <div class="card">
            <div class="header-flex">
                <h2>📦 Active Orders (Today)</h2>
                <?php if (!empty($orders)): ?>
                    <a href="admin.php?clear_all_orders=1" class="btn clear-all-btn" onclick="return confirm('⚠️ هل أنت متأكد من مسح جميع الطلبات بالكامل؟')">🗑️ Clear All</a>
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
                                $c_name = !empty($order['customer_name']) ? $order['customer_name'] : 'بدون اسم';
                                $c_phone = !empty($order['customer_phone']) ? $order['customer_phone'] : '';
                                $c_address = !empty($order['customer_address']) ? $order['customer_address'] : '';
                                $c_status = !empty($order['status']) ? $order['status'] : 'قيد التحضير 🔥';
                                $c_time = !empty($order['time']) ? $order['time'] : '';
                                $c_total = !empty($order['total']) ? $order['total'] : '$0.00';
                                
                                $clean_phone = preg_replace('/[^0-9]/', '', $c_phone);
                                $wa_text = "مرحباً " . $c_name . "، نود إعلامك أن طلبك من *Mazaj Cafe* أصبح حالياً: *" . $c_status . "*. شكراً لاختيارك لنا! ☕";
                                $wa_link = "https://wa.me/" . $clean_phone . "?text=" . urlencode($wa_text);
                            ?>
                            <tr>
                                <td><small style="color:var(--text-muted);"><?= htmlspecialchars($c_time) ?></small></td>
                                <td>
                                    <strong><?= htmlspecialchars($c_name) ?></strong><br>
                                    <small style="color:var(--accent);"><?= htmlspecialchars($c_phone) ?></small>
                                </td>
                                <td><?= htmlspecialchars($c_address) ?></td>
                                <td>
                                    <ul class="item-list">
                                        <?php if (isset($order['items']) && is_array($order['items'])): ?>
                                            <?php foreach ($order['items'] as $item_name => $item_data): ?>
                                                <li>☕ <?= htmlspecialchars($item_name) ?> (<?= $item_data['qty'] ?? 1 ?>)</li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                                <td style="color:var(--accent); font-weight:900;"><?= htmlspecialchars($c_total) ?></td>
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
                                        <a href="<?= $wa_link ?>" target="_blank" class="wa-btn">💬 إرسال</a>
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

        <!-- قسم إضافة وجبة جديدة -->
        <div class="card">
            <h2>✨ Add New Menu Item</h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data" style="margin-top: 12px;">
                <input type="text" name="name" placeholder="Item Name (مثلاً: لفة زنجر خارقة)" required>
                <input type="text" name="category" placeholder="Category (مثلاً: كريب حلو، كريب مالح...)" required>
                <input type="number" step="0.01" name="price" placeholder="Price ($)" required>
                <textarea name="desc_text" placeholder="Description..."></textarea>
                
                <label style="display:block; margin-bottom:6px; font-size:0.85rem; color:var(--text-muted);">
                    📷 Choose Product Image:
                </label>
                <input type="file" name="product_image" accept="image/*">
                
                <button type="submit" name="add_product" style="width:100%; margin-top:8px;">☕ Add Item to Menu</button>
            </form>
        </div>

        <!-- قائمة المنتجات الحالية مع أزرار التعديل والحذف -->
        <div class="card">
            <h2>المنتجات الحالية في المنيو (تعديل أو حذف)</h2>
            <?php if (empty($products)): ?>
                <p style="color:var(--text-muted); font-size:0.9rem; margin-top:10px;">لا توجد منتجات حالياً.</p>
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
                            <?php foreach ($products as $idx => $prod): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($prod['image'])): ?>
                                        <img src="<?= htmlspecialchars($prod['image']) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                    <?php else: ?>
                                        <span style="font-size:0.75rem;">بدون</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($prod['name'] ?? '') ?></strong><br>
                                    <small style="color:var(--text-muted);"><?= htmlspecialchars($prod['category'] ?? '') ?></small>
                                </td>
                                <td style="color:var(--accent); font-weight:800;">$<?= number_format($prod['price'] ?? 0, 2) ?></td>
                                <td>
                                    <button type="button" class="btn edit-btn" onclick='openEditModal(<?= $idx ?>, <?= json_encode($prod['name'] ?? "") ?>, <?= json_encode($prod['category'] ?? "") ?>, <?= $prod['price'] ?? 0 ?>, <?= json_encode($prod['desc_text'] ?? "") ?>)'>تعديل</button>
                                    <a href="admin.php?delete_product=<?= $idx ?>" class="btn del-btn" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- نافذة تعديل المنتج المنبثقة -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-modal" onclick="closeEditModal()">إغلاق X</button>
            <h2 style="margin-bottom: 15px; color: #fef3c7; clear: both;">✏️ تعديل المنتج</h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_index" id="edit_index">
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">اسم المنتج:</label>
                <input type="text" name="name" id="edit_name" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">التصنيف:</label>
                <input type="text" name="category" id="edit_category" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">السعر ($):</label>
                <input type="number" step="0.01" name="price" id="edit_price" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">الوصف:</label>
                <textarea name="desc_text" id="edit_desc"></textarea>
                
                <label style="display:block; margin-bottom:6px; font-size:0.85rem; color:var(--text-muted);">
                    📷 استبدال الصورة (اختياري):
                </label>
                <input type="file" name="product_image" accept="image/*">
                
                <button type="submit" name="edit_product" style="width:100%; margin-top:8px; background: #3b82f6;">حفظ التعديلات</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(index, name, category, price, desc) {
            document.getElementById('edit_index').value = index;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_desc').value = desc;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
