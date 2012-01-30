<?php 
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
</head>

<body>
	<div id="wrapper">
		<h1><?php echo $title; ?></h1>

		<?php echo $content; ?>

		<p class="footer">
			Page rendered in {exec_time}s using {mem_usage}mb of memory.
		</p>
		
		<ul class="note">
			<li>FuelPHP でのコンタクトフォームのサンプルです。</li>
			<li>セッション、データベースは使っていません。</li>
			<li>ページのデザインはさておき、セキュアな実用的なコードを目指しています。</li>
			<li>実際にメールが管理者宛に送信されます。確認のメールは実装されていません。</li>
			<li>以下のページから、ソースコードを入手できます。<br />
				<?php echo Html::anchor('https://github.com/kenjis/sample-contact-form', 'https://github.com/kenjis/sample-contact-form'); ?></li>
			<li>セキュリティホール、バグ、よりよいコードなどありましたら、是非、お知らせください。</li>
		</ul>
	</div>
</body>
</html>
