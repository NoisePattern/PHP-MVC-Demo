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
		'html' => [
			'element' => 'a',
			'params' => [
				URLROOT . '/articles/edit',
				['article_id' => '{article_id}'],
				'Edit',
				['class' => 'btn btn-primary btn-sm']
			]
		]
	],
	[
		'columnLabel' => '',
		'html' => [
			'element' => 'a',
			'params' => [
				URLROOT . '/articles/delete',
				['article_id' => '{article_id}'],
				'Delete',
				['class' => 'btn btn-danger btn-sm']
			]
		]
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