
<form method="post" action="/forgot-password" autocomplete="off" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <fieldset>
        <div class="login-wrap">
            <div class="login-box">
                <legend>Forget Password?</legend>
                <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
                <input type="text" name="email" id="email" placeholder="E-mail Address"/>
                <hr>
                <div class="row">
                    <input name="submit" type="submit" class="btn btn-primary" value="Submit"/>
                </div>
            </div>
        </div>
    </fieldset>
</form>

