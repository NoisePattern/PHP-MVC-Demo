
<h1>Edit Article</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST", ['novalidate' => true]);
echo $form->using($model, 'article_id')->hidden();
echo $form->using($model, 'published')->checkbox(['unchecked' => 0])->label('', ['class' => 'form-check-label'])->wrap(['class' => 'form-check mb-3']);
echo $form->using($model, 'caption')->input('text')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'content')->textarea(['id' => 'summernote'])->label()->wrap(['class' => 'mb-3']);
echo $form->using()->button('submit', 'Submit')->label(false)->wrap(['class' => 'mb-3']);
echo $form->closeForm();

?>