<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Rally dos Sertões</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="<?= asset("aereo/css/load.css"); ?>">
	<link rel="stylesheet" href="<?= asset("aereo/css/main.css"); ?>">
	<?= $v->section("styles"); ?>
	<link rel="icon" href="img/favicon.png" />
</head>

<body>
	<div id="app">
		<header>
			<div class="container">
				<div class="row">
					<div class="col logo">
						<a href="#">
							<img class="logo" src="<?= asset("global/img/logo.png"); ?>" alt="Eventos">
						</a>
					</div>
					<div class="col email">
						<a class="email" href="mainto:contato@eventos.com.br">contato@eventos.com.br</a>
					</div>
				</div>
			</div>
		</header>
		<main>
			<div ref="load" class="ajax_load" id="ajax_load" style="visibility: hidden;">
				<div class="ajax_load_box">
					<div class="ajax_load_box_circle"><img src="<?= asset("global/img/preload.png"); ?>" alt=""></div>
					<div class="ajax_load_box_title">Aguarde, carregando...</div>
				</div>
			</div>

			<?= $v->section("content"); ?>

		</main>
		<footer>
			<div class="container">
				<div class="row">
					<div class="col">
						<a href="#">
							<img class="logo" src="<?= asset("global/img/logo.png"); ?>" alt="Eventos">
						</a>
					</div>
					<div class="col">
						<div class="social">
							<a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
									<path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" />
								</svg></i></a>
							<a href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
									<path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
								</svg></a>
						</div>
					</div>
					<div class="col">
						<p class="copy">Todos os direitos reservados © 2021</p>
					</div>
				</div>
			</div>
		</footer>
	</div>

	<script src="<?= asset("global/js/vue/vue.js"); ?>"></script>
	<?= $v->section("scripts"); ?>

</body>

</html>