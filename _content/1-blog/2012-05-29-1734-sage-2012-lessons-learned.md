---
title: 'Sage 2012 Lessons Learned'
categories: [design, technology]
tags: ['responsive design', sass, compass, typekit, takitapart, svg, mobile-first]
---
{{ theme:partial src="image" title="Sage White Theme" file="sage.png" }}

I recently completed a [website redesign][2] for one of our clients in advance of a significant expansion in their service footprint. 

   [2]: http://sagelpg.com

The site took the company branding in a new direction and grew out of some concepts we'd been kicking around for the last year or so. The overall look aims to be open, light, modern and friendly.

## Tools Used

This was my second stab at building a responsive site. Like my previous attempt it demonstrated a few points:

  * Each screen-width break point gives you an opportunity to design a whole new site, with new layout challenges and usability decisions
  * Various browsers respond to a responsive site in various ways
  * Some browsers don't respond at all

My take-away is this:

> Assume that going responsive means you're going to design about ten variants of any theme and you'll be OK.

The following list is a partial rundown of our tools. Others will be discussed in more detail below.

  * The [Takitapart CSS Framework][3]
  * All text but the logo set in [Mark Simonson's][4] [Proxima Nova][5] from [Typekit][6]
  * [Compass][7] and [Sass][8]
  * [Conditional Comments for IE][9]

   [3]: http://takitapart.net/framework
   [4]: http://www.ms-studio.com/bioresume.html
   [5]: http://www.marksimonson.com/article/118/proxima-sans-going-nova
   [6]: http://typekit.com
   [7]: http://compass-style.org
   [8]: http://sass-lang.com
   [9]: http://msdn.microsoft.com/en-us/library/ms537512(v=vs.85).aspx

## Responsive Media

The biggest challenge I've run into so far when designing responsive sites is ensuring that videos and images scale properly for various screen widths. Two short bits of JavaScript have proven extremely helpful:

### Slideshow

This one requires jQuery so maybe it's not so short, but at least the plugin itself is less than 1KB. 

[Responsiveslides.js][10] by [Viljami Salminen][11]

   [10]: http://responsive-slides.viljamis.com/
   [11]: http://viljamis.com/

It's the nicest, most flexible responsive slideshow solution I've found. 

From the description:

> ResponsiveSlides.js is a tiny jQuery plugin that creates a responsive slider using list items inside `<ul>`. It works with wide range of browsers including all IE versions from IE6 and up. It also adds css max-width support for IE6 and other browsers that don't natively support it. Only dependency is jQuery (1.4 and up supported) and that all the images are same size.
> 
> Biggest difference to other responsive slider plugins is the file size (1kb minified and gzipped) + that this one doesn't try to do everything. Responsive Slides has basically only two different modes: Either it just automatically fades the images, or operates as a responsive image container with pagination and/or navigation to fade between slides.

The host site is beautifully designed and provides both code samples and well written instructions. I highly recommend it.

### Video

The responsive video script comes from [an article][12] by [Chris Coyier][13] for .Net magazine. Rescaling video dynamically without altering its aspect ratio was beyond my abilities, but this script provides an elegant solution.

   [12]: http://www.netmagazine.com/tutorials/create-fluid-width-videos
   [13]: http://chriscoyier.net/

## Scalable Vector Graphics (SVG)

Alongside responsive design, the rise of high DPI mobile displays has reinforced the need for images that work at a variety of scales. With the help of the [Vector Doodlekit][14] by Petr Vlk I decided to try my hand at using Scalable Vector Graphics. 

   [14]: http://doodlekit.imagiag.com/

Since many of my graphics are rollovers, I decided to place most of them using the CSS `background-image` property. This also allowed me to replace them with PNGs for older versions of Internet Explorer using IE specific CSS called via the aforementioned [conditional comments][15].

   [15]: http://msdn.microsoft.com/en-us/library/ms537512(v=vs.85).aspx

### Browser support

Webkit (Chrome and Safari on Mac and iOS), Firefox, and Internet Explorer 9 do a surprisingly good job of supporting SVG. I had to explicitly set `background-size` for all images (and their hover equivalents) to maintain a consistent presentation across these three browsers, so if your design breaks that's a good place to start checking for bugs. 

Internet Explorer 8 and below don't support SVG at all, so I used Illustrator to export traditional PNGs. I'm not incredibly worried about responsive window scaling or retina-quality displays for these browsers, so I picked a fixed size and went with it.

## Conclusions

I'm still happy with responsive, mobile-first development despite the additional workload. I'll definitely be using SVG more in our future designs. Not only was browser support better than I expected, but the workarounds for older browsers were straightforward. 

Many of these techniques are still new to me, so any critiques, comments, or suggestions on how to improve this or any of the other sites I've developed would be greatly appreciated.