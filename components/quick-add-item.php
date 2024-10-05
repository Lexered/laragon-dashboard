<?php function quick_add_item($class = '', $icon = null, $title = 'Quick add item', $primaryColor = "#f6895a", $textHoverColor = "#fff", $link = null)
{
  ob_start();

  $link = $link ?? "http://" . strtolower($title) . ".com";
  // SVG par défaut (vous pouvez le personnaliser selon vos besoins)
  $default_icon = <<<EOD
    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <rect width="16" height="16" fill="currentColor" transform="translate(0 0.5)"/>
    </svg>
    EOD;

  // Utiliser les valeurs par défaut si non fournies
  // $icon = $icon ?? $default_icon;
  // $icon = (filter_var($icon, FILTER_VALIDATE_URL)) ? a : b ;

  $quickAddId = uniqid('quick-add-');

  // Créer les styles inline pour les variables CSS
  $style = "
      #{$quickAddId}{
          --{$quickAddId}-color: {$primaryColor};
          --{$quickAddId}-bg-color: color-mix(in srgb, var(--{$quickAddId}-color) 20%, white);
          color: var(--{$quickAddId}-color);
          background-color: var(--{$quickAddId}-bg-color);
      }
    ";

  // Ajouter les styles de survol
  $hoverStyle = "
      #{$quickAddId}:hover{
            background: color-mix(in srgb, var(--{$quickAddId}-color) 100%, white);
            color: $textHoverColor;
      }
    ";
?>

  <style type="text/css">
    <?= $style ?><?= $hoverStyle ?>
  </style>

  <a href="<?= $link ?>" target="_blank" class="quick-add-btn <?= htmlspecialchars($class); ?>" id="<?= $quickAddId ?>">
    <?php if (!is_null($icon) && !filter_var($icon, FILTER_VALIDATE_URL)) { ?>
      <?= $icon; ?>
    <?php } elseif (filter_var($icon, FILTER_VALIDATE_URL)) {

      $iconPath = str_replace($_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "/", '', $icon);
      if (file_exists($iconPath)) {
        include $iconPath;
      }
    }
    ?>

    <?= htmlspecialchars($title); ?>
  </a>

<?php return ob_get_clean();
} ?>