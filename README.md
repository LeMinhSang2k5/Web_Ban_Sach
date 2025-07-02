# Website Bán Sách - Tài Liệu Chức Năng

## Tổng Quan

Website Bán Sách là một hệ thống thương mại điện tử chuyên về bán sách online, được phát triển bằng PHP và MySQL. Website cung cấp đầy đủ các chức năng cho cả người dùng cuối và quản trị viên.

## Kiến Trúc Hệ Thống

### Cấu Trúc Thư Mục

```
Web_Ban_Sach/home/
├── pages/          # Các trang chức năng chính
├── admin/          # Hệ thống quản trị
├── assets/         # Tài nguyên (CSS, JS, Images)
├── includes/       # File include chung (header, footer)
├── api/           # API endpoints
├── SQL/           # Scripts database
└── config.php     # File cấu hình chính
```

### Database

- **Host**: localhost
- **Database**: mydb
- **Các bảng chính**: users, books, cart, cart_items

## Chức Năng Dành Cho Người Dùng

### 1. Trang Chủ (`pages/home.php`)

- **Hiển thị sách bán chạy**: Top 4 sách có label "Bán chạy"
- **Phân loại theo danh mục**:
  - Văn học
  - Thiếu nhi
  - Khoa học
  - Kinh tế
  - Sách ngoại ngữ
- **Phân loại theo nhà cung cấp**:
  - Nhà Xuất Bản Kim Đồng
  - Đình Tị
  - AZ Việt Nam
  - NXB Văn Học
- **Slider banner quảng cáo**
- **Tab switching** để chuyển đổi giữa các danh mục
- **Product cards** hiển thị thông tin sách (tên, giá, discount, số lượng đã bán)

### 2. Danh Mục Sách Chuyên Biệt

#### Sách Thiếu Nhi (`pages/chilldrenBook.php`)

- Hiển thị sách dành cho trẻ em
- Giao diện tối ưu cho danh mục này
- Banner và filter riêng biệt

#### Văn Học (`pages/literatureBook.php`)

- Chuyên mục sách văn học
- Hiển thị theo thể loại văn học khác nhau
- Banner và layout đặc thù

#### Khoa Học (`pages/scienceBook.php`)

- Sách khoa học, công nghệ
- Phân loại theo lĩnh vực khoa học
- Giao diện chuyên nghiệp

### 3. Chi Tiết Sách (`pages/bookDetail.php`)

- **Thông tin chi tiết**: Tên sách, tác giả, NXB, giá, mô tả
- **Hình ảnh sản phẩm** chất lượng cao
- **Thông tin giảm giá** và giá gốc
- **Số lượng đã bán**
- **Nút thêm vào giỏ hàng**
- **Sách liên quan/gợi ý**

### 4. Hệ Thống Giỏ Hàng

#### Thêm Vào Giỏ (`pages/add_to_cart.php`)

- Thêm sách vào giỏ hàng
- Kiểm tra tồn kho
- Cập nhật số lượng nếu sách đã có trong giỏ
- Đồng bộ giữa session và database (cho user đã đăng nhập)

#### Xem Giỏ Hàng (`pages/cart.php`)

- Hiển thị danh sách sách trong giỏ
- **Cập nhật số lượng** (`pages/update_cart.php`)
- **Xóa sản phẩm** (`pages/remove_from_cart.php`)
- **Xóa toàn bộ giỏ hàng** (`pages/clear_cart.php`)
- Tính tổng tiền
- Nút chuyển đến thanh toán

### 5. Thanh Toán (`pages/checkout.php`)

- **Form thông tin giao hàng**:
  - Họ tên người nhận
  - Số điện thoại
  - Địa chỉ giao hàng
  - Ghi chú đơn hàng
- **Phương thức thanh toán**:
  - Thanh toán khi nhận hàng (COD)
  - Chuyển khoản ngân hàng
- **Xác nhận đơn hàng**
- **Tính phí vận chuyển**

### 6. Theo Dõi Đơn Hàng (`pages/order_tracking.php`)

- Tra cứu đơn hàng theo mã
- Xem trạng thái đơn hàng
- Lịch sử đặt hàng (cho user đã đăng nhập)

### 7. Hệ Thống Tìm Kiếm

#### Tìm Kiếm Cơ Bản (`search_demo.php`)

- **Search autocomplete**: Gợi ý tự động khi nhập từ khóa
- **Debounce**: Tối ưu hiệu suất, chỉ gọi API sau khi dừng gõ
- **Highlight từ khóa** trong kết quả gợi ý
- **Navigation bằng phím**: Mũi tên lên/xuống, Enter
- **Responsive design**: Tương thích mobile

#### API Gợi Ý (`api/search_suggestions.php`)

- Endpoint API trả về gợi ý tìm kiếm
- Tìm kiếm theo tên sách, tác giả, NXB
- Giới hạn số lượng kết quả
- Format JSON response

### 8. Hệ Thống Người Dùng (`config.php`)

#### Đăng Ký

- **Form đăng ký**: Username, email, password
- **Validation**: Kiểm tra email trùng lặp
- **Password hashing**: Mã hóa mật khẩu an toàn
- **Tự động tạo giỏ hàng** cho user mới
- **Thông báo kết quả** đăng ký

#### Đăng Nhập

- **Form đăng nhập**: Email và password
- **Xác thực mật khẩu** với password_verify()
- **Session management**: Lưu thông tin user
- **Đồng bộ giỏ hàng**: Merge giỏ hàng session với database
- **Chuyển hướng** sau đăng nhập

#### Đăng Xuất

- **Xóa session** user
- **Giữ lại giỏ hàng** trong session
- **Thông báo** đăng xuất thành công

## Chức Năng Dành Cho Quản Trị Viên

### 1. Dashboard Quản Trị (`admin/admin_dashboard.php`)

- **Thống kê tổng quan**:
  - Tổng số sách trong hệ thống
  - Số lượng người dùng đăng ký
  - Số đơn hàng đã xử lý
  - Doanh thu (tính năng sẽ bổ sung)
- **Danh sách user mới** đăng ký gần đây
- **Biểu đồ thống kê** (sẽ phát triển)

### 2. Quản Lý Sách

#### Thêm Sách Mới (`admin/add-book.php`)

- **Form nhập thông tin**:
  - Tên sách, tác giả, NXB
  - Danh mục, nhà cung cấp
  - Giá bán, giá gốc, discount
  - Mô tả, hình ảnh
  - Số lượng tồn kho
- **Upload hình ảnh** sản phẩm
- **Validation** dữ liệu đầu vào

#### Quản Lý Sách (`admin/manage-books.php`)

- **Danh sách tất cả sách**
- **Tìm kiếm và filter** sách
- **Chỉnh sửa thông tin** sách
- **Xóa sách** khỏi hệ thống
- **Cập nhật tồn kho**
- **Quản lý trạng thái** (còn hàng/hết hàng)

### 3. Quản Lý Người Dùng (`admin/ManageUser.php`)

- **Danh sách tất cả user**
- **Thông tin chi tiết** từng user
- **Phân quyền**: Admin/User
- **Khóa/mở khóa** tài khoản
- **Xem lịch sử** đặt hàng của user

### 4. Bảo Mật Admin (`admin/auth_check.php`)

- **Kiểm tra quyền truy cập** admin
- **Redirect** về trang đăng nhập nếu chưa xác thực
- **Session timeout** cho bảo mật

### 5. Layout Admin (`admin/layout.php`)

- **Giao diện thống nhất** cho admin panel
- **Navigation menu** admin
- **Header/Footer** riêng cho admin
- **Responsive design** cho admin

## Tính Năng Kỹ Thuật

### 1. Session Management

- **Multi-session support**: Hỗ trợ nhiều session cùng lúc
- **Cart persistence**: Giỏ hàng được lưu qua sessions
- **User state**: Trạng thái đăng nhập được maintain
- **Security**: Session timeout và validation

### 2. Database Security

- **Prepared Statements**: Tránh SQL Injection
- **Password Hashing**: Mã hóa mật khẩu bằng PASSWORD_DEFAULT
- **Input Validation**: Kiểm tra và làm sạch dữ liệu đầu vào
- **Error Handling**: Xử lý lỗi database một cách an toàn

### 3. Performance

- **Query Optimization**: Tối ưu truy vấn database
- **Image Optimization**: Tối ưu hình ảnh sản phẩm
- **Caching**: Cache session và query results
- **Pagination**: Phân trang cho danh sách dài

### 4. Responsive Design

- **Mobile-first**: Thiết kế ưu tiên mobile
- **Cross-browser**: Tương thích đa trình duyệt
- **Touch-friendly**: Giao diện thân thiện với touch
- **Fast Loading**: Tối ưu tốc độ tải trang

## Luồng Hoạt Động Chính

### 1. Quy Trình Mua Hàng

```
Trang chủ → Chọn danh mục → Xem chi tiết sách → Thêm vào giỏ 
→ Xem giỏ hàng → Thanh toán → Xác nhận đơn → Theo dõi đơn hàng
```

### 2. Quy Trình Đăng Ký/Đăng Nhập

```
Form đăng ký → Validation → Tạo tài khoản → Tạo giỏ hàng → Đăng nhập
```

### 3. Quy Trình Quản Trị

```
Đăng nhập admin → Dashboard → Quản lý sách/user → Thống kê → Báo cáo
```

## Kế Hoạch Phát Triển

### Tính Năng Sắp Bổ Sung

- **Payment Gateway**: Tích hợp cổng thanh toán online
- **Order Management**: Hệ thống quản lý đơn hàng đầy đủ
- **Reviews & Ratings**: Đánh giá và nhận xét sách
- **Wishlist**: Danh sách yêu thích
- **Voucher System**: Hệ thống mã giảm giá
- **Email Notifications**: Thông báo qua email
- **Advanced Search**: Tìm kiếm nâng cao với filter
- **Analytics**: Báo cáo và thống kê chi tiết

### Cải Tiến Kỹ Thuật

- **API RESTful**: Chuyển đổi sang API architecture
- **Modern PHP**: Nâng cấp lên PHP framework hiện đại
- **Database Optimization**: Tối ưu cấu trúc database
- **Security Enhancement**: Nâng cao bảo mật
- **Performance Monitoring**: Giám sát hiệu suất hệ thống

---

*Tài liệu được viết vào ngày 1 tháng 7 năm 2025 lúc 21:22*
