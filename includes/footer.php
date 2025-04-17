<?php if (!isset($hide_footer) || !$hide_footer): ?>
<footer class="page-footer teal lighten-1" style="position: relative; bottom: 0; width: 100%;">
    <div class="container">
        <div class="row">
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            © <?= date('Y') ?> 校园二维码墙
        </div>
    </div>
</footer>
<?php endif; ?>

<!-- 脚本加载 -->
<script src="/assets/jquery.min.js"></script>
<script src="/assets/materialize.min.js"></script>
<script>
// 初始化组件
document.addEventListener('DOMContentLoaded', function() {
    // 初始化侧边导航
    var elems = document.querySelectorAll('.sidenav');
    M.Sidenav.init(elems);
    
    // 初始化模态框
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
    
    // 初始化下拉选择框
    var selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
});

// 动态加载效果
window.addEventListener('load', function() {
    [].forEach.call(document.querySelectorAll('.progress'), function(progress) {
        progress.style.display = 'none';
    });
});
</script>

<?php if (file_exists('assets/js/app.js')): ?>
<script src="/assets/js/app.js"></script>
<?php endif; ?>

</body>
</html>