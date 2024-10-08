<?php

function getLocalEmails(array $ignoredFiles = ['.', '..', '00-default.conf'])
{
  $rootDir = realpath(__DIR__ . "/../../../bin/sendmail/output");
  if ($rootDir === false || !is_dir($rootDir)) {
    return false;
  }
  $scandir = scandir($rootDir);
  foreach ($ignoredFiles as $ignoredFile) {
    $key = array_search($ignoredFile, $scandir);
    if ($key !== false) {
      unset($scandir[$key]);
    }
  }
  $emails = [];
  foreach ($scandir as $file) {
    $filePath = $rootDir . "/" . $file;
    if (!file_exists($filePath)) {
      return false;
    }
    $content = file_get_contents($filePath);
    $email = [
      'filename' => $file,
      'to' => preg_match('/To: (.+)/', $content, $matches) ? $matches[1] : 'Vide',
      'subject' => preg_match('/Subject: (.+)/', $content, $matches) ? $matches[1] : 'Vide',
      'from' => preg_match('/From: (.+)/', $content, $matches) ? $matches[1] : 'Vide',
      'date' => preg_match('/Date: (.+)/', $content, $matches) ? date('D, d F Y H:i', strtotime($matches[1])) : 'Vide',
      'content' => $content,
    ];
    $emails[] = $email;
  }
  return $emails;
}

?>

<section class="popup-container email" id="email-popup" data-popup-content="email-popup">
  <div class="popup-content">
    <div class="dashboard-content-alignement">
      <aside>
        <div>
          <div class="popup-content__actions">
            <button class="popup-content__actions__item close"></button>
          </div>
          <h3>Emails récents</h3>
        </div>
        <input type="search" name="search" id="search" placeholder="Rechercher un email...">
        <div class="email-list">
          <!--  -->
          <?php if (!empty(getLocalEmails())) :
            foreach (getLocalEmails() as $email) : ?>
              <div class="email-item" data-email="<?= htmlspecialchars($email['filename']) ?>" data-email-content="<?= htmlspecialchars($email['content']) ?>">
                <span class="email-item__title" title="<?= htmlspecialchars($email['from']) ?>"><?= htmlspecialchars($email['from']) ?></span>
                <span class="email-item__subject" title="<?= htmlspecialchars($email['subject']) ?>"><?= htmlspecialchars($email['subject']) ?></span>
                <span class="email-item__date" title="<?= htmlspecialchars($email['date']) ?>"><?= $email['date'] ?></span>
              </div>
            <?php endforeach;
          else: ?>
            <!--  -->
            <div class="message error">
              Aucun email trouvé 🫤.
            </div>
          <?php endif; ?>
        </div>
      </aside>
      <hr />
      <main class="email-container">
        <div class="email-content">
          <div class="message">
            Aucun mail ouvert pour le moment 🤷‍♂️.
          </div>
        </div>
      </main>
    </div>
  </div>
</section>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    const emailItems = document.querySelectorAll('.email-item');
    const emailContent = document.querySelector('.email-content');

    emailItems.forEach(emailItem => emailItem.addEventListener('click', () => {

      fetch('inbox/email.php?email=' + encodeURIComponent(emailItem.getAttribute('data-email'))).then(response => response.text()).then(
        data => {
          emailContent.innerHTML = data;
        }
      ).catch(error => {
        console.log('Error :', error);
      });
    }));
  })
</script>