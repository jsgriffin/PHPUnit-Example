PHP Unit Testing Example
========================

Intro
-----

This is a small application which shows some of the features of PHPUnit and some of the benefits of Unit Testing. It tests a simple file which extracts articles from an RSS feed. There are three versions of this importer which can be tested, showing a progression of code and how the unit tests benefit that progression. 

* __importer.php__ This is a first-pass at the import script, which works as a finite-state machine. It's quite complex, but passes all the unit tests. 
* __importer_2.php__ This is a refactoring of the import script which uses SimplePie to parse the RSS feed rather than doing it by hand, thus improving the readability of the code.
* __importer_3.php__ This fixes some of the bugs that appeared during the refactoring of the original import script, thus completing the refactoring process.

Usage
-----

### Running normally

Running the index.php script will run the importer and output the articles extracted.

`$ php index.php`

You can alter which importer version is used by editing `index.php` and changing the file include at the top of the file. 

### Running the unit tests

Make sure you have PHPUnit installed. Follow the instructions on [PHPUnit.de](https://github.com/sebastianbergmann/phpunit/). On OS X, I've found that the tutorial on [Frodo's Ghost](http://frodosghost.com/2011/04/18/phpunit-installed-on-mac-osx/) is very helpful.

When PHPUnit is installed, change into the test cases directory:

`$ cd tests/cases`

And then run:

`$ phpunit ImporterTest`

This will run the test suite for the importer. 