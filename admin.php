<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$products_file = 'products.json';
$orders_file = 'orders.json';

function get_products_data($file) {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function save_products_data($file, $data) {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$products_by_cat = get_products_data($products_file);
$orders = file_exists($orders_file) ? json_decode(file_get_contents($orders_file), true) : [];
if (!is_array($orders)) $orders = [];

// 1. إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $category = trim($_POST['category'] ?? 'عام');
    $name = trim($_POST['name'] ?? '');
    $price = floatval(preg_replace('/[^\d.]/', '', $_POST['price'] ?? 0));
    $desc_text = $_POST['desc_text'] ?? '';
    
    $image_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $destination = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            $image_path = $destination;
        }
    }

    if (!isset($products_by_cat[$category])) {
        $products_by_cat[$category] = [];
    }

    $products_by_cat[$category][] = [
        'name' => $name,
        'price' => $price,
        'desc_text' => $desc_text,
        'image' => $image_path
    ];

    save_products_data($products_file, $products_by_cat);
    header("Location: admin.php");
    exit;
}

// 2. تعديل منتج موجود
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $old_cat = $_POST['old_category'] ?? '';
    $index = $_POST['product_index'] ?? null;
    
    $new_cat = trim($_POST['category'] ?? 'عام');
    $name = trim($_POST['name'] ?? '');
    $price = floatval(preg_replace('/[^\d.]/', '', $_POST['price'] ?? 0));
    $desc_text = $_POST['desc_text'] ?? '';

    if ($old_cat !== '' && $index !== null && isset($products_by_cat[$old_cat][$index])) {
        $product = $products_by_cat[$old_cat][$index];
        $product['name'] = $name;
        $product['price'] = $price;
        $product['desc_text'] = $desc_text;

        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['product_image']['name']);
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $destination = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                if (!empty($product['image']) && file_exists($product['image'])) {
                    @unlink($product['image']);
                }
                $product['image'] = $destination;
            }
        }

        unset($products_by_cat[$old_cat][$index]);
        $products_by_cat[$old_cat] = array_values($products_by_cat[$old_cat]);
        if (empty($products_by_cat[$old_cat])) {
            unset($products_by_cat[$old_cat]);
        }

        if (!isset($products_by_cat[$new_cat])) {
            $products_by_cat[$new_cat] = [];
        }
        $products_by_cat[$new_cat][] = $product;

        save_products_data($products_file, $products_by_cat);
    }
    header("Location: admin.php");
    exit;
}

// 3. حذف منتج
if (isset($_GET['delete_cat']) && isset($_GET['delete_idx'])) {
    $del_cat = $_GET['delete_cat'];
    $del_idx = $_GET['delete_idx'];

    if (isset($products_by_cat[$del_cat][$del_idx])) {
        if (!empty($products_by_cat[$del_cat][$del_idx]['image']) && file_exists($products_by_cat[$del_cat][$del_idx]['image'])) {
            @unlink($products_by_cat[$del_cat][$del_idx]['image']);
        }
        unset($products_by_cat[$del_cat][$del_idx]);
        $products_by_cat[$del_cat] = array_values($products_by_cat[$del_cat]);
        if (empty($products_by_cat[$del_cat])) {
            unset($products_by_cat[$del_cat]);
        }
        save_products_data($products_file, $products_by_cat);
    }
    header("Location: admin.php");
    exit;
}

// 4. تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_index = $_POST['order_index'] ?? null;
    $new_status = $_POST['new_status'] ?? '';
    if (is_array($orders) && isset($orders[$order_index])) {
        $orders[$order_index]['status'] = $new_status;
        file_put_contents($orders_file, json_encode(array_values($orders), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header("Location: admin.php");
    exit;
}

// 5. مسح الطلبات
if (isset($_GET['clear_all_orders'])) {
    file_put_contents($orders_file, json_encode([], JSON_UNESCAPED_UNICODE));
    header("Location: admin.php");
    exit;
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
        .edit-btn { background: #3b82f6; padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; margin-left: 4px; }
        .clear-all-btn { background: transparent; border: 1px solid var(--accent); color: var(--accent); padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; }
        .clear-all-btn:hover { background: var(--accent); color: #fff; }
        .status-form { display: flex; gap: 4px; align-items: center; }
        .status-form select { margin-bottom: 0; padding: 5px; font-size: 0.78rem; min-width: 115px; }
        .status-form button { padding: 5px 10px; font-size: 0.78rem; white-space: nowrap; }
        .item-list { padding-right: 12px; font-size: 0.8rem; color: var(--text-muted); }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); backdrop-filter: blur(5px); justify-content: center; align-items: center; padding: 15px; }
        .modal-content { background: #1c140f; border: 1px solid var(--border-color); padding: 20px; border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .close-modal { background: #ef4444; float: left; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; color: #fff; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom: 20px; font-size: 1.5rem; text-align: center; color: #fef3c7;">☕ إدارة المنيو والطلبات - Mazaj Cafe</h1>

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
                                $c_name = $order['customer_name'] ?? 'بدون اسم';
                                $c_phone = $order['customer_phone'] ?? '';
                                $c_address = $order['customer_address'] ?? '';
                                $c_status = $order['status'] ?? 'قيد التحضير 🔥';
                                $clean_phone = preg_replace('/[^0-9]/', '', $c_phone);
                                $wa_text = "مرحباً $c_name، طلبك من *Mazaj Cafe* أصبح: *$c_status*. شكراً لاختيارك لنا! ☕";
                            ?>
                            <tr>
                                <td><small style="color:var(--text-muted);"><?= htmlspecialchars($order['time'] ?? '') ?></small></td>
                                <td><strong><?= htmlspecialchars($c_name) ?></strong><br><small style="color:var(--accent);"><?= htmlspecialchars($c_phone) ?></small></td>
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
                                <td style="color:var(--accent); font-weight:900;"><?= htmlspecialchars($order['total'] ?? '$0.00') ?></td>
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

        <div class="card">
            <h2>✨ إضافة منتج جديد ضمن الأقسام</h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data" style="margin-top: 12px;">
                <input type="text" name="category" placeholder="اسم القسم (مثال: الكريب، العصائر)" required>
                <input type="text" name="name" placeholder="اسم المنتج (مثال: كريب نوتيلا)" required>
                <input type="number" step="0.01" name="price" placeholder="السعر ($)" required>
                <textarea name="desc_text" placeholder="وصف المنتج..."></textarea>
                <input type="file" name="product_image" accept="image/*">
                <button type="submit" name="add_product" style="width:100%; margin-top:8px;">☕ حفظ وإضافة المنتج</button>
            </form>
        </div>

        <div class="card">
            <h2>📋 المنتجات الحالية مقسمة حسب الفئات</h2>
            <?php if (empty($products_by_cat)): ?>
                <p style="color:var(--text-muted); font-size:0.9rem; margin-top:10px;">لا توجد منتجات مضافة حالياً.</p>
            <?php else: ?>
                <?php foreach ($products_by_cat as $category_name => $items): ?>
                    <h3 style="color:var(--accent); margin-top:15px; border-bottom:1px solid var(--border-color); padding-bottom:5px;">📁 <?= htmlspecialchars($category_name) ?></h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>السعر</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $idx => $prod): 
                                    $raw_price = $prod['price'] ?? 0;
                                    $safe_price = floatval(preg_replace('/[^\d.]/', '', $raw_price));
                                ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($prod['image'])): ?>
                                            <img src="<?= htmlspecialchars($prod['image']) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                        <?php else: ?>
                                            <span style="font-size:0.75rem;">بدون</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($prod['name'] ?? '') ?></strong></td>
                                    <td style="color:var(--accent); font-weight:800;">$<?= number_format($safe_price, 2) ?></td>
                                    <td>
                                        <button type="button" class="btn edit-btn" onclick='openEditModal(<?= json_encode($category_name) ?>, <?= $idx ?>, <?= json_encode($prod['name'] ?? "") ?>, <?= json_encode($category_name) ?>, <?= $safe_price ?>, <?= json_encode($prod['desc_text'] ?? "") ?>)'>تعديل</button>
                                        <a href="admin.php?delete_cat=<?= urlencode($category_name) ?>&delete_idx=<?= $idx ?>" class="btn del-btn" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-modal" onclick="closeEditModal()">إغلاق X</button>
            <h2 style="margin-bottom: 15px; color: #fef3c7; clear: both;">✏️ تعديل المنتج</h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_category" id="edit_old_category">
                <input type="hidden" name="product_index" id="edit_index">
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">اسم القسم:</label>
                <input type="text" name="category" id="edit_category" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">اسم المنتج:</label>
                <input type="text" name="name" id="edit_name" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">السعر ($):</label>
                <input type="number" step="0.01" name="price" id="edit_price" required>
                
                <label style="font-size: 0.8rem; color: var(--text-muted);">الوصف:</label>
                <textarea name="desc_text" id="edit_desc"></textarea>
                
                <label style="display:block; margin-bottom:6px; font-size:0.85rem; color:var(--text-muted);">استبدال الصورة:</label>
                <input type="file" name="product_image" accept="image/*">
                
                <button type="submit" name="edit_product" style="width:100%; margin-top:8px; background: #3b82f6;">حفظ التعديلات</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(oldCat, index, name, category, price, desc) {
            document.getElementById('edit_old_category').value = oldCat;
            document.getElementById('edit_index').value = index;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_desc').value = desc;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('editModal');
            if (event.target === modal) modal.style.display = 'none';
        }
    </script>
</body>
</html>
