<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('ID及びパスワードを入力してください。'); ?></legend>
    <?php
        echo $this->Form->input('username', array('label' => 'ユーザー名'));
        echo $this->Form->input('password', array('label' => 'パスワード'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('ログイン')); ?>
</div>