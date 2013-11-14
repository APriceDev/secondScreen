
<div class="row-fluid">
    <div class="span12">
        <div class="contact-wrap">
            <legend>Contact Us</legend>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <form action="/contact" method="post" id="contact">
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>" />
                <input type="text" name="name" placeholder="Your name" class="input-xlarge">
                <input id="prependedInput" type="text" name="email" size="16" placeholder="Email Address" class="input-xlarge">
                <center>
                    <textarea name="message" placeholder="Message" class="field span9" rows="10"></textarea><br/>
                </center>
                <input type="submit" name="submit" class="btn btn-primary" value="Send"/>
            </form>
        </div>
    </div>
</div>


