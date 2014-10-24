---
title: 'Generalized 301 Redirects'
categories: [technology]
tags: [htaccess, regex, blogging, ruhoh, squarespace]
banner: htaccess.jpg
summary: "I move my blog around to new engines a lot. Unfortunately, nearly every blogging engine has a different way to map post URLs. As a result, a lot of old links can get broken. Here's how I make them work again."
---

I move my blog around to new engines a lot. I think I've been averaging once every three months. Unfortunately, nearly every blogging engine has a different way to map post URLs. As a result, a lot of old links can get broken.

A detailed description of how to use Regular Expressions to robustly redirect an arbitrary URL via an .htaccess file is outside the scope of this site, especially because it's been [done well already](http://searchengineland.com/url-rewrites-and-redirects-part1-16574). But when [Harry Marks](https://twitter.com/hcmarks) asked for help in getting redirects working for the latest iteration of [Curious Rat](http://curiousrat.com), I figured it was time to do some digital housekeeping of my own.

### Ruhoh URL Scheme

###### Input URL
	
	http://themindfulbit.com/2012/11/09/meet-nerdquery/

###### Output URL

	http://themindfulbit.com/blog/meet-nerdquery

###### .htaccess Rule

	RewriteRule ^[0-9]+/?[0-9]+/?[0-9]+/?([^/]+)/?$ /blog/$1 [R=301,L]

All this does is strip out the `/Year/Month/Day` part, replaces it with `/blog/`, and strips the trailing slash.

### Squarespace 5 URL Scheme

###### Input URL
	
	http://themindfulbit.com/articles/2012/11/09/meet-nerdquery.html

###### Output URL

	http://themindfulbit.com/blog/meet-nerdquery

###### .htaccess Rule

	RewriteRule ^articles/?[0-9]+/?[0-9]+/?[0-9]+/?([^/]+)\.html$ /blog/$1 [R=301,L]

This strips out the `articles/Year/Month/Day` part, replaces it with `/blog/`, and strips the trailing `.html`.

### Squarespace 6 URL Scheme

I believe Squarespace 6 uses a slightly different scheme as well. For completeness:

###### Input URL
	
	http://themindfulbit.com/home/2012/11/09/meet-nerdquery/

###### Output URL

	http://themindfulbit.com/blog/meet-nerdquery

###### .htaccess Rule

	RewriteRule ^home/?[0-9]+/?[0-9]+/?[0-9]+/?([^/]+)$ /blog/$1 [R=301,L]

This strips out `home/Year/Month/Day`, replaces it with `/blog/`, and also strips off the trailing slash.

Hit me up on [Twitter](http://twitter.com/themindfulbit) or [App.net](http://alpha.app.net/themindfulbit) if you have any deeper questions on how this stuff works. I don't know a ton, but I'll do my best.