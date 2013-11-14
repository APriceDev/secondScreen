
<form method="post" action="/login" autocomplete="off" class="form-inline">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <fieldset>
        <div class="login-wrap">
            <div class="login-box">
                <div class="login-image"><img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y"/></div>
                <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
                <input type="text" name="username_email" id="username-or-email" placeholder="Username or E-mail Address" value="<?php echo (isset($this->form_values['username_email'])) ? $this->form_values['username_email'] : '';?>"/>
                <input type="password" name="password" id="password" placeholder="Password"/>
                <div class="row">
                    <input name="submit" type="submit" class="btn btn-primary" value="Login"/>
                </div>
            </div>
            <div>
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1"/>
                    Remember Me
                </label>
                <a class="pull-right" href="/forgot-password">Forgot username or password?</a>
            </div>
        </div>
    </fieldset>
</form>

