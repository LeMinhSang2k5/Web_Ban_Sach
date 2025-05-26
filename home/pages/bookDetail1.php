<div class="book-detail-container">
  <!-- Cột trái: Ảnh sách và các ảnh nhỏ -->
  <div class="book-detail-left">
    <div class="main-image">
      <img src="assets/img/bookdetail2.png" alt="" onclick="showImageOverlay(this.src)">
    </div>
    <div class="thumbnail-list">
      <img src="assets/img/bookdetail2.png" alt="" onclick="showImageOverlay(this.src)">
      <img src="assets/img/bookdetail2.png" alt="" onclick="showImageOverlay(this.src)">
      <img src="assets/img/bookdetail2.png" alt="" onclick="showImageOverlay(this.src)">
      <img src="assets/img/bookdetail2.png" alt="" onclick="showImageOverlay(this.src)">
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
    <h1 class="book-title">999 Lá Thư Gửi Cho Mình</h1>
    <div class="book-supplier">Nhà cung cấp: <a href="#">AZ Việt Nam</a></div>
    <div class="book-publisher">Nhà xuất bản: Thanh niên</div>
    <div class="book-rating">
      <span>★</span> (1 đánh giá) | Đã bán 1.4k
    </div>
    <div class="book-price">
      <span class="new-price">64.000 đ</span>
      <span class="old-price">99.000 đ</span>
      <span class="discount">-36%</span>
    </div>
    <div class="book-stock">110 nhà sách còn hàng</div>
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
          <li>Mã hàng: 	8935325015618</li>
          <li>Tên Nhà Cung Cấp: AZ Việt Nam</li>
          <li>Tác giả: Miêu Công Tử</li>
          <li>Nhà xuất bản: Thanh niên</li>
          <li>Năm xuất bản: 2023</li>
          <li>Thể loại: Văn học</li>
          <li>Số trang: 104</li>
          <li>Kích thước: 24 x 19 x 0.5 cm</li>
          <li>Hình thức bìa: Bìa Mềm</li>
        </ul>
      </div>
    </div>

    <!-- mô tả sản phẩmm-->
    <div class="book-info-content" id="tab-desc" style="display:none">
      <div class="book-description">
    <b>"999 Lá Thư Gửi Cho Mình" - Phiên bản "Tô màu cuộc sống"</b> Chính thức ra đời với sứ mệnh mang đến cho cuộc sống của bạn thêm nhiều điều hạnh phúc ngọt ngào thông qua những lá thư đầy ý nghĩa kết hợp cùng những bức tranh sinh động nhất đợi bạn đặt bút tô điểm. Đến với cuốn sách này, chúng mình mong rằng bạn có thể tự tay vẽ nên những giấc mơ của riêng bạn, những giây phút thăng hoa trong cuộc sống, có thể tô điểm thêm cho những khoảnh khắc đời thường trở nên rực rỡ và muôn phần lộng lẫy hơn. Mong rằng những gam màu ấm áp và tươi vui do chính tay bạn tô vẽ có thể xoa dịu và chữa lành những bất an bên trong bạn, để thế giới xung quanh bạn trở nên muôn màu muôn vẻ, mang không khí tươi trẻ và nhiệt huyết ngập tràn, đón ánh ban mai và vui sống!
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
</script>