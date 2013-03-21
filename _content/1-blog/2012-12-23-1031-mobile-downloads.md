---
title: 'Mobile Downloads with IFTTT'
categories: technology
tags: [dropbox, bitly, ifttt]
---

Have you ever needed to download a small file but only had an iOS device handy? As long as it's less than 30MB, you can do it by connecting [bit.ly](http://bit.ly) to [Dropbox](http://dropbox.com) with [If This Then That (IFTTT)](http://ifttt.com).

## Setup

You'll need:

* A bit.ly account and [the bit.ly iPhone app](
https://itunes.apple.com/us/app/bitly/id525106063?mt=8)
* A Dropbox account and [the Dropbox iOS app](
https://itunes.apple.com/us/app/dropbox/id327630330?mt=8)
* An IFTTT account

After you've configured these accounts, go to the Channels tab in IFTTT and activate bit.ly and Dropbox there.

{{ theme:partial src="image" file="ifttt-channels.jpg" }}

Then set up a new recipe that looks like this:

{{ theme:partial src="image" file="ifttt-recipe.png" }}

Now any time you send a new public link to bit.ly, IFTTT will try to grab that file and send it to your Dropbox. In my case, I direct them to a folder called "Downloads", but you can send yours anywhere you like.

You can use the bit.ly app to save the link, or any app with bit.ly integration. 

## Caveats

No solution is ever perfect, and this one is no exception. 

First off, this will monopolize your bit.ly account. If you're a heavy user that might be an issue for you. Next, it only pulls from public links. That means anyone can see what you're downloading if they know where to look. Finally, you've got a 30MB limit per link.

This may not do anything for you, but if this happens to scratch your particular itch then have at it.