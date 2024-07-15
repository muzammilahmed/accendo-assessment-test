## Software Engineer Technical Test

This is an technical test assessment application by [Accendo](https://www.accendotechnologies.com/) to demonstrate good coding practices. 

#### Quick Start:

* Install [PHP](https://www.digitalocean.com/community/tutorials/how-to-install-php-8-1-and-set-up-a-local-development-environment-on-ubuntu-22-04) if you don't already have it.
* Clone or [download](https://github.com/7ep/demo/archive/master.zip) this repo.  (if you download, unzip the file to a directory.)
* Install [Composer](https://www.cherryservers.com/blog/how-to-install-composer-ubuntu) to run Laravel by following few steps.
```
$ curl -sS https://getcomposer.org/installer -o composer-setup.php
$ sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
$ sudo composer self-update
$ composer -v
$ composer update
```
* Laravel packages and dependencies are installed. Time to run the application
```
$ php artisan serve
```
* The application will run at http://localhost:8000/

#### Summary:

The application has two RESTful endpoints which has the responsibility to upload the entire organizational chart and update any record by uploading CSV.

* Import the [Postman Collection](https://orolia-prisma-c2.postman.co/workspace/My-Workspace~b1291353-acc3-401d-8485-d1bce8eb51b0/collection/2914555-74a39893-53fb-4cf2-b405-d02a0854b9bb?action=share&creator=2914555&active-environment=2914555-12d97412-c34a-4997-98a7-850dd6a56c42) on local. There're two endpoints in this collection.
* Also import the environment into postman [Postman environment](https://orolia-prisma-c2.postman.co/workspace/My-Workspace~b1291353-acc3-401d-8485-d1bce8eb51b0/environment/2914555-12d97412-c34a-4997-98a7-850dd6a56c42?action=share&creator=2914555&active-environment=2914555-12d97412-c34a-4997-98a7-850dd6a56c42)
* Here's the endpoints which are used in this project.
  * __POST api/v1/upload-full-csv__ -> this will upload entire organization chart on S3 bucket.
  * __POST api/v1/upload-updated-csv__ -> this will update the record into organization chart on S3 bucket.
* This application are responsible to upload CSV files on S3. So no database is involved in it.
---

#### API - /api/v1/upload-full-csv
* It is RESTful endpoint with __POST__ method as shown on postman collection. 
* Endpoint has only one parameter which take file as an input.
   * __Parameter__: organizationFile
* The breakdown structure of the endpoint is.
   * Request pass through middleware which has multiple validation checks.
   * Takes CSV file as an input and upload it on S3 bucket.



#### API - /api/v1/upload-updated-csv
* It is RESTful endpoint with __POST__ method as shown on postman collection. 
* Endpoint has only one parameter which take file as an input.
   * __Parameter__: organizationFile
* The breakdown structure of the endpoint is.
   * Request pass through middleware which has multiple validation checks.
   * Takes CSV file as an input.
   *  Download existing organization chart csv file from S3.
   * Process the entire organization file csv.
   * Process uploaded csv
   * Update the latest changes from uploaded csv into existing organization chart csv
   * Create new organization chart csv
   * Upload it to S3

### Route Middleware
There is route middleware which validates multiple checks which are as follow.
* File parameter exist
* File only be CSV
* Size of the file

### Error Messages
There are several error messages are handled in the application.

~~~
| Error Type                | Error                                                                   |
|:--------------------------|:------------------------------------------------------------------------|
| file_not_exist            | CSV file is missing from parameters                                     |
| file_size                 | The file size is too large.                                             |
| file_type                 | Invalid file format. Please upload .csv format files.                   |
| failed_upload             | File upload failed.                                                     |
| file_empty                | The file is empty.                                                      |
| master_file_download      | Failed to download original organization file.                          |
| create_new_csv            | Failed to created new csv.                                              |
~~~