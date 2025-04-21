# WPContentVault

This is a command line tool for managing WordPress articles that allows you to import all articles from your website and work with them in Markdown format. I developed this tool for my own blog, so it may contain specific logic in some places, but it could potentially be useful to others as well.

## Why?

WordPress is an excellent CMS for creating a blog. It's stable, has good backward compatibility, and offers a huge number of plugins. However, it has some issues that this project tries to solve:

- Available solutions for creating multilingual sites require a lot of manual work, such as translation, image insertion. Moreover, synchronizing multiple translations when changing the original article is even more complicated
- Since the introduction of the Gutenberg editor, no console tool has been created to convert articles to the new format
- Images are inserted into articles as thumbnails; if you change the thumbnail size in settings and regenerate thumbnails, all images in articles will become broken with no way to update them without editing each article manually
- If you replace an image in an article with another one or delete the article entirely, the images will remain in the media library, and there's no way to find and delete them all without risking breaking something
- Images in articles are difficult to modify. For example, if you wanted to draw an arrow on one of the screenshots after you uploaded it, you would need to save this image on your PC, edit it, and then upload it again

This tool is designed to try to solve these problems using WordPress as a frontend. The idea is that all work with articles is done on your computer. Articles are stored in Markdown format, and all images related to the article are located in the article's folder.
When an article is ready, you execute a command that uploads it to the website. If you need to change the article or one of the images, you make the change and execute the command again.
Since all images are stored locally only in their original size, WordPress thumbnail settings don't matter; you can reload everything from scratch at any time.
Changing images becomes easier because you can simply edit the image and run a command that updates the article on the website.
Similarly, Gutenberg block settings become much easier to change because you can simply set the settings you need and generate the required articles.
But the biggest advantage is for multilingual sites. You edit only the original. When the original is ready, you execute a command and AI creates localizations for the needed language versions. If you need to update an article, you edit the original again and execute a command that updates the translations. This way, you don't have to think about how to synchronize changes between localizations.

## Disclaimer

The project is in Alpha. Much of what is described works, but errors and bugs may occur. Additionally, its structure and capabilities may change over time. Do not use this in Production!

## Technologies

- PHP programming language and Laravel framework (Laravel-Zero) were used for the implementation. Articles are most conveniently edited in Obsidian.
- SQLite is used as a database for storing image identifiers and translations.
- OpenRouter is used for AI interaction, which requires an API key to be passed as an environment variable, and the model used is Claude Sonnet 3.5.
- On the WordPress side, WPAjaxConnector is used for receiving and uploading posts.
- Also, for organizing a multilingual site, it's assumed that you have WordPress Multisite configured and use a separate sub-site for each locale. The Multisite Language Switcher plugin must be installed.

## System Requirements

- PHP: 8.2 and higher
- PHP Extensions: mbstring, pdo, pdo_mysql, mysqli, zip, intl, pcntl, gd, exif, dom, libxml

## Features

Supported:

- Importing posts from HTML to Markdown
- Exporting posts in Gutenberg format
- Exporting post titles
- Translating articles using AI

Not supported yet:

- Importing posts from Gutenberg to Markdown (Currently Gutenberg articles are first converted to HTML)
- Exporting post categories
- Exporting post tags
- Selecting post categories using AI
- Selecting post tags using AI
- Fixing parent_id for previously uploaded images
- Updating previously uploaded images with thumbnail regeneration

## Installation From Sources

The tool can be run from source in Docker. To run tool in Docker you need to clone the repository first:

```bash

```

Next, go to the project folder, build the container, and run the container shell:

```bash
./build.sh
./run.sh
```

In this shell, you can execute program commands using the application executable file. For example:

```bash
php application {command_name} {arguments}
```

## Commands

Supported commands:

- **create-vault** - initializes a new vault after program installation
- **create-article** - creates an article in the vault
- **discover-article-from-path {path}** - load an article to the database. When adding an article, the program parses Markdown and uploads all images from the article to the WordPress site, also for correctly setting parent_id for images on the site, an empty article is created to which content will be inserted in the future.
- **discover-articles** - add all articles from disk to the database.
- **reload-article {id}** - re-read an article from disk, accepts the article identifier on the main WordPress site.
- **reload-articles-from-disk** - re-read all previously added articles from disk
- **import-article {id}** - import an article from WordPress to local storage, downloads all images and article content
- **import-articles** - import all articles from WordPress to local storage
- **lint-article {id}** - check the article's Markdown file for errors, looks for common mistakes and outputs information about them to the console
- **lint-articles** - check all articles in the vault
- **translate-article {id}** - translate the article to all languages for which Manifest files have been created
- **upload-article {id}** - upload article content and translations to the site
- **upload-articles** - upload all articles and their content to the site

## Supported WordPress Blocks

- core/code
- core/heading
- core/image
- core/list
- core/video
- core/paragraph
- core/quote
- core/table
- core/embed

## Environment Variables

- WPCONTENTVAULT_APP_ENV - used in development
- WPCONTENTVAULT_DATABASE - database file name
- WPCONTENTVAULT_DATA_PATH - path to internal data, by default used data folder in current working directory
- WPCONTENTVAULT_VAULT_PATH - path to vault with articles, by default used vault dir in current folder
- WPCONTENTVAULT_LOG_LEVEL - logs verbosity

## Creating Vault

After installing and running the program, you need to initialize a Vault where articles will be stored, as well as fill in access data for websites and AI. To do this, execute the following command:

```bash
php applicoation create-vault
```

Follow the installation wizard instructions to create the Vault. After this, the following configuration files will appear in the vault folder:

- locales.json - supported languages
- sites.json - list of sites and access keys for them
- ai.json - AI client configuration
- articles - folder in which all articles will be located
- extensions - folder in which all extensions settings will be placed

## Importing Existing Articles

After the site access keys are configured, you can import articles from an existing site to the vault:

```bash
php application import-articles
```

This command will retrieve all articles from the main site and save them in the Vault. Each article gets a separate folder with a name containing the article title and its identifier. The article content, images, and preview are downloaded. Downloading article localizations from subsites is not yet supported.

## Creating a New Article

If you want to create a new article, you need to execute the following command, and then specify the article title and the original language. Here you can also select the locales to which you want to translate the article:

```bash
php application create-article
```

The command will create an article, and in the article folder, an `original.json` file with article metadata and an `original.md` file where you can start writing the article will appear.

## Discover Article

The fact that an article is created on disk does not yet mean that the tool will see it. To add an article to the database, you need to use this command:

```bash
php application discover-article-from-path {path_to_article_dir}
```

In this command, you need to specify the path to the folder where the article is located. This path is returned by the `create-article` command after successful creation.
And the `discover-article-from-path` command creates an empty article on the main site and returns its identifier, which you will then use to upload the article or to translate it.

## Translating Article

When the original is ready, to translate the article, you need to execute the following command:

```bash
php application trasnalte-article {external_id}
```

The command will perform the translation and create `.md` files for each locale for which a json file was created in the article folder. Changes during re-translation are overwritten, so if you need to fix something, it's better to always edit only the original.

## Uploading Article

And when article is ready, you can upload it to the site. Use this command:

```bash
php application upload-article {external_id}
```

The command will update the article on the site according to the article in the Vault. It will upload all translations as Gutenberg blocks and change the title.