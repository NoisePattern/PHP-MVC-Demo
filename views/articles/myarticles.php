<h1>My articles</h1>
<?php

$table = new Table();
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
		'buttonLink' => ['Edit', 'articles/edit', 'article_id'],
	],
	[
		'columnLabel' => '',
		'buttonLink' => ['Delete', 'articles/delete', 'article_id']
	]
];

echo $table->openTable($columns, $model, $articles);
echo $table->tableHead();
echo $table->tableRows();
echo $table->closeTable();

if(sizeof($articles) == 0){
	echo '<p>You have no articles.</p>';
}
?>