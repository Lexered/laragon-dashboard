<?php

function getEmailData($filename)
{
  $rootDir = realpath(__DIR__ . "/../../../bin/sendmail/output");
  if (is_dir($rootDir)) {
    $emailPath = $rootDir . '/' . $filename;
    if (file_exists($emailPath)) {
      // echo $emailPath;
      $content = file_get_contents($emailPath);
      $parts = preg_split("/\r?\n\r?\n/", $content, 2);
      $headers = $parts[0] ?? '';
      $explodeHeaders = explode("\n", $headers);
      $parsedHeaders = [];
      foreach ($explodeHeaders as $header) {
        if (preg_match("/^([^:]+):\s*(.+)$/", $header, $matches)) {
          $parsedHeaders[$matches[1]] = $matches[2];
        }
      }
      $body = $parts[1] ?? '';
      return [
        'headers' => $parsedHeaders,
        'body' => $body,
      ];
    }
  }
}

if (isset($_GET['email'])) {
  $data = getEmailData($_GET['email']);
}else{
  return false;
}

?>

<div>
  <?php foreach ($data['headers'] as $key => $value) : ?>
    <p style="font-family: sans-serif; margin-bottom: 5px"><strong><?= htmlspecialchars($key) ?> : </strong><span><?= htmlspecialchars($value) ?></span></p>
  <?php endforeach ?>
  <br />
  <pre style="font-family: sans-serif; font-size: inherit;"><?= $data["body"] ?></pre>
</div>