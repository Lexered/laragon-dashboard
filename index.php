<?php

session_start();

// Fonction pour afficher et arrêter l'exécution du script avec var_dump
function dd($value = '')
{
  echo "<pre>" . var_dump($value) . "</pre>";
  die();
}

// Fonction pour obtenir le domaine du serveur
function serverDomain()
{
  return $serverDomain = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}

// Fonction pour obtenir le chemin d'un asset
function asset($path)
{
  $assetPath = trim(serverDomain() . "/src/assets/" . $path);
  return $assetPath;
}

// Fonction pour obtenir la lang sinon choisir l'anglais comme langue par défaut
function language($lang = "en")
{
  $langFile = __DIR__ . "/src/lang/$lang.json";
  if (file_exists($langFile)) {
    return json_decode(file_get_contents($langFile), true);
  }
}

$availableLang = [
  "de" => 'Deutsch',
  "en" => 'English',
  "es" => 'Español',
  "fr" => 'Français',
  "id" => 'Bahasa Indonesia',
  "it" => 'Italiano',
  "pt" => 'Português',
  "tl" => 'Tagalog',
];

if (isset($_COOKIE['lang']) && array_key_exists($_COOKIE['lang'], $availableLang)) {
  $_SESSION['lang'] = $_COOKIE['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';
$translations = language($lang);

function translate($string)
{
  global $translations;
  return htmlspecialchars($translations[$string] ?? $string);
}

// Fonction pour obtenir des informations sur le serveur
function getServerInfo()
{
  $server = explode(' ', $_SERVER['SERVER_SOFTWARE']);
  return [
    'httpdVer' => $server[0] ?? null,
    'openSsl' => $server[2] ?? null,
    'phpVer' => phpversion(),
    'docRoot' => $_SERVER['DOCUMENT_ROOT']
  ];
};

$serverInfo = getServerInfo();

// Fonction pour obtenir des informations sur SQL
function getSqlInfo()
{
  $output = shell_exec("mysql -V");
  preg_match('/\d+\.\d+\.\d+/', $output, $version);
  return $version[0] ?? "Version Not Found";
}

// ---------------------------------------------------------------

// Fonction pour obtenir le répertoire des sites
function getSiteDir()
{
  $rootDir = realpath(__DIR__ . "/../");
  if (preg_match('/^Apache/', $_SERVER['SERVER_SOFTWARE'])) {
    return $rootDir . '/laragon/etc/apache2/sites-enabled';
  } else {
    return $rootDir . '/laragon/etc/nginx/sites-enabled';
  }
}

getSiteDir();

// Fonction pour obtenir les sites locaux
function getLocalSites(string $server = 'apache', array $ignoredFiles = ['.', '..', '00-default.conf'])
{
  if ($server === "apache") {
    $sitesDir = "C:/laragon/etc/apache2/sites-enabled";
  } elseif ($server === 'nginx') {
    $sitesDir = "C:/laragon/etc/nginx/sites-enabled";
  } else {
    throw new Exception("Ce type de serveur( $server ) n'est pas pris en compte! Désolé");
  }

  $scanDir = scandir($sitesDir);

  foreach ($ignoredFiles as $ignoredFile) {
    $key = array_search($ignoredFile, $scanDir);
    if ($key !== false) {
      unset($scanDir[$key]);
    };
  }

  $sites = [];
  foreach ($scanDir as $filename) {
    $path = "$sitesDir/$filename";
    $fileConfigInfo = file_get_contents($path);

    $domainRegex = "/^\s*ServerName\s+(.+)$/m";
    $documentRootRegex = "/^\s*DocumentRoot\s+(.+)$/m";
    preg_match($domainRegex, $fileConfigInfo, $domainMatches);
    preg_match($documentRootRegex, $fileConfigInfo, $documentRootMatches);

    $site = [
      "filename" => $filename,
      "domain" => $domainMatches[1],
      "documentRoot" => $documentRootMatches[1],
    ];

    $sites[] = $site;
  }

  return $sites;
}

// Fonction pour obtenir les éléments d'ajout rapide autorisés
function getAllowedQuickAdd()
{
  $scanDir = realpath(__DIR__ . "/../../");
  $quickAddItems = file_get_contents("{$scanDir}/usr/sites.conf");
  preg_match_all('/^^(?!#)(\w+)\s*=\s*(?!\s*(true|false|\B))/m', $quickAddItems, $quickAddItemMatches);

  foreach ($quickAddItemMatches[1] as $quickAddItemMatche) {
    switch (strtolower($quickAddItemMatche)) {
      case 'wordpress':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#21759B',
          'link' => 'https://wordpress.com',
          'logo' => asset('logo/wordpress.svg'),
        ];
        break;

      case 'laravel':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#ff2d20',
          'link' => 'https://laravel.com',
          'logo' => asset('logo/laravel.svg'),
        ];
        break;

      case 'drupal':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#009dde',
          'link' => 'https://drupal.org',
          'logo' => asset('logo/drupal.svg'),
        ];
        break;

      case 'joomla':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#009dde',
          'link' => 'https://joomla.org',
          'logo' => asset('logo/joomla.svg'),
        ];
        break;

      case 'typo3':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#ff8700',
          'link' => 'https://typo3.org',
          'logo' => asset('logo/typo3.svg'),
        ];
        break;

      case 'symfony':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#1A171B',
          'link' => 'https://symfony.com',
          'logo' => asset('logo/symfony.svg'),
        ];
        break;

      case 'cakephp':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#d32f2f',
          'link' => 'https://cakephp.org',
          'logo' => asset('logo/cakephp.svg'),
        ];
        break;

      case 'mautic':
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#ffb900',
          'link' => 'https://mautic.org',
          'logo' => asset('logo/mautic.svg'),
        ];
        break;

      default:
        $quickAddItem = [
          'title' => $quickAddItemMatche,
          'color' => '#f6895a',
          'link' => null,
          'logo' => asset('logo.svg'),
        ];
        break;
    }
    $AllowedQuickAddItems[strtolower($quickAddItem['title'])] = $quickAddItem;
  }

  return $AllowedQuickAddItems;
}

// Afficher les informations PHP si le paramètre 'q' est défini à 'info'
if (isset($_GET['q'])) {
  if ($_GET['q'] === 'info') {
    phpinfo();
    die();
  }
}


?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="shortcut icon" href="src/assets/logo.svg" type="image/x-icon">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Syne:wght@400..800&display=swap" rel="stylesheet" /> -->
  <!-- <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->
  <link rel="stylesheet" href="src/css/app.css" />
  <title>Laragon</title>
</head>

<body>
  <!--header-->

  <header class="header">
    <h1 class="header__logo"><img src="<?= asset('logo.svg') ?>" /></h1>
    <nav class="header__navigation-menu">
      <ul class="header__primary-menu">
        <li class="header__menu-item dropdown-container">
          <a href="#" class="header__link link"><?= translate('Projects') ?></a>
          <ul class="header__sub-menu dropdown-content">
            <?php foreach (getLocalSites() as $site) : ?>
              <li class="header__menu-item sub-menu-item">
                <a target="_blank" href="http://<?= $site['domain'] ?>" class="header__link link"><?= preg_replace('/^auto\.|-|\.test.conf$/', ' ', $site['filename']) ?></a>
              </li>
            <?php endforeach ?>
          </ul>
        </li>
        <li class="header__menu-item">
          <a href="http://localhost/phpmyadmin" class="header__link link" target="_blank"><?= translate('PhpMyAdmin') ?></a>
        </li>
        <li class="header__menu-item">
          <a href="?q=info" class="header__link link" target="_blank"><?= translate('PHP Info') ?></a>
        </li>
        <li class="header__menu-item">
          <button class="header__link link" target="_blank" data-popup-target="email-popup"><?= translate('Email') ?></button>
        </li>
      </ul>
    </nav>

    <!-- header actions button container -->
    <div class="header__actions">

      <!-- dropdown language -->

      <div class="dropdown-container lang-dropdown">
        <span href="#" class="link"><?= $availableLang[$lang] ?></span>
        <ul class="dropdown-content">
          <?php foreach ($availableLang as $key => $value) : ?>
            <li class="header__menu-item dropdown-content__item"><span data-lang="<?= $key ?>" class="set-lang link <?= $key === $lang ? 'active' : null ?>"><?= $value ?></span></li>
          <?php endforeach ?>
        </ul>
      </div>

      <!-- toggle theme mode -->
      <div class="theme-toggle" id="theme-toggle">
        <div class="theme-toggle__content">
          <?php include 'src/assets/icons/interface/sun.svg' ?>
          <?php include 'src/assets/icons/interface/moon.svg' ?>
        </div>
      </div>

      <!-- Documentation button -->
      <button class="header__filled-btn filled-btn toggle-menu-mobile">Menu</button>
      <a href="https://laragon.org/docs" target="_blank" class="documentation-btn header__filled-btn filled-btn"><?= translate('Documentation') ?></a>
    </div>
  </header>

  <!--header-->
  <section class="main-container">
    <d class="main-content">
      <div class="main-content__laragon-title-and-version">
        <div class="main-content__laragon-version-container">
          <p>Full 6.0.220916</p>
        </div>
        <h1 class="main-content__title" title="Laragon"><?= translate('Laragon') ?></h1>
      </div>
      <h3 class="main-content__subtitle"><?= translate('subtitle') ?></h3>

      <div class="server-info">
        <div class="server-info__item">
          <strong class="server-info__item__title"><?= translate('PHP version') ?></strong>
          <p class="server-info__item__subtitle"><?= $serverInfo['phpVer'] ?></p>
        </div>

        <div class="server-info__item">
          <strong class="server-info__item__title"><?= translate('OpenSSL') ?></strong>
          <p class="server-info__item__subtitle"><?= $serverInfo['openSsl'] ?></p>
        </div>
        <div class="server-info__item">
          <strong class="server-info__item__title"><?= translate('MySql') ?></strong>
          <p class="server-info__item__subtitle"><?= translate(getSqlInfo()) ?></p>
        </div>
        <div class="server-info__item">
          <strong class="server-info__item__title"><?= translate('Document Root') ?> </strong>
          <p class="server-info__item__subtitle"><?= $_SERVER['DOCUMENT_ROOT']; ?></p>
        </div>
      </div>

      <div class="quick-add-container">
        <?php include 'components/quick-add-item.php' ?>
        <?php
        foreach (getAllowedQuickAdd() as $quickAddItem) {
          echo quick_add_item(title: "{$quickAddItem['title']}", icon: $quickAddItem['logo'], primaryColor: "{$quickAddItem['color']}", link: $quickAddItem['link']);
        }
        ?>
      </div>
      </div>
  </section>
  <?php include 'inbox/emails.php' ?>
  <script src="src/js/app.js"></script>
</body>

</html>