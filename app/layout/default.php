<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo $this->page_title;?></title>
    <meta name="author" content="Sobella Enterprises">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--[if lt IE 9]>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <script type="text/javascript" src="<?php echo App_Util::auto_version('/js/html5-ie.js'); ?>"></script>
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo App_Util::auto_version('/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/bootstrap-responsive.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/default.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/font-awesome.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/291232.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/test.css'); ?>" rel="stylesheet">
    <link href="<?php echo App_Util::auto_version('/css/timeline.css'); ?>" rel="stylesheet">
   
    <link href='http://fonts.googleapis.com/css?family=Scada|Fenix|Oswald|Ubuntu' rel='stylesheet' type='text/css'>
    
    <?php if ($this->swfobject):?>
    <script type="text/javascript" src="/swfupload/js/swfupload.js"></script>
    <script type="text/javascript" src="/swfupload/js/swfupload.queue.js"></script>
    <script type="text/javascript" src="/swfupload/js/fileprogress.js"></script>
    <script type="text/javascript" src="/swfupload/js/swfupload.speed.js"></script>
    <script type="text/javascript" src="/swfupload/js/handlers.js"></script>
    <?php endif;?>
    
    <!-- <link rel="shortcut icon" href="/favicon.ico"> -->
  </head>

<body>

<div id="wrapper">
    <div id="header">
        <div class="top_info">
            <div class="logo"><p>Sobella Video Timeline Demo</p></div>
            <!-- 
            <div class="socials">
                <a href="#"><img alt="" src="images/fb_icon.png"></a>
                <a href="#"><img alt="" src="images/twitter_icon.png"></a>
                <a href="#"><img alt="" src="images/in_icon.png"></a>
            </div>
            -->
        </div>

    </div>
    <?php include $this->template('_nav');?>

    <div class="container">
        <?php echo $this->layout_content; ?>
        <footer>
            <p class="pull-left">Copyright &copy; <?php echo date('Y', time());?> <a href="http://www.sobellaenterprises.com">So Bella Enterprises</a></p>
            <p class="pull-right"><a href="/contact">Contact</a></p>
        </footer>
    </div>

     <!-- js -->
    <script type="text/javascript" src="<?php echo App_Util::auto_version('/js/jquery-1.8.3.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo App_Util::auto_version('/js/jquery-ui.1.8.23.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo App_Util::auto_version('/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo App_Util::auto_version('/js/ss.js'); ?>"></script>

    <script type="text/javascript">
        // site wide
        ss.presence.init('<?php echo $this->csrf_token;?>');
        ss.main.global();
        // page specific
        var controller = <?php echo json_encode(strtolower($this->controller)); ?>;
        var action = <?php echo json_encode(strtolower($this->action)); ?>;

        if (typeof ss == 'object' &&
            typeof ss[controller] == 'object' &&
            typeof ss[controller][action] == 'object' &&
            typeof ss[controller][action].init == 'function')
        {
            ss[controller][action].init(<?php echo $this->js_args;?>);
        }
    </script>
    </div> <!-- end wrapper -->
  </body>
</html>
