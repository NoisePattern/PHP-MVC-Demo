<h1>Articles</h1>
<?php
$form = new Form();
echo $form->openForm('', 'POST', 'row');
echo $form->using($userModel, 'selectedUser')->dropdown($dropdownContent)->wrap(['class' => 'col-md-6']);
echo $form->button('Select user', 'submit', ['divClass' => 'col-md-6']);
echo $form->closeForm();

echo '<br><br>';

$table = new Table();
$columns = [
	[
		'field' => 'caption',
		'format' => ['maxLength', 80]
	],
	[
		'columnLabel' => 'Author',
		'field' => 'author'
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

echo $table->openTable($columns, $articleModel, $articles);
echo $table->tableHead();
echo $table->tableRows();
echo $table->closeTable();

if(sizeof($articles) == 0){
	echo '<p>This user has no articles.</p>';
}
?>