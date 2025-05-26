<div class="book-detail-container">
  <!-- Cột trái: Ảnh sách và các ảnh nhỏ -->
  <div class="book-detail-left">
    <div class="main-image">
      <img src="assets/img/book1.png" alt=""onclick="showImageOverlay(this.src)">
    </div>
    <div class="thumbnail-list">
      <img src="assets/img/modau1.png" alt=""onclick="showImageOverlay(this.src)">
      <img src="assets/img/modau2.png" alt=""onclick="showImageOverlay(this.src)">
      <img src="assets/img/modau3.png" alt=""onclick="showImageOverlay(this.src)">
      <img src="assets/img/modau4.png" alt=""onclick="showImageOverlay(this.src)">
      <div class="more-thumbnails">+5</div>
    </div>
    <button class="add-to-cart">Thêm vào giỏ hàng</button>
    <button class="buy-now">Mua ngay</button>
    <div class="policy-list">
      <p>Thời gian giao hàng: Giao nhanh và uy tín</p>
      <p>Chính sách đổi trả: Đổi trả miễn phí toàn quốc</p>
      <p>Chính sách khách sỉ: Ưu đãi khi mua số lượng lớn</p>
    </div>
  </div>
  <!-- Cột phải: Thông tin sách -->
  <div class="book-detail-right">
    <div class="book-badge">Bán chạy</div>
    <h1 class="book-title">Mãi Mãi Là Bao Xa</h1>
    <div class="book-supplier">Nhà cung cấp: <a href="#">Đinh Tị</a></div>
    <div class="book-publisher">Nhà xuất bản: Thanh niên</div>
    <div class="book-rating">
      <span>★</span> (0 đánh giá)
    </div>
    <div class="book-price">
      <span class="new-price">106.650 đ</span>
      <span class="old-price">135.000 đ</span>
      <span class="discount">-21%</span>
    </div>
    <div class="book-stock">52 nhà sách còn hàng</div>
    <div class="shipping-info">
      <div>Giao hàng đến <b>Phường Bến Nghé, Quận 1, Hồ Chí Minh</b> <a href="#">Thay đổi</a></div>
      <div>Giao hàng tiêu chuẩn - Dự kiến giao Thứ ba - 27/05</div>
    </div>
    <div class="book-promotions">
      <span>Mã giảm 10k</span>
      <span>Mã giảm 20k</span>
      <span>Zalopay: giảm 15%</span>
    </div>

    <!-- phần số lượng sản phẩm-->
    <div class="book-quantity">
      <button type="button" onclick="changeQuantity(-1)">-</button>
      <input type="text" id="quantity-input" value="1" min="1">
      <button type="button" onclick="changeQuantity(1)">+</button>
    </div>

    <!-- phần thông tin chi tiết và mô tả sản phẩm-->
    <div class="book-tabs">
      <div class="tab active" onclick="showTab('info')">Thông tin chi tiết</div>
      <div class="tab" onclick="showTab('desc')">Mô tả sản phẩm</div>
    </div>

    <!-- phần thông tin chi tiết-->
    <div class="book-info-content" id="tab-info">
      <div class="book-info-table">
        <ul>
          <li>Mã hàng: 	8935212358231</li>
          <li>Tên Nhà Cung Cấp: Định Tị</li>
          <li>Tác giả: Diệp Lạc Vô Tâm</li>
          <li>Nhà xuất bản: Thanh niên</li>
          <li>Năm xuất bản: 2022</li>
          <li>Thể loại: Ngôn tình</li>
          <li>Số trang: 590</li>
          <li>Kích thước: 24 x 16 x 2.7 cm</li>
          <li>Hình thức bìa: Bìa Mềm</li>
        </ul>
      </div>
    </div>

    <!-- mô tả sản phẩmm-->
    <div class="book-info-content" id="tab-desc" style="display:none">
      <div class="book-description">
    <i>"Em là cây hoa loa kèn hoang dã mãi mãi chỉ vì chính mình mà nở hoa, rời khỏi đất mẹ là cái giá phải trả khi yêu anh."</i> 
      </div>
    </div>
  </div>
</div>

<!-- phần javascript-->
<script>
function showTab(tab) {
  document.getElementById('tab-info').style.display = tab === 'info' ? '' : 'none';
  document.getElementById('tab-desc').style.display = tab === 'desc' ? '' : 'none';
  var tabs = document.querySelectorAll('.book-tabs .tab');
  tabs[0].classList.toggle('active', tab === 'info');
  tabs[1].classList.toggle('active', tab === 'desc');
}

function changeQuantity(delta) {
  var input = document.getElementById('quantity-input');
  var value = parseInt(input.value) || 1;
  value += delta;
  if (value < 1) value = 1;
  input.value = value;
}
function changeMainImage(src) {
  document.getElementById('main-img').src = src;
}
function showImageOverlay(src) {
  // Tạo overlay
  var overlay = document.createElement('div');
  overlay.className = 'img-overlay';
  overlay.onclick = function() { document.body.removeChild(overlay); };
  // Tạo ảnh lớn
  var img = document.createElement('img');
  img.src = src;
  overlay.appendChild(img);
  document.body.appendChild(overlay);
}
</script>