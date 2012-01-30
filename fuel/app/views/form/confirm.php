<?php 
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
?>
<p>Confirm</p>

<p>
	名前:
	<?php echo $input['name']; ?>
</p>
<p>
	メールアドレス:
	<?php echo $input['email']; ?>
</p>
<p>
	コメント:
	<?php echo nl2br($input['comment']); ?>
</p>
<p>
	性別:
	<?php echo $input['gender']; ?>
</p>
<p>
	問い合わせの種類:
	<?php echo $input['kind']; ?>
</p>
<p>
	使用プログラミング言語:
	<?php echo implode(' ', $input['lang']); ?>
</p>

<?php echo Form::open('form/'); ?>
<?php echo Form::hidden('name', $input['name'], array('dont_prep' => true)); ?>
<?php echo Form::hidden('email', $input['email'], array('dont_prep' => true)); ?>
<?php echo Form::hidden('comment', $input['comment'], array('dont_prep' => true)); ?>
<?php echo Form::hidden('gender', $input['gender'], array('dont_prep' => true)); ?>
<?php echo Form::hidden('kind', $input['kind'], array('dont_prep' => true)); ?>
<?php foreach ($input['lang'] as $lang): ?>
<?php echo Form::hidden('lang[]', $lang, array('dont_prep' => true)); ?>
<?php endforeach; ?>
<div class="actions">
	<?php echo Form::submit('submit1', '修正'); ?>
</div>
<?php echo Form::close(); ?>

<?php echo Form::open('form/send'); ?>
<?php echo Form::hidden(Config::get('security.csrf_token_key'), Security::fetch_token()); ?>
<?php echo Form::hidden('name', $input['name'], array('id' => 'name', 'dont_prep' => true)); ?>
<?php echo Form::hidden('email', $input['email'], array('id' => 'email', 'dont_prep' => true)); ?>
<?php echo Form::hidden('comment', $input['comment'], array('id' => 'comment', 'dont_prep' => true)); ?>
<?php echo Form::hidden('gender', $input['gender'], array('id' => 'gender', 'dont_prep' => true)); ?>
<?php echo Form::hidden('kind', $input['kind'], array('id' => 'kind', 'dont_prep' => true)); ?>
<?php foreach ($input['lang'] as $lang): ?>
<?php echo Form::hidden('lang[]', $lang, array('id' => 'lang', 'dont_prep' => true)); ?>
<?php endforeach; ?>
<div class="actions">
	<?php echo Form::submit('submit2', '送信'); ?>
</div>
<?php echo Form::close(); ?>
