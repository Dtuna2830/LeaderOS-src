<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Fırat KAYA">
<link rel="shortcut icon" type="image/x-icon" href="/apps/main/public/assets/img/extras/favicon.png?cache=<?php echo $readSettings["updatedAt"]; ?>">

<?php
  $seoPages = $db->prepare("SELECT * FROM SeoPages WHERE page = ?");
  $seoPages->execute(array(get("route")));
  $readSeoPages = $seoPages->fetch();
  
  $image = null;
  $description = $readSettings["siteDescription"];
  
  if ($seoPages->rowCount() > 0) {
    if ($readSeoPages["title"] != "") {
      $siteTitle = str_replace(
        [
          '%serverName%',
          '%title%'
        ],
        [
          $serverName,
          $readSettings["siteSlogan"]
        ],
        $readSeoPages["title"]
      );
    }
    if ($readSeoPages["description"] != "" || $readSeoPages["description"] != null) {
      $description = $readSeoPages["description"];
    }
    if ($readSeoPages["image"] != "" || $readSeoPages["image"] != null) {
      $image = $readSeoPages["image"];
    }
  }
?>

<title><?php echo $siteTitle; ?></title>

<?php $siteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]); ?>
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $readSettings["siteTags"]; ?>">
<link rel="canonical" href="<?php echo $siteURL; ?>" />
<meta property="og:locale" content="tr_TR" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $siteTitle; ?>" />
<meta property="og:description" content="<?php echo $description; ?>" />
<meta property="og:url" content="<?php echo $siteURL; ?>" />
<meta property="og:site_name" content="<?php echo $serverName; ?>" />
<?php if ($image != null): ?>
  <meta property="og:image" content="<?php echo $image; ?>" />
<?php endif; ?>

<!-- MAIN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">

<!-- EXTRAS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.4.0/dist/select2-bootstrap4.min.css">

<!-- FONTS -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">

<!-- THEMES -->
<?php if ($readTheme["themeID"] == 0): ?>
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/custom/main.min.css?v=<?php echo BUILD_NUMBER; ?>">
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/custom/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">
<?php elseif ($readTheme["themeID"] == 1): ?>
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/flat/main.min.css?v=<?php echo BUILD_NUMBER; ?>">
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/flat/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">
<?php elseif ($readTheme["themeID"] == 2): ?>
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/epic/main.min.css?v=<?php echo BUILD_NUMBER; ?>">
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/epic/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">
<?php else: ?>
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/flat/main.min.css?v=<?php echo BUILD_NUMBER; ?>">
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/themes/flat/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">
<?php endif; ?>

<?php if (get("route") == 'lottery'): ?>
	<link rel="stylesheet" type="text/css" href="/apps/main/public/assets/css/plugins/superwheel/superwheel.min.css">
	<style type="text/css">
		.superWheel .sWheel-inner {
			background-image: url(/apps/main/public/assets/img/extras/lottery-bg.png?cache=<?php echo $readSettings["updatedAt"]; ?>);
			background-repeat: no-repeat;
			background-position: center;
			background-size: 120px;
		}
	</style>
<?php endif; ?>

<style>
  .header-banner {
    background: url(/apps/main/public/assets/img/extras/header-bg.png?cache=<?php echo $readTheme["updatedAt"]; ?>) no-repeat center center #212b38;
  }
</style>

<!-- COLORS -->
<?php if ($readTheme["themeID"] != 0): ?>
	<style type="text/css">
		<?php $readColors = json_decode($readTheme["colors"], true); ?>
		<?php foreach ($readColors as $selector => $styles): ?>
			<?php echo $selector; ?> {
				<?php foreach ($styles as $key => $value): ?>
					<?php echo $key.':'.$value.';'; ?>
				<?php endforeach; ?>
			}
		<?php endforeach; ?>
	</style>
<?php endif; ?>

<!-- CUSTOM CSS -->
<style type="text/css">
	<?php echo $readTheme["customCSS"]; ?>
</style>
