<?php
  if (!checkPerm($readAdmin, 'MANAGE_ACCOUNTS')) {
    go('/yonetim-paneli/hata/001');
  }
?>
<?php if (get("target") == 'chest'): ?>
  <?php if (get("action") == 'delete' && get("chestID") && get("accountID")): ?>
    <?php
      $deleteChest = $db->prepare("DELETE FROM Chests WHERE id = ?");
      $deleteChest->execute(array(get("chestID")));
      go("/yonetim-paneli/hesap/goruntule/".get("accountID"));
    ?>
  <?php else: ?>
    <?php go("/404"); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go("/404"); ?>
<?php endif; ?>
