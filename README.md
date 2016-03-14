# TakeOutTrash
This Script Notifies You on Trash Day if You Forgot the Trash, Before it's Too Late!  I work late hours and if I don't remember to take the trash out on Thursday evening before I go to sleep, I'll certainly miss it on Friday mornings when the garbage truck comes.  I don't like to be woken up with a reminder on Friday mornings if I did remember the trash, so I wrote this. :)

The script leverages an IP Camera to take an image of the location where you put your trash can.  On a schedule it takes an image a day before trash day, and on trash day before the garbage truck comes.  It does this so lighting conditions outside should be relatively similar.  The script then crops the images down to see just the can location and calculates the percentage difference between the contrast in the two images.  If the contrast is over a specific threshold you can set, it will notify you that you forgot the trash!  You should set this up to give you enough time to take out the trash if you do get a notification.  Consider setting up a special notification tone for these messages on your phone so it wakes you up!  

# Requirements
  Linux Server (or RaspberryPi-like-device)
  
  IP Camera
  
  Trash Can :)
  
# Instructions
  1) Set up your IP Camera in a stationary location to take an image of your trash can's position on a schedule, one hour before the trash is taken on trash day and another 24 hours before that.
  
  2) Have the IP Camera save these images to your Linux Server, via FTP or whatever method you prefer.
  
  3) Clone this repo to your Linux Server.
  
  4) Configure SMTP settings in trash.php so that email notifications will work. (you could use another method, like an SMS API for notifications)
  
  5) Configure the crop settings in trash.php to narrow down on the trash can.
  
  6) Set up a cron job on your Linux Server to execute the script an hour or so before trash day and enjoy the automation!

You may need to play around with the threshold settings.  Everyone has different looking trash cans!  I included the cropped images with and without a can for comparison.
