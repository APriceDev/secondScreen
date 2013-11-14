<VirtualHost *:80>
        ServerAdmin agentilex@sobellaenterprises.com
        ServerName ss.sobellaenterprises.com
        DocumentRoot /var/www/ss.sobellaenterprises.com/public/

        AddType application/x-font-woff .woff

        ExpiresActive On
        ExpiresByType image/png "now plus 30 days"
        ExpiresByType image/jpeg "now plus 30 days"
        ExpiresByType image/gif "now plus 30 days"
        ExpiresByType image/vnd.microsoft.icon "now plus 1 months"
        ExpiresByType image/x-icon "now plus 1 months"
        ExpiresByType image/ico "now plus 1 months"
        ExpiresByType text/css "now plus 30 days"
        ExpiresByType text/javascript "now plus 30 days"
        ExpiresByType application/javascript "now plus 30 days"
        ExpiresByType application/x-font-woff "now plus 30 days"

        <Directory />
                AuthType Basic
                AuthName "Access for ss.sobellaenterprises.com"
                AuthUserFile "/srv/auth/ss.htpasswd"
                Require valid-user
                Options FollowSymLinks
                AllowOverride None
        </Directory>

        <Directory /var/www/>
                Options Indexes FollowSymLinks
                AllowOverride all
                Order allow,deny
                allow from all
                # This directive allows us to have apache2's default start page
                # in /apache2-default/, but still have / go to the right place
                # Commented out for Ubuntu
                #RedirectMatch ^/$ /apache2-default/
        </Directory>

        ErrorLog /var/log/apache2/ss.sobellaenterprises.com-error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog /var/log/apache2/ss.sobellaenterprises.com-access.log combined

</VirtualHost>

