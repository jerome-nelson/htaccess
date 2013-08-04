<?php
include('htaccessible.class.php');
if(isset($_POST['submit']) && $_POST['submit']) {
	$htaset = new htaccessible;
	$htaset->filelocation($_POST['location'].'/');
	$htaset->add_user($_POST['username']);
	$htaset->add_pwd($_POST['passwd']);
	$htaset->add_auths($_POST['auth_type'],$_POST['directory']);
	$htaset->htcreate();
	unset($htaset);	
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>HTACCESSIBLE Test Form</title>
	<link rel="stylesheet" href="stylesheets/normalize.css" media="screen,projector,print" type="text/css" />
	<link rel="stylesheet" href="stylesheets/app.css" media="screen,projector,print" type="text/css" />
</head>
<body>
<?php
if(isset($_POST['submit']) && $_POST['submit']) {
?>
	<p data-alert class="row alert-box success radius text-center">Directory has been created <a href="#" class="close">&times;</a></p>
<?php
}
?>
<section class="row">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="createht" class="custom" method="POST">
		<fieldset>
			<legend>Create HTACCESSIBLE</legend>
			<div class="row">
				<div class="small-12 columns"><label for="">Location</label><input name="location" type="text" value="<?php echo dirname(__FILE__) ?>" placeholder="location" /></div>
				<div class="small-12 columns"><label for="">Username</label><input type="text" name="username" placeholder="Username Here" /></div>
				<div class="small-12 columns"><label for="">Password</label><input type="text" name="passwd" placeholder="Password Here" /></div>
				<div class="small-12 columns"><label for="">Directory Name</label><input type="text" placeholder="Directory Name" name="directory" /></div>
				<div class="columns small-12">
				<label for="auth_types">Auth Type</label>
				<select name="auth_type" id="auth_types" class="medium">
						<option value="basic">Basic</option>
						<option value="digest">Digest</option>
					</select>
				</div>
				<div class="columns small-12"><input type="submit" class="button medium rounded" name="submit" id="submit" /></div>
			</div>
		</fieldset>
	</form>
</section> 
<section class="row">
	<div class="small-6 columns small-offset-3"><a href="http://badge.fury.io/rb/zurb-foundation">zurb-foundation <img src="https://badge.fury.io/rb/zurb-foundation@2x.png" alt="Zurb Foundation Gem Version" height="18"></a></div>
</section>
<script>
  document.write('<script src=' +
  ('__proto__' in {} ? 'javascripts/vendor/zepto' : 'javascripts/vendor/jquery') +
  '.js><\/script>')
</script>
<script src="javascripts/foundation/foundation.js"></script>
<script src="javascripts/foundation/foundation.alerts.js"></script>
<script src="javascripts/foundation/foundation.forms.js"></script>

  <script>
    $(document).foundation();
  </script>
</body>
</html>