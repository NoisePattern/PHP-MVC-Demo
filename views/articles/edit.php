
<h1>Edit Article</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST");
echo $form->inputField('hidden', $model, 'article_id');
echo $form->inputField('hidden', $model, 'user_id');
echo $form->inputField('text', $model, 'caption', ['divClass' => 'mb-3']);
echo $form->textArea($model, 'content', ['divClass' => 'mb-3', 'elementId' => 'summernote']);
echo $form->button('Save article', 'submit');
echo $form->closeForm();

?>