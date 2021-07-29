<h1>Write article</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST");
echo $form->using('', 'user_id')->hidden(['value' => Session::getKey('user_id')]);
echo $form->using($model, 'caption')->input('text')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'content')->textarea(['id' => 'summernote'])->label()->wrap(['class' => 'mb-3']);
echo $form->button('Save article', 'submit');
echo $form->closeForm();

?>