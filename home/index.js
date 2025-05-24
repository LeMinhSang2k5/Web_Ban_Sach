document.addEventListener('DOMContentLoaded', function () {
    const allBooks = [
        {
            id: 1,
            title: "Đắc Nhân Tâm",
            author: "Dale Carnegie",
            price: "120.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Dac+Nhan+Tam", // Thay bằng URL ảnh thật
            isFeatured: true,
            isNew: false
        },
        {
            id: 2,
            title: "Nhà Giả Kim",
            author: "Paulo Coelho",
            price: "95.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Nha+Gia+Kim",
            isFeatured: true,
            isNew: true
        },
        {
            id: 3,
            title: "Muôn Kiếp Nhân Sinh",
            author: "Nguyên Phong",
            price: "150.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Muon+Kiep+Nhan+Sinh",
            isFeatured: false,
            isNew: true
        },
        {
            id: 4,
            title: "Tội Ác và Hình Phạt",
            author: "Fyodor Dostoevsky",
            price: "180.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Toi+Ac+Va+Hinh+Phat",
            isFeatured: true,
            isNew: false
        },
        {
            id: 5,
            title: "Hoàng Tử Bé",
            author: "Antoine de Saint-Exupéry",
            price: "70.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Hoang+Tu+Be",
            isFeatured: false,
            isNew: true
        },
        {
            id: 6,
            title: "Thép Đã Tôi Thế Đấy",
            author: "Nikolai Ostrovsky",
            price: "110.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Thep+Da+Toi+The+Day",
            isFeatured: true,
            isNew: false
        },
            {
            id: 7,
            title: "Người Vợ Paris",
            author: "Paula McLain",
            price: "135.000 VNĐ",
            imageUrl: "https://via.placeholder.com/150x220.png?text=Nguoi+Vo+Paris",
            isFeatured: false,
            isNew: true
        }
    ];

    const featuredBooksContainer = document.getElementById('featured-books');
    const newBooksContainer = document.getElementById('new-books');


    function renderBooks(books, container) {
        container.innerHTML = ''; // Clear the container
        books.forEach(book => {
            const bookElement = document.createElement('div');
            bookElement.className = 'book';
            bookElement.innerHTML = `
                <img src="${book.imageUrl}" alt="${book.title}">
                <h3>${book.title}</h3>
                <p>${book.author}</p>
                <p>${book.price}</p>
            `;
            container.appendChild(bookElement);
        });
    }
})