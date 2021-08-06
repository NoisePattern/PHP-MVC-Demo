<h1>REGISTER</h1>
<?php

$form = new Form();
echo $form->openForm('', "POST", ['novalidate' => true]);
echo $form->using($model, 'username')->input('text')->wrap(['class' => 'mb-3']);
echo $form->using($model, 'email')->input('email')->wrap(['class' => 'mb-3']);
echo $form->using($model, 'password')->input('password')->wrap(['class' => 'mb-3']);
echo $form->using($model, 'confirmPassword')->input('password')->wrap(['class' => 'mb-3']);
echo $form->using()->button('submit', 'Submit');
echo $form->closeForm();

?>