## Mail Address Manipulator

This plugin require https://github.com/mautic/mautic/pull/7977. This PR add event on contact save.   

### Company domain sync
- require enabled service and field to sync domain/domains blacklist in plugin settings 

### How it works:

#### On company save 

- ##### Sync company address to contact

Loop through all company contacts and sync address to contact based on plugin settings.

#### On contact save

##### Sync contact domain to company 
 
    - if primary company domain field not empty
    - grab domain from contact email address (for example microsoft.com) and If it's most used domain from all contacts of company, then sync domain to domain field
    
##### Sync contact address to company address

Loop through all company contacts and sync address to contact based on plugin settings.

 

### Icons

<div>Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a>