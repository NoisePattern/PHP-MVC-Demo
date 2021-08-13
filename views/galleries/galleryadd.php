<h1>Create gallery</h1>
<?php

$form = new Form();
echo $form->openForm('', 'POST');
echo $form->using($model, 'name')->input('text')->wrap(['class' => 'mb-3']);
echo $form->using($model, 'parent_id')->select($selectOptions)->wrap(['class' => 'mb-3']);
echo $form->using($model, 'public')->checkbox(['unchecked' => 0])->wrap(['class' => 'form-check mb-3']);
echo $form->using()->button('submit', 'Create', ['name' => 'update'])->label(false);
echo $form->closeForm();

?>