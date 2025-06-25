// Search Autocomplete Functionality
class SearchAutocomplete {
    constructor(searchInput, searchForm) {
        this.searchInput = searchInput;
        this.searchForm = searchForm;
        this.suggestionsContainer = null;
        this.currentSuggestions = [];
        this.selectedIndex = -1;
        this.searchTimeout = null;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        // Tạo container cho suggestions
        this.createSuggestionsContainer();
        
        // Bind events
        this.bindEvents();
    }
    
    createSuggestionsContainer() {
        // Wrap search input trong container
        const parent = this.searchInput.parentNode;
        const container = document.createElement('div');
        container.className = 'search-container';
        
        parent.insertBefore(container, this.searchInput);
        container.appendChild(this.searchInput);
        
        // Tạo suggestions dropdown
        this.suggestionsContainer = document.createElement('div');
        this.suggestionsContainer.className = 'search-suggestions';
        container.appendChild(this.suggestionsContainer);
    }
    
    bindEvents() {
        // Input events
        this.searchInput.addEventListener('input', (e) => {
            this.handleInput(e.target.value);
        });
        
        this.searchInput.addEventListener('keydown', (e) => {
            this.handleKeyDown(e);
        });
        
        this.searchInput.addEventListener('focus', () => {
            if (this.currentSuggestions.length > 0) {
                this.showSuggestions();
            }
        });
        
        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideSuggestions();
            }
        });
        
        // Form submit
        this.searchForm.addEventListener('submit', (e) => {
            this.hideSuggestions();
        });
    }
    
    handleInput(query) {
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }
        
        // Debounce search
        this.searchTimeout = setTimeout(() => {
            this.fetchSuggestions(query);
        }, 300);
    }
    
    handleKeyDown(e) {
        if (!this.suggestionsContainer.classList.contains('show')) {
            return;
        }
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.navigateDown();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.navigateUp();
                break;
            case 'Enter':
                e.preventDefault();
                this.selectCurrent();
                break;
            case 'Escape':
                e.preventDefault();
                this.hideSuggestions();
                this.searchInput.blur();
                break;
        }
    }
    
    async fetchSuggestions(query) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();
        
        try {
            const response = await fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            
            this.currentSuggestions = suggestions;
            this.selectedIndex = -1;
            this.renderSuggestions(query);
            
        } catch (error) {
            console.error('Search error:', error);
            this.showNoResults();
        } finally {
            this.isLoading = false;
        }
    }
    
    renderSuggestions(query) {
        if (this.currentSuggestions.length === 0) {
            this.showNoResults();
            return;
        }
        
        const html = this.currentSuggestions.map((item, index) => {
            const highlightedTitle = this.highlightText(item.title, query);
            const highlightedAuthor = this.highlightText(item.author, query);
            
            return `
                <a href="index.php?page=bookDetail&id=${item.id}" class="suggestion-item" data-index="${index}">
                    <img src="${item.image}" alt="${item.title}" class="suggestion-image" 
                         onerror="this.src='assets/img/icon/ico_sach.png'">
                    <div class="suggestion-content">
                        <div class="suggestion-title">${highlightedTitle}</div>
                        <div class="suggestion-author">${highlightedAuthor}</div>
                        <div class="suggestion-price">
                            <span class="suggestion-current-price">${item.price}đ</span>
                            ${item.old_price ? `<span class="suggestion-old-price">${item.old_price}đ</span>` : ''}
                            ${item.discount ? `<span class="suggestion-discount">${item.discount}</span>` : ''}
                        </div>
                    </div>
                </a>
            `;
        }).join('');
        
        this.suggestionsContainer.innerHTML = html;
        this.showSuggestions();
        
        // Bind click events
        this.bindSuggestionEvents();
    }
    
    bindSuggestionEvents() {
        const suggestions = this.suggestionsContainer.querySelectorAll('.suggestion-item');
        suggestions.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                this.setSelectedIndex(index);
            });
            
            item.addEventListener('click', (e) => {
                // Let the default link behavior handle navigation
                this.hideSuggestions();
            });
        });
    }
    
    highlightText(text, query) {
        if (!query || !text) return text;
        
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<span class="suggestion-highlight">$1</span>');
    }
    
    navigateDown() {
        if (this.selectedIndex < this.currentSuggestions.length - 1) {
            this.setSelectedIndex(this.selectedIndex + 1);
        } else {
            this.setSelectedIndex(0);
        }
    }
    
    navigateUp() {
        if (this.selectedIndex > 0) {
            this.setSelectedIndex(this.selectedIndex - 1);
        } else {
            this.setSelectedIndex(this.currentSuggestions.length - 1);
        }
    }
    
    setSelectedIndex(index) {
        // Remove previous highlight
        const prevSelected = this.suggestionsContainer.querySelector('.highlighted');
        if (prevSelected) {
            prevSelected.classList.remove('highlighted');
        }
        
        this.selectedIndex = index;
        
        // Add new highlight
        const suggestions = this.suggestionsContainer.querySelectorAll('.suggestion-item');
        if (suggestions[index]) {
            suggestions[index].classList.add('highlighted');
            
            // Scroll into view if needed
            suggestions[index].scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }
    
    selectCurrent() {
        if (this.selectedIndex >= 0 && this.selectedIndex < this.currentSuggestions.length) {
            const selectedItem = this.currentSuggestions[this.selectedIndex];
            window.location.href = `index.php?page=bookDetail&id=${selectedItem.id}`;
        } else {
            // Submit form with current input value
            this.searchForm.submit();
        }
    }
    
    showSuggestions() {
        this.suggestionsContainer.classList.add('show');
        this.searchInput.parentNode.classList.add('suggestions-active');
    }
    
    hideSuggestions() {
        this.suggestionsContainer.classList.remove('show');
        this.searchInput.parentNode.classList.remove('suggestions-active');
        this.selectedIndex = -1;
    }
    
    showLoading() {
        this.suggestionsContainer.innerHTML = '<div class="search-loading">Đang tìm kiếm...</div>';
        this.showSuggestions();
    }
    
    showNoResults() {
        this.suggestionsContainer.innerHTML = '<div class="search-no-results">Không tìm thấy kết quả phù hợp</div>';
        this.showSuggestions();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('#searchForm input[type="search"]');
    const searchForm = document.querySelector('#searchForm');
    
    if (searchInput && searchForm) {
        new SearchAutocomplete(searchInput, searchForm);
    }
}); 