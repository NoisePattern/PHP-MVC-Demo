<h1>REGISTER</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST", ['novalidate' => true]);
echo $form->using($model, 'username')->input('text')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'email')->input('email')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'password')->input('password')->label()->wrap(['class' => 'mb-3']);
echo $form->using($model, 'confirmPassword')->input('password')->label()->wrap(['class' => 'mb-3']);
echo $form->button('Register', 'submit');
echo $form->closeForm();

?>