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


Usage - For Images
------------------

Upload the image to your server.

In your controller that is sending email, add this line:

			$this->email->attach("/home/yoursite/location-of-file.jpg", "inline");

Then in the email view add this:

< img src="cid:location-of-file.png" border="0" >

And location-of-file.jpg will be changed to the content id for that resource.

Other things will work as well such as ...src=", ...href=", url('')


