<h1 align=center>
  <img src="https://github.com/LarraPress/blog-poster/blob/master/public/assets/img/logo_trans.png" width=300px>
  <br>
  LarraPress BlogPoster
</h1>
<h3 align=center>
  Autoscraping from third party sources, automatically posting to DB, downloading media files to your storage!
</h3>

[![Latest Version on Packagist][ico-version]][link-packagist]
[![StyleCI][ico-styleci]][link-styleci]
![TESTED OS](https://img.shields.io/badge/Tested%20OS-Linux-brightgreen.svg)
[![Total Downloads][ico-downloads]][link-downloads]
![](https://komarev.com/ghpvc/?username=larra-press-blog-poster&label=Repo+views&color=brightgreen&style=flat-square)

# About Package
This package was developed by [Alexey Khachatryan](https://github.com/alkhachatryan) for personal usage, but then author decided to make it public package for world usage and created [LarraPress Project](https://github.com/LarraPress) 
This project will help developers to create powerful blogs and use third party  package for better blog owning.
Then meaning of this package is to scrape articles from third party sources and post on your blog. There are many things to add and fix, because this is in Alpha version. Feel free to report bugs, ask questions and create PRs.

So far this package has the following features:
- Scrape posts from third party resources
- Downlaod selected media files
- Remove useless elements from scraped articles
- Work with lazy-loaded media files by replacing HTML tag attributes values
- Detect duplications
- Scrape multi-value elements such as article tags
- Create thumbnails
- Test the job before publishing

# Installation

```bash
composer require larra-press/blog-poster
```

# Configuration

### Publish package assets
```bash
php artisan vendor:publish --tag=larrapress-blog-poster
```

### Add routes
```php 
LarraPress\BlogPoster\Facades\BlogPoster::routes();
```
ATTENTION! These routes MUST to be added under some **auth** middleware to prevent everybody to edit your blog poster.
For example:
```php
Route::middleware('auth', function (){
    LarraPress\BlogPoster\Facades\BlogPoster::routes();
});
```

### Run migrations, create required tables
```bash
php artisan migrate
```

### Create your scraping job
You can create one scraping job class for all jobs you'll create or have different job classes for each of your scraping job.
Creating a scraping job class which will work for all of yours scraping jobs
```bash
php artisan make:scraping_job ScrapingJobName
```

Or you can create a separated job special for CNN or whatever you want
```bash
php artisan make:scraping_job ScrapingCNNSource
```
No matter how you call them, but how you use them.

# Queues
As website scraping job takes some time to finish we use laravel queues for proper work.
If you don't want to use the queues you can override parent ScrapingJob class: \LarraPress\BlogPoster\Jobs\ScrapingJob and remove queueable traits and interfaces.



# Setting Up your first scraping job
ScrapingJob classes handle ScrapingJobModel with all configs. To create your scraping job, go to dashboard.
The URL of the dashboard depends on how and where you put its routes. If you not sure where are they kidly run this command:
```bash
php artisan route:list # on UNIX machines you can filter by adding "| grep blog-poster" without quotation marks
```

1. Click on Add New Job button
![image](https://user-images.githubusercontent.com/22774727/131214205-35a46ff7-38d5-4ae3-b9a7-29c2b30d5021.png)

2. Fill Job Properties Form
![image](https://user-images.githubusercontent.com/22774727/131214265-85796f37-6028-45e9-98a6-685027dbd374.png)

* **Name** - the name of the source, it's a hint just for you
* **Source** - the full URL of the web page where the articles/posts are. The list of posts
* **Icon** - the icon of the source. You can manually put some icon URL here or click on PARSE button to fetch it
* **Identifier In List** - the selector of single post in the list. You need to put a selector of anchor
* **Category** - tell the system in which category you want to post the articles came from this source
* **Daily Limit** - some of the source posts a lot of articles. You can set a daily limit for this source
* **Is Draft** - the status of the scraping job. Useful when you do some tests or decided to pause scraping from this source

3. Add New Attribute
![image](https://user-images.githubusercontent.com/22774727/131214380-d52ebab3-97b7-47db-9c6a-59473da85bcb.png)

Each post/article has title, body, image(with thumb), tags and so on. We call that elements here Article Attribute.
If you want to parse titles, bodies and images you need to create 3 Article Attributes.

In this box you can see 3 tabs:

**Attribute Main Configs** - the basis of the information about attribute.
It contains: 
* As Thumbnail - if you set a selector to some image and want to make it a thumbnail - enable it. Note that the real file will not be downloaded. To have both of full image and thumb you need to create two Article Attributes
* Is File - let Crawler know that it must to download the content of the selector
* Is HTML - this is usefull for articles bodies where you can get comments in HTML or other bad staff
* Attribute Name - this name will be processed with a Crawler and then passed to the ScrapingJob class where you can play with it. It'll be the index of the attribute.
* Attribute Selector - the CSS selector of the attribute
* Attribute Type - There are 3 types so far: array, URL and default. If you want to scrape and image or some file, set the type to URL. By that way you tell Crawler that it's a URL. Sometimes there can be not full URL like this: /path/to/image.jpg If you want to scrape article tags (there are many tags) use array type. By this way you tell Crawler that there are many elements in the article with this selector and all of them must to be scraped
* Custom Tag Attribute - There are lazy loading in modern blogs. So the real URL of the media will not be in SRC attr, but, let's say, in SRCSET. Set **srcset** here to get URL from different attr.

**Ignoring Elements**
![image](https://user-images.githubusercontent.com/22774727/131214667-6c97a419-ce8d-4da7-a673-204bc83d9683.png)

You can have elements in original article body which need to be removed. Elements such as injected ads, or some referal links.
Just create a new Ignoring Attribute and add that selector of the HTML tag you want to remove from body or whatever.

**Replacing Elements**
![image](https://user-images.githubusercontent.com/22774727/131214721-cbe81d3b-6d21-4f8d-a8f9-656ee7310783.png)

If the body of the article you want to scrape has lazy-loaded media you can use this feature.
Unlike _Custom Tag attribute_ field from Attribute Main Configs tab this feature will work in a body or whereever.
For example if you want to scrape a single image and get the URL from custom attribute, you use _Custom Tag attribute_.
If you want to scrape an article body, but it **contains** media with lazyloading, you need to use it. The differense between these features is that Custom Tag attribute work for a single element with specific selector, while Replaing Elements feature works with CHILD elements in the element with a specific selector.

# Run scraping job
After you create a scraping job class a model with all configs, you can start the scraping process.
Just dispatch the ScrapingJob job and pass the new created model to the job construct.

# TODO
* Handle errors from Crawler and pass to the user while testing
* Handle all errors from Crawler and properly log
* Create queue management in dashboard to check the health and status of scraping queue
* Write tests
* Write full documentation

# Security

If you discover any security related issues, please email alexey.khachatryan@gmail.com
instead of using the issue tracker.

# Credits

- [Larra Press](https://github.com/larrapress/blog-poster)
- [All contributors](https://github.com/larrapress/blog-poster/graphs/contributors)

# Used packages
- [Theme by Creative Tim](https://www.creative-tim.com/)
- [Spatie Enum](https://github.com/spatie/enum)
- [Symfony CSS Selector](https://github.com/symfony/css-selector)
- [Symfony DOM Crawler](https://github.com/symfony/dom-crawler)

# Versioning
The version example: 1.0.0
The package version is divided by 3 parts:
- Global update
- Feature
- Bugfix

[ico-version]: https://img.shields.io/packagist/v/larra-press/blog-poster.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/396071507/shield
[link-packagist]: https://packagist.org/packages/larra-press/blog-poster
[link-styleci]: https://github.styleci.io/repos/396071507
[ico-downloads]: https://img.shields.io/packagist/dt/larra-press/blog-poster.svg?style=flat-square
[link-downloads]: https://packagist.org/packages/larra-press/blog-poster
