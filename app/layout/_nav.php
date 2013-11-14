
<?php if ($this->logged_in):?>
<div class="navigation_wrapper">
        <div id="navigation">
        <ul class="nav nav-pills">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/admin/series">Series</a></li>
            <li><a href="/admin/videos">Videos</a></li>

            <!-- <ul class="nav nav-pills pull-right"> -->

                 <li class="pull-right dropdown<?php echo ($this->controller == 'Index') ? ' active' : '';?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-user<?php echo ($this->controller == 'Index') ? ' icon-white' : '';?>"></i>&nbsp;<?php echo $this->escape($this->logged_in_username);?><b class="caret"></b></a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="/"><i class="icon-home"></i>&nbsp;Dashboard</a></li>
                        <li><a href="/settings"><i class="icon-cogs"></i>&nbsp;Settings</a></li>
                        <li><a href="/logout"><i class="icon-off"></i>&nbsp;Log Out</a></li>
                    </ul>
                </li>
                           
                <li class="pull-right dropdown<?php echo ($this->controller == 'admin') ? ' active' : '';?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-wrench<?php echo ($this->controller == 'admin') ? ' icon-white' : '';?>"></i>&nbsp;Admin<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/users"><i class="icon-user"></i>&nbsp;Users</a></li>
                        <li><a href="/admin/groups"><i class="icon-group"></i>&nbsp;Groups</a></li>
                        <li><a href="/admin/permissions"><i class="icon-unlock"></i>&nbsp;Permissions</a></li>
                    </ul>
                </li>

            <!-- </ul> -->
        </ul>
        </div>
    </div>
<!-- <hr class="clear-both"/> -->
<?php else:?>
<div class="login-header">
    <h1>Second Screen</h1>
</div>
<?php endif;?>

