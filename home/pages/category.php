<?php
// Get category and subcategory from URL
$category = $_GET['category'] ?? '';
$subcategory = $_GET['subcategory'] ?? '';
$sort = $_GET['sort'] ?? 'default';
$page = $_GET['page_num'] ?? 1;
// Ensure page is always at least 1
$page = max(1, (int)$page);
$limit = 24; // Number of books per page
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($category)) {
    $where_conditions[] = "category = ?";
    $params[] = $category;
}

if (!empty($subcategory)) {
    $where_conditions[] = "subcategory = ?";
    $params[] = $subcategory;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Build ORDER BY clause
$order_clause = "ORDER BY ";
switch ($sort) {
    case 'price_asc':
        $order_clause .= "price ASC";
        break;
    case 'price_desc':
        $order_clause .= "price DESC";
        break;
    case 'name_asc':
        $order_clause .= "title ASC";
        break;
    case 'name_desc':
        $order_clause .= "title DESC";
        break;
    case 'newest':
        $order_clause .= "id DESC";
        break;
    case 'bestseller':
        $order_clause .= "sold DESC";
        break;
    default:
        $order_clause .= "id DESC";
}

// Count total books
$count_sql = "SELECT COUNT(*) as total FROM books $where_clause";
$count_stmt = mysqli_prepare($conn, $count_sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, str_repeat('s', count($params)), ...$params);
}
mysqli_stmt_execute($count_stmt);
$total_books = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_books / $limit);

// Get books with pagination
$sql = "SELECT * FROM books $where_clause $order_clause LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
$all_params = array_merge($params, [$limit, $offset]);
$types = str_repeat('s', count($params)) . 'ii';
mysqli_stmt_bind_param($stmt, $types, ...$all_params);
mysqli_stmt_execute($stmt);
$books = mysqli_stmt_get_result($stmt);

// Get all categories for sidebar
$categories_sql = "SELECT DISTINCT category FROM books WHERE category != ''";
$categories_result = mysqli_query($conn, $categories_sql);

// Get all subcategories for current category
$subcategories = [];
if (!empty($category)) {
    $sub_sql = "SELECT DISTINCT subcategory FROM books WHERE category = ? AND subcategory != ''";
    $sub_stmt = mysqli_prepare($conn, $sub_sql);
    mysqli_stmt_bind_param($sub_stmt, 's', $category);
    mysqli_stmt_execute($sub_stmt);
    $subcategories_result = mysqli_stmt_get_result($sub_stmt);
    while ($row = mysqli_fetch_assoc($subcategories_result)) {
        $subcategories[] = $row['subcategory'];
    }
}

function renderBookCard($book) {
    $html = '<div class="book-card" data-book-id="' . $book['id'] . '">';
    $html .= '  <a href="index.php?page=bookDetail&id=' . $book['id'] . '" class="book-link">';
    $html .= '    <div class="book-image-container">';
    if (!empty($book['label'])) {
        $html .= '      <span class="book-label">' . $book['label'] . '</span>';
    }
    if (!empty($book['discount'])) {
        $html .= '      <span class="book-discount">' . $book['discount'] . '</span>';
    }
    $html .= '      <img src="' . $book['image'] . '" alt="' . htmlspecialchars($book['title']) . '" class="book-image">';
    $html .= '      <div class="book-overlay">';
    $html .= '        <button class="quick-view-btn" onclick="quickView(' . $book['id'] . ')">Xem nhanh</button>';
    $html .= '      </div>';
    $html .= '    </div>';
    $html .= '    <div class="book-info">';
    $html .= '      <h3 class="book-title">' . htmlspecialchars($book['title']) . '</h3>';
    $html .= '      <p class="book-author">' . htmlspecialchars($book['author']) . '</p>';
    $html .= '      <div class="book-rating">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<i class="fas fa-star' . ($i <= ($book['reviews'] ?? 0) ? '' : ' star-empty') . '"></i>';
    }
    $html .= '      </div>';
    $html .= '      <div class="book-price">';
    $html .= '        <span class="current-price">' . number_format($book['price'], 0, ",", ".") . 'đ</span>';
    if (!empty($book['old_price']) && $book['old_price'] > $book['price']) {
        $html .= '        <span class="old-price">' . number_format($book['old_price'], 0, ",", ".") . 'đ</span>';
    }
    $html .= '      </div>';
    $html .= '      <div class="book-sold">Đã bán ' . $book['sold'] . '</div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <button class="add-to-cart-btn" onclick="addToCart(' . $book['id'] . ')">';
    $html .= '    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
    $html .= '  </button>';
    $html .= '</div>';
    return $html;
}
?>

<div class="category-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <?php if (!empty($category)): ?>
            <span> / </span>
            <a href="index.php?page=category&category=<?= urlencode($category) ?>"><?= htmlspecialchars($category) ?></a>
        <?php endif; ?>
        <?php if (!empty($subcategory)): ?>
            <span> / </span>
            <span><?= htmlspecialchars($subcategory) ?></span>
        <?php endif; ?>
    </div>

    <div class="category-container">
        <!-- Sidebar Filters -->
        <div class="sidebar">
            <div class="filter-section">
                <h3>Danh mục sách</h3>
                <div class="category-list">
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <div class="category-item <?= $category === $cat['category'] ? 'active' : '' ?>">
                            <a href="index.php?page=category&category=<?= urlencode($cat['category']) ?>">
                                <?= htmlspecialchars($cat['category']) ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <?php if (!empty($subcategories)): ?>
            <div class="filter-section">
                <h3>Thể loại</h3>
                <div class="subcategory-list">
                    <?php foreach ($subcategories as $subcat): ?>
                        <div class="subcategory-item <?= $subcategory === $subcat ? 'active' : '' ?>">
                            <a href="index.php?page=category&category=<?= urlencode($category) ?>&subcategory=<?= urlencode($subcat) ?>">
                                <?= htmlspecialchars($subcat) ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="filter-section">
                <h3>Khoảng giá</h3>
                <div class="price-filter">
                    <div class="price-range">
                        <input type="range" id="priceMin" min="0" max="1000000" value="0" step="10000">
                        <input type="range" id="priceMax" min="0" max="1000000" value="1000000" step="10000">
                    </div>
                    <div class="price-inputs">
                        <input type="text" id="minPrice" placeholder="Từ" value="0">
                        <span>-</span>
                        <input type="text" id="maxPrice" placeholder="Đến" value="1000000">
                    </div>
                    <button class="apply-price-filter">Áp dụng</button>
                </div>
            </div>

            <div class="filter-section">
                <h3>Nhà cung cấp</h3>
                <div class="supplier-filter">
                    <label><input type="checkbox" value="Nhã Nam"> Nhã Nam</label>
                    <label><input type="checkbox" value="Kim Đồng"> Kim Đồng</label>
                    <label><input type="checkbox" value="Đinh Tị"> Đinh Tị</label>
                    <label><input type="checkbox" value="Alpha Books"> Alpha Books</label>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Category Header -->
            <div class="category-header">
                <h1><?= !empty($category) ? htmlspecialchars($category) : 'Tất cả sách' ?></h1>
                <p>Tìm thấy <?= $total_books ?> sản phẩm</p>
            </div>

            <!-- Sort and View Options -->
            <div class="sort-bar">
                <div class="sort-options">
                    <label>Sắp xếp theo:</label>
                    <select id="sortSelect" onchange="changeSortOrder()">
                        <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Mặc định</option>
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="bestseller" <?= $sort === 'bestseller' ? 'selected' : '' ?>>Bán chạy</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </div>

                <div class="view-options">
                    <button class="view-btn active" data-view="grid" title="Lưới">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="Danh sách">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="books-grid" id="booksContainer">
                <?php if (mysqli_num_rows($books) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($books)): ?>
                        <?= renderBookCard($book) ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-books">
                        <i class="fas fa-book"></i>
                        <h3>Không tìm thấy sách nào</h3>
                        <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=category&category=<?= urlencode($category) ?>&subcategory=<?= urlencode($subcategory) ?>&sort=<?= $sort ?>&page_num=<?= $page - 1 ?>" class="page-btn">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=category&category=<?= urlencode($category) ?>&subcategory=<?= urlencode($subcategory) ?>&sort=<?= $sort ?>&page_num=<?= $i ?>" 
                       class="page-btn <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=category&category=<?= urlencode($category) ?>&subcategory=<?= urlencode($subcategory) ?>&sort=<?= $sort ?>&page_num=<?= $page + 1 ?>" class="page-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="quickViewContent">
            <!-- Content loaded by AJAX -->
        </div>
    </div>
</div>

<script>
function changeSortOrder() {
    const sortValue = document.getElementById('sortSelect').value;
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortValue);
    urlParams.set('page_num', '1'); // Reset to first page
    window.location.search = urlParams.toString();
}

function addToCart(bookId) {
    $.ajax({
        url: 'pages/add_to_cart.php',
        method: 'POST',
        data: { book_id: bookId, quantity: 1 },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update cart count
                $('#cart-count').text(response.cart_count);
                
                // Show success message
                showNotification('Đã thêm sách vào giỏ hàng!', 'success');
            } else {
                showNotification(response.message || 'Có lỗi xảy ra!', 'error');
            }
        },
        error: function() {
            showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
        }
    });
}

function quickView(bookId) {
    $.ajax({
        url: 'pages/quick_view.php',
        method: 'GET',
        data: { id: bookId },
        success: function(data) {
            $('#quickViewContent').html(data);
            $('#quickViewModal').fadeIn();
        },
        error: function() {
            showNotification('Không thể tải thông tin sách!', 'error');
        }
    });
}

function showNotification(message, type) {
    const notification = $(`
        <div class="notification ${type}">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// View toggle
$(document).ready(function() {
    $('.view-btn').click(function() {
        $('.view-btn').removeClass('active');
        $(this).addClass('active');
        
        const view = $(this).data('view');
        const booksContainer = $('#booksContainer');
        
        if (view === 'list') {
            booksContainer.addClass('list-view');
        } else {
            booksContainer.removeClass('list-view');
        }
    });
    
    // Price filter
    $('.apply-price-filter').click(function() {
        const minPrice = $('#minPrice').val();
        const maxPrice = $('#maxPrice').val();
        // Apply price filter logic here
        console.log('Price filter:', minPrice, maxPrice);
    });
    
    // Modal close
    $('.close, #quickViewModal').click(function(e) {
        if (e.target === this) {
            $('#quickViewModal').fadeOut();
        }
    });
});
</script> 