---
title: 'Ruhoh, I Rebuilt the Blog Again'
categories: [design, technology]
tags: [blogging, hazel, textastic, ruhoh, dropbox, swiftype, squarespace]
---
You read that right. For the fourth time in 14 months, The Mindful Bit has been rebuilt and moved to a new host. Why did I do it? How did I do it? When will the madness stop? Read on for the whole story.

## The Why

For the last several months I’ve hosted the blog on [Squarespace](http://squarespace.com). It’s a great publishing platform offering [heroic support](http://techcrunch.com/2012/11/01/squarespace-fog-creek-peer1-kept-ny-data-center-alive-by-carrying-fuel-buckets-to-the-17th-floor-in-the-dark/) at a [professional’s price](http://www.squarespace.com/pricing/). They’ve been iterating like crazy and are already launching version 6 with some significant improvements for developers.

I admire the way Squarespace does business, but I don’t think the service is the right fit for me. Here are a few reasons.

First, I'm already paying for hosting elsewhere as part of my day job, and it's tough to justify paying nearly $200 a year for a blog hosting service I could provide myself. This is doubly true since I've only written 73 posts in the last year. I'm not sure I've proved that I can write content that's worth more than $2 a post.

Additionally, I'm trying to move away from a "web form" based workflow. Squarespace offers some nice looking iOS apps, but until recently they didn't work well on iOS 6. Web design in an online form is problematic, even more so when you're trying to swim upstream like me. 

<p class="has-pullquote" data-pullquote="The possibility of writing a post in Markdown, saving it to Dropbox and having it automagically appear on my blog was too tempting to pass up.">For example, I use Compass and Sass in my design workflow. In order to make a minor CSS change, I had to write the Compass/Sass code locally, compile it on my machine, then go through three or four clicks on a web form to upload that code and check it out in place. This quickly became cumbersome.</p>

I didn't have a real need for comments, so losing them wasn't a huge worry for me.

Above all, I wanted deep control over the look, feel, and content of my site. Squarespace v6 offers that level of control, but if I was going to have to rebuild the site from scratch anyway then all bets were off as far as platform was concerned. 

I'd originally considered a switch back to Tumblr for sheer simplicity's sake, until [Gabe Weatherhead](http://macdrifter.com) planted an insidious suggestion in my brain:

<blockquote class="twitter-tweet" data-in-reply-to="255813722499596288"><p>@<a href="https://twitter.com/themindfulbit">themindfulbit</a> Static blog! (kidding. If tumblr works for you, it's pretty low overhead)</p>&mdash; macdrifter (@macdrifter) <a href="https://twitter.com/macdrifter/status/255814144178155520" data-datetime="2012-10-09T23:36:59+00:00">October 9, 2012</a></blockquote>
<script src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

The possibility of writing a post in Markdown via the text editor of my choosing, saving it to Dropbox and having it automagically appear on my blog was too tempting to pass up. Web forms are great, but if you're just copying and pasting text you've written elsewhere, why not cut out the middle man?

With the gauntlet cast down, I set off on a one month crusade to save three seconds and five clicks per post.

## The How

In a random survey of static blogging systems, I came across [Ruhoh](http://ruhoh.com). Authored by [Jade Dominguez](http://twitter.com/ruhohBlog) of [Jekyll Bootstrap](http://jekyllbootstrap.com/) fame, Ruhoh is a young but surprisingly robust ruby-based static blog generator.

Ruhoh's goal is to offer a universal, platform-agnostic static blog API. I like how it uses mustache templating and incorporates tag-based blogging without plugins.

As expected with any new and growing application, there are some quirks and limitations. Pagination isn't implemented, and the RSS feed required a little bit of tweaking. I'm still trying to figure out why smart quotes aren't working. But the core feature set meshed very well with my requirements, so I dived in.

Static blogs usually need a creative solution for site-search, and I've settled on [Swiftype](http://swiftype.com) for now. Their service is free while in beta, and they'll switch to a traffic-based pricing model once launched. I like their interface and the easy way you can have your site recrawled on demand. Their widgets styled easily and were simple to add to my site. I'll let you know more after I've used the service for a while.

The Ruhoh application itself runs on a Mac mini in my office, and the raw files live in Dropbox. The Mac mini offers me direct control over my ruby installation (1.9.3 running on rvm) and I like keeping a local copy of everything. Dropbox lets me write posts or tweak the site from any of a dozen different text editors on both of my Macs, my iPad or iPhone. 

As a static blog, Ruhoh has to compile my site into flat HTML files, then put them somewhere that people can read them. My solution uses a combination of [Textastic](http://www.textasticapp.com/), [Hazel](http://www.noodlesoft.com/hazel.php) and `rsync`.

First I needed a way to trigger the whole process. I wanted something that didn't require me to connect directly to my Mac at home, since dynamic DNS is a bit of a pain. My solution was to have Hazel watch the site's core folder (which lives in Dropbox) for a text file named `aaa.txt` to appear. 

As soon as that file pops up, Hazel runs the following shell script:

	source ~/.bash_profile; 
		# Load all the appropriate PATH info into the subshell
	cd /Users/myusername/Dropbox/tmb; 
		# Change the working directory
	ruhoh compile; 
		# Generate the static site
	rsync -a -e ssh compiled/* myusername@themindfulbit.com:public_html;
		# Sync to the remote version of the site
	mv aaa.txt log/$(date +%Y-%m-%d-%H%M).txt
		# Move the trigger file out of the way and rename it with a timestamp so I can keep track of the last time the script ran

*By the way, Ruhoh offers code highlighting via [Google Prettify](https://code.google.com/p/google-code-prettify/). As you can see, bash highlighting leaves a bit to be desired.*

I really like Textastic on iOS, and its Dropbox integration gave me just the tool I needed to trigger the script.

{{ theme:partial src="image" title="The Trigger" file="aaa.jpg" }} 

I keep an empty local file named `aaa.txt`, then select and upload it to the Ruhoh root in Dropbox whenever I need to update the blog. Hazel and `rsync` take care of the rest.

## When will the madness stop?

I don't know. I had a lot more fun than expected rebuilding the blog, and this is by far my favorite layout and publishing workflow.

{{ theme:partial src="image" title="The Chart" file="geeks-vs-nongeeks-repetitive-tasks.png" }}

I'm still nowhere near the "geek wins" stage of [Bruno Oliveira's](https://plus.google.com/102451193315916178828/posts/MGxauXypb1Y) classic chart, but I think it was worth it. We'll know more when I redesign the site again early next year.

KIDDING!