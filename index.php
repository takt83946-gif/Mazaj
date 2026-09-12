<?php
$products_file = 'products.json';
$products = [];

if (file_exists($products_file)) {
    $json_data = file_get_contents($products_file);
    $decoded = json_decode($json_data, true);
    if (is_array($decoded)) {
        $products = $decoded;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mazaj Cafe | المنيو</title>
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
            padding: 15px; 
            min-height: 100vh; 
        }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #fef3c7; font-weight: 900; font-size: 1.5rem; text-align: center; margin-bottom: 20px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px; }
        .product-card { background: var(--bg-card); backdrop-filter: blur(8px); border: 1px solid var(--border-color); border-radius: 16px; padding: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.5); display: flex; flex-direction: column; justify-content: space-between; }
        .product-card img { width: 100%; height: 160px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
        .product-card h3 { color: #fef3c7; font-size: 1.1rem; margin-bottom: 5px; }
        .product-card .category-tag { color: var(--accent); font-size: 0.8rem; font-weight: bold; margin-bottom: 8px; }
        .product-card p { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 10px; flex-grow: 1; }
        .product-card .price { color: var(--accent); font-weight: 900; font-size: 1.1rem; }
        .no-products { text-align: center; color: var(--text-muted); font-size: 1rem; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>☕ قائمة منيو Mazaj Cafe</h1>

        <?php if (empty($products)): ?>
            <div class="no-products">عذراً، لا توجد منتجات مضافة حالياً في المنيو.</div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $prod): 
                    $p_price = floatval(preg_replace('/[^\d.]/', '', $prod['price'] ?? 0));
                ?>
                    <div class="product-card">
                        <?php if (!empty($prod['image'])): ?>
                            <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name'] ?? '') ?>">
                        <?php endif; ?>
                        <div>
                            <h3><?= htmlspecialchars($prod['name'] ?? '') ?></h3>
                            <div class="category-tag"><?= htmlspecialchars($prod['category'] ?? 'عام') ?></div>
                            <p><<?= htmlspecialchars($prod['desc_text'] ?? '') ?></p>
                        </div>
                        <div class="price">$<?= number_format($p_price, 2) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
