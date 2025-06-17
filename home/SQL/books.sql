-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th6 17, 2025 lúc 07:50 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `mydb`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `label` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sold` int(11) DEFAULT 0,
  `old_price` decimal(10,2) DEFAULT NULL,
  `discount` varchar(10) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publish_year` int(11) NOT NULL,
  `pages` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `cover_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `reviews` decimal(10,0) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `label`, `price`, `image`, `sold`, `old_price`, `discount`, `supplier`, `publisher`, `publish_year`, `pages`, `size`, `cover_type`, `description`, `reviews`, `code`, `stock`) VALUES
(1, 'Đắc Nhân Tâm', 'Dale Carnegie', 'Văn học', 'Bán chạy', 85000.00, './assets/img/sach-help-self/dacnhantam-review.png', 448, 100000.00, '-15%', 'AZ Việt Nam', 'Thế Giới', 2025, 360, '20.5 x 14.5 x 1.5 ', 'Bìa Miềm', '<p><b>Đắc Nhân Tâm </b></p>\r\n<br><br>\r\nĐắc nhân tâm (tên tiếng Anh là How to Win Friends and Influence People) là một trong những cuốn sách về chủ đề nghệ thuật ứng xử và giao tiếp thành công nhất, bán chạy nhất và được biết đến nhiều nhất cho đến tận ngày nay, đưa tên tuổi của Dale Carnegie vang danh khắp thế giới.\r\n<br><br>\r\nDale Carnegie từng nói, việc kiếm được một triệu đô la vẫn dễ hơn thêm một cụm từ vào từ điển tiếng Anh. Vậy mà Đắc nhân tâm đã trở thành một cụm từ như thế: được trích dẫn, diễn giải, tái chế; được sử dụng trong vô số bối cảnh, từ phim hoạt hình, chính trị cho tới tiểu thuyết. Bản thân cuốn sách cũng được dịch sang hầu hết các ngôn ngữ được biết đến trên thế giới. Mỗi thế hệ lại khám phá tác phẩm theo một cách hoàn toàn mới và tìm ra những giá trị vẫn hữu dụng trong thời đại của họ.\r\n<br><br>\r\nCho đến ngày nay, cuốn sách vẫn đang từng bước hoàn thiện để trở nên hoàn hảo hơn, phù hợp với nhu cầu ngày một phát triển. Bản tu chỉnh mới này sẽ không có một sự thay đổi nào ngoại trừ cắt tỉa một vài chi tiết, thêm vào một số ví dụ gần gũi hơn với bối cảnh đương thời. Phong cách viết như diễn thuyết, lối trò chuyện bình dị, gần gũi mà không kém phần hoa lệ của tác giả Dale Carnegie sẽ làm rõ hơn những ý nghĩa của cuốn sách đối với độc giả hiện đại, mà không làm xáo trộn nội dung của tác phẩm.\r\n<br><br>\r\nHàng nghìn người đang đọc và học hỏi từ Đắc nhân tâm, tìm thấy cảm hứng trong việc áp dụng các nguyên tắc trong đó để có cuộc sống tốt đẹp hơn.\r\n<br><br>\r\nĐắc nhân tâm sẽ giúp bạn:\r\n<br><br>\r\n- Thoát khỏi tư duy lối mòn, hình thành các suy nghĩ mới, có được tầm nhìn mới và khám phá được những tham vọng mới.\r\n<br><br>\r\n- Gia tăng sự mến mộ và tín nhiệm của mọi người đối với bạn.\r\n<br><br>\r\n- Xử lý những ý kiến trái chiều, tránh những bất đồng và giữ các quan hệ luôn suôn sẻ, dễ chịu.\r\n<br><br>\r\n- Kết bạn nhanh chóng và dễ dàng hơn.\r\n<br><br>\r\n- Thuyết phục mọi người đồng tình với ý kiến của mình.\r\n<br><br>\r\n- Gia tăng sức ảnh hưởng và uy danh trong công việc.\r\n<br><br>\r\n- Trở thành diễn giả tốt hơn và là một người giao tiếp thú vị hơn.\r\n<br><br>\r\n- Khơi dậy lòng nhiệt huyết của các đồng sự.', 550, '8935325011894', 255),
(2, 'Búp sen xanh', 'Sơn tùng', 'Văn học', 'Bán chạy', 99000.00, './assets/img/sach-van-hoc/bupsenxanh.png', 448, 100000.00, '-15%', 'Nhà xuất bản Kim Đồng', 'Kim Đồng', 2025, 444, '19 x 13 x 2.4', 'Bìa Cứng', 'Búp Sen Xanh\r\n<br><br>\r\n“Búp Sen Xanh” là cuốn tiểu thuyết nổi tiếng nhất của nhà văn Sơn Tùng - người đã dành trọn cuộc đời mình để viết về Bác Hồ và các vị anh hùng, những nhà cách mạng lỗi lạc của dân tộc. Tác phẩm “Búp Sen Xanh” kể về thời thơ ấu và một phần tuổi trẻ của cậu bé Nguyễn Sinh Cung ở làng quê Nghệ An, rồi kinh đô Huế - nơi đã nuôi dưỡng ý chí, tâm hồn và nhân cách của một nhà hoạt động cách mạng vĩ đại.\r\n<br><br>\r\nKỷ niệm 135 năm ngày sinh Chủ tịch Hồ Chí Minh (19.5.1890 - 19.5.2025), Nhà xuất bản Kim Đồng và FAHASA trân trọng giới thiệu ấn bản đặc biệt Búp Sen Xanh - thể hiện lòng tôn kính, tưởng nhớ sâu sắc gửi đến Người.\r\n<br><br>\r\nTác phẩm là lời nhắc nhở đầy xúc động về hành trình tìm đường cứu nước của vị lãnh tụ vĩ đại - người đã cống hiến cả đời mình cho độc lập, tự do của dân tộc Việt Nam. Một cuốn sách giàu cảm xúc, giàu giá trị lịch sử và nhân văn, dành tặng bạn đọc trong dịp đặc biệt này.\r\n<br><br>\r\n“Chúng ta có một khẩu hiệu rất đúng, rất hay: “Chủ tịch Hồ Chí Minh vĩ đại sống mãi trong sự nghiệp của chúng ta.” Đúng như vậy, Hồ Chủ tịch sống mãi trong những tư tưởng và tình cảm lớn, trong toàn bộ hoạt động cách mạng của nhân dân Việt Nam ta, của mọi người chúng ta. Đồng thời Hồ Chủ tịch cũng sống mãi trong những tác phẩm văn học và nghệ thuật có giá trị diễn tả một cuộc đời đã trở thành lịch sử, những trang sử đẹp nhất và vẻ vang nhất của dân tộc Việt Nam ta.\r\n<br><br>\r\nĐến đây, tôi muốn nói đôi điều về cuốn tiểu thuyết “Búp sen xanh” của nhà văn Sơn Tùng, mà nhiều độc giả, nhất là trong giới thanh, thiếu niên ưa thích; và báo chí nước ta đã đăng những bài bình luận và đánh giá mà theo tôi biết, tác giả rất chú trọng. Cuốn sách “Búp sen xanh” nêu lên một vấn đề: ở đây tiểu thuyết và lịch sử có thể gặp nhau không? Vấn đề này các đồng chí hoạt động trong lĩnh vực văn học và nghệ thuật và nói chung tất cả chúng ta cần suy nghĩ để có thái độ. Song ở đây cũng vậy, lời nói có trọng lượng rất lớn thuộc về người đọc, nghĩa là nhân dân.”\r\n<br><br>\r\n- PHẠM VĂN ĐỒNG, Tháng 1, 1983\r\n<br><br>\r\n“Không hiểu sao, mỗi lần nhớ đến nhà văn Sơn Tùng, tôi lại nghĩ ông như một quả dừa khô, tích nắng, tích nước để cuối cùng bật lên một chồi cây xanh, sắc và đẹp như một lưỡi mác.\r\n\r\nCả cuộc đời mình, nhà văn Sơn Tùng đã không ngừng tích lũy, tìm kiếm tư liệu, vượt lên thương tật để nghiên cứu, viết về Chủ tịch Hồ Chí Minh. Ông có những hiểu biết, tư liệu về lãnh tụ mà không phải bất cứ một nhà nghiên cứu nào cũng có được…”\r\n<br><br>\r\n- NHÀ VĂN NGUYỄN TRÍ HUÂN (Tạp chí Nhà văn, số 4/2011)\r\n<br><br>\r\n“… Mỗi lần, được đọc một bài thơ, một quyển sách, được xem một bức tranh, một cuốn phim về Bác, nếu thấy nó thoát khỏi ước lệ, không đơn giản là một tác phẩm được đặt hàng nhân một ngày kỉ niệm nào đó, nếu thấy nó xuất phát từ đáy lòng, từ lòng kính yêu, từ tinh thần trách nhiệm, là tôi hết sức cảm ơn tác giả đã can đảm vượt qua nỗi run sợ trước khi sáng tác. Tôi chấp nhận tất cả các tác phẩm làm cho tôi nhớ đến Bác, gần gũi thêm với Bác, và gạn lọc rồi để lại cho tôi một cảm xúc trong sáng. Đó chính là cảm xúc mỗi khi gấp lại trang cuối cùng Búp sen xanh của nhà văn Sơn Tùng.”\r\n<br><br>\r\n- NHÀ VĂN HÓA NGUYỄN KHẮC VIỆN\r\n<br><br>\r\n---\r\n<br><br>\r\nNHÀ VĂN SƠN TÙNG\r\n<br><br>\r\nNhà văn Sơn Tùng tên thật là Bùi Sơn Tùng (1928-2021) sinh ra trong một gia đình nhà nho nghèo hiếu học ở Nghệ An.\r\n<br><br>\r\nÔng đã tham gia hai cuộc kháng chiến cứu nước vừa là chiến sĩ trực tiếp chiến đấu vừa là phóng viên chiến trường. Tháng 4/1971, ông bị thương nặng trở về với 14 mảnh đạn trên mình: liệt tay phải, vỡ vai trái, mắt phải còn 1/10, 3 mảnh đạn găm trong sọ não không thể mổ gắp ra được. Mất 81% sức khỏe, xếp hạng thương tật 1/4 (hạng thương binh nặng nhất), song ông vẫn cầm bút viết. Từ 1974 đến năm 2010, với bàn tay chỉ còn hai ngón cầm bút được, ông đã viết hàng chục tiểu thuyết, tập truyện ngắn, văn xuôi.\r\n<br><br>\r\nNhà văn Sơn Tùng được biết đến với nhiều tác phẩm viết về Hồ Chí Minh, trong đó nổi tiếng nhất là BÚP SEN XANH. Ông vinh dự nhận danh hiệu \"Anh hùng Lao động\" năm 2011.\r\n<br><br>\r\nCùng một tác giả:\r\n<br><br>\r\n• Bên khung cửa sổ (NXB Lao động, 1974)\r\n<br><br>\r\n• Nhớ nguồn (NXB Phụ nữ, 1975)\r\n<br><br>\r\n• Con người và con đường (NXB Phụ nữ, 1976)\r\n<br><br>\r\n• Trần Phú (NXB Thanh niên, 1980)\r\n<br><br>\r\n• Nguyễn Hữu Tiến (NXB Thanh niên, 1981)\r\n<br><br>\r\n• Búp sen xanh (NXB Kim Đồng, 1982)\r\n<br><br>\r\n• Bông sen vàng (NXB Đà Nẵng, 1990)\r\n<br><br>\r\n• Trái tim quả đất (NXB Thanh niên, 1990)\r\n<br><br>\r\n• Hoa râm bụt (NXB Công an Nhân dân, 1990)\r\n<br><br>\r\n• Mẹ về (NXB Phụ nữ, 1990)\r\n<br><br>\r\n• Vườn nắng (NXB Thanh niên, 1997)\r\n<br><br>\r\n• Sáng ánh tâm đăng Hồ Chí Minh (NXB Công an Nhân dân, 2005)\r\n<br><br>\r\n• Bác ở nơi đây (NXB Thanh niên, 2005)\r\n<br><br>\r\n• Chung một tình thương của Bác (NXB Thông tấn, 2006)\r\n<br><br>\r\n• Lõm (NXB Thanh niên, 2006)\r\n<br><br>\r\n• Tấm chân dung Bác Hồ (NXB Kim Đồng, 2013)\r\n<br><br>\r\n• Cuộc chia li trên bến Nhà Rồng (NXB Kim Đồng, 2015)\r\n<br><br>\r\n• Thầy giáo Nguyễn Tất Thành ở trường Dục Thanh (NXB Kim Đồng, 2016)', NULL, '9786042253741', 200),
(3, 'Nhà Giả Kim', '', 'Văn Học', 'Bán chạy', 110000.00, './assets/img/sach-van-hoc/nhagiakim.png', 448, 100000.00, '-15%', NULL, NULL, 0, 0, '0', '', '', NULL, '', 0),
(4, 'Bông sen vàng', '', 'Văn Học', NULL, 110000.00, './assets/img/sach-van-hoc/bongsenvang.png', 448, 100000.00, '-15%', NULL, NULL, 0, 0, '0', '', '', NULL, '', 0),
(5, 'Mãi Mãi là Bao xa ', '', 'Văn Học', 'Bán chạy', 85000.00, './assets/img/sach-van-hoc/maimailabaoxa.png', 455, 100000.00, '-15%', NULL, NULL, 0, 0, '0', '', '', NULL, '', 0),
(6, 'Đột Phá Tư Duy Kì Thi Tốt Nghiệp THPT - Môn Sinh Học', '', 'Sách tham khảo', NULL, 120000.00, './assets/img/sach-kham-khao/sinhhoc.png', 500, 150000.00, '-20%', NULL, NULL, 0, 0, '0', '', '', NULL, '', 0),
(7, 'Đề Minh Họa Tốt Nghiệp THPT 2025', '', 'Sách tham khảo', NULL, 80000.00, './assets/img/sach-kham-khao/nguvan.png', 600, 10000.00, '-20%', NULL, NULL, 0, 0, '0', '', '', NULL, '', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
