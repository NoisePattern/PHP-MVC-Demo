<h1>Gallery management</h1>
<br>
<?php

// Gallery selection form.
$form = new Form();
echo $form->openForm('', 'POST', ['class' => 'row']);
echo $form->using($galleryModel, 'selectedGallery')->select($selectOptions)->label(false)->wrap(['class' => 'col-md-6']);
echo '<div class="col-md-6">';
echo $form->using()->button('submit', 'Select', ['name' => 'selectGallery'])->wrap(['noWrap' => true]);
echo Html::a(URLROOT . '/galleries/galleryadd', [], "Add gallery", ['class' => 'btn btn-primary ms-4']);
echo '</div>';
echo $form->closeForm();

// Gallery settings form.
if(isset($galleryModel->gallery_id)){
	echo '<br><br><h3>Gallery settings</h3>';
	echo $form->openForm('', 'POST', ['id' => 'galleryForm']);
	echo $form->using($galleryModel, 'gallery_id')->hidden();
	echo $form->using($galleryModel, 'name')->input('text')->wrap(['class' => 'mb-3']);
	echo $form->using($galleryModel, 'parent_id')->select($parentSelectOptions)->wrap(['class' => 'mb-3']);
	echo $form->using($galleryModel, 'public')->checkbox()->label($galleryModel->getLabel('public') . ' (if gallery is not public, none of its subgalleries can be viewed)')->wrap(['class' => 'form-check mb-3']);
	echo $form->using()->button('submit', 'Save settings', ['name' => 'update'])->label(false)->wrap(['noWrap' => true]);
	echo html::a(URLROOT . '/galleries/gallerydelete', ['gallery_id' => $galleryModel->gallery_id], 'Delete gallery', ['id' => 'delete', 'class' => 'ms-4 btn btn-danger']);
	echo $form->closeForm();
}
?>
<script>
document.getElementById('delete').addEventListener('click', function(e){
	choice = confirm('Deleting a gallery will remove all its images, subgalleries and all of their images. Do you want to delete the gallery?');
	if(!choice) e.preventDefault();
}, false)
</script>