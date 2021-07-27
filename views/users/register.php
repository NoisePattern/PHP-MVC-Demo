<h1>REGISTER</h1>
<?php

$form = new Form();
echo $form->openForm(URLROOT .'/users/register', "POST");
echo $form->inputField('text', $model, 'username', ['placeholder' => 'Your username', 'divClass' => 'mb-3']);
echo $form->inputField('email', $model, 'email', ['divClass' => 'mb-3']);
echo $form->inputField('password', $model, 'password', ['divClass' => 'mb-3']);
echo $form->inputField('password', $model, 'confirmPassword', ['divClass' => 'mb-3']);
echo $form->button('Submit', 'submit');
echo $form->closeForm();

?>