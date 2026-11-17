<?php
require_once __DIR__ . '/includes/functions.php';
$errors = []; $old = ['name'=>'','email'=>'','subject'=>'','message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($old as $k => $_) $old[$k] = trim((string)($_POST[$k] ?? ''));

    if (!empty($_POST['website'])) { redirect('contact.php?sent=1'); } // honeypot: bots fill this hidden field

    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 100) $errors['name'] = 'Enter your name (2–100 characters).';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 150) $errors['email'] = 'Enter a valid email address.';
    if (mb_strlen($old['subject']) < 3 || mb_strlen($old['subject']) > 150) $errors['subject'] = 'Subject must be 3–150 characters.';
    if (mb_strlen($old['message']) < 20 || mb_strlen($old['message']) > 3000) $errors['message'] = 'Message must be 20–3000 characters.';

    if (!$errors) {
        $st = db()->prepare('SELECT COUNT(*) FROM messages WHERE ip_hash = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
        $st->execute([client_hash()]);
        if ((int)$st->fetchColumn() >= cfg('max_messages_per_hour')) $errors['form'] = 'Too many messages from your network. Please try again later.';
    }
    if (!$errors) {
        db()->prepare('INSERT INTO messages (name,email,subject,message,ip_hash) VALUES (?,?,?,?,?)')
            ->execute([$old['name'],$old['email'],$old['subject'],$old['message'],client_hash()]);
        redirect('contact.php?sent=1');
    }
}

$title = 'Contact'; require __DIR__ . '/includes/header.php'; ?>
<div class="page-header"><h1>Let's talk</h1>
  <p class="section-lead">Have an opportunity, a question, or feedback on a project? Reach out any of these ways.</p></div>
<section class="section contact-grid">
  <div>
    <h2 class="h-small">Contact details</h2>
    <div class="contact-info-list">
      <div class="contact-info-item"><span class="glyph">@</span><div><h3>Email</h3><a href="mailto:<?= e($S['email']) ?>"><?= e($S['email']) ?></a></div></div>
      <div class="contact-info-item"><span class="glyph">GH</span><div><h3>GitHub</h3><a href="<?= e($S['github']) ?>" target="_blank" rel="noopener noreferrer"><?= e(preg_replace('#^https?://#','',$S['github'])) ?></a></div></div>
      <div class="contact-info-item"><span class="glyph">📍</span><div><h3>Location</h3><span class="value"><?= e($S['location']) ?></span></div></div>
    </div>
  </div>
  <div>
    <h2 class="h-small">Send a message</h2>
    <?php if (isset($_GET['sent'])): ?><div class="form-status success">Thanks! Your message was sent.</div><?php endif; ?>
    <?php if (isset($errors['form'])): ?><div class="form-status error"><?= e($errors['form']) ?></div><?php endif; ?>
    <form method="post" action="<?= e(url('contact.php')) ?>" data-contact-form novalidate>
      <?= csrf_field() ?>
      <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <?php foreach (['name'=>['Name','text','Your full name'],'email'=>['Email','email','you@example.com'],'subject'=>['Subject','text',"What's this about?"]] as $f => [$label,$type,$ph]): ?>
      <div class="form-group"><label for="<?= $f ?>"><?= $label ?></label>
        <input type="<?= $type ?>" id="<?= $f ?>" name="<?= $f ?>" value="<?= e($old[$f]) ?>" placeholder="<?= e($ph) ?>" maxlength="150" required>
        <p class="field-error"><?= e($errors[$f] ?? '') ?></p></div>
      <?php endforeach; ?>
      <div class="form-group"><label for="message">Message</label>
        <textarea id="message" name="message" maxlength="3000" placeholder="Tell me a bit about it (at least 20 characters)" required><?= e($old['message']) ?></textarea>
        <p class="field-error"><?= e($errors['message'] ?? '') ?></p>
        <p class="char-count" data-char-count>0 characters</p></div>
      <button class="btn btn-primary" type="submit">Send message</button>
    </form>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
