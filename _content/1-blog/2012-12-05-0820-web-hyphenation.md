---
title: 'Web Hyphenation Lives'
categories: [design, technology]
tags: [typography, compass]
---
Inspired by a [recent article](http://www.webmonkey.com/2012/11/better-web-typography-with-css-hyphens/) at Webmonkey, I've now added hyphenation support to this site, along with justified body text. What follows is a bit about what CSS Hyphenation is, why it's nice to have, and how to implement it yourself.  

## CSS Hyphenation

Hyphenation works to decrease excessive white space in the lines of narrowly justified text. The [CSS3 working draft](http://www.w3.org/TR/css3-text/#hyphenation) offers two properties that affect hyphenation: `hyphens` and `word-wrap`. 

<p class="has-pullquote" data-pullquote="Hyphenation is a holdover from print typesetting, where it was used to prevent rivers of white space in narrow, tightly packed columns of justified text">The <code>hyphens</code> property is fairly self-explanatory, and includes a <code>manual</code> setting that lets you hand-specify "soft hyphens" using the Unicode character <code>U+00AD</code>. The <code>word-wrap</code> property lets your browser decide to break a long string that normally wouldn't fit, at an arbitrary point if necessary.</p>

Right now it looks like Firefox, IE 10 and Safari (including iOS) support the hyphenation spec. Chrome [claims to support it](http://blog.chromium.org/2012/11/a-web-developers-guide-to-latest-chrome.html) in version 24 beta, but it hasn't been working for me. 

The good news is that unsupported browsers fall back to non-hyphenated text. The bad news is that you'll probably still be stuck with justified text unless you do some browser sniffing.

One important caveat ([pointed out](http://www.quirksmode.org/blog/archives/2012/11/hyphenation_wor.html) by [Peter-Paul Koch](http://twitter.com/ppk) at [QuirksMode](http://www.quirksmode.org)) is that you'll have to explicitly declare your language, so the browser knows what break dictionary to use. This is done in the `html` tag like so: 

	<html lang="en">

## Why Bother

You probably shouldn't. 

Hyphenation is a holdover from print typesetting, where it was used to prevent rivers of white space in narrow, tightly packed columns of justified text. It's still useful anytime you're using text in columns, especially with grid-type layouts, but I wouldn't say it's strictly required for anything on the web.

My grid-heavy site lends itself to a throwback-style of typography (or at least I think it does) so I'm going to give it a shot for a while and see how it feels a few months from now.

## Implementation

The bare CSS to support hyphenation looks like this:

	p { 
		-webkit-hyphens: auto;
    	-moz-hyphens: auto;
    	-ms-hyphens: auto;
    	-o-hyphens: auto;
    	hyphens: auto;
	}

The Opera prefix doesn't do anything right now, but it's there in case it's needed later. As always, [Compass](http://compass-style.org) offers [a handy mixin](http://compass-style.org/reference/compass/css3/hyphenation/) to do it all in an abbreviated manner (although without `-ms` or `-o` support)

First you've got to import the plugin:

	@import "compass/css3/hyphenation" 

Then you apply the mixin to your element:

	p {
		@include hyphens(auto);
	}

That generates:

	p {
		-moz-hyphens: auto;
		-webkit-hyphens: auto;
		hyphens: auto;
	}

Even though I use Compass on my site I'm still doing it manually to ensure the `-ms` and `-o` tags get in there.

<h3 class="references">References</h3>

* [Exercise Better Web Typography With CSS Hyphens](http://www.webmonkey.com/2012/11/better-web-typography-with-css-hyphens/) by Scott Gilbertson at [Webmonkey](http://www.webmonkey.com/)
* [Hyphenation Works!](http://www.quirksmode.org/blog/archives/2012/11/hyphenation_wor.html) by [Peter-Paul Koch](http://twitter.com/ppk) at [QuirksMode](http://www.quirksmode.org)
* [Compass Hyphenation](http://compass-style.org/reference/compass/css3/hyphenation/) from the [Compass](http://compass-style.org/) documentation site
