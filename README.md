# pfx-ssl-converter
This is a very simplified PHP form that would convert your SSL certificate into a .pfx format. This would save you time as you will not have to save the files on your PC and then execute the super long openssl command, nor would you need to use a third party service like sslshopper to convert your files.

You can just copy and paste the content of the convert.php file on your server and start using the converter.

I would also suggest adding a cron job that would delete any files older than 60 seconds as some users might forget to hit the delete button. Here's an example that you could use:

```
/usr/bin/find /path/to/the/tmp-folder/tmp -mindepth 1 -mmin +1 -exec rm -rf {} \;
```

Here is a blog post where you could see the simple script in action:

[Blog Post](https://bobbyiliev.com/blog/php-ssl-pfx-convertion)
