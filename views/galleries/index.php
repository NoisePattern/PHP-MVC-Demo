<?php
$form = new Form();
echo $form->openForm('', 'post', ['class' => 'row']);
echo $form->using($galleryModel, 'selectedGallery')->select($selectOptions)->label(false)->wrap(['class' => 'col-6']);
echo $form->using()->button('submit', 'Select gallery')->wrap(['class' => 'col-6']);
echo $form->closeForm();

echo '<br><h1>' . $galleryModel->name . '</h1>';
?>
<br>
<div class="d-flex flex-wrap">
<?php
// Display gallery's images.
if(!empty($images)){
	foreach($images as $image){
		// Open card.
		echo '<div class="card m-1" style="width: 192px">';
		// Card image.
		echo '<img class="card-img-top" src="../gallery/thumb.php?path=' . $image['fullPath'] . '&mode=clip&width=190&height=190">';
		// Card text.
		echo '<div class="card-body small">' . $image['name'] . '</div>';
		// Close card.
		echo '</div>';
	}
} else {
	echo '<p>This gallery is empty.</p>';
}
?>

</div>