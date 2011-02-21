=== Plugin Name ===
Contributors: jonathonbyrd, byrd
Donate link: http://community.5twentystudios.com/
Tags: widgets
Requires at least: 3.0.0
Tested up to: 3.0.5
Stable tag: 1.3

Now you can decide which pages and which sidebar that your widgets should display on. You can also decide who sees them and more!

== Description ==

It's about time! Are you a WordPress Webmaster? Are you a WordPress site Administrator? This plugin was built 
with you in mind. My name is [Jonathon Byrd](http://www.jonathonbyrd.com/ "Software Engineer"), I'm a PHP Software 
Engineer and Co-founder of [5Twenty Studios](http://www.5twentystudios.com/ "Outsource in America")
that just so happens to develop for WordPress.

= The Problem =
WordPress is a CMS that lacks the ability to micro manage it's themed pages from the wp-admin area. The greatness
of WordPress allows developers to create numerous page templates for the many different layouts that websites demand.
Many theme developers have found themselves building new template over rides simply to declare a different widget sidebar.
This is a problem, couple this problem with the extra code required to create a widget and the entire widget system
becomes counter-productive. 

It's time to take control of the widgets in WordPress. [Total Widget Control](http://community.5twentystudios.com/ "Total Widget Control")
is the solution. There are no alternatives that can do what Total Widget Control is capable of. And TWC is only going 
to get better.

= The Solution for Developers =
Total Widget Control allows developers to develop complex websites with no more then a single index.php file. How, you ask?
With the use of the [Total Widget Control API and Codex](http://community.5twentystudios.com/kb/ "Total Widget Control API"), 
developers can create hundreds of widgets a day, without breaking
out the overtime. Not only is creating websites incredibly fast with the Total Widget Control Plugin, but all of the widgets
that you used to build your first site can easily be ported to the next thousand websites that you develop. Triune rapid
development and code recycling with a community of developers sharing their widgets and you could easily double your
development profits with the Total Widget Control Plugin.

The first benefit is a simple and well documented API that allows developers to create their own multi-instance widgets 
with little to no brain power. I've done all of the work, I've written all of the code, all you need to do is copy and paste
it. With this API you have the ability to not only create multi-instance widgets, but to also easily create shortcodes
out of your widget files as well. Easily drop new widget instances and short codes into your template files, or call
a specific widget instance in your template files using the widgets ID. The possibilities are endless.

The second benefit to developers is the widget community that is being developed as you read this. It's a community of
developers that are sharing and selling their widgets to other developers. 5Twenty Studios is specifically finding the 
widgets that we know you need in order to develop amazing websites.

The third benefit explained here, is the ability for developers to cut down on the amount of code that's required to develop
great WordPress websites. You no longer have to get creative in your attempts to structure hundreds of sidebars in order
to mimic the concept files presented by your designer. Rethink how you use your sidebars. No longer think of them as
sidebars, but instead as widget regions or widget positions. Declare as many positions as you would like in your template
files and you're already almost done. Now simple upload a few of those widgets that you've built for past websites and
drop them into their locations, now you're done.

= The Solution for Administrators =
WordPress as a content management system offers you the ability to create, read, update and delete any kind of listing
that you could want. Normally the re-arranging of ornaments on your site was left up to the developers, encourage your
developer to use the Total Widget Control system and the power will be in your hands. Use the 5Twenty Widget App Store
to locate hundreds of pre-developed widgets.

With the Total Widget Control Plugin, you can now have fully functional membership areas by adjusting the visiblity
rights of each plugin instance, you can literally create any number of display combinations.

= The Source =
5Twenty Studios is a Software Development company based out of Portland, Oregon. 5Twenty Studios specializes in complete
website solutions tailored to fit the needs of other website development companies. That's right, we would rather develop
for the developer, then develop for the end users. Bring us your designs!

* [5Twenty Studios Homepage](http://www.5twentystudios.com/ "The Developers Developer")
* [5Twenty Studios Community](http://community.5twentystudios.com/ "Total Widget Control")
* [Total Widget Control API and Codex](http://community.5twentystudios.com/kb/ "Total Widget Control API")


== Installation ==

= Standard installation procedures =

1. Upload the folder `total-widget-control` to the `/wp-content/plugins/` directory
1. Activate the `Total Widget Control` plugin through the 'Plugins' menu in WordPress
1. Now navigate back to the Wordpress Widget Area, this plugin actually takes control of this area without creating any new menu links.
1. Click, `I Agree` on the registration page and let the plugin do the rest. You should see the new plugin management page display momentarily.


= Theme Preparations =

The Wordpress Widget area will display an error if you don't have any sidebars defined in your theme. This means
that you'll need to declare some sidebars in your functions.php file and then call those sidebars from your theme files.

   1) This is the standard delcaration code for a new sidebar. 
`<?php
register_sidebar(array(
	//change the string top_sidebar with whatever you would like the name of this sidebar to be. leave the quotes in place.
    'name' => 'top_sidebar', 
	// I suggest that you keep these next values empty.
	'before_widget'=>'','after_widget'=>'','before_title'=>'','after_title'=>'',
));
?>`

   2) The following code is the call statement in php. This line of code should be added to all of your theme files. Where ever you add this code is where widgets will display. Widgets that you have defined to display from the wp-admin area, of course.
`<?php dynamic_sidebar('top_sidebar'); //top_sidebar is the name of the sidebar that will show here. ?>`

   3) Repeat the last two steps as many times as you would like.


= Administrative Setup =

1. Upon activation all of your widgets will dissappear. This is because none of the widgets have been set to display on any particular page. 
1. Simply head to the wp-admin widgets area and begin editing the widgets.
1. When editing a widget, you will see a list of your websites menu items on the right hand side. If you check any of these menu items, and save, then your widget will display on those pages, whether or not the menu item is included in any menu.


== Frequently Asked Questions ==

I have focused these questions on new users that have not yet used the Total Widget Control Plugin. Questions regarding the code and the use of the Total Widget Control Plugin, should be directed to the support community website.

= The free version of this software is designed for small websites =

We are interested in providing excellent software solutions to this community, therefore we unlock the greatest TWC features with the purchase of a PRO license. The sales of the PRO license will help us to launch greater plugins in the days to come.

= What if I deactivate the plugin? =

This plugin routes around the native widget control structure without affecting any of the code. If you deactivate this Plugin after placing all of your widgets, then the system will snap back to its default logic and your widgets will all display as normal. At any time you can come back and reactivate the Total Widget Control Plugin and it will remember all of your past decisions as well as your new ones.

= What if your plugin doesn't offer the features that I need? =

Our development rates are very affordable, couple that with rapid development time and a free pro license and you've got yourself the features that you need. We encourage people to help us invest in this software.

= I'm building a website that will be administered by an idiot, can they use this software? =

YES. Just show them where the help tab is or activate the help features for their user account. Our built in help takes advantage of 
jquery tooltips and will display helpful information to the user as they navigate through the system. You are in good hands my friend.

= Are there bugs in your software that are going to make me look like the idiot? =

Yes and NO. In beta releases, we cannot guarantee that there are not bugs in the code. We actually launch our beta releases to the community in order to weed out all of the bugs. This has proved to be the most successful form of beta testing.

In Stable releases you can be completely assured that there are no bugs in the system that are going to make you look like an idiot. Head to our website in order to get a complete look at all of the releases that we have available for you.

= If I download an Stable version and find a bug, what's the likely hood that it will get fixed? =

On stable releases, we have given you our word that it is free of bugs, if you do find a bug please contact us immediately. We'll drop what we're doing or pull an all nighter to get the bug fixed. Keep in mind that most bugs are caused by an incompatibility in the server stack and the software, we may require access to an installation of the software on your server stack, with your plugin combination that has caused the error, in order to fix the error. Without access to your compilation, there's no guarantees that we can fix the reported bugs.


== Screenshots ==

1. This is the main widget management page. Here I have three widgets per page and nearly five pages of widgets being managed. You can see that I have each widget displayed in a different sidebar, each in the first position. From this page I can filter my widgets, search for specific widgets and declare bulk actions to be performed on any number of selected widgets. What you don't see on this screenshot is the quick edit screen that is available for all widgets. It works just like the quick edit for posts.
2. This is the Widget editing page. Because this plugin is designed for hundreds of widgets, you can easily loose track of the widget instances. Total Widget Control adds the admin title and displays that title in the widget listings page. You can also use the search feature to locate a widget by its title. This makes widget management quick and easy. In the publish area, notice the new features for displaying the widget to different kinds of users at and specifically defined time frames. In thw TWC Pro Settings meta box, you'll notice the addition of many new features for pro users, these features add an extra layer of useability to this system.


== 5Twenty Studios ==

[5Twenty Studios](http://www.5twentystudios.com/ "Outsource in America") is the developers development company. Please support us so that we can continue to support you. Our mission is to launch phenominal plugins for Commercial WordPress Developers.