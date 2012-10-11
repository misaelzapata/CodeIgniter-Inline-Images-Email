CodeIgniter Inline Images in Email
======================

General Information
--------------------

This is a small modification to CI Email core to allow inline images in your application


General Instructions
---------------------

1. Download CI Installer (https://github.com/misaelzapata/CodeIgniter-Inline-Images-Email/zipball/master)
2. Move the file to application/libraries and application/config
3. When attaching add the following line to the email instance:
$this->email->attach("image.png", "inline"); 		

If you have problems or have recommendations, please file an issue at
https://github.com/misaelzapata/CodeIgniter-Inline-Images-Email/issues