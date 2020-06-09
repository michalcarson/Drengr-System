# Caliban for Wordpress

This is NOT an MVC framework. It does, however, have a lot of the features common
to MVC frameworks and noticeably missing from Wordpress--autoloading, dependency injection,
good class structure.

This is the Caliban of Wordpress. The goal of this project is to encapsulate all of the
nastiness and treachery of Wordpress plugin development into a single body that will
serve us as developers. In particular, the Container object and it's associated file
`config/bindings.php` hide away all of the `require` and `global` mess. Anyone still 
asking "why Caliban?" should refer to William Shakespeare's final play 
["The Tempest"](https://en.wikipedia.org/wiki/The_Tempest)
(or at least read a bit of the Wikipedia summary). 

It is also our goal to avoid the anti-patterns common among plugin developers. For instance,
how often have you seen this?

```php
class MyPluginClass {
    public function __construct() {
        // do everything here!    
    }
}
$a = new MyPluginClass();
```

This __active constructor__ makes the class nearly impossible to test and really doesn't
count as object-oriented design. This is procedural programming with a little wrapper. The
developer has not done themselves any favors--it would be as well to leave this in a 
procedural block--but this is the way many, many Wordpress plugins begin. Let's not.

How about the following bit of code? See any security problems with this?

```php
   public function require_php_files_in_directory($directory, $options=array()) {  
       $filenames = $this->get_php_files_in_directory($directory);
       $filepaths = array();

       foreach ($filenames as $filename) {
           $filepath = $directory.$filename;
           $filepaths[] = $filepath;
           $this->require_file($filepath);
       }

       return $filenames;
   }

   private function require_file($filepath) {
       require_once $filepath;
       return true;
   }
```

__Directory scanning__ is another bad habit I see frequently in Wordpress plugins. The code above comes
directly from a mature Wordpress plugin MVC project that I was considering using. It is far too easy
for hackers to drop a `.php` file into a directory on a shared host. I have lost web sites repeatedly
to this problem (and changed hosting sites as a result). When one of those files is `require`'d in, the PHP
is executed. We don't want that.
