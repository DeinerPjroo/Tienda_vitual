// Funcionalidad de botones de favoritos
document.addEventListener('DOMContentLoaded', function() {
    initFavoriteButtons();
    initProductCards();
    initFilters();
    initSortDropdown();
});

// Inicializar botones de favoritos
function initFavoriteButtons() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleFavorite(btn);
        });
    });
}

// Alternar estado de favorito
function toggleFavorite(button) {
    const isFavorited = button.textContent.trim() === '❤️';
    button.textContent = isFavorited ? '♡' : '❤️';
    
    // Aquí podrías agregar lógica para guardar en localStorage o backend
    // saveFavoriteToBackend(productId, !isFavorited);
}

// Inicializar tarjetas de producto
function initProductCards() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('click', (e) => {
            // Evitar navegación si se hace clic en el botón de favoritos
            if (e.target.classList.contains('favorite-btn')) {
                return;
            }
            
            // Aquí podrías agregar navegación al detalle del producto
            // window.location.href = '/producto/' + productId;
            console.log('Producto seleccionado');
        });
    });
}

// Inicializar filtros
function initFilters() {
    const filterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
    
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            applyFilters();
        });
    });
}

// Aplicar filtros
function applyFilters() {
    const selectedFilters = Array.from(
        document.querySelectorAll('.filter-option input[type="checkbox"]:checked')
    ).map(checkbox => checkbox.parentElement.textContent.trim());
    
    console.log('Filtros aplicados:', selectedFilters);
    
    // Aquí podrías agregar lógica para filtrar productos
    // filterProducts(selectedFilters);
}

// Inicializar dropdown de ordenamiento
function initSortDropdown() {
    const sortDropdown = document.querySelector('.sort-dropdown');
    
    if (sortDropdown) {
        sortDropdown.addEventListener('change', (e) => {
            sortProducts(e.target.value);
        });
    }
}

// Ordenar productos
function sortProducts(sortType) {
    console.log('Ordenar por:', sortType);
    
    // Aquí podrías agregar lógica para ordenar productos
    const productsGrid = document.querySelector('.products-grid');
    const products = Array.from(productsGrid.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        const priceA = parseFloat(a.querySelector('.price-current').textContent.replace('$', ''));
        const priceB = parseFloat(b.querySelector('.price-current').textContent.replace('$', ''));
        
        switch(sortType) {
            case 'Precio: Menor a Mayor':
                return priceA - priceB;
            case 'Precio: Mayor a Menor':
                return priceB - priceA;
            default:
                return 0;
        }
    });
    
    // Reorganizar el DOM
    products.forEach(product => productsGrid.appendChild(product));
}

// Utilidad para animaciones suaves al scroll
function smoothScroll(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth'
    });
}

// Exportar funciones si se usa como módulo
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initFavoriteButtons,
        toggleFavorite,
        applyFilters,
        sortProducts
    };
}