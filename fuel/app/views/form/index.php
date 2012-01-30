<?php 
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
?>
<p>Index</p>

<?php if (isset($html_error)): ?>
<?php echo $html_error; ?>
<?php endif; ?>

<?php echo Form::open('/form/confirm'); ?>
<p>
	<?php echo Form::label('名前', 'name'); ?>(*) :
	<?php echo Form::input('name', Input::post('name')); ?>
</p>
<p>
	<?php echo Form::label('メールアドレス', 'email'); ?>(*) :
	<?php echo Form::input('email', Input::post('email')); ?>
</p>
<p>
	<?php echo Form::label('コメント', 'comment'); ?>(*) :
	<?php echo Form::textarea('comment', Input::post('comment'), array('rows' => 6, 'cols' => 70)); ?>
</p>
<p>
	性別:
	<?php echo Form::radio('gender', '男性', Input::post('gender') === '男性' ? array('checked' => 'checked', 'id' => 'form_male') : array('id' => 'form_male')); ?>
	<?php echo Form::label('男性', 'male'); ?>
	<?php echo Form::radio('gender', '女性', Input::post('gender') === '女性' ? array('checked' => 'checked', 'id' => 'form_female') : array('id' => 'form_female')); ?>
	<?php echo Form::label('女性', 'female'); ?>
</p>
<p>
	<?php echo Form::label('問い合わせの種類', 'kind'); ?>:
	<?php
	echo Form::select('kind', Input::post('kind'), array(
		'' => '',
		'製品購入前のお問い合わせ' => '製品購入前のお問い合わせ',
		'製品購入後のお問い合わせ' => '製品購入後のお問い合わせ',
		'その他' => 'その他',
	));
	?>
</p>
<p>
	使用プログラミング言語:
	<?php echo Form::checkbox('lang[]', 'PHP', in_array('PHP', Input::post('lang', array())) ? array('checked' => 'checked', 'id' => 'form_PHP') : array('id' => 'form_PHP')); ?>
	<?php echo Form::label('PHP', 'PHP'); ?>
	<?php echo Form::checkbox('lang[]', 'Perl', in_array('Perl', Input::post('lang', array())) ? array('checked' => 'checked', 'id' => 'form_Perl') : array('id' => 'form_Perl')); ?>
	<?php echo Form::label('Perl', 'Perl'); ?>
	<?php echo Form::checkbox('lang[]', 'Python', in_array('Python', Input::post('lang', array())) ? array('checked' => 'checked', 'id' => 'form_Python') : array('id' => 'form_Python')); ?>
	<?php echo Form::label('Python', 'Python'); ?>
</p>

<div class="actions">
	<?php echo Form::submit('submit', '確認'); ?>
</div>
<?php echo Form::close(); ?>
