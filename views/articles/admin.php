<h1>Articles</h1>
<?php
$form = new Form();
echo $form->openForm('', 'POST', ['class' => 'row']);
echo $form->using($userModel, 'selectedUser')->select($dropdownContent)->label(false)->wrap(['class' => 'col-md-6']);
echo $form->using()->button('submit', 'Select')->wrap(['class' => 'col-md-6']);
echo $form->closeForm();
echo '<br><br>';

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
		'buttonLink' => ['text' => 'Edit', 'route' => 'articles/edit', 'params' => ['article_id'], 'options' => ['class' => 'btn btn-primary btn-sm']]
	],
	[
		'columnLabel' => '',
		'buttonLink' => ['text' => 'Delete', 'route' => 'articles/delete', 'params' => ['article_id'], 'options' => ['class' => 'btn btn-primary btn-sm']]
	]
];
$table = new Table($articleModel, $columns, $articles);
echo $table->createTable();

if(sizeof($articles) == 0){
	echo '<p>This user has no articles.</p>';
}

$pageNav = new Pagenav($total, $pageSize, $page, ['adjacentCount' => 2]);
echo $pageNav->nav(URLROOT . '/articles/admin', ['selectedUser' => $userModel->selectedUser]);

?>