---
title: 'More simple Type2Phone and Keyboard Maestro tricks'
categories: [technology]
tags: [type2phone, 'keyboard maestro', productivity]
---
Logitech's recent announcement of a new [bluetooth solar keyboard][1] for Mac and iOS devices got me pretty excited. In addition to being a beautiful keyboard, it features a handy multi-device pairing system which you can use to swap between up to three bluetooth devices.

   [1]: http://www.logitech.com/en-us/keyboards/keyboard/devices/Wireless-Solar-Keyboard-K760-for-Mac

{{ theme:partial src="image" title="Logitech K760" file="logitech-k760.png" }}

This is a great idea and could really help my office "desktop" setup, discussed in an [earlier post][3].

   [3]: http://themindfulbit.com/articles/2012/5/2/use-your-macs-keyboard-to-type-on-your-ipad-with-type2phone.html

{{ theme:partial src="image" title="My Desk" file="desk.jpg" }}

Unfortunately, the new keyboard isn't out yet. Also, I'm a little fond of [my current keyboard's][5] number pad, feel, backlighting, and non-grimy, non-yellowing black color.

   [5]: http://www.logitech.com/en-us/keyboards/keyboard/devices/illuminated-keyboard

While the new keyboard's release is certainly not far away, impatience and dissatisfaction are the mother of invention… or something like that. 

After a moment of thought I realized that I might already have a solution ready-to-go with a combination of [Type2Phone][6] and [Keyboard Maestro][7].

   [6]: http://itunes.apple.com/us/app/type2phone/id472717129?mt=12
   [7]: http://www.keyboardmaestro.com/main/

Luckily for me, the latest version of Type2Phone includes support for basic AppleScript commands, namely `reconnect`, `disconnect`, and `send`. I've now got four commands mapped to F1-F4 that allow me to type on my desktop, iPad, iPhone, or disconnect Type2Phone entirely.

## F1 - Switch to Mac

This command backs out of Type2Phone so I can type on my MacBook Pro (named  Jupiter) again.  All it does is switch to the last application used, which works great if Type2Phone has focus.

{{ theme:partial src="image" title="F1 Settings" file="F1.png" }}

## F2 and F3 - Switch to iPad or iPhone

The next two commands select my iPad (Mars) or iPhone (Mercury) respectively.  The syntax is exactly the same for both with the exception of the hotkey and device name.

{{ theme:partial src="image" title="F2 Settings" file="F2.png" }}

## F4 - Disconnect

If you take your iOS devices away from your desk you'll want to disconnect them from Type2Phone entirely.  This script does that with the simple command `disconnect`.

{{ theme:partial src="image" title="F4 Settings" file="F4.png" }}

So far this setup works great, and Type2Phone has proven speedy and stable in regular use. Obviously if you're looking for an iOS keyboard that will work if your Mac is off or you're on the road, my solution isn't going to help. 

Still, if you're sitting at a machine all day and want to quickly switch between a few devices at minimal expense, you could do a lot worse than try this approach. If you do, let me know how it turns out.