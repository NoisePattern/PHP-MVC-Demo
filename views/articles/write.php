<h1 class="test">Write article</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST");
echo $form->using($model, 'published')->checkbox(['unchecked' => 0])->wrap(['class' => 'form-check mb-3']);
echo $form->using($model, 'user_id')->hidden(['value' => Session::getKey('user_id')]);
echo $form->using($model, 'caption')->input('text')->label()->wrap(['class' => 'mb-3']);
// Note: summernote hides the real texarea and constructs a custom editor div. This breaks label element's functionality.
echo $form->using($model, 'content')->textarea(['id' => 'summernote'])->label()->wrap(['class' => 'mb-3']);
echo $form->button('Save article', 'submit');
echo $form->closeForm();

?>