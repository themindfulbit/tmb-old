---
title: 'Point your naked domain to Dropbox with Site44'
categories: technology
tags: [blogging, dropbox]
---

[TJ Luoma](http://twitter.com/tjluoma) recently posted a [great article](http://tj.luo.ma/articles/dropbox-site44-and-making-your-own-indexes) about how to host your website on [Dropbox](https://www.dropbox.com/home) with [site44](http://www.site44.com/). In his own words:

> Site44 does one thing well: it takes a folder, and it turns it into a website.

> More specifically, it takes a folder in your Dropbox and turns it into a website.

> You can use a free sub-domain on their site (i.e. “tjluoma.site44.com”) or you can use your own domain. All it takes is a simple CNAME in your DNS configuration.

This is a really cool concept, but it has one small limitation. From the [site44 custom domain documentation page](http://www.site44.com/custom-domains):

> CNAME aliases do not apply to "naked" domains like example.com—they only apply to subdomains like www.example.com or blog.example.com. Site44 only supports subdomains; use "forwarding" (see below) to handle naked domains.

> [Optional] Forward your top-level domain to a subdomain. For instance, you might forward example.com to www.example.com. Without this, you may find that your naked domain doesn’t resolve to anything useful.

They've left this forwarding as an exercise to the reader. Here's a couple of ways to do it.

### The easy way

If you've got a decent domain registrar (like [Namecheap](https://www.namecheap.com/)) they'll provide an option that's probably called "URL Forwarding". Here's what that control panel looks like at Namecheap:

{{ theme:partial src="image" file="redirect.png" }}

I recommend you use a [301 Redirect](https://en.wikipedia.org/wiki/HTTP_301). From Wikipedia:

> The HTTP response status code 301 Moved Permanently is used for permanent redirection, meaning current links or records using the URL that the 301 Moved Permanently response is received for should be updated to the new URL provided in the Location field of the response.

Just point the `www` subdomain to `domains.site44.com` and have the root point to `www.yourdomain.com`.

### The hard way

*There's an easier way than this. See the update at the end of the post.*

If your domain is hosted on a less-capable host (like Network Solutions) you can't do 301 redirection from the control panel. That's OK, there's a harder way. It's not that the actual effort is that much more difficult, it's that you'll need a web host for your domain, which removes most of the value of actually hosting your site on Dropbox. 

The reason is that you need to be able to serve up an `.htaccess` file. This is a small text file that lives in the root of your web host's public folder and tells your web server what to do. 

Create a file in the `public_html` folder of your web host called `.htaccess`. Inside that file you'll need three lines of code:

	RewriteEngine on
	RewriteCond %{HTTP_HOST} ^yourdomain\.com$ [NC]
	RewriteRule ^(.*)$ http://www.yourdomain.com/$1 [R=301,L]

Be sure to replace `yourdomain` with... well... your domain. 

Somebody I know forgot to do that once and it took me, uh, *him* about fifteen minutes to troubleshoot.

### Why I'm not doing it right now

After all that work (including a highly successful test) I've decided against hosting my website on Dropbox for the near future.

A minor reason is redundancy. I'm still on Network Solutions (for now), so I need a web host so I don't have to use a web host. That's just a little too redundant for me, especially since my design firm has web hosting solutions coming out of our ears. *(This is no longer necessary. See the update below)*

Other reasons (for me) are cost and bandwidth. The 100MB per month free plan is great, but it won't stand up to many hits. Past that, I've already got a lot of $4.95 per month online services, and many of them are already web hosting plans.

The main reason is the redirect. I'm just not a big fan of those `www` prefixes. There's no crusade against them or anything ([don't tell Dan](http://5by5.tv/buildanalyze/70)). The prefix just seems a little archaic in 2012 and doesn't flow well in my already-long URL.

Still, if your domain registrar supports the 301 redirect natively, and you're not too precious for a `www` in front of your domain name, I'd say go for it. I really like what site44 is doing, and can't wait to see where they go with this next.

### Update - 2012.11.18 8:13 AM

[Steve Marx](http://twitter.com/smarx) (founder of site44) has an [even better way](https://twitter.com/smarx/status/270195981453242368) to skip the `.htaccess` hassle: use [wwwizer](http://wwwizer.com/naked-domain-redirect).