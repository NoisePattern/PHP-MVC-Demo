<h1>My articles</h1>
<?php

$columns = [
	[
		'field' => 'caption',
		'format' => ['maxLength', 80]
	],
	[
		'columnLabel' => 'Created',
		'field' => 'created',
		'format' => ['date', 'j.n.Y']
	],
	[
		'field' => 'published',
		'format' => ['keyValues', [0 => 'No', 1 => 'Yes']]
	],
	[
		'columnLabel' => '',
		'buttonLink' => ['text' => 'Edit', 'route' => 'articles/edit', 'params' => ['article_id'], 'options' => ['class' => 'btn btn-primary btn-sm']]
	],
	[
		'columnLabel' => '',
		'buttonLink' => ['text' => 'Delete', 'route' => 'articles/delete', 'params' => ['article_id'], 'options' => ['class' => 'btn btn-primary btn-sm']]
	]
];
$table = new Table($model, $columns, $articles);
echo $table->createTable();

if(sizeof($articles) == 0){
	echo '<p>You have no articles.</p>';
}

$pageNav = new Pagenav($total, $pageSize, $page, ['adjacentCount' => 2]);
echo $pageNav->nav(URLROOT . '/articles/myarticles');

?>