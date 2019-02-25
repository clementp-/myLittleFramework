<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<title><?= $config['pageTitle']; ?> | <?= $config['siteTitle']; ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="<?= $config['metaDesc']; ?>" />
		<meta name="keywords" content="<?= $config['keywords']; ?>">
		<meta name="robots" content="<?= $config['metaRobot']; ?>">
		<!-- CSS ⤵ -->
		<link rel="stylesheet" href="<?= ROOT; ?>css/style.css" />
	<body>

		<!-- Header ⤵ -->
		<header>
			<a href="<?= ROOT; ?>"><?= $config['siteTitle']; ?></a>
		</header>

		<!-- Menu ⤵ -->
		<nav>
			<ul>
				<li><a href="<?= $liens['index']['url']; ?>"><?= $liens['index']['title']; ?></a></li>
				<?php if ($_SESSION['userId']): ?>
				<li><a href="<?= $liens['account']['url']; ?>"><?= $liens['account']['title']; ?></a></li>
				<li><a href="<?= $liens['logout']['url']; ?>"><?= $liens['logout']['title']; ?></a></li>
				<?php else: ?>
				<li><a href="<?= $liens['login']['url']; ?>"><?= $liens['login']['title']; ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<!-- Message information ⤵ -->
		<?php if($msgs): ?>
			<div class="msgs"><?php foreach($msgs as $msg) : ?><p class="<?= $msg[1]; ?>"><?=$msg[0]; ?></p><?php endforeach; ?></div>
		<?php endif; ?>

		<!-- Content ⤵ -->
		<main>
			<?= $contents_template ?>
		</main>


		<!-- Scripts ⤵ -->
		<script type="text/javascript" src="<?= ROOT; ?>js/jquery.min.js"></script>
		<script type="text/javascript" src="<?= ROOT; ?>js/slick.min.js"></script>
		<script type="text/javascript" src="<?= ROOT; ?>js/lightbox.js"></script>
		<script type="text/javascript" src="<?= ROOT; ?>js/script.js"></script>
	</body>
</html>
