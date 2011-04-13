=== Plugin Name ===
Contributors: jonbyrd, byrd
Author link: http://www.totalwidgetcontrol.com/
Donate link: http://www.totalwidgetcontrol.com/
Tags: widgets, dynamic sidebars, dynamic widgets, widget themes, widget styles, widget management, sidebar management
Requires at least: 3.0.0
Tested up to: 3.1.1
Stable tag: 1.6.15

Total Widget Control is a plugin for WordPress that allows administrators to display widgets when, where and to whom they want.

== Description ==
Now you can dynamically assign widgets to any page, post category, or custom listing type, change the order widgets display in, decide which users can see which widgets, and/or quickly change the style of a widget entirely.

= Dedicated Support Website =
You will not find another plugin in the WordPress repository that has developers as dedicated to support as 5Twenty Studios is dedicated to supporting The Total Widget Control. Let this website testify to our dedication, try it out. <a href="http://www.totalwidgetcontrol.com/">Total Widget Support</a>

= Getting Started Tutorial =
[youtube http://www.youtube.com/watch?v=WNZ0cfAgWu4]

= Feature List =
* *Dynamic Sidebars* apply widgets to any page, post, category, or custom listing type.
* *Widget Themes* Quickly change the design for any widget by selecting from a dropdown list of your predesigned widget styles.
* *Widget Positions* use TWC to place and order widgets throughout your site
* *Widget Notes* add titles to widgets only which are only viewable in the admin panel.
* *Default Widgets* easily set default widgets to your sidebar.
* *Privledges* set which to be viewable by specifc user groups including public, admin, or auther.
* *Search, Sort, Filter* your widgets on demand.
* *Future Publish Dates* set a date in the future for your widget to automatically be published. 
* *Bulk actions* update multiple widgets at once using bulk options.

= Who's Using Total Widget Control? =
The Total Widget Control website itself is using TWC. We have also installed the latest version on our demo website for you to try out, [Total Widget Control Demo Site](http://demo.5twentystudios.com/)
`User: Demo
Password: Demo`


= The Source =
5 Twenty is a Portland, Oregon based development firm which specializes in the development of interactive web applications. 100% of our team members are located within the United States and are full-time employees of 5 Twenty. We provide extensive white label development sevices for marketing, branding, and web design firms throughout the world. Working as their inhouse development teams, we have the ability to quickly and effiencently produce high quality applications that meet their client's needs. Interested in working with us? Visit [5Twenty Studios](http://www.5twentystudios.com/ "Outsource in America")

* [www.totalwidgetcontrol.com](http://www.totalwidgetcontrol.com "www.totalwidgetcontrol.com")
* [5Twenty Studios Homepage](http://www.5twentystudios.com/ "The Developers Developer")
* [Jonathon Byrd](http://www.jonathonbyrd.com/ "Software Engineer")

== Installation ==

= Standard installation procedures =

*The installation is REALLY EASY. Just click and activate. But I'm providing as much detail as I possibly can, just to give you some comfort.*

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

1. Upon activation NONE of your widgets will dissappear. TWC works alongside the core WordPress without any conflicts, you can actually use the old widget area without any problems.
1. Simply head to the wp-admin widgets area and begin setting widget specifics.
1. When editing a widget, you will see a list of your websites menu items on the right hand side. If you check any of these menu items, and save, then your widget will display on those pages, whether or not the menu item is included in any menu.


== Frequently Asked Questions ==

These questions are focused towards new users, that have not yet used the Total Widget Control Plugin. For more indepth information or questions regarding the code and/or use of the Total Widget Control Plugin, please visit the support community website.

= Are there any online resources for using TWC? =

[TotalWidgetControl.com](http://www.TotalWidgetControl.com/ "Total Widget Control") Codex, Tutorials, Support Forum, and also a Questions and Answers section.

= What if I have a problem with my installation? =

That's why we're here, [TotalWidgetControl.com](http://www.TotalWidgetControl.com/ "Total Widget Control") and here support@5twentystudios.com. The tech support provided by 5Twenty Studios, a U.S.A based company, will overcome any issues that you may have with your installation. We've gone to great lengths to make our users happy, we'll do the same for you.

= The free version of this software is designed for small websites =

We are interested in providing excellent software solutions to this community, therefore we unlock the greatest TWC features with the purchase of a PRO license. The sales of the PRO license will help us to launch greater plugins in the days to come.

= What features does Total Widget Control Provide? =

TWC provides numerous very helpful features for dynamically managing your website and it's widgets. The following are just a couple of the features you will find:

* "Dynamic Sidebars" apply widgets to any page, post, category, or custom listing type.
* "Widget Themes" Quickly change the design for any widget by selecting from a dropdown list of your predesigned widget styles.
* "Widget Positions" use TWC to place and order widgets throughout your site
* "Widget Notes" add titles to widgets only which are only viewable in the admin panel.
* "Default Widgets" easily set default widgets to your sidebar.
* "Privledges" set which to be viewable by specifc user groups including public, admin, or auther.
* "Search, Sort, Filter" your widgets on demand.
* "Future Publish Dates" set a date in the future for your widget to automatically be published. 
* "Bulk actions" update multiple widgets at once using bulk options.

= What if I deactivate the plugin? =

This plugin routes around the native widget control structure without affecting any of the code. If you deactivate this Plugin after placing all of your widgets, then the system will snap back to its default logic and your widgets will all display as normal. At any time you can come back and reactivate the Total Widget Control Plugin and it will remember all of your past decisions as well as your new ones.

= I'm building a website that will be administered by an idiot, can they use this software? =

YES. Just show them where the help tab is and also where the support website is, we'll take care of them for you. Our built in help takes advantage of jquery tooltips and will display helpful information to the user as they navigate through the system. You are in good hands my friend.

= Is TWC production ready? =

Yes. We are confident in this release and have installed it on all of our websites. We will still be actively maintaining Total Widget Control, but we will not be releasing versions as often as we have been.

= Who is 5 Twenty Studios? =

5 Twenty is a Portland, Oregon based development firm which specializes in the development of interactive web applications. 100% of our team members are located within the United States and are full-time employees of 5 Twenty. We provide extensive white label development sevices for marketing, branding, and web design firms throughout the world. Working as their inhouse development teams, we have the ability to quickly and effiencently produce high quality applications that meet their client's needs. Interested in working with us? Visit [5Twenty Studios](http://www.5twentystudios.com/ "Outsource in America")

= What if your plugin doesn't offer the features that I need? =

Our development rates are very affordable, couple that with rapid development time and a free pro license and you've got yourself the features that you need. We encourage people to help us invest in this software.


== Screenshots ==

1. This is the main widget management page. Here I have three widgets per page and nearly five pages of widgets being managed. You can see that I have each widget displayed in a different sidebar, each in the first position. From this page I can filter my widgets, search for specific widgets and declare bulk actions to be performed on any number of selected widgets. What you don't see on this screenshot is the quick edit screen that is available for all widgets. It works just like the quick edit for posts.
2. This is the Widget editing page. Because this plugin is designed for hundreds of widgets, you can easily loose track of the widget instances. Total Widget Control adds the admin title and displays that title in the widget listings page. You can also use the search feature to locate a widget by its title. This makes widget management quick and easy. In the publish area, notice the new features for displaying the widget to different kinds of users at and specifically defined time frames. In thw TWC Pro Settings meta box, you'll notice the addition of many new features for pro users, these features add an extra layer of useability to this system.
3. Easily select which pages, posts, categories, or custom listing types your widget will appear on.
4. Quickly choose where on the page your widget sets.
5. The TWC allows you to manage your widgets while editing your pages, now there's no need to bounce around in wordpress just to work on a single page.
6. The widget wrappers allow you to create your own widget styles that can be assigned to any widget instance.
7. The options available to administrators are nearly endless. You can now manage your widgets with as much control as you can manage your pages with.

== Changelog ==

= 1.5.2 = 
* Added localization support

= 1.5.3 =
* Added a Metabox to pages, posts and all custom post types.
* Fixed the registration process
* Fixed quick edit saving
* Fixed quick saving sidebar
* Fixed add new button and page

= 1.5.10 = 
* Was able to fix bugs related to ancient widgets

= 1.5.11 = 
* Updated links
* Added a last sidebar position to the select box
* Stabilized some menu item code

= 1.5.12 = 
* Activation and deactivation remembering has been removed due to errors
* Changed how default displays work
* Upgraded inherited displays to include all taxonomies
* Adjusted the positions select box to autoupdate on changed sidebar

== Upgrade Notice ==

= 1.5.3 =
This is the most stable version to date. All features have been thouroughly tested and are ready for heavy testing.

== 5Twenty Studios ==

[5Twenty Studios](http://www.5twentystudios.com/ "Outsource in America") is a [white label development company](http://www.5twentystudios.com/ "Outsource in America"). Please support us so that we can continue to support you. Our mission is to launch phenominal plugins for Commercial WordPress Developers.

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