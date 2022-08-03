<?php 

$t = 't';

$body = <<<MAIL
<!DOCTYPE html>
<html>

<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?Open+Sans:300,400,600,700);
		body {
			font-family: 'Open Sans';
		}
		a:hover {
			text-decoration: underline !important;
		}
	</style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
	<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8">
		<tr>
			<td>
				<table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
					align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td style="height:80px;"></td>
					</tr>
					<tr>
						<td style="text-align:center;">
						  <a href="{$config->get('url')}" title="logo">
							<img src="{$config->get('url')}/ressources/img/logo.png" width="48" height="48" alt="Clever">
						  </a>
						</td>
					</tr>
					<tr>
						<td style="height:20px;"></td>
					</tr>
					<tr>
						<td>
							<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
								style="max-width:670px;background:#fff; border-radius:10px; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
								<tr>
									<td style="height:40px;"></td>
								</tr>
								<tr>
									<td style="padding:0 35px;">
										<h3 style="font-size: 1.1rem;">{$t('password_reset_mail_hi')},</h3>
										<div style="font-size: 1rem;">
											<p>{$t('password_reset_mail_1')}</p><p>{$t('password_reset_mail_2')}</p>
											<p><a href="{$config->get('url')}/password-reset-link?s=$serial&t=$token&m=$email">{$t('login_reset_password')}</a></p>
											<hr/>
											<p>{$t('password_reset_mail_3')}</p>
										</div>
									</td>
								</tr>
								<tr>
									<td style="height:40px;"></td>
								</tr>
							</table>
						</td>
					<tr>
						<td style="height:20px;"></td>
					</tr>
					<tr>
						<td style="height:80px;"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>
MAIL;