<h1>LOGIN</h1>
<?php

$form = new Form();
echo $form->openForm(URLROOT .'/users/login', "POST");
echo $form->inputField('text', $model, 'username', ['divClass' => 'mb-3']);
echo $form->inputField('password', $model, 'password', ['divClass' => 'mb-3']);
echo $form->button('Submit', 'submit');
echo $form->closeForm();

?>