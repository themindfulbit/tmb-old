---
title: 'Troubleshooting RSS with NetNewsWire'
categories: [technology, design]
tags: [netnewswire, rss]
banner: netnewswire.jpg
caption: Some days I wonder if RSS is more trouble than it's worth.
---

RSS is an old technology, but it's still the primary way I read things on the web. With the imminent demise of Google Reader more people are paying attention to RSS than they have in a long time. Still, there are a couple of challenges when troubleshooting your own site's RSS feed that can make it a bit of a pain as a developer.

First, a minor change in format often leads to an RSS reset, pushing your last several posts out to your readers again. While this is a regular occurrence for many sites, at one point I had my feed hooked up to Twitter and App.net via [IFTTT](https://ifttt.com/). After tweaking one line of code it blasted out nearly a dozen ADN posts in less than a minute. Luckily I got chastized by a follower for link spam just in time. I managed to kill the recipe before IFTTT got around to updating Twitter.

Additionally, most RSS readers parse your feed using their own internal stylesheets. Figuring out what something will look like in that format often seems like a combination of voodoo and prayer. Google Reader relentlessly caches RSS crawls, so if a broken version of your post makes it there your mistakes might as well be carved in stone.

Despite the widespread use of RSS feeds across the web, I hadn't found many tools for helping you build and troubleshoot one. Then I downloaded [NetNewsWire](http://netnewswireapp.com/mac) and things got a whole lot better.

While much of the web eagerly awaits [Black Pixel's post-Google-Reader overhaul](http://blackpixel.com/blog/2013/03/the-return-of-netnewswire.html), the current version of NetNewsWire has two features that make it great for troubleshooting RSS feeds. 

First, it has its own stand-alone RSS feed capability. It's surprisingly hard to find a Google-Reader-era app that has this feature, since most joined the bandwagon in pursuit of easy sync. You'll need it so you can unsubscribe, and resubscribe to your site's local dev URL every time you make a change to the feed. That will let you avoid the frustrating caching issues that often hide your most recent changes. It also keeps the ugly troubleshooting on your dev machine, rather than blasting it out to the world ten posts at a time.

Also, NetNewsWire has a handy "View XML Source" option in the feed's contextual menu. By looking at the code your site is actually generating, you can find where the ugly parts of your RSS markup are and fix them. I prefer to use full-content feeds. Knowing that my site looks tolerably attractive in most RSS readers is a major plus, since many of those readers will rarely click through to my full site.

RSS is here for the forseeable future, with or without Google Reader. We might as well do our best to make it a pleasant experience for those who use it. Right now NetNewsWire is the best tool I know to make that happen.