# RB Agency Interact
Allows profiles to self regsiter and manage their information.

### Current Version 2.4.2


## Change Log

### 2.4.2
* created "RB Login Widget" widget which you can add to the sidebar. This widget will show the "Log Out" if user is logged in.
* fixed - blank page upon registration
* fixed - Login Settings > Redirect first time users - i set it to "Redirect to /profile-member/account/" but when I tried to login for the first time, I am still redirected to /profile-member/
* created a setting similar to models where site admin can redirect casting agents to another url. They want they clients to be * able to register but not view the /casting-dashboard/
* fixed - html codes in email notifications
* if "Default Profile Status" setting is "Change the status to "pending approval" whenever a profile is updated" - admin is sent an email notification when a users edited their profile

### 2.4.1
* fixed - Username Self-Generated Password Auto-Generated - username is being converted into random characters or letters e.g. rbagencytalent becomes demo_2c5rkin list Core Issues 
