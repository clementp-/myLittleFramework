<section class="login">
  <h1><?= $liens['login']['title']; ?></h1>

  <form action="#" method="post">
    <label for="email">Coureil</label>
    <input type="email" id="email" name="email" autocomplete="off" value="<?= $mail; ?>">

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" value="">

    <input type="submit" value="<?= $liens['login']['title']; ?>">
  </form>
  <p><a href="<?= $liens['forgetPassword']['url']; ?>">Mot de passe perdu</a></p>
</section>
