# AlmaInventory
This code will facilitate an inventory of items cataloged in the Alma integrated library system.

- Physical items are handled one at a time and scanned with a barcode scanner
- The user can press a button to indicate if there is a mismatch in the title, volume or call number of the physical item
- A request is sent to the Alma Bib API to retrieve known information for that Barcode
  - To avoid cross-origin restrictions, a PHP service is used to call the Alma API and to add the Alma API key
  - This service simply appends the api key to the request and returns the json object provided from the Alma API
- Results are displayed in a table with common errors highlighted
- Optionally, the user can upload results of a scanning session to Google sheets

## Demonstration Video
This video demonstrates a barcode scanning inventory workflow developed by the Georgetown University Library.  

[![Demonstration Video](https://i.ytimg.com/vi/PW_hdI65h10/hqdefault.jpg)](https://www.youtube.com/watch?v=PW_hdI65h10)

## Pre-requisites

### Pre-requisites (Alma)

The Alma Bib API will be used. https://developers.exlibrisgroup.com/alma/apis/bibs

- Obtain a login for the Alma Developer Network
- Create a Read-only API key for the Bib API
  - If you do not have an institutional instance, you can [test the application using the Alma Sandbox](almaApi.md)
- Copy `local.prop.template` to `local.prop`
  - For production deployment, save this file to a location that is not web accessible.
  - The path to this file is set in a separate property file.
- Add your api key to `local.prop`

### Pre-requisites (Docker)
This application has been published to Docker Hub.  
- https://hub.docker.com/r/terrywbrady/alma-inventory-php
- https://hub.docker.com/r/terrywbrady/alma-inventory-node
- https://hub.docker.com/r/terrywbrady/alma-inventory-jetty

Install [Docker for Windows or MacOS](https://www.docker.com/get-started) to test this process.

Clone this repository to your desktop and follow the instructions below to configure the application with your Alma credentials.

From a terminal window
- cd to the directory containing these files
- run docker-compose up -d to start the service you choose
  - `docker-compose -f docker-compose.php.yml up -d`
  - `docker-compose -f docker-compose.node.yml up -d`
  - `docker-compose -f docker-compose.jetty.yml up -d`
  - Docker compose will pass your local.prop file to the correct location.
- run docker-compose down to stop the service
  - `docker-compose -f docker-compose.php.yml down`
  - `docker-compose -f docker-compose.node.yml down`
  - `docker-compose -f docker-compose.jetty.yml down`

Open http://localhost/barcodeReport.html to test the program.
- Click "Add Barcode"
- Using a barcode scanner or the keyboard, enter a 14 digit barcode
- If your API has been correctly enabled, you will see results returned for the item

### Pre-requisites (Google Sheets)

Deploy a web service in Google Drive to save CSV data into a Google Drive Folder
- See https://github.com/Georgetown-University-Libraries/PlainTextCSV_GoogleAppsScript
- Copy `gsheet.prop.json.template` to `gsheet.prop.json`
- Add the address of your web service and the folder ids for a test and production inventory to the json file

Restart the docker service `docker-compose restart` to refresh the resources.
- View http://localhost/gsheet.prop.json to verify that your changes are in place.
- Clear your cache and refresh the file if needed

The following code illustrates how to collect metrics for a set of inventory spreadsheets
- [Inventory Statistics](stats/README.md)

## Configuration Files

| Purpose | Server Type | Default File Location | Note |
| ------- | ----------- | --------------------- | ---- |
| Store Alma API Key | All | /var/data/local.prop |This file should not be web accessible|
| Set path to local.prop | Jetty | jetty/prop.jsp | JSP code file|
| | Node.js| node/prop.js | Node.js code file |
| | PHP | php/Alma.prop | PHP prop file format |
| Set client side properties | All | */barcode.init.js | Alma API URL is set for all instances |
| | All | */barcode.init.js | Location validation regular expression, barcode validation regular expression |
| | Jetty | jetty/barcode.init.js | Alma requests are pre-processed by inventory/redirect.jsp|
| | Node.js | node/barcode.init.js | Alma requests are pre-processed by redirect.js |
| | PHP | php/barcode.init.js | Alma requests are pre-processed by barcodeReportRedirect.php |
| Set Google Drive Upload Properties | All | gsheet.prop.json | Save gsheet.prop.json.template to gsheet.prop.json note that these values will be visible to the client app.|

## Docker Build Options
If you wish to build a customized instance of this Docker image, the following commands can be used.
- `docker build -t terrywbrady/alma-inventory-php -f Dockerfile.php .`
- `docker build -t terrywbrady/alma-inventory-node -f Dockerfile.node .`
- `docker build -t terrywbrady/alma-inventory-jetty -f Dockerfile.jetty .`

## PHP Runtime
`Dockerfile.php` builds from the official `php:8.5-apache` image.
- The tag tracks the latest PHP 8.5 patch release. Rebuild the image to pick up PHP security fixes.
  - PHP 8.5.12 or newer is required (fixes CVE-2026-109618 / CVE-2026-109619).
  - PHP 8.5 receives security support until 2029-12-31. See https://www.php.net/supported-versions.php
- The PHP code (`php/*.php`) is compatible with PHP 8.2 through 8.5.
- The image enables PHP's `php.ini-production` settings:
  - `display_errors` is off, so PHP warnings and deprecations are never written into API responses.
    - The PHP proxy returns raw JSON to the browser. Any extra output corrupts it, and the app reports `Connection Error`.
  - Warnings and errors are logged to the container output (`docker logs`). Deprecation notices are not logged.

### Troubleshooting `Connection Error`
The browser shows `Connection Error` whenever the PHP proxy response cannot be parsed as JSON, not only on network failures.
- Open `php/barcodeReportRedirect.php?apipath=...` directly (or check the response body in browser DevTools) to see the raw response.
- An XML `UNAUTHORIZED` response means the Alma API key in `local.prop` is missing, expired, or lacks Bib API access.
