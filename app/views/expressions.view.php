<?php require('partials/head.php'); ?>

<h1>Import Your Expressions</h1>

<form method="POST" action="/expressions" enctype="multipart/form-data">
    <input name="expressions_xml" id="expressions_xml" type="file">
    <button type="submit">Submit</button>
</form>

<h1>All Expressions</h1>

<table>
    <tr>
        <th>Id</th>
        <th>Expression</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
    <?php foreach ($expressions as $expression) : ?>
    <tr>
        <td><?= $expression->id; ?></td>
        <td><?= $expression->expression; ?></td>
        <td><?= $expression->total; ?></td>
        <td>
            <a href="/expressions/<?= $expression->id; ?>/edit">Edit</a>
            <a href="/expressions/<?= $expression->id; ?>/delete">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require('partials/footer.php'); ?>
