<section class="account">
  <article>
    <h1><?= $config['pageTitle'] ?></h1>
    <?php if ($userInfo['avatar']): ?>
      <img class="avatar" src="upload/thumbs/<?= $userInfo['avatar']; ?>" alt="mon avatar">
    <?php else: ?>
      <img class="avatar" src="https://via.placeholder.com/150?text=Aucun+Avatar" alt="mon avatar">
    <?php endif; ?>

    <p>id : <?= $userInfo['id']; ?></p>
    <p>Pseudo : <?= $userInfo['pseudo']; ?></p>
    <p>Couriel : <?= $userInfo['mail']; ?></p>
    <p>Niveau : <?= ($userInfo['level'] == 1)? 'Admin' : 'Membre'; ?></p>
    <p><?= $userInfo['avatar']; ?></p>
  </article>


  <article>
    <h2>Modifié l'avatar</h2>
    <form action="<?= $liens['changeAvatar']['url']; ?>" method="post" enctype="multipart/form-data">
      <label for="avatar">Choisie une image pour l'avatar:</label>
      <input type="file" name="avatar" id="avatar">
      <input type="hidden" name="idUser" value="<?= $userInfo['id']; ?>">
      <br />
      <input type="submit" value="Télécharge l'image" name="submit">
    </form>
  </article>


<section>
