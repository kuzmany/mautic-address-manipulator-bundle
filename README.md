## Mail Address Manipulator

This plugin require https://github.com/mautic/mautic/pull/7977. This PR add event on contact save.   

## Installation

### Manual

1. Use last version
2. Unzip files to plugins/MauticAddressManipulatorBundle
3. Clear cache (app/cache/prod/)
4. Go to /s/plugins/reload

## Usage

1. Go to Mautic > Settings > Plugins
2. You should see new Address Manipulator
3. Enable it
4. Enable feature what you want to use

### Debug mode

Enable debug mode add logs about sync to System Info > Log

### Company domain sync
- require enabled service and field to sync domain/domains blacklist in plugin settings 

### How it works:

#### On company save 

- ##### Sync company address to contact

![image](https://user-images.githubusercontent.com/462477/67880786-9900f900-fb3f-11e9-9a09-bc433ab3261d.png)

Loop through all company contacts and sync address to contact based on plugin settings.

#### On contact save

##### Sync contact domain to company 

![image](https://user-images.githubusercontent.com/462477/67880841-bcc43f00-fb3f-11e9-8dc8-29c91e1239db.png)
 
    - if primary company domain field not empty
    - grab domain from contact email address (for example microsoft.com) and If it's most used domain from all contacts of company, then sync domain to domain field
    
##### Sync contact address to company address

![image](https://user-images.githubusercontent.com/462477/67880820-b209aa00-fb3f-11e9-9b89-1a0ce93c8cd4.png)

Loop through all company contacts and sync address to company based on plugin settings and own logic criteria.
 
### Icons

<div>Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a>