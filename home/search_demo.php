<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Search Autocomplete</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/search-autocomplete.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .demo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .demo-search {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }
        .search-bar {
            display: flex;
            width: 100%;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .search-bar input {
            flex: 1;
            padding: 12px 16px;
            border: none;
            outline: none;
            font-size: 16px;
        }
        .search-bar button {
            padding: 12px 20px;
            background: #d70018;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .search-bar button:hover {
            background: #c40016;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .demo-note {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <h1>Demo Search Autocomplete</h1>
        <p class="demo-note">Nhập từ khóa để xem gợi ý tự động (VD: "harry", "potter", "hoàng"...)</p>
        
        <div class="demo-search">
            <form class="search-bar" id="searchForm" method="GET" action="pages/search.php">
                <input type="search" name="q" placeholder="Tìm kiếm sách..." aria-label="Tìm kiếm sách" maxlength="128">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <div style="margin-top: 30px; text-align: center; color: #666;">
            <h3>Các tính năng:</h3>
            <ul style="text-align: left; display: inline-block;">
                <li>🔍 Gợi ý tự động khi gõ (từ 2 ký tự trở lên)</li>
                <li>⚡ Debounce để tối ưu hiệu suất</li>
                <li>🎯 Highlight từ khóa tìm kiếm</li>
                <li>⌨️ Điều hướng bằng phím mũi tên và Enter</li>
                <li>📱 Responsive design</li>
                <li>🖱️ Click để chọn hoặc Enter để tìm kiếm</li>
            </ul>
        </div>
    </div>

    <script src="assets/js/search-autocomplete.js"></script>
</body>
</html> 