<h1>Add image</h1>
<?php

$form = new Form();
echo $form->openForm('', 'post', ['enctype' => 'multipart/form-data']);
echo $form->using($model, 'user_id')->hidden(['value' => Application::$app->user->user_id]);
echo $form->using($model, 'gallery_id')->select($selectOptions)->wrap(['class' => 'mb-3']);
echo $form->using($model, 'name')->input('text')->wrap(['class' => 'mb-3']);
echo $form->using($model, 'imageFile')->file(['accept' => 'image/*'])->wrap(['class' => 'mb-3']);
echo $form->using()->button('submit', 'Send image');
$form->closeForm();

?>