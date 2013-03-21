---
title: "Use your Mac's keyboard to type on your iPad"
categories: [technology]
tags: [type2phone, 'keyboard maestro', productivity]
---
{{ theme:partial src="image" title="My Desk" file="desk.jpg" }}

[Type2Phone][2] is an app that allows you to use your Mac's keyboard to type on iOS devices via Bluetooth. It even lets you paste your Mac's clipboard to your iPad with a special hotkey (Cmd-Shift-V).

   [2]: http://itunes.apple.com/us/app/type2phone/id472717129?mt=12

The app works great and typing is quite responsive. You can pair your Mac with multiple iOS devices and the process is relatively straightforward. 

So far the only downside is that the window is larger than it needs to be - 880 pixels wide at a minimum:

{{ theme:partial src="image" title="Normal Window" file="normal.jpg" }}

You can hide your text with "Stealth Mode", which replaces all the keys with pictures of a lock. 

I prefer "Collapsed Mode" which removes the keys altogether. The main bar stubbornly remains:

{{ theme:partial src="image" title="Collapsed Window" file="collapsed.jpg" }}

I wanted to be able to switch quickly between typing on my Mac and typing on my iPad, so I set up a [Keyboard Maestro][5] macro to launch Type2Phone or bring it into focus when necessary.

   [5]: http://www.keyboardmaestro.com/main/

{{ theme:partial src="image" title="Macro" file="macro.jpg" }}

To return keyboard focus to the Mac, just trigger Spotlight (or the alternate launcher of your choice - I use [Alfred][7]) and you're right back.

   [7]: http://www.alfredapp.com/

[Type2Phone][8] is $4.99 on the App Store. [Keyboard Maestro][9] is $36 from the developer or $35.99 on the App Store.

   [8]: http://itunes.apple.com/us/app/type2phone/id472717129?mt=12
   [9]: http://www.keyboardmaestro.com/main/

For an awesome introduction to Keyboard Maestro, I highly recommend listening to [Macdrifter's][10] appearance on [Mac Power Users Episode #64][11].

   [10]: http://www.macdrifter.com/
   [11]: http://5by5.tv/mpu/64
