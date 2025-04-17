document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const mainSearch = document.getElementById('main-search');
    const searchContainer = document.querySelector('.search-wrapper');
    const resultsContainer = document.querySelector('.animated-content');

    // 处理表单提交
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchValue = mainSearch.value.trim();
        
        if(searchValue) {
            // 添加加载类
            document.body.classList.add('show-results');
            
            // 提交表单
            this.submit();
        }
    });

    // 初始化Materialize组件
    M.AutoInit();
});

// 防抖函数 (300ms延迟)
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// AJAX 搜索处理
const performSearch = debounce(function(searchTerm) {
    if (searchTerm.length < 2) {
        $('#search-results').html('');
        $('#original-content').show();
        return;
    }

    $('#loading').show();
    $('#original-content').hide();

    $.ajax({
        url: 'search.php',
        data: { q: searchTerm },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#search-results').html(response.html);
                if (response.count === 0) {
                    $('#search-results').html('<p class="center">没有找到相关结果</p>');
                }
            } else {
                M.toast({html: '搜索失败: ' + response.error});
            }
        },
        error: function() {
            M.toast({html: '网络请求失败，请稍后重试'});
        },
        complete: function() {
            $('#loading').hide();
            // 重新初始化 Materialize 组件
            M.AutoInit(); 
            // 触发懒加载
            triggerLazyLoad();
        }
    });
}, 300);

// 输入监听
$('#live-search').on('input', function() {
    performSearch($(this).val().trim());
});

// 手动触发懒加载
function triggerLazyLoad() {
    const lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));
    if ('IntersectionObserver' in window) {
        let lazyImageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    let lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.add('loaded');
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });
        lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    }
}

// 图片预览功能
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('qr-preview');
    const previewArea = document.querySelector('.preview-area');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewArea.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        previewArea.style.display = 'none';
    }
});

// 初始化日期选择器
document.addEventListener('DOMContentLoaded', function() {
    const datepickers = document.querySelectorAll('.datepicker');
    M.Datepicker.init(datepickers, {
        format: 'yyyy-mm-dd',
        autoClose: true,
        minDate: new Date()
    });
});

// 表单验证增强
$('#add-form').on('submit', function(e) {
    const groupNumber = $('#group_number').val();
    if (groupNumber && !/^\d{5,15}$/.test(groupNumber)) {
        M.toast({html: '群号必须为5-15位数字'});
        e.preventDefault();
        return false;
    }
    return true;
});

// 搜索建议功能
const searchInput = document.getElementById('main-search');
const suggestions = document.getElementById('suggestions');

const fetchSuggestions = debounce(async (term) => {
    if (term.length < 2) {
        suggestions.style.display = 'none';
        return;
    }

    try {
        const response = await fetch(`suggest.php?term=${encodeURIComponent(term)}`);
        const data = await response.json();
        
        suggestions.innerHTML = data.map(item => `
            <a href="#" class="collection-item suggestion-item">
                ${item}
            </a>
        `).join('');
        
        suggestions.style.display = 'block';
    } catch (error) {
        console.error('搜索建议获取失败:', error);
    }
}, 300);

searchInput.addEventListener('input', (e) => {
    fetchSuggestions(e.target.value.trim());
});

// 点击建议填充搜索框
suggestions.addEventListener('click', (e) => {
    if (e.target.classList.contains('suggestion-item')) {
        searchInput.value = e.target.textContent;
        document.getElementById('search-form').submit();
    }
});

// 点击外部隐藏建议
document.addEventListener('click', (e) => {
    if (!e.target.closest('#search-wrapper')) {
        suggestions.style.display = 'none';
    }
});

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}