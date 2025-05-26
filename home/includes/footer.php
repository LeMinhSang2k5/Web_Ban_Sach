<footer>
    <div class="container">
        <div class="footer-columns">
            <div class="footer-column">
                <h4>Về BookStore</h4>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                    <li><a href="#">Điều khoản dịch vụ</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Hỗ Trợ Khách Hàng</h4>
                <ul>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Hướng dẫn mua hàng</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Kết Nối Với Chúng Tôi</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Đăng Ký Nhận Tin</h4>
                <form id="newsletter-form">
                    <input type="email" placeholder="Nhập email của bạn" required>
                    <button type="submit">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>
</footer>
</body>
<script>
    const wrapper = document.querySelector('.wrapper');
    const loginLink = document.querySelector('.login-link');
    const registerLink = document.querySelector('.register-link');
    const loginpopup = document.querySelector('.btnLogin-popup')
    const iconClose = document.querySelector('.icon-close');
    registerLink.addEventListener('click', () => {
        wrapper.classList.add('active');
    });

    loginLink.addEventListener('click', () => {
        wrapper.classList.remove('active');
    });
    loginpopup.addEventListener('click', () => {
        wrapper.classList.add('active-popup');
    })
    iconClose.addEventListener('click', () => {
        wrapper.classList.remove('active-popup');
    })
</script>

</html>