# MapSeven.Piwik
Simple Piwik Integration into Neos CMS

## Installation
`composer require mapseven/piwik`

## Features
This Package contains currently:

* A Neos CMS Module to manage your Piwik Sites
* Integration of the Piwik Tracking Code by adding your Piwik Settings to Document NodeType Properties

### Neos CMS Module
Add Host and Token of your Piwik Installation and press the Save Button.
After the Settings have been saved to the Global Configuration you can manage your Piwik Sites on the right.
To create a new Site just fill in the Name and Url of your Neos CMS Site and remember the newly created ID.

![Piwik Module](/Module_Piwik.png "Neos CMS Module to manage your Piwik Sites")

### Integration of the Piwik Tracking Code
Add Host and ID of your Piwik Site to the NodeType Properties of your Startpage and the Tracking Code is included to all Sites of your Neos CMS Project.
By default only Sites without Backend Login are tracked.

![Piwik Property](/NodeType_Piwik.png "Piwik Property for Document NodeType")

## License
MapSeven.Piwik is licensed under the [MIT Licence](LICENSE)

