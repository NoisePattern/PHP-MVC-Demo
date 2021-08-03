<h1>LOGIN</h1>
<?php

$form = new Form();
echo $form->openForm(URLROOT .'/users/login', "POST", ['novalidate' => true]);
echo $form->using($model, 'username')->input('text')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'password')->input('password')->label()->wrap(['class' => 'mb-3']);
echo $form->button('Login', 'submit', ['divClass' => 'mb-3']);
echo $form->closeForm();

?>