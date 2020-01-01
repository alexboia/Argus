# LVD-BlogInfo-Display

Displays all the information provided by WordPress' get_bloginfo() function and dumps all the non-transient options. 
Strictly developed for learning purposes, but it may turn out to yield some use anyways.

## What it does

This plug-in creates a menu entry ("Debug blog information") with two sub-entries:

- "Debug blog information" -> Displays all information that can be obtained using the `get_bloginfo()` function;
- "Debug blog options" -> Displays all options that do not act as storage for WordPress transients and their values, by directly scanning the `wp_options` table. Values that are of composite types (arrays, objects) are displayed in a separate window, using Kint.

## Screenshots

### Debug blog options

![Debug blog options](/screenshots/Debug_blog_options_ALL.png?raw=true)

### Debug blog options - Complex type details

![Debug blog options - Complex type details](/screenshots/Debug_blog_options_DETAILS.png?raw=true)

### Debug blog information

![Debug blog information](/screenshots/Debug_bloginfo_ALL.png?raw=true)

## Credits

1. [Kint](https://kint-php.github.io/kint/) - Kint - a modern and powerful PHP debugging helper
2. [ClipboardJS]() - https://clipboardjs.com/ - A JavaScript library used to copy text to clipboard
3. [jQuery BlockUI](http://jquery.malsup.com/block/#overview) - jQuery modal view plug-in