<?php if ($pagination['totalPages'] > 1): ?>
<ul class="pagination">
    <?php if ($pagination['current'] > 1): ?>
    <li class="waves-effect">
        <a href="?q=<?= urlencode($search) ?>&page=<?= $pagination['current'] - 1 ?>">
            <i class="material-icons">chevron_left</i>
        </a>
    </li>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
    <li class="<?= $i == $pagination['current'] ? 'active teal' : 'waves-effect' ?>">
        <a href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>

    <?php if ($pagination['current'] < $pagination['totalPages']): ?>
    <li class="waves-effect">
        <a href="?q=<?= urlencode($search) ?>&page=<?= $pagination['current'] + 1 ?>">
            <i class="material-icons">chevron_right</i>
        </a>
    </li>
    <?php endif; ?>
</ul>
<?php endif; ?>