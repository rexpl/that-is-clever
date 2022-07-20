function Login(url, textLogin, textUnexpectedError) {

	this.url = url;

	this.textLogin = textLogin;
	this.textUnexpectedError = textUnexpectedError;

	this.fields = document.getElementById("fields");
	this.alertBox = document.getElementById("alert_box");
	this.alertText = document.getElementById("text_alert");
	this.formData = document.getElementById("login_form");
	this.passwordField = document.getElementById("password");
	this.submitButton = document.getElementById("butn_submit");

	this.submit = function() {

		this.submitButton.innerHTML = "<span class=\"spinner-border spinner-border-sm\"></span> Loading..";
		this.submitButton.disabled = true;

		this.fields.classList.remove("invalid");
		this.alertBox.style.visibility='hidden';

		_this = this;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {

			if (this.readyState == 4) {

				result = JSON.parse(this.responseText);				

				if (this.status == 200) {

					_this.submitButton.innerHTML = _this.textLogin;
					_this.submitButton.disabled = false;

					if (result['succes'] == true) {
						window.location.href = _this.url + "/home";
						return;
					}

					_this.passwordField.focus();

					_this.alertBox.classList.add("alert-warning");
					_this.alertText.innerHTML = result['message'];

					_this.alertBox.style.visibility = 'visible';
				}
				else {

					_this.alertBox.classList.add("alert-danger");
					_this.alertText.innerHTML = _this.textUnexpectedError;

					_this.alertBox.style.visibility = 'visible';
				}
			}

		}

		xhttp.open("POST", this.url + "/call/login/login", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(new FormData(this.formData));

		this.passwordField.value = '';

		return false;
	}
}

function Register(url, textRegister, textUnexpectedError, textErrorSomething) {

	this.url = url;

	this.textLogin = textRegister;
	this.textUnexpectedError = textUnexpectedError;
	this.textErrorSomething = textErrorSomething;

	this.alertBox = document.getElementById("alert_box");
	this.alertText = document.getElementById("text_alert");

	this.fieldUsername = document.getElementById('username');
	this.fieldPassword = document.getElementById('password');
	this.fieldPasswordConfirm = document.getElementById('password_confirm');
	this.submitButton = document.getElementById("butn_submit");
	this.formData = document.getElementById('register_form');

	this.validUsername = false;
	this.validPassword = false;
	this.validConfirm = false;

	this.submit = function() {

		if (!this.validUsername || !this.validPassword || !this.validConfirm) {

			this.alertBox.classList.add("alert-warning");
			this.alertText.innerHTML = _this.textErrorSomething;

			this.alertBox.style.visibility = 'visible';
			return;
		}

		this.submitButton.innerHTML = "<span class=\"spinner-border spinner-border-sm\"></span> Loading..";
		this.submitButton.disabled = true;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {

			if (this.readyState == 4) {

				result = JSON.parse(this.responseText);				

				if (this.status == 200) {

					_this.submitButton.innerHTML = _this.textLogin;
					_this.submitButton.disabled = false;

					if (result['succes'] == true) {
						window.location.href = _this.url + "/login?register=true";
						return;
					}

					_this.passwordField.focus();

					_this.alertBox.classList.add("alert-warning");
					_this.alertText.innerHTML = result['message'];

					_this.alertBox.style.visibility = 'visible';
				}
				else {

					_this.alertBox.classList.add("alert-danger");
					_this.alertText.innerHTML = _this.textUnexpectedError;

					_this.alertBox.style.visibility = 'visible';
				}
			}

		}

		xhttp.open("POST", this.url + "/call/login/register", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(new FormData(this.formData));
	}

}