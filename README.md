# phpbb-ext-topic-view-counter
This is a phpbb extension that replaces the topic view numbers on the viewforum page. 
This topic view counter register a new view from a user based on time span set from his last visit of the topic page. Time span can be custom defined. So view number won't increase from spamming.

## Installation

Unzip the files to your phpbb\ext\ directory, standard full path would look like this phpbb\ext\lansingred\topicviewcounter\

Go to ACP on your forum, then go to Customize->Manage extensions and enable Topic View Counter extension

## screenshots
### before
![before](https://user-images.githubusercontent.com/10624724/53447957-51d8cc00-39e4-11e9-84fa-a3d43cdeda3a.jpg)

### after
![after](https://user-images.githubusercontent.com/10624724/53447992-674df600-39e4-11e9-8eee-e85c63382a0b.jpg)

### Important Notes: 
This extension will stores IP addresses from guests and view time from registered users, board owners please take that into consideration with their GDPR compliance.
