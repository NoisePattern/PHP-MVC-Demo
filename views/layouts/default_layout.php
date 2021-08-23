<!DOCTYPE html>
<html lang="fi">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo SITETITLE; ?></title>

		<!-- Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
		<?php if(isset($useEditor)){ ?>
		<!-- Summernote editor CSS -->
		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
		<?php } ?>
		<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
	</head>
	<body>
		<nav id="navside" class="navbar navbar-expand-md py-0">
			<div id="navback" class="container-md py-2 py-md-3 px-md-5">
			<a class="navbar-brand" href="<?php echo URLROOT; ?>/articles/index">MVC DEMO</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse ms-md-5" id="navContent">
				<ul class="navbar-nav me-auto">
					<li class="nav-item dropdown px-3">
						<a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="#">ARTICLES</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/articles/index'; ?>">Browse articles</a></li>
							<?php if(Session::isLogged()){ ?>
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/articles/myarticles'; ?>">My articles</a></li>
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/articles/write'; ?>">Write article</a></li>
							<?php } ?>
							<?php if(Auth::authorize('admin', Application::$app->controller->permissions())){ ?>
								<li><a class="dropdown-item" href="<?php echo URLROOT . '/articles/admin'; ?>">Article management</a></li>
							<?php } ?>
						</ul>
					</li>
					<li class="nav-item dropdown px-3">
						<a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="#">GALLERIES</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/galleries/index'; ?>">Browse galleries</a></li>
							<?php if(Session::isLogged()){ ?>
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/galleries/myimages'; ?>">My images</a></li>
							<li><a class="dropdown-item" href="<?php echo URLROOT . '/galleries/imageadd'; ?>">Upload image</a></li>
							<?php } ?>
							<?php if(Auth::authorize('admin', Application::$app->controller->permissions())){ ?>
								<li><a class="dropdown-item" href="<?php echo URLROOT . '/galleries/galleryadmin'; ?>">Gallery management</a></li>
								<li><a class="dropdown-item" href="<?php echo URLROOT . '/galleries/imageadmin'; ?>">Image management</a></li>
							<?php } ?>
						</ul>
					</li>
					<?php if(Session::isLogged()){ ?>
					<li class="nav-item px-3"><a class="nav-link" href="<?php echo URLROOT; ?>/users/logout">LOGOUT</a></li>
					<?php } else { ?>
					<li class="nav-item px-3"><a class="nav-link" href="<?php echo URLROOT; ?>/users/login">LOGIN</a></li>
					<li class="nav-item px-3"><a class="nav-link" href="<?php echo URLROOT; ?>/users/register">REGISTER</a></li>
					<?php } ?>
				</ul>
			</div>
			</div>
		</nav>
		<div id="main" class="container-md py-5 px-md-5">
		<?php
			if(Session::getFlash('error')){
				echo '<div class="alert alert-danger alert-dismissible" role="alert">' . Session::getFlash('error');
				echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
				echo '</div>';
				}
			if(Session::getFlash('success')){
				echo '<div class="alert alert-success alert-dismissible" role="alert">' . Session::getFlash('success');
				echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
				echo '</div>';
				}
		?>
		{{viewContent}}
		</div>
		<!-- Bootstrap -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
		<?php if(isset($useEditor)){ ?>
		<!-- jQuery, for Summernote WYSIWYG editor. -->
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<!-- Summernote WYSIWYG editor initialization. -->
		<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
		<script>
			$(document).ready(function() {
				$('#summernote').summernote({
					minHeight: 400,
					toolbar: [
						['style', ['style']],
						['font', ['bold', 'underline', 'clear']],
						['fontname', ['fontname']],
						['para', ['ul', 'ol']],
					],
				});
			});
		</script>
		<?php } ?>
	</body>
</html>