Bugzilla-Command-Line-Stats
===========================

Installation
-------------

Add the "bugz" file to /usr/local/bin or equivalent.
Change the path in this file to match the upload location of the tools folder.

Useage
-------

bugz v0.2a

USAGE: bugz [--option]=[value]
Eg. bugz --name="Garry Welding"

OPTIONS:
--name         The name of the user. Eg. bugz --name="Garry Welding"
--duration     How many days to get data for. Eg. bugz --name="Garry Welding" --duration=14
--to           Which day to start on, default to today if not provided. Eg. bugz --name="Garry Welding" --to="9-6-2011"
--detail       The detail flag will give you a breakdown of the individual bugs worked on per day with a time count

Background
----------

Bugzilla time reporting suck donkey balls.

Copyright
----------

Work here is relesed under Phil Sturgeons DBAD license. http://philsturgeon.co.uk/code/dbad-license. Just give me some goddam credit if you use it and don't be a dick.