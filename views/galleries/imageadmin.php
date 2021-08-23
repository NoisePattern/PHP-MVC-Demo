<h1>Image management</h1>
<br>
<?php

// Gallery selection form.
$form = new Form();
echo $form->openForm('', 'POST', ['class' => 'row']);
echo $form->using($galleryModel, 'selectedGallery')->select($selectOptions)->label(false)->wrap(['class' => 'col-md-6']);
echo '<div class="col-md-6">';
echo $form->using()->button('submit', 'Select gallery', ['name' => 'selectGallery'])->wrap(['noWrap' => true]);
echo '</div>';
echo $form->closeForm();
echo '<br>';

$columns = [
	[
		'columnLabel' => '',
		'theadClass' => 'col-1',
		'html' => [
			'element' => 'checkbox',
			'params' => [
				'image_id',							// First param of checkbox method is element's name attribute.
				'{image_id}',						// Second param of checkbox method is element's value attribute.
				['class' => 'form-check-input']		// Third param of checkbox method is attribute array.
			]
		]
	],
	[
		'columnLabel' => 'Image',
		'theadClass' => 'col-1',
		'html' => [
			'element' => 'img',
			'params' => [
				'../gallery/thumb.php?path={fullPath}&mode=clip&width=50&height=50',	// First param of img method url attribute.
				[																		// Second param of img method is attribute array.
					'class' => 'imageThumb',
					'data-bs-toggle' => 'modal',
					'data-bs-target' => '#imageModal',
					'data-bs-path' => '{fullPath}',
					'data-bs-name' => '{name}'
				]
			]
		]
	],
	[
		'field' => 'name',
		'theadClass' => 'col-6',
		'maxlength' => 80

	],
	[
		'columnLabel' => 'Gallery',
		'theadClass' => 'col-3',
		'maxlength' => 40,
		'field' => 'galleryName'
	],
	[
		'columnLabel' => '',
		'theadClass' => 'col-1',
		'html' => [
			'element' => 'a',
			'params' => [
				URLROOT . '/galleries/imagedelete',
				[
					'image_id' => '{image_id}',
					'selectedGallery' => $galleryModel->selectedGallery
				],
				'Delete',
				[
					'class' => 'btn btn-danger btn-sm'
				]
			]
		]
	]
];

$table = new Table($imageModel, $columns, $images);
echo $table->createTable(['class' => 'table table-sm table-striped align-middle']);

if(sizeof($images) == 0){
	echo '<p>This gallery has no images.</p>';
}

$pageNav = new Pagenav($total, $pageSize, $page, ['adjacentCount' => 2]);
echo $pageNav->nav(URLROOT . '/galleries/imageadmin', ['selectedGallery' => $galleryModel->selectedGallery]);

?>
<div class="modal" id="imageModal">
	<div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="titleSpace" class="modal-title"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div id="targetArea" class="modal-body text-center vh-100"></div>
		</div>
	</div>
</div>

<script>

var resizable = false;
var resized = false;
var newWidth = 0;
var newHeight = 0;
var imageElement;

/**
 * Switch between shrunk and full image dimensions.
 */
function sizeSwitch(){
	if(resizable){
		if(resized){
			imageElement.style.width = 'auto';
			imageElement.style.height = 'auto';
			resized = false;
		} else {
			imageElement.style.width = newWidth + 'px';
			imageElement.style.height = newHeight + 'px';
			resized = true;
		}
	}
}

/**
 * Load image when a thumbnail is clicked and set it to fit to screen.
 */
document.getElementById('imageModal').addEventListener('shown.bs.modal', function(event){
	event.stopPropagation();
	// Get image URL and displayable name from clicked thumbnail element.
	let clickedThumb = event.relatedTarget;
	let url = '../gallery/' + clickedThumb.getAttribute('data-bs-path');
	let name = clickedThumb.getAttribute('data-bs-name');
	// Set name to title space.
	document.getElementById('titleSpace').innerHTML = name;

	// Get size of modal content area and calculate area's aspect.
	let area = document.getElementById('targetArea');
	let areaWidth = area.offsetWidth;
	let areaHeight = area.offsetHeight;
	let areaAspect = areaWidth / areaHeight;

	// Get padding and font measurements.
	computedStyles = window.getComputedStyle(area);
	let padding = parseFloat(computedStyles.getPropertyValue('padding-top'));
	let textSize = parseFloat(computedStyles.getPropertyValue('font-size'));
	areaWidth -= padding * 2;
	areaHeight -= padding * 2;

	// Load the image from URL and set an onload callback.
	let image = new Image();
	image.onload = function(){
		// Create img element, set loaded image as its source.
		imageElement = document.createElement('img');
		imageElement.src = this.src;

		// Get image size and calculate its aspect.
		let imageWidth = this.width;
		let imageHeight = this.height;
		let imageAspect = imageWidth / imageHeight;

		// If image doesn't fit on modal content area, it must be shrunk at aspect ratio to fit.
		if(imageWidth > areaWidth || imageHeight > areaHeight){
			resizable = true;
			resized = true;
			areaHeight -= textSize * 3;

			// Create guidance element and append it to modal's content.
			guidance = document.createElement('p');
			guidance.innerHTML = 'Click for full size';
			area.appendChild(guidance);

			// Calculate shrunk image size depending on relative aspects of image and modal content area.
			if(imageAspect > areaAspect){
				newWidth = areaWidth;
				newHeight = areaWidth / imageAspect;
			}
			else {
				newWidth = areaHeight * imageAspect;
				newHeight = areaHeight;
			}
			imageElement.style.width = newWidth + 'px';
			imageElement.style.height = newHeight + 'px';

			// Set click event listener to image.
			imageElement.addEventListener('click', function(){
				sizeSwitch();
			});
		}
		// Append image to modal's content.
		area.appendChild(imageElement);

	}
	image.src = url;
});

/**
 * Remove all content from modal when it is closed. Bootstrap modals are not destroyed between close and open,
 * so content must be cleared before it is repopulated and reopened.
 */
document.getElementById('imageModal').addEventListener('hidden.bs.modal', function(){
	var area = document.getElementById('targetArea');
	area.innerHTML = '';
});
</script>