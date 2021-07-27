<h1>Write article</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST");
echo $form->inputField('hidden', SESSION::getKey('user_id'), 'user_id');
echo $form->inputField('text', $model, 'caption', ['divClass' => 'mb-3']);
echo $form->textArea($model, 'content', ['divClass' => 'mb-3', 'elementId' => 'summernote']);
echo $form->button('Save article', 'submit');
echo $form->closeForm();
?>