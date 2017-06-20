<li>
    <input type="checkbox"
           id="drop_collaborator_<?= htmlReady($user->getId()) ?>"
           name="drop_collaborator[]"
           value="<?= htmlReady($user->getId()) ?>"
           style="display: none;">
    <span>
        <?= Avatar::getAvatar($user->getId())->getImageTag(Avatar::SMALL) ?>
        <?= htmlReady($user->getFullName()) ?>
    </span>
    <input type="hidden" name="collaborator[]" value="<?= htmlReady($user->getId()) ?>">
    <label for="drop_collaborator_<?= htmlReady($user->getId()) ?>" style="cursor: pointer; display: inline;">
        <?= Icon::create("trash", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
    </label>
</li>